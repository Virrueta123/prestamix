<?php

namespace App\Http\Controllers;

use App\Helpers\Encryptor;
use App\Models\cronograma;
use App\Models\detalle_ingreso;
use App\Models\gastos;
use App\Models\ingreso;
use App\Models\pagos;
use App\Models\Prestamo;
use App\Models\Solicitud;
use App\Utils\ticketera;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ingresos_controller extends Controller
{
    public function delete_ingreso(Request $request)
    {
        try {
            $Params = $request->all();



            if (Auth::user()->rol == "recepcionista") {
                return response()->json([
                    'message' => 'Opcion solo disponible para gerencia',
                    'error' => '',
                    'success' => false,
                    'data' => '',
                ]);
            }

            $ingreso_id = Encryptor::decrypt($Params['urlapi']);

            $ingreso = ingreso::where("ingreso_id", $ingreso_id)->first();

            DB::beginTransaction();
            if ($ingreso->prestamo_cancelado == "Y") {
                $prestamo = Prestamo::find($ingreso->prestamo_id);

                $cronogramas = cronograma::where("prestamo_id", $prestamo->prestamo_id)->where('periodo', '>=', $prestamo->cuota_inicio)->get();

                foreach ($cronogramas as $c) {
                    $c->yes_pago = "N";
                    $c->save();
                }

                $prestamo->status = "A";
                $prestamo->prestamo_cancelado = "N";
                $prestamo->save();

                $ingreso->prestamo_cancelado = "N";
                $ingreso->save();
                $ingreso->delete();

                $pago = pagos::where("ingreso_id", Encryptor::decrypt($Params['urlapi']));
                $pago->delete();

                DB::commit();

                return response()->json([
                    'message' => 'el ingreso se elimino correctamente',
                    'error' => '',
                    'success' => true,
                    'data' => '',
                ]);
            }

            $detalle_ingresos =  detalle_ingreso::where("ingreso_id", $ingreso_id)->first();

            if ($detalle_ingresos->cuota_cancelada == "Y") {
                $cronograma = cronograma::where("cronograma_id", $detalle_ingresos->cronograma_id)->first();
                $cronograma->yes_pago = "N";

                $prestamo = Prestamo::find($cronograma->prestamo_id);

                if ($cronograma->periodo == $prestamo->ncuotas) {
                    $prestamo->status = "A";
                    $prestamo->save();
                }

                $cronograma->save();
            }

            $detalle_ingresos->delete();

            $pago = pagos::where("ingreso_id", Encryptor::decrypt($Params['urlapi']));

            $pago->delete();

            ingreso::where("ingreso_id", Encryptor::decrypt($Params['urlapi']))->delete();

            DB::commit();

            if (true) {
                return response()->json([
                    'message' => 'el ingreso se elimino correctamente',
                    'error' => '',
                    'success' => true,
                    'data' => '',
                ]);
            } else {
                return response()->json([
                    'message' => 'error al eliminar el ingreso',
                    'error' => '',
                    'success' => false,
                    'data' => '',
                ]);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());
            return response()->json([
                'message' => 'error del servidor',
                'error' => $th->getMessage(),
                'success' => false,
                'data' => '',
            ]);
        }
    }

    public function load_ingresos_por_cuota(Request $request)
    {
        try {
            $Params = $request->all();
            $detalle_ingresos = detalle_ingreso::with(
                ["ingreso" => function ($query) {
                    return $query->with(["pagos"]);
                }]
            )->where("cronograma_id", Encryptor::decrypt($Params['urlapi']))->get();

            return response()->json([
                'message' => 'Ingresos cargados correctamente',
                'error' => '',
                'success' => true,
                'data' =>  $detalle_ingresos,
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

    public function load_ingresos_cliente(Request $request)
    {
        try {

            $ingresos = ingreso::with(["cliente"])->where("tipo_ingreso", "G")->where("yes_procesado", "N")->get();

            return response()->json([
                'message' => 'Ingresos cargados correctamente',
                'error' => '',
                'success' => true,
                'data' => $ingresos,
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

    public function print_voucher_ingreso(Request $request)
    {
        try {
            $Params = $request->all();

            $ingreso = ingreso::with([
                "usuario",
                "prestamo" => function ($query) {
                    $query->with([
                        "solicitud" => function ($query) {
                            return $query->with("analista", "cliente");
                        }
                    ]);
                }
            ])->find(Encryptor::decrypt($Params['urlapi']));

            if ($ingreso->prestamo_cancelado == "Y") {

                ticketera::impresion_voucher_prestamo_cancelado(
                    Carbon::parse($ingreso->created_at)->format('Y-m-d h:is:'),
                    $ingreso->prestamo->solicitud->solicitud_nombre,
                    $ingreso->prestamo->solicitud->solicitud_documento,
                    $ingreso->prestamo->solicitud->code,
                    $ingreso->prestamo->moto_credito,
                    $ingreso->prestamo->frecuencia_pagos,
                    $ingreso->prestamo->intervalo,
                    $ingreso->prestamo->interes,
                    $ingreso->prestamo->interespordia,
                    $ingreso->prestamo->diferenciadias,
                    $ingreso->prestamo->montointeres,
                    $ingreso->prestamo->amortizacion,
                    $ingreso->prestamo->total,
                    Carbon::parse($ingreso->prestamo->fecha_inicio)->format('d/m/Y'),
                    Carbon::parse($ingreso->prestamo->fecha_final)->format('d/m/Y')
                );
            } else {
                $detalle_ingresos =  detalle_ingreso::where("ingreso_id", Encryptor::decrypt($Params['urlapi']))->first();
                $cronograma_id =  $detalle_ingresos->cronograma_id;
                $cronograma = cronograma::find($cronograma_id);
                if ($ingreso->prestamo->frecuencia_pagos == "Mensual") {


                    if ($detalle_ingresos->cuota_cancelada == "Y") {
                        ticketera::imprimir_ingreso_grupal(
                            $ingreso->descripcion,
                            $cronograma->periodo,
                            "Cuota cancelada",
                            $ingreso->monto,
                            $ingreso->codigo,
                            $ingreso->prestamo->solicitud->cliente->cli_nombre . " " . $ingreso->prestamo->solicitud->cliente->cli_apellido,
                            $ingreso->prestamo->solicitud->code,
                            $ingreso->prestamo->solicitud->analista->name,
                            $ingreso->usuario->name,
                            0
                        );

                        ticketera::imprimir_ingreso_grupal(
                            $ingreso->descripcion,
                            $cronograma->periodo,
                            "Cuota cancelada",
                            $ingreso->monto,
                            $ingreso->codigo,
                            $ingreso->prestamo->solicitud->cliente->cli_nombre . " " . $ingreso->prestamo->solicitud->cliente->cli_apellido,
                            $ingreso->prestamo->solicitud->code,
                            $ingreso->prestamo->solicitud->analista->name,
                            $ingreso->usuario->name,
                            0,
                            "Recepcion"
                        );
                    } else {

                        $detalles_ingresos = detalle_ingreso::where("cronograma_id", $cronograma_id)->where(
                            "detalle_ingreso_id",
                            "<=",
                            $detalle_ingresos->detalle_ingreso_id
                        )->get();

                        $sumar_restante = 0;
                        foreach ($detalles_ingresos as $d_i) {
                            $sumar_restante += ingreso::where("ingreso_id", $d_i->ingreso_id)->first()->monto;
                        }

                        // la cuota total

                        $total_cuota = $cronograma->cuota;
                        if ($cronograma->yes_mora == "Y") {
                            $total_cuota += $cronograma->mora;
                        }
                        if ($cronograma->yes_interes != "Y") {
                            $total_cuota -= $cronograma->interes;
                        }

                        $saldo_restante = $total_cuota - $sumar_restante;
                        $saldo_restante = round($saldo_restante, 1);
                        $saldo_restante = number_format($saldo_restante, 2, '.', ',');


                        ticketera::imprimir_ingreso_grupal(
                            $ingreso->descripcion,
                            $cronograma->periodo,
                            $saldo_restante,
                            $ingreso->monto,
                            $ingreso->codigo,
                            $ingreso->prestamo->solicitud->cliente->cli_nombre . " " . $ingreso->prestamo->solicitud->cliente->cli_apellido,
                            $ingreso->prestamo->solicitud->code,
                            $ingreso->prestamo->solicitud->analista->name,
                            $ingreso->usuario->name,
                            0
                        );

                        ticketera::imprimir_ingreso_grupal(
                            $ingreso->descripcion,
                            $cronograma->periodo,
                            $saldo_restante,
                            $ingreso->monto,
                            $ingreso->codigo,
                            $ingreso->prestamo->solicitud->cliente->cli_nombre . " " . $ingreso->prestamo->solicitud->cliente->cli_apellido,
                            $ingreso->prestamo->solicitud->code,
                            $ingreso->prestamo->solicitud->analista->name,
                            $ingreso->usuario->name,
                            0,
                            "Recepcion"
                        );
                    }
                } else {
                    $prestamo = Prestamo::find($ingreso->prestamo_id);
                    $ingresos = ingreso::where("prestamo_id", $prestamo->prestamo_id)
                        ->where(
                            "ingreso_id",
                            "<",
                            $ingreso->ingreso_id
                        )
                        ->get()->sum("monto");

                    $dt = $prestamo->d_t - $ingresos;

                    $saldo_pendiente = $prestamo->d_t - $ingreso->monto;
                    $saldo_pendiente = round($saldo_pendiente, 1);
                    $saldo_pendiente = number_format($saldo_pendiente, 2, '.', ',');

                    ticketera::imprimir_ingreso(
                        $dt,
                        $ingreso->monto,
                        $ingreso->codigo,
                        $ingreso->prestamo->solicitud->cliente->cli_nombre . " " . $ingreso->prestamo->solicitud->cliente->cli_apellido,
                        $ingreso->prestamo->solicitud->code,
                        $ingreso->prestamo->solicitud->analista->name,
                        $ingreso->usuario->name,
                        $saldo_pendiente
                    );

                    ticketera::imprimir_ingreso(
                        $dt,
                        $ingreso->monto,
                        $ingreso->codigo,
                        $ingreso->prestamo->solicitud->cliente->cli_nombre . " " . $ingreso->prestamo->solicitud->cliente->cli_apellido,
                        $ingreso->prestamo->solicitud->code,
                        $ingreso->prestamo->solicitud->analista->name,
                        $ingreso->usuario->name,
                        $saldo_pendiente,
                        "Recepcion"
                    );
                }
            }

            if ($ingreso) {
                return response()->json([
                    'message' => 'Impresion exitosa',
                    'error' => '',
                    'success' => true,
                    'data' => '',
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

    // esta funcion sirve para ditar el pago de un ingreso
    public function editar_pago_ingreso(Request $request)
    {
        try {
            $Params = $request->all();

            $ingreso = $Params["get_ingreso"];

            $ingreso = ingreso::find(Encryptor::decrypt($ingreso["urlapi"]));
            $ingreso->monto = $Params["monto"];
            $ingreso->save();


            foreach ($Params["pagos"] as $pago) {
                $cuentasId = Encryptor::decrypt($pago["cuentas_id"]);
                if ($cuentasId <= 0) {
                    return response()->json([
                        'message' => 'Debe seleccionar una cuenta válida para el pago',
                        'success' => false,
                        'data' => '',
                    ]);
                }

                if (isset($pago["urlapi"])) {

                    $pag = pagos::where("pagos_id", Encryptor::decrypt($pago["urlapi"]))->first();
                    $pag->cuentas_id = $cuentasId;
                    $pag->monto = $pago["monto"];
                    $pag->save();
                } else {

                    $pag = new pagos();
                    $pag->ingreso_id = Encryptor::decrypt($ingreso["urlapi"]);
                    $pag->monto = $pago["monto"];
                    $pag->cuentas_id = $cuentasId;
                    $pag->tipo = "I";
                    $pag->caja_chica_id = $ingreso["caja_chica_id"];
                    $pag->created_user  = auth()->user()->id;
                    $pag->sucursal_id  = auth()->user()->sucursal_id;
                    $pag->save();
                }
            }


            if (!empty($Params["delete_array"])) {
                foreach ($Params["delete_array"] as $id) {
                    $pago = pagos::find(Encryptor::decrypt($id));
                    $pago->delete();
                }
            }


            return response()->json([
                'message' => 'Pago actualizado correctamente',
                'success' => true,
                'data' => '',
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
