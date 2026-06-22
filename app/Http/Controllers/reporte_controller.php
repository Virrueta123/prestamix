<?php

namespace App\Http\Controllers;

use App\Helpers\Encryptor;
use App\Models\caja;
use App\Models\gastos;
use App\Models\ingreso;
use App\Models\pagos;
use App\Models\Prestamo;
use App\Models\Solicitud;
use App\Models\tipo_gasto;
use App\Utils\funciones;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class reporte_controller extends Controller
{
    public function load_leyenda(Request $request)
    {
        try {

            $query = Prestamo::with([
                "solicitud",
                "analista"
            ])->where("status", "A");

            $query = $query->get();

            $cronograma = $query;

            $cliente_moroso = 0;
            $cuota_hoy = 0;
            $cuota_pendientes = 0;

            $fechaActual = Carbon::now();
            foreach ($cronograma as $c_g) {
                if (!is_null($c_g->cuota_actual)) {
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
            }

            $leyenda = array(
                "cliente_moroso" => $cliente_moroso,
                "cuota_hoy" => $cuota_hoy,
                "cuota_pendientes" => $cuota_pendientes
            );


            if ($query) {
                return response()->json([
                    'message' => 'datos cargados correctamente',
                    'error' => '',
                    'success' => true,
                    'data' => $leyenda
                ]);
            } else {
                return response()->json([
                    'message' => 'error al cargar los datos de la',
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

    public function report_limit_aprobados(Request $request)
    {
        try {

            $query = Solicitud::with([
                "cliente",
                "analista",
                "prestamo",
                "usuario"
            ])
                ->where("status", "A")
                ->where("sucursal_id", Auth::user()->sucursal_id)
                ->orderBy('created_at')
                ->limit(8)
                ->get();

            if ($query) {
                return response()->json([
                    'message' => 'datos cargados correctamente',
                    'error' => '',
                    'success' => true,
                    'data' => $query
                ]);
            } else {
                return response()->json([
                    'message' => 'error al cargar los datos del reporte ',
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

    public function load_anual()
    {
        try {

            $query = caja::selectRaw('DISTINCT YEAR(created_at) as text, YEAR(created_at) as value')
                ->orderBy('text')
                ->get();

            if ($query) {
                return response()->json([
                    'message' => 'datos cargados correctamente',
                    'error' => '',
                    'success' => true,
                    'data' => $query
                ]);
            } else {
                return response()->json([
                    'message' => 'error al cargar los datos del reporte ',
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

    public function load_mensual()
    {
        try {

            $query = caja::selectRaw('DISTINCT MONTH(created_at) as mes, YEAR(created_at) as anio')
                ->orderBy('mes')
                ->get();

            if ($query) {
                return response()->json([
                    'message' => 'datos cargados correctamente',
                    'error' => '',
                    'success' => true,
                    'data' => $query
                ]);
            } else {
                return response()->json([
                    'message' => 'error al cargar los datos del reporte ',
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

    public function load_data_by_year(Request $request)
    {
        try {

            $Datax = $request->all();

            $for_meses = pagos::selectRaw('DISTINCT MONTH(created_at) as mes')
                ->whereYear('created_at', $Datax["year"])
                ->get();

            $meses = array();
            $data_ingresos = array();
            $data_egresos = array();
            $data_desembolso = array();

            foreach ($for_meses as $ms) {

                $carbon  = Carbon::parse($Datax["year"] . "-" . $ms->mes);

                $nombre_mes = $carbon->isoFormat('MMMM');

                $ingresos_normales = ingreso::where('tipo_ingreso', "C")
                    ->whereMonth('created_at', $ms->mes)
                    ->get();

                $query_gastos_normales = gastos::where('tipo_gastos', "C")
                    ->whereMonth('created_at', $ms->mes)
                    ->where('tipo_gasto_id', '!=', 3)
                    ->get();

                $query_gastos_desembolso = gastos::where('tipo_gastos', "C")
                    ->whereMonth('created_at', $ms->mes)
                    ->where('tipo_gasto_id', 3)
                    ->get();

                array_push($meses, $nombre_mes);

                array_push($data_ingresos, number_format($ingresos_normales->sum("monto"), 2, ".", ""));

                array_push($data_egresos, number_format($query_gastos_normales->sum("monto"), 2, ".", ""));

                array_push($data_desembolso, number_format($query_gastos_desembolso->sum("monto"), 2, ".", ""));
            }

            return response()->json([
                'message' => 'datos cargados correctamente',
                'error' => '',
                'success' => true,
                'data' => [
                    "meses" => $meses,
                    "data_ingresos" => $data_ingresos,
                    "data_egresos" => $data_egresos,
                    "data_desembolso" => $data_desembolso
                ]
            ]);
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


    public function load_data_by_month(Request $request)
    {
        try {

            $Datax = $request->all();

            $date = explode("-", $Datax["month"]);


            $month = $date[0];
            $year =   $date[1];


            $for_days = pagos::selectRaw('DISTINCT DAY(created_at) as day')
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->get();


            $fechas = array();
            $data_ingresos = array();
            $data_egresos = array();
            $data_desembolso = array();



            //para el reporte general 

            $data_ingresos_general = array();
            $data_egresos_general = array();
            $data_desembolso_general = array();

            // enviar datos generales

            $ingresos_normales = ingreso::where('tipo_ingreso', "C")
                ->whereMonth('created_at', $month)
                ->get();

            $query_gastos_normales = gastos::where('tipo_gastos', "C")
                ->whereMonth('created_at', $month)
                ->where('tipo_gasto_id', '!=', 3)
                ->get();

            $query_gastos_desembolso = gastos::where('tipo_gastos', "C")
                ->whereMonth('created_at', $month)
                ->where('tipo_gasto_id', 3)
                ->get();

            $data_ingresos_general = array(
                "name" => "Ingresos",
                "value" => number_format($ingresos_normales->sum("monto"), 2, ".", "")
            );

            $data_egresos_general = array(
                "name" => "Gastos",
                "value" => number_format($query_gastos_normales->sum("monto"), 2, ".", "")
            );

            $data_desembolso_general = array(
                "name" => "desembolsos",
                "value" => number_format($query_gastos_desembolso->sum("monto"), 2, ".", "")
            );


            // ------------------------------------ 

            array_push($fechas, "Fechas");
            array_push($data_ingresos, "ingresos");
            array_push($data_egresos, "gastos");
            array_push($data_desembolso, "desembolsos");

            foreach ($for_days as $F_d) {


                array_push($fechas, $F_d->day . "-" . $month . "-" . $year);

                $ingresos_normales = ingreso::where('tipo_ingreso', "C")
                    ->whereDay('created_at', $F_d->day)
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->get();

                $query_gastos_normales = gastos::where('tipo_gastos', "C")
                    ->whereDay('created_at', $F_d->day)
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->where('tipo_gasto_id', '!=', 3)
                    ->get();

                $query_gastos_desembolso = gastos::where('tipo_gastos', "C")
                    ->whereDay('created_at', $F_d->day)
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->where('tipo_gasto_id', 3)
                    ->get();

                array_push($data_ingresos, number_format($ingresos_normales->sum("monto"), 2, ".", ""));

                array_push($data_egresos, number_format($query_gastos_normales->sum("monto"), 2, ".", ""));

                array_push($data_desembolso, number_format($query_gastos_desembolso->sum("monto"), 2, ".", ""));
            }

            // reporte de categorias

            $tipo_gasto = tipo_gasto::where("status","A")->get();
            $data_gastos_por_tipo = array();
            foreach ($tipo_gasto as $tg) {
                $gastos_por_tipo = gastos::where('tipo_gasto_id', $tg->tipo_gasto_id)
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->get();

                $data_gastos_por_tipo[str_replace(" ", "_", $tg->nombre)] = number_format($gastos_por_tipo->sum("monto"), 2, ".", "");
 
            }


            Log::info([
                "fechas" => $fechas,
                "data_ingresos" => $data_ingresos,
                "data_egresos" => $data_egresos,
                "data_desembolso" => $data_desembolso
            ]);


            // enviar datos por json
            return response()->json([
                'message' => 'datos cargados correctamente',
                'error' => '',
                'success' => true,
                'data' => [
                    "fechas" => $fechas,
                    "data_ingresos" => $data_ingresos,
                    "data_egresos" => $data_egresos,
                    "data_desembolso" => $data_desembolso,
                    "data_ingresos_general" => $data_ingresos_general,
                    "data_egresos_general" => $data_egresos_general,
                    "data_desembolso_general" => $data_desembolso_general,
                    "data_gastos_por_tipo" => $data_gastos_por_tipo,
                ]
            ]);
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

    public function time_line_request(Request $request)
    {
        try {

            $Datax = $request->all();
            $solicitud_id = Encryptor::decrypt($Datax["solicitud_id"]);



            $solicitud = solicitud::find($solicitud_id);



            // buscar solicitudes anteriores y retornarlos en un array

            $solicitud_id_relacionado =  $solicitud->solicitud_id_relacionado;

            $array_solicitudes_anteriores = array();

            if ($solicitud_id_relacionado != 0) {
                array_push($array_solicitudes_anteriores, $solicitud_id_relacionado);
            }

            while ($solicitud_id_relacionado != 0) {
                $solicitud_id_relacionado = Solicitud::find($solicitud_id_relacionado)->solicitud_id_relacionado;
                if ($solicitud_id_relacionado != 0) {
                    array_push($array_solicitudes_anteriores, $solicitud_id_relacionado);
                }
            }



            // buscar solicitudes mas adelante y retornarlos en un array

            $array_solicitudes_posteriores = array();

            $solicitud_id_relacionado_posterior =  $solicitud = solicitud::find($solicitud_id);



            while ($solicitud_id_relacionado_posterior !== null) {
                $solicitud_id_relacionado_posterior = Solicitud::where("solicitud_id_relacionado", $solicitud_id_relacionado_posterior->solicitud_id)->first();
                if ($solicitud_id_relacionado_posterior) {
                    array_push($array_solicitudes_posteriores, $solicitud_id_relacionado_posterior->solicitud_id);
                }
            }

            $array_de_solicitudes = array_merge($array_solicitudes_anteriores, [$solicitud_id], $array_solicitudes_posteriores);

            $data = array();

            foreach ($array_de_solicitudes as $ads) {

                $solicitud = solicitud::find($ads);

                $desembolso = gastos::where('solicitud_id', $solicitud->solicitud_id)->first();

                $estado_desembolso = "";

                if ($desembolso !== null) {
                    $estado_desembolso = "N";
                } else {
                    $estado_desembolso = "Y";
                }

                array_push($data, array(
                    "solicitud_id" => Encryptor::encrypt($solicitud->solicitud_id),
                    "estado" => $solicitud->status,
                    "serie" => $solicitud->serie,
                    "fecha_creacion" => $solicitud->created_at,
                    "desemboloso" => $desembolso->monto,
                    "estado_desembolso" => $estado_desembolso,
                    "fecha_desembolso" => $desembolso->created_at,
                    "tipo_solicitud" => $solicitud->tipo_solicitud
                ));
            }



            return response()->json([
                'message' => 'datos cargados correctamente',
                'error' => '',
                'success' => true,
                'data' => $data
            ]);
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
