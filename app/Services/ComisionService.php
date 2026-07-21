<?php

namespace App\Services;

use App\Models\comision_detalle;
use App\Models\comision_periodo;
use App\Models\cronograma;
use App\Models\Prestamo;
use App\Models\sistema_config;
use App\Models\tipo_gasto;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ComisionService
{
    public const PORCENTAJE_DEFAULT = 30.0;

    public function porcentajeDecimal(): float
    {
        return $this->porcentaje() / 100;
    }

    public function porcentaje(): float
    {
        return sistema_config::valorFloat('comision_interes_porcentaje', self::PORCENTAJE_DEFAULT);
    }

    /** Tipo de gasto para el pago de comisiones (ADELANTOS o tipo con "COMISION" en el nombre). */
    public function tipoGastoId(): int
    {
        $configurado = sistema_config::valor('comision_tipo_gasto_id');
        if ($configurado && ($tipo = tipo_gasto::where('tipo_gasto_id', (int) $configurado)->where('status', 'A')->first())) {
            return (int) $tipo->tipo_gasto_id;
        }

        $comision = tipo_gasto::where('status', 'A')
            ->where(function ($q) {
                $q->where('nombre', 'like', '%COMISION%')
                    ->orWhere('nombre', 'like', '%COMISIÓN%');
            })
            ->first();

        if ($comision) {
            return (int) $comision->tipo_gasto_id;
        }

        $adelantos = tipo_gasto::where('status', 'A')->where('nombre', 'like', '%ADELANTO%')->first();

        return (int) ($adelantos->tipo_gasto_id ?? 6);
    }

    public function calcularComision(float $interesPagado): float
    {
        return round($interesPagado * $this->porcentajeDecimal(), 2);
    }

    public function calcularInteresPagado(array $pagoGrupalItem, cronograma $cronograma): float
    {
        $yesInteres = ($pagoGrupalItem['yes_interes'] ?? $cronograma->yes_interes) === 'Y';

        if (!$yesInteres) {
            return 0.0;
        }

        $interes = (float) $cronograma->interes;

        if (($pagoGrupalItem['yes_mora'] ?? 'N') === 'Y' && $interes > 0) {
            $vencimiento = Carbon::parse($cronograma->fechaVencimiento)->startOfDay();
            $hoy = Carbon::today();
            if ($hoy->gt($vencimiento)) {
                $dias = min(30, $vencimiento->diffInDays($hoy));
                $interes += ((float) $cronograma->interes / 30) * $dias;
            }
        }

        return round($interes, 2);
    }

    public function acumularDesdePagoGrupal(
        Prestamo $prestamo,
        int $ingresoId,
        array $pagoGrupal,
        ?Carbon $fecha = null
    ): void {
        if (!$prestamo->analista_id) {
            return;
        }

        $fecha = $fecha ?? Carbon::now();

        foreach ($pagoGrupal as $item) {
            try {
                $cronogramaId = \App\Helpers\Encryptor::decrypt($item['urlapi']);
            } catch (\Throwable) {
                continue;
            }

            $cronograma = cronograma::find($cronogramaId);
            if (!$cronograma) {
                continue;
            }

            $interesPagado = $this->calcularInteresPagado($item, $cronograma);
            if ($interesPagado <= 0) {
                continue;
            }

            $this->registrarLinea(
                (int) $prestamo->analista_id,
                (int) ($prestamo->sucursal_id ?? 0),
                $fecha,
                $ingresoId,
                null,
                (int) $cronograma->cronograma_id,
                (int) $prestamo->prestamo_id,
                $interesPagado,
                'Cuota período ' . ($item['periodo'] ?? $cronograma->periodo) . ' — Préstamo ' . $prestamo->code
            );
        }
    }

    public function registrarLinea(
        int $trabajadorId,
        int $sucursalId,
        Carbon $fecha,
        ?int $ingresoId,
        ?int $detalleIngresoId,
        ?int $cronogramaId,
        ?int $prestamoId,
        float $interesPagado,
        string $descripcion
    ): ?comision_detalle {
        if ($interesPagado <= 0) {
            return null;
        }

        $comisionMonto = $this->calcularComision($interesPagado);
        $periodo = $this->obtenerOCrearPeriodoPendiente($trabajadorId, $sucursalId, $fecha);
        $anio = (int) $fecha->format('Y');
        $mes = (int) $fecha->format('n');

        if ($this->existeLineaCuota($trabajadorId, $anio, $mes, $cronogramaId, $ingresoId, $periodo->comision_periodo_id)) {
            return null;
        }

        $detalle = comision_detalle::create([
            'comision_periodo_id' => $periodo->comision_periodo_id,
            'ingreso_id' => $ingresoId,
            'detalle_ingreso_id' => $detalleIngresoId,
            'cronograma_id' => $cronogramaId,
            'prestamo_id' => $prestamoId,
            'interes_pagado' => $interesPagado,
            'comision_monto' => $comisionMonto,
            'descripcion' => $descripcion,
        ]);

        $periodo->monto_interes_pagado = round((float) $periodo->monto_interes_pagado + $interesPagado, 2);
        $periodo->monto_acumulado = round((float) $periodo->monto_acumulado + $comisionMonto, 2);
        $periodo->save();

        return $detalle;
    }

    public function obtenerOCrearPeriodoPendiente(int $trabajadorId, int $sucursalId, Carbon $fecha): comision_periodo
    {
        $anio = (int) $fecha->format('Y');
        $mes = (int) $fecha->format('n');

        $periodo = comision_periodo::where('trabajador_id', $trabajadorId)
            ->where('anio', $anio)
            ->where('mes', $mes)
            ->where('status', 'P')
            ->first();

        if ($periodo) {
            return $periodo;
        }

        return comision_periodo::create([
            'trabajador_id' => $trabajadorId,
            'sucursal_id' => $sucursalId ?: null,
            'anio' => $anio,
            'mes' => $mes,
            'monto_interes_pagado' => 0,
            'monto_acumulado' => 0,
            'status' => 'P',
        ]);
    }

    /**
     * Si se eliminó el gasto de un pago de comisión, el período vuelve a pendiente
     * y se recalcula el acumulado desde el detalle de cuotas.
     */
    public function revertirPorGasto(int $gastosId): bool
    {
        $periodo = comision_periodo::where('gastos_id', $gastosId)->where('status', 'C')->first();

        if (!$periodo) {
            return false;
        }

        $this->revertirPeriodoProcesado($periodo);

        return true;
    }

    /**
     * Períodos marcados como procesados pero cuyo gasto ya no existe (eliminado).
     */
    public function revertirPeriodosHuerfanos(?int $trabajadorId = null, ?int $sucursalId = null): int
    {
        $query = comision_periodo::query()
            ->where('status', 'C')
            ->whereNotNull('gastos_id')
            ->whereDoesntHave('gasto');

        if ($trabajadorId) {
            $query->where('trabajador_id', $trabajadorId);
        }

        if ($sucursalId) {
            $query->where('sucursal_id', $sucursalId);
        }

        $count = 0;
        foreach ($query->get() as $periodo) {
            $this->revertirPeriodoProcesado($periodo);
            $count++;
        }

        return $count;
    }

    public function revertirPeriodoProcesado(comision_periodo $periodo): void
    {
        if ($periodo->status !== 'C') {
            return;
        }

        $existenteP = comision_periodo::where('trabajador_id', $periodo->trabajador_id)
            ->where('anio', $periodo->anio)
            ->where('mes', $periodo->mes)
            ->where('status', 'P')
            ->where('comision_periodo_id', '!=', $periodo->comision_periodo_id)
            ->first();

        if ($existenteP) {
            comision_detalle::where('comision_periodo_id', $periodo->comision_periodo_id)
                ->update(['comision_periodo_id' => $existenteP->comision_periodo_id]);
            $periodo->delete();
            $existenteP = $existenteP->fresh();
            $this->deduplicarDetallesPeriodo($existenteP);
            $this->recalcularPeriodo($existenteP);

            return;
        }

        $periodo->status = 'P';
        $periodo->gastos_id = null;
        $periodo->procesado_por = null;
        $periodo->fecha_procesado = null;
        $periodo->save();

        $this->recalcularPeriodo($periodo->fresh());
    }

    /**
     * Unifica varios períodos pendientes del mismo trabajador y mes en uno solo.
     */
    public function consolidarPeriodosPendientes(
        ?int $trabajadorId = null,
        ?int $sucursalId = null,
        ?int $mes = null,
        ?int $anio = null
    ): int {
        $query = comision_periodo::query()->where('status', 'P');

        if ($trabajadorId) {
            $query->where('trabajador_id', $trabajadorId);
        }
        if ($sucursalId) {
            $query->where('sucursal_id', $sucursalId);
        }
        if ($mes) {
            $query->where('mes', $mes);
        }
        if ($anio) {
            $query->where('anio', $anio);
        }

        $fusionados = 0;
        $grupos = $query->get()->groupBy(fn ($p) => "{$p->trabajador_id}-{$p->anio}-{$p->mes}");

        foreach ($grupos as $grupo) {
            if ($grupo->count() <= 1) {
                continue;
            }

            $principal = $grupo->sortBy('comision_periodo_id')->first();

            foreach ($grupo->where('comision_periodo_id', '!=', $principal->comision_periodo_id) as $duplicado) {
                comision_detalle::where('comision_periodo_id', $duplicado->comision_periodo_id)
                    ->update(['comision_periodo_id' => $principal->comision_periodo_id]);
                $duplicado->delete();
                $fusionados++;
            }

            $principal = $principal->fresh();
            $this->deduplicarDetallesPeriodo($principal);
            $this->recalcularPeriodo($principal);
        }

        return $fusionados;
    }

    /**
     * Elimina líneas repetidas de la misma cuota (mismo cronograma) en un período.
     */
    public function deduplicarDetallesPeriodo(comision_periodo $periodo): int
    {
        $detalles = comision_detalle::where('comision_periodo_id', $periodo->comision_periodo_id)->get();
        $eliminados = 0;

        $grupos = $detalles->groupBy(function ($d) {
            if ($d->cronograma_id) {
                return 'c' . $d->cronograma_id;
            }

            return 'i' . ($d->ingreso_id ?? 0) . '-p' . ($d->prestamo_id ?? 0);
        });

        foreach ($grupos as $grupo) {
            if ($grupo->count() <= 1) {
                continue;
            }

            $keeper = $grupo->sortByDesc(function ($d) {
                $score = 0;
                if ($d->ingreso_id) {
                    $score += 20;
                }
                if ($d->descripcion && stripos($d->descripcion, 'demo') === false) {
                    $score += 10;
                }

                return $score;
            })->first();

            foreach ($grupo as $detalle) {
                if ($detalle->comision_detalle_id !== $keeper->comision_detalle_id) {
                    $detalle->delete();
                    $eliminados++;
                }
            }
        }

        return $eliminados;
    }

    private function existeLineaCuota(
        int $trabajadorId,
        int $anio,
        int $mes,
        ?int $cronogramaId,
        ?int $ingresoId,
        int $periodoActualId
    ): bool {
        $baseQuery = function ($query) use ($trabajadorId, $anio, $mes) {
            $query->where('trabajador_id', $trabajadorId)
                ->where('anio', $anio)
                ->where('mes', $mes)
                ->where('status', 'P');
        };

        if ($cronogramaId) {
            if (comision_detalle::where('cronograma_id', $cronogramaId)
                ->whereHas('periodo', $baseQuery)
                ->exists()) {
                return true;
            }
        }

        if ($ingresoId && $cronogramaId) {
            return comision_detalle::where('comision_periodo_id', $periodoActualId)
                ->where('ingreso_id', $ingresoId)
                ->where('cronograma_id', $cronogramaId)
                ->exists();
        }

        return false;
    }

    public function resolverPeriodoPendiente(
        int $trabajadorId,
        int $mes,
        int $anio,
        ?int $sucursalId = null
    ): ?comision_periodo {
        $this->consolidarPeriodosPendientes($trabajadorId, $sucursalId, $mes, $anio);

        $query = comision_periodo::where('trabajador_id', $trabajadorId)
            ->where('mes', $mes)
            ->where('anio', $anio)
            ->where('status', 'P');

        if ($sucursalId) {
            $query->where('sucursal_id', $sucursalId);
        }

        $periodo = $query->orderByDesc('monto_acumulado')->first();

        if ($periodo) {
            $this->deduplicarDetallesPeriodo($periodo);
            $this->recalcularPeriodo($periodo->fresh());
            $periodo = $periodo->fresh();
        }

        return $periodo;
    }

    public function revertirPorIngreso(int $ingresoId): void
    {
        $detalles = comision_detalle::where('ingreso_id', $ingresoId)->get();

        foreach ($detalles as $detalle) {
            $periodo = comision_periodo::find($detalle->comision_periodo_id);
            if ($periodo && $periodo->status === 'P') {
                $periodo->monto_interes_pagado = max(0, round((float) $periodo->monto_interes_pagado - (float) $detalle->interes_pagado, 2));
                $periodo->monto_acumulado = max(0, round((float) $periodo->monto_acumulado - (float) $detalle->comision_monto, 2));
                $periodo->save();
            }
            $detalle->delete();
        }
    }

    /**
     * Recalcula comisión de cada línea con el % vigente y actualiza totales del período.
     */
    public function recalcularPeriodo(comision_periodo $periodo): array
    {
        if ($periodo->status !== 'P') {
            throw new \InvalidArgumentException('Solo se puede recalcular un período pendiente.');
        }

        $this->deduplicarDetallesPeriodo($periodo);
        $periodo = $periodo->fresh();

        $pctDecimal = $this->porcentajeDecimal();
        $detalles = comision_detalle::where('comision_periodo_id', $periodo->comision_periodo_id)->get();

        $totalInteres = 0.0;
        $totalComision = 0.0;

        foreach ($detalles as $detalle) {
            $interes = round((float) $detalle->interes_pagado, 2);
            $comision = $this->calcularComision($interes);

            if ((float) $detalle->comision_monto !== $comision) {
                $detalle->comision_monto = $comision;
                $detalle->save();
            }

            $totalInteres += $interes;
            $totalComision += $comision;
        }

        $periodo->monto_interes_pagado = round($totalInteres, 2);
        $periodo->monto_acumulado = round($totalComision, 2);
        $periodo->save();

        return $this->armarResumenPeriodo($periodo->fresh(['trabajador']));
    }

    public function armarResumenPeriodo(comision_periodo $periodo): array
    {
        $pct = $this->porcentaje();

        $detalles = comision_detalle::with(['prestamo.solicitud.cliente'])
            ->where('comision_periodo_id', $periodo->comision_periodo_id)
            ->orderBy('prestamo_id')
            ->orderByDesc('created_at')
            ->get();

        $lineas = $detalles->map(fn ($d) => $this->formatearLineaDetalle($d, $pct))->values();

        $agrupado = [];
        foreach ($lineas as $linea) {
            $key = (string) ($linea['prestamo_id'] ?? 'sin_prestamo');
            if (!isset($agrupado[$key])) {
                $agrupado[$key] = [
                    'prestamo_id' => $linea['prestamo_id'],
                    'prestamo_code' => $linea['prestamo_code'], // = N° solicitud
                    'solicitud_code' => $linea['solicitud_code'] ?? $linea['prestamo_code'],
                    'prestamo_urlapi' => $linea['prestamo_urlapi'],
                    'planilla_url' => $linea['planilla_url'],
                    'cliente_nombre' => $linea['cliente_nombre'],
                    'cuotas' => 0,
                    'interes_total' => 0.0,
                    'comision_total' => 0.0,
                    'porcentaje' => $pct,
                    'lineas' => [],
                ];
            }
            $agrupado[$key]['cuotas']++;
            $agrupado[$key]['interes_total'] = round($agrupado[$key]['interes_total'] + (float) $linea['interes_pagado'], 2);
            $agrupado[$key]['comision_total'] = round($agrupado[$key]['comision_total'] + (float) $linea['comision_monto'], 2);
            $agrupado[$key]['lineas'][] = $linea;
        }

        $porPrestamo = array_values($agrupado);

        return [
            'periodo' => $periodo,
            'porcentaje' => $pct,
            'lineas' => $lineas,
            'por_prestamo' => $porPrestamo,
            'totales' => [
                'cuotas' => $lineas->count(),
                'prestamos' => count($porPrestamo),
                'interes' => round((float) $periodo->monto_interes_pagado, 2),
                'comision' => round((float) $periodo->monto_acumulado, 2),
                'porcentaje' => $pct,
            ],
        ];
    }

    private function formatearLineaDetalle(comision_detalle $detalle, float $porcentaje): array
    {
        $prestamo = $detalle->prestamo;
        $clienteNombre = '—';
        $solicitudCode = '—';
        $prestamoUrlapi = null;
        $planillaUrl = null;

        if ($prestamo) {
            $prestamoUrlapi = $prestamo->urlapi ?? null;
            // Número de solicitud (no serie del préstamo)
            $solicitud = $prestamo->solicitud;
            if ($solicitud) {
                $solicitudCode = $solicitud->code
                    ?? sprintf('%06d', $solicitud->serie ?? 0);
            }
            $planillaUrl = '/planilla_prestamos/' . ($prestamo->code ?? $solicitudCode);
            $cliente = $solicitud?->cliente;
            if ($cliente) {
                $clienteNombre = trim(($cliente->cli_nombre ?? '') . ' ' . ($cliente->cli_apellido ?? ''));
            } elseif ($solicitud?->solicitud_nombre) {
                $clienteNombre = $solicitud->solicitud_nombre;
            }
        }

        return [
            'comision_detalle_id' => $detalle->comision_detalle_id,
            'prestamo_id' => $detalle->prestamo_id,
            // Código mostrado = N° de solicitud
            'prestamo_code' => $solicitudCode,
            'solicitud_code' => $solicitudCode,
            'prestamo_urlapi' => $prestamoUrlapi,
            'planilla_url' => $planillaUrl,
            'cliente_nombre' => $clienteNombre,
            'descripcion' => $detalle->descripcion,
            'interes_pagado' => (float) $detalle->interes_pagado,
            'comision_monto' => (float) $detalle->comision_monto,
            'porcentaje' => $porcentaje,
            'created_at' => $detalle->created_at,
        ];
    }
}