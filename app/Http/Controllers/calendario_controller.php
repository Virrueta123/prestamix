<?php

namespace App\Http\Controllers;

use App\Models\cronograma;
use App\Models\prestamo;
use App\Utils\funciones;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class calendario_controller extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('bloqueado');
    }
    public function calendario_planilla()
    {


        return view("modules.calendario.calendario_planilla");
    }
    //obtener los eventos
    public function load_calendar()
    {
        try {

            $query = prestamo::with([
                "solicitud" => function ($query) {
                    $query->with([
                        "cliente"
                    ]);
                },
                "analista"
            ])->where("status", "A");
 
            $query = $query->get();

            $cronograma = $query;

            $cliente_moroso = 0;
            $cuota_hoy = 0;
            $cuota_pendientes = 0;

            $fechaActual = Carbon::now();
            foreach ($cronograma as $c_g) {
                $events[] = [
                    'title' => "{$c_g->solicitud->code}-{$c_g->solicitud->solicitud_nombre}",
                    "color" => funciones::colores_calendar($c_g->cuota_actual->fechaVencimiento),
                    'allDay' => true,
                    'start' =>  $c_g->cuota_actual->fechaVencimiento,
                    'cronograma' =>  $c_g
                ];

                $fechaDada = new Carbon($c_g->cuota_actual->fechaVencimiento);
                // Comparar la fecha actual con la fecha dada
                if ($fechaDada->isBefore($fechaActual->startOfDay())) {
                    $cliente_moroso++;
                } else if ($fechaDada->isSameDay($fechaActual)) {
                    $cuota_hoy++;
                } else {
                    $cuota_pendientes++;
                }
            }

            $leyenda = array(
                "cliente_moroso" => $cliente_moroso,
                "cuota_hoy" => $cuota_hoy,
                "cuota_pendientes" => $cuota_pendientes
            );

            $datos = array(
                "events" => $events,
                "leyenda" => $leyenda,
            );

            if ($cronograma) {
                return response()->json([
                    'message' => 'cargado exitosamente',
                    'error' => '',
                    'success' => true,
                    'data' => $datos,
                ]);
            } else {
                return response()->json([
                    'message' => '',
                    'error' => '',
                    'success' => false,
                    'data' => '',
                ]);
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json([
                'message' => 'error del servidor',
                'error' => $th->getMessage(),
                'success' => false,
                'data' => '',
            ]);
        }
    }
}
