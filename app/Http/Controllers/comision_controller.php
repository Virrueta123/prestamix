<?php

namespace App\Http\Controllers;

use App\Helpers\Encryptor;
use App\Models\caja;
use App\Models\comision_detalle;
use App\Models\comision_periodo;
use App\Models\gastos;
use App\Models\pagos;
use App\Models\sistema_config;
use App\Models\User;
use App\Services\CajaService;
use App\Services\ComisionService;
use App\Utils\funciones;
use App\Utils\ticketera;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class comision_controller extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('modules.comision.index');
    }

    public function show_cobrador(string $trabajador_id)
    {
        try {
            $id = Encryptor::decrypt($trabajador_id);
        } catch (\Throwable) {
            abort(404);
        }

        $trabajador = User::find($id);
        if (!$trabajador) {
            abort(404);
        }

        $mes = (int) request('mes', Carbon::now()->format('n'));
        $anio = (int) request('anio', Carbon::now()->format('Y'));

        return view('modules.comision.cobrador', compact('trabajador', 'mes', 'anio'));
    }

    public function load_cobrador_comisiones(Request $request)
    {
        try {
            $trabajadorId = Encryptor::decrypt($request->input('trabajador_id'));
            $mes = (int) ($request->input('mes') ?: Carbon::now()->format('n'));
            $anio = (int) ($request->input('anio') ?: Carbon::now()->format('Y'));

            $trabajador = User::find($trabajadorId);
            if (!$trabajador) {
                return response()->json(['success' => false, 'message' => 'Cobrador no encontrado.'], 404);
            }

            $service = app(ComisionService::class);
            $service->revertirPeriodosHuerfanos($trabajadorId, Auth::user()->sucursal_id);
            $service->consolidarPeriodosPendientes($trabajadorId, Auth::user()->sucursal_id, $mes, $anio);

            $historial = comision_periodo::with(['gasto'])
                ->withCount('detalles')
                ->where('trabajador_id', $trabajadorId)
                ->where('sucursal_id', Auth::user()->sucursal_id)
                ->where('status', 'C')
                ->whereHas('gasto')
                ->orderByDesc('fecha_procesado')
                ->limit(36)
                ->get()
                ->map(function ($p) {
                    $p->prestamos_count = comision_detalle::where('comision_periodo_id', $p->comision_periodo_id)
                        ->whereNotNull('prestamo_id')
                        ->distinct('prestamo_id')
                        ->count('prestamo_id');

                    return $p;
                });

            $pendiente = $service->resolverPeriodoPendiente(
                $trabajadorId,
                $mes,
                $anio,
                Auth::user()->sucursal_id
            );

            $acumulado = null;
            if ($pendiente) {
                $acumulado = $service->armarResumenPeriodo($pendiente->load('trabajador'));
            }

            return response()->json([
                'success' => true,
                'trabajador' => $trabajador,
                'mes' => $mes,
                'anio' => $anio,
                'porcentaje' => $service->porcentaje(),
                'historial_pagos' => $historial,
                'acumulado_actual' => $acumulado,
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());

            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function load_comisiones(Request $request)
    {
        try {
            $mes = (int) ($request->input('mes') ?: Carbon::now()->format('n'));
            $anio = (int) ($request->input('anio') ?: Carbon::now()->format('Y'));

            $service = app(ComisionService::class);
            $service->revertirPeriodosHuerfanos(null, Auth::user()->sucursal_id);
            $service->consolidarPeriodosPendientes(null, Auth::user()->sucursal_id, $mes, $anio);

            $periodos = comision_periodo::with(['trabajador'])
                ->withCount('detalles')
                ->where('sucursal_id', Auth::user()->sucursal_id)
                ->where('anio', $anio)
                ->where('mes', $mes)
                ->orderByDesc('monto_acumulado')
                ->get()
                ->groupBy('trabajador_id')
                ->map(function ($grupo) {
                    $pendiente = $grupo->firstWhere('status', 'P');
                    if ($pendiente) {
                        return $pendiente;
                    }

                    return $grupo->where('status', 'C')
                        ->sortByDesc('fecha_procesado')
                        ->first();
                })
                ->filter()
                ->values()
                ->sortByDesc('monto_acumulado')
                ->values();

            $periodos = $periodos->map(function ($periodo) use ($service) {
                $prestamosCount = comision_detalle::where('comision_periodo_id', $periodo->comision_periodo_id)
                    ->whereNotNull('prestamo_id')
                    ->distinct('prestamo_id')
                    ->count('prestamo_id');

                $periodo->prestamos_count = $prestamosCount;

                return $periodo;
            });

            return response()->json([
                'message' => 'Comisiones cargadas',
                'success' => true,
                'data' => $periodos,
                'porcentaje' => $service->porcentaje(),
                'mes' => $mes,
                'anio' => $anio,
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json([
                'message' => 'Error del servidor',
                'error' => $th->getMessage(),
                'success' => false,
                'data' => [],
            ], 500);
        }
    }

    public function load_detalle(Request $request)
    {
        try {
            $periodoId = Encryptor::decrypt($request->input('comision_periodo_id'));
            $periodo = comision_periodo::with('trabajador')->find($periodoId);

            if (!$periodo) {
                return response()->json(['success' => false, 'message' => 'Período no encontrado.'], 404);
            }

            $service = app(ComisionService::class);
            $resumen = $service->armarResumenPeriodo($periodo);

            return response()->json([
                'success' => true,
                'data' => $resumen,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'data' => [], 'message' => $th->getMessage()], 500);
        }
    }

    public function recalcular_comision(Request $request)
    {
        try {
            $periodoId = Encryptor::decrypt($request->input('comision_periodo_id'));
            $periodoInicial = comision_periodo::with('trabajador')->find($periodoId);

            if (!$periodoInicial) {
                return response()->json(['success' => false, 'message' => 'Período no encontrado.'], 404);
            }

            $service = app(ComisionService::class);
            $service->revertirPeriodosHuerfanos((int) $periodoInicial->trabajador_id, Auth::user()->sucursal_id);
            $service->consolidarPeriodosPendientes(
                (int) $periodoInicial->trabajador_id,
                Auth::user()->sucursal_id,
                (int) $periodoInicial->mes,
                (int) $periodoInicial->anio
            );

            $periodo = comision_periodo::with('trabajador')->find($periodoId)
                ?? $service->resolverPeriodoPendiente(
                    (int) $periodoInicial->trabajador_id,
                    (int) $periodoInicial->mes,
                    (int) $periodoInicial->anio,
                    Auth::user()->sucursal_id
                );

            if (!$periodo || $periodo->status !== 'P') {
                return response()->json(['success' => false, 'message' => 'No hay comisión pendiente para calcular.'], 422);
            }

            $resumen = $service->recalcularPeriodo($periodo);

            return response()->json([
                'success' => true,
                'message' => 'Totales recalculados desde el detalle de cuotas.',
                'data' => $resumen,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }

    public function procesar_comision(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole(['gerente', 'super_admin', 'admin'])) {
            return response()->json(['success' => false, 'message' => 'Sin permiso para procesar comisiones.'], 403);
        }

        $committed = false;
        $gastoRespuesta = null;

        try {
            $periodoId = Encryptor::decrypt($request->input('comision_periodo_id'));
            $periodoInicial = comision_periodo::with('trabajador')->find($periodoId);
            $service = app(ComisionService::class);

            if (!$periodoInicial) {
                return response()->json(['success' => false, 'message' => 'Período de comisión no encontrado.'], 404);
            }

            if ($periodoInicial->status === 'C' && $periodoInicial->gastos_id) {
                $gastoExistente = gastos::find($periodoInicial->gastos_id);
                if ($gastoExistente) {
                    return $this->respuestaComisionProcesada([
                        'codigo' => $gastoExistente->codigo,
                        'caja_chica_id' => $gastoExistente->caja_chica_id,
                        'caja_codigo' => sprintf('%06d', $gastoExistente->caja_chica_id ?? 0),
                        'monto' => (float) $gastoExistente->monto,
                    ], true);
                }
            }

            $trabajadorId = (int) $periodoInicial->trabajador_id;
            $mesPeriodo = (int) $periodoInicial->mes;
            $anioPeriodo = (int) $periodoInicial->anio;

            $service->revertirPeriodosHuerfanos($trabajadorId, $user->sucursal_id);
            $service->consolidarPeriodosPendientes($trabajadorId, $user->sucursal_id, $mesPeriodo, $anioPeriodo);

            $periodo = comision_periodo::with('trabajador')->find($periodoId)
                ?? $service->resolverPeriodoPendiente($trabajadorId, $mesPeriodo, $anioPeriodo, $user->sucursal_id);

            if ($periodo && $periodo->status === 'C') {
                $gastoVigente = $periodo->gastos_id && gastos::find($periodo->gastos_id);
                if (!$gastoVigente) {
                    $service->revertirPeriodoProcesado($periodo);
                    $periodo = $service->resolverPeriodoPendiente($trabajadorId, $mesPeriodo, $anioPeriodo, $user->sucursal_id);
                } else {
                    $alterno = $service->resolverPeriodoPendiente($trabajadorId, $mesPeriodo, $anioPeriodo, $user->sucursal_id);
                    if ($alterno) {
                        $periodo = $alterno;
                    } else {
                        $gastoExistente = gastos::find($periodo->gastos_id);

                        return $this->respuestaComisionProcesada([
                            'codigo' => $gastoExistente?->codigo ?? '',
                            'caja_chica_id' => $gastoExistente?->caja_chica_id,
                            'caja_codigo' => sprintf('%06d', $gastoExistente?->caja_chica_id ?? 0),
                            'monto' => (float) ($gastoExistente?->monto ?? 0),
                        ], true);
                    }
                }
            }

            if (!$periodo || $periodo->status !== 'P') {
                $periodo = $service->resolverPeriodoPendiente($trabajadorId, $mesPeriodo, $anioPeriodo, $user->sucursal_id);
            }

            if (!$periodo || $periodo->status !== 'P') {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay comisión pendiente para procesar en este período.',
                ], 422);
            }
            $resumen = $service->recalcularPeriodo($periodo);
            $periodo = $resumen['periodo'];

            $montoPago = $request->has('monto')
                ? round((float) $request->input('monto'), 2)
                : round((float) $periodo->monto_acumulado, 2);

            if ($montoPago <= 0) {
                return response()->json(['success' => false, 'message' => 'El monto a pagar debe ser mayor a cero.'], 422);
            }

            if ($montoPago !== round((float) $periodo->monto_acumulado, 2)) {
                $periodo->monto_acumulado = $montoPago;
                $periodo->save();
            }

            $cajaService = app(CajaService::class);
            $caja = $cajaService->resolverCajaDisponible($user);

            if (!$caja) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay caja chica abierta. Abra una caja en el módulo Caja chica e intente de nuevo.',
                ], 422);
            }

            DB::beginTransaction();

            $pct = $service->porcentaje();
            $nombreTrabajador = trim(($periodo->trabajador->name ?? '') . ' ' . ($periodo->trabajador->lastname ?? ''));
            $descripcion = 'Comisión ' . $pct . '% interés — ' . $nombreTrabajador . ' — ' . $periodo->mes_nombre . ' ' . $periodo->anio;
            $cajaCodigo = sprintf('%06d', $caja->caja_chica_id);

            $gasto = new gastos();
            $gasto->gastos_descripcion = $descripcion;
            $gasto->monto = $montoPago;
            $gasto->tipo_gasto_id = $service->tipoGastoId();
            $gasto->analista_id = $periodo->trabajador_id;
            $gasto->created_user = $user->id;
            $gasto->sucursal_id = $user->sucursal_id;
            $gasto->caja_chica_id = $caja->caja_chica_id;
            $gasto->codigo = 'Co' . funciones::generar_codigo($montoPago);
            $gasto->save();

            $pago = new pagos();
            $pago->gastos_id = $gasto->gastos_id;
            $pago->monto = $gasto->monto;
            $pago->cuentas_id = 1;
            $pago->tipo = 'G';
            $pago->caja_chica_id = $caja->caja_chica_id;
            $pago->created_user = $user->id;
            $pago->sucursal_id = $user->sucursal_id;
            $pago->save();

            $periodo->status = 'C';
            $periodo->gastos_id = $gasto->gastos_id;
            $periodo->procesado_por = $user->id;
            $periodo->fecha_procesado = now();
            $periodo->save();

            DB::commit();
            $committed = true;

            $gastoRespuesta = [
                'codigo' => $gasto->codigo,
                'caja_chica_id' => $caja->caja_chica_id,
                'caja_codigo' => $cajaCodigo,
                'monto' => $montoPago,
            ];

            try {
                $showGasto = gastos::with(['tipo_gasto', 'usuario'])->find($gasto->gastos_id);
                if ($showGasto) {
                    ticketera::imprimir_gasto(
                        $showGasto->gastos_descripcion,
                        $showGasto->tipo_gasto->nombre ?? 'Comisión',
                        $showGasto->codigo,
                        $showGasto->usuario->name ?? $user->name,
                        $showGasto->monto
                    );
                }
            } catch (\Throwable $e) {
                Log::warning('Ticketera comisión: ' . $e->getMessage());
            }

            return $this->respuestaComisionProcesada($gastoRespuesta);
        } catch (\Throwable $th) {
            if ($committed && $gastoRespuesta) {
                Log::warning('procesar_comision post-commit: ' . $th->getMessage());

                return $this->respuestaComisionProcesada($gastoRespuesta);
            }

            DB::rollBack();
            Log::error('procesar_comision: ' . $th->getMessage(), ['trace' => $th->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar comisión: ' . $th->getMessage(),
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    private function respuestaComisionProcesada(array $gasto, bool $yaProcesado = false)
    {
        $cajaCodigo = $gasto['caja_codigo'] ?? sprintf('%06d', $gasto['caja_chica_id'] ?? 0);
        $codigoGasto = $gasto['codigo'] ?? '';

        return response()->json([
            'success' => true,
            'already_processed' => $yaProcesado,
            'message' => $yaProcesado
                ? "Esta comisión ya fue pagada (gasto {$codigoGasto})."
                : "Comisión procesada. Gasto registrado en caja #{$cajaCodigo}. El acumulado reinicia en cero para el próximo mes.",
            'gasto' => [
                'codigo' => $codigoGasto,
                'caja_chica_id' => $gasto['caja_chica_id'] ?? null,
                'caja_codigo' => $cajaCodigo,
                'monto' => $gasto['monto'] ?? null,
            ],
        ]);
    }

    public function get_config()
    {
        $service = app(ComisionService::class);

        return response()->json([
            'success' => true,
            'porcentaje' => $service->porcentaje(),
        ]);
    }

    public function save_config(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole(['gerente', 'super_admin', 'admin'])) {
            return response()->json(['success' => false, 'message' => 'Sin permiso para cambiar la configuración.'], 403);
        }

        $porcentaje = (float) $request->input('porcentaje');
        if ($porcentaje < 0 || $porcentaje > 100) {
            return response()->json(['success' => false, 'message' => 'El porcentaje debe estar entre 0 y 100.'], 422);
        }

        sistema_config::guardar(
            'comision_interes_porcentaje',
            (string) $porcentaje,
            $user->id,
            'Porcentaje de comisión del trabajador cobrador sobre el interés pagado'
        );

        return response()->json([
            'success' => true,
            'message' => 'Porcentaje de comisión actualizado.',
            'porcentaje' => $porcentaje,
        ]);
    }
}