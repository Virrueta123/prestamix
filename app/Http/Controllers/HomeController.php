<?php

namespace App\Http\Controllers;

use App\Events\notificacion_gerente_event;
use App\Models\Prestamo;
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
