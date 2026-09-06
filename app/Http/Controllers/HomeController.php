<?php

namespace App\Http\Controllers;

use App\Events\notificacion_gerente_event;
use App\Models\Cliente;
use App\Models\comision_periodo;
use App\Models\cronograma;
use App\Models\Prestamo;
use App\Services\ComisionService;
use App\Utils\funciones;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Hash;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Mpdf\Mpdf;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('bloqueado');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    { 
        // dd(Hash::make("Calixto789"));
        return view('home');
    }

    public function dashboard_resumen()
    {
        try {
            $sucursalId = Auth::user()->sucursal_id;
            $now = Carbon::now();
            $mes = (int) $now->format('n');
            $anio = (int) $now->format('Y');

            $prestamosActivos = Prestamo::with(['solicitud'])
                ->where('status', 'A')
                ->where('sucursal_id', $sucursalId)
                ->get();

            $capitalTotal = 0.0;
            foreach ($prestamosActivos as $prestamo) {
                $amortizado = (float) cronograma::where('prestamo_id', $prestamo->prestamo_id)
                    ->where('yes_pago', 'Y')
                    ->sum('amortizacion');
                $saldo = (float) $prestamo->moto_credito - $amortizado;
                $capitalTotal += max(0, $saldo);
            }

            $clientesActivos = $prestamosActivos
                ->map(function ($p) {
                    return $p->solicitud->cli_id ?? $p->cli_id ?? null;
                })
                ->filter()
                ->unique()
                ->count();

            $service = app(ComisionService::class);
            $service->revertirPeriodosHuerfanos(null, $sucursalId);
            $service->consolidarPeriodosPendientes(null, $sucursalId, $mes, $anio);

            $periodosMes = comision_periodo::where('sucursal_id', $sucursalId)
                ->where('anio', $anio)
                ->where('mes', $mes)
                ->get();

            $interesMes = (float) $periodosMes->sum('monto_interes_pagado');
            $comisionTrabajador = (float) $periodosMes->sum('monto_acumulado');

            $mesesCortos = [
                1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr',
                5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
                9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic',
            ];

            $evolucion = [];
            for ($i = 6; $i >= 0; $i--) {
                $fechaMes = $now->copy()->subMonths($i);
                $m = (int) $fechaMes->format('n');
                $y = (int) $fechaMes->format('Y');

                $prestamosMes = (float) Prestamo::where('sucursal_id', $sucursalId)
                    ->whereYear('created_at', $y)
                    ->whereMonth('created_at', $m)
                    ->sum('moto_credito');

                $comisionesMes = (float) comision_periodo::where('sucursal_id', $sucursalId)
                    ->where('anio', $y)
                    ->where('mes', $m)
                    ->sum('monto_acumulado');

                $evolucion[] = [
                    'mes' => $m,
                    'anio' => $y,
                    'label' => $mesesCortos[$m],
                    'prestamos' => round($prestamosMes, 2),
                    'comisiones' => round($comisionesMes, 2),
                ];
            }

            $prev = $now->copy()->subMonth();
            $periodosPrev = comision_periodo::where('sucursal_id', $sucursalId)
                ->where('anio', (int) $prev->format('Y'))
                ->where('mes', (int) $prev->format('n'))
                ->get();
            $interesPrev = (float) $periodosPrev->sum('monto_interes_pagado');
            $comisionPrev = (float) $periodosPrev->sum('monto_acumulado');

            $delta = function (float $actual, float $anterior) {
                if ($anterior <= 0) {
                    return $actual > 0 ? 100.0 : 0.0;
                }

                return round((($actual - $anterior) / $anterior) * 100, 1);
            };

            $ultimosClientes = Cliente::where('sucursal_id', $sucursalId)
                ->orderByDesc('cli_id')
                ->limit(5)
                ->get()
                ->map(function (Cliente $cliente) {
                    $prestamo = Prestamo::with('solicitud')
                        ->where(function ($q) use ($cliente) {
                            $q->where('cli_id', $cliente->cli_id)
                                ->orWhereHas('solicitud', function ($s) use ($cliente) {
                                    $s->where('cli_id', $cliente->cli_id);
                                });
                        })
                        ->orderByDesc('prestamo_id')
                        ->first();

                    $estado = 'Sin préstamo';
                    $estadoKey = 'none';
                    $monto = 0;
                    $cuota = 0;
                    $plazo = '';

                    if ($prestamo) {
                        $monto = (float) $prestamo->moto_credito;
                        $cuota = (float) $prestamo->cuotas;
                        $plazo = ($prestamo->intervalo ?? '') . ' ' . strtolower($prestamo->frecuencia_pagos ?? '');
                        if ($prestamo->status === 'A') {
                            $estado = 'Activo';
                            $estadoKey = 'activo';
                        } elseif ($prestamo->status === 'C' || $prestamo->status === 'P') {
                            $estado = 'Pagado';
                            $estadoKey = 'pagado';
                        } else {
                            $estado = 'Pendiente';
                            $estadoKey = 'pendiente';
                        }
                    }

                    return [
                        'nombre' => trim(($cliente->cli_nombre ?? '') . ' ' . ($cliente->cli_apellido ?? '')),
                        'dni' => $cliente->cli_dni ?? '',
                        'urlapi' => $cliente->urlapi,
                        'monto' => $monto,
                        'cuota' => $cuota,
                        'plazo' => trim($plazo),
                        'estado' => $estado,
                        'estado_key' => $estadoKey,
                        'fecha' => optional($cliente->created_at)->format('d/m/Y'),
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Resumen cargado',
                'data' => [
                    'capital_total' => round($capitalTotal, 2),
                    'prestamos_activos' => $prestamosActivos->count(),
                    'interes_mes' => round($interesMes, 2),
                    'comision_trabajador' => round($comisionTrabajador, 2),
                    'porcentaje_comision' => $service->porcentaje(),
                    'clientes_activos' => $clientesActivos,
                    'mes' => $mes,
                    'anio' => $anio,
                    'ultimos_clientes' => $ultimosClientes,
                    'evolucion' => $evolucion,
                    'delta_interes' => $delta($interesMes, $interesPrev),
                    'delta_comision' => $delta($comisionTrabajador, $comisionPrev),
                ],
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al cargar el panel',
                'error' => $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function notificaciones_prestamos(Request $request)
    {
        try {
            $prestamos = Prestamo::with([
                'solicitud' => function ($query) {
                    $query->with('cliente');
                },
            ])->where('status', 'A')->get();

            $cuota_hoy = 0;
            $cliente_moroso = 0;
            $cuota_pendientes = 0;
            $fechaActual = Carbon::now();
            $lista = [];

            $prioridad = ['vencido' => 0, 'hoy' => 1, 'pendiente' => 2];

            foreach ($prestamos as $prestamo) {
                $cuota = $prestamo->cuota_actual;
                if (!$cuota) {
                    continue;
                }

                $fechaDada = new Carbon($cuota->fechaVencimiento);
                $estado = 'pendiente';

                if ($fechaDada->isBefore($fechaActual->copy()->startOfDay())) {
                    $cliente_moroso++;
                    $estado = 'vencido';
                } elseif ($fechaDada->isSameDay($fechaActual)) {
                    $cuota_hoy++;
                    $estado = 'hoy';
                } else {
                    $cuota_pendientes++;
                }

                $lista[] = [
                    'code' => $prestamo->solicitud->code ?? $prestamo->code,
                    'cliente' => $prestamo->solicitud->solicitud_nombre ?? '',
                    'monto' => $prestamo->moto_credito,
                    'cuota' => $cuota->cuota,
                    'fecha' => $fechaDada->format('d/m/Y'),
                    'estado' => $estado,
                    'orden' => $prioridad[$estado],
                    'timestamp' => $fechaDada->timestamp,
                ];
            }

            usort($lista, function ($a, $b) {
                if ($a['orden'] !== $b['orden']) {
                    return $a['orden'] <=> $b['orden'];
                }

                return $a['timestamp'] <=> $b['timestamp'];
            });

            $lista = array_slice(array_map(function ($item) {
                unset($item['orden'], $item['timestamp']);
                return $item;
            }, $lista), 0, 15);

            return response()->json([
                'message' => 'Notificaciones cargadas',
                'success' => true,
                'data' => [
                    'total_activos' => $prestamos->count(),
                    'cuota_hoy' => $cuota_hoy,
                    'cliente_moroso' => $cliente_moroso,
                    'cuota_pendientes' => $cuota_pendientes,
                    'prestamos' => $lista,
                ],
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json([
                'message' => 'error del servidor',
                'success' => false,
                'data' => '',
            ]);
        }
    }
}
