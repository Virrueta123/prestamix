<?php

namespace App\Http\Controllers;

use App\Helpers\Encryptor;
use App\Models\caja;
use App\Models\Cliente;
use App\Models\cronograma;
use App\Models\cuentas;
use App\Models\gastos;
use App\Models\ingreso;
use App\Models\prestamo;
use App\Models\salario;
use App\Models\Solicitud;
use App\Models\solicitud_trabajador;
use App\Models\tipo_gasto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class tables_controller extends Controller
{
    public function tabla_solicitud_rechazado_data(Request $request)
    {

        if ($request->ajax()) {

            $query = Solicitud::with([
                "cliente",
                "analista",
                "prestamo",
                "usuario"
            ])
                ->where("status", "R")
                ->where("sucursal_id", Auth::user()->sucursal_id)
                ->orderBy('created_at')
                ->get();


            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('created_at', static function ($Data) {
                    return Carbon::parse($Data->created_at)->format('Y-m-d');
                })
                ->toJson();
        }
    }
    // cargar de datos a la tabla de solicitud aprobadas
    public function tabla_solicitud_aprobado_data(Request $request)
    {
        if ($request->ajax()) {

            $query = Solicitud::with([
                "cliente",
                "analista",
                "prestamo",
                "usuario"
            ])
                ->where("status", "A")
                ->where("sucursal_id", Auth::user()->sucursal_id)
                ->orderBy('created_at')
                ->get();


            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('created_at', static function ($Data) {
                    return Carbon::parse($Data->created_at)->format('Y-m-d');
                })
                ->toJson();
        }
    }

    // cargar de datos a la tabla de solicitud pendiente
    public function tabla_solicitud_pendiente_data(Request $request)
    {
        if ($request->ajax()) {

            $query = Solicitud::with([
                "cliente",
                "analista",
                "prestamo",
                "usuario"
            ])
                ->where("status", "P")
                ->where("sucursal_id", Auth::user()->sucursal_id)
                ->orderBy('created_at')
                ->get();


            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('created_at', static function ($Data) {
                    return Carbon::parse($Data->created_at)->format('Y-m-d');
                })
                ->toJson();
        }
    }

    // cargar de datos a la tabla de solicitud procesado
    public function tabla_solicitud_procesado_data(Request $request)
    {
        if ($request->ajax()) {

            $query = Solicitud::with([
                "cliente",
                "analista",
                "prestamo",
                "usuario"
            ])
                ->where("status", "G")
                ->where("sucursal_id", Auth::user()->sucursal_id)
                ->orderBy('created_at', 'desc');

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->addColumn('code', function ($data) {
                    return str_pad($data->serie, 6, '0', STR_PAD_LEFT);
                })
                ->editColumn('created_at', function ($data) {
                    return \Carbon\Carbon::parse($data->created_at)->format('Y-m-d');
                })
                ->toJson();
        }
    }

    public function table_sueldo(Request $request)
    {
        if ($request->ajax()) {

            $Datax = $request->all();

            $query = salario::with([
                "trabajador",
                "usuario"
            ])
                ->where("trabajador_id", Encryptor::decrypt($Datax['trabajador_id']))
                ->orderBy('created_at')
                ->get();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('mes', static function ($Data) {
                    return ucfirst(Carbon::parse($Data->fecha_inicio)->isoFormat('MMMM'));
                })

                ->addColumn('pagado', static function ($Data) {
                    $sumar_solicitudes = solicitud_trabajador::where("salario_id", $Data->salario_id)->where("status", "G")->get()->sum("monto");
                    return $sumar_solicitudes;
                })
                ->addColumn('restante', static function ($Data) {
                    $sumar_solicitudes = solicitud_trabajador::where("salario_id", $Data->salario_id)->where("status", "G")->get()->sum("monto");
                    return $Data->monto - $sumar_solicitudes;
                })
                ->editColumn('created_at', static function ($Data) {
                    return Carbon::parse($Data->created_at)->format('Y-m-d');
                })
                ->toJson();
        }
    }

    public function tabla_cliente_data(Request $request)
    {
        if ($request->ajax()) {

            $query = Cliente::with([
                "ContactosCliente",
                "solicitudes"
            ])
                ->orderBy('created_at', "desc")
                ->get();


            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('created_at', static function ($Data) {
                    return Carbon::parse($Data->created_at)->format('d/m/y h:is');
                })
                ->toJson();
        }
    }

    public function tabla_ingresos_cliente_data(Request $request)
    {
        if ($request->ajax()) {

            $clienteId = Encryptor::decrypt($request->input('cliente_id'));


            $query = ingreso::with([
                "prestamo" => function ($query) {
                    $query->with([
                        "solicitud" => function ($query) {
                            $query->with(["cliente"]);
                        }
                    ]);
                },
                "pagos"
            ])
                ->whereHas('prestamo.solicitud.cliente', function ($query) use ($clienteId) {
                    $query->where('cli_id', $clienteId);  // Filtra por el id del cliente en la tabla cliente
                })
                ->orderBy('created_at', "desc");


            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('created_at', static function ($Data) {
                    return Carbon::parse($Data->created_at)->format('d/m/y h:is');
                })
                ->toJson();
        }
    }

    public function load_table_cuotas_prestamo(Request $request)
    {

        if ($request->ajax()) {

            $query = prestamo::with([
                "solicitud" => function ($query) {
                    $query->with([
                        "cliente" => function ($query) {
                            $query->with([
                                "ContactosCliente"
                            ]);
                        }
                    ]);
                },
                "analista",
                "mensajes_prestamo"
            ])->where("status", "A");
 
            if (!empty($request->all()["analistas_seleted"])) {

                $analistas = $request->all()["analistas_seleted"];
                $analistas = array_map(function ($item) {
                    return Encryptor::decrypt($item);
                }, $analistas);
                $query = $query->whereHas('analista', function ($query) use ($analistas) {
                    $query->whereIn('id', $analistas);
                });
            }

            if (!empty($request->all()["tipo_cliente_seleted"])) {

                $tipo_cliente = $request->all()["tipo_cliente_seleted"];

                $query = $query->whereHas('solicitud.cliente', function ($query) use ($tipo_cliente) {
                    $query->whereIn('tipo_cliente', $tipo_cliente);
                });
            }

            if (!empty($request->all()["frecuencia_seleted"])) {

                $query =  $query->whereIn('frecuencia_pagos', $request->all()["frecuencia_seleted"]);
            }
            if (!empty($request->all()["between_date"])) {

                $between_date = $request->all()["between_date"];

                $query = $query->whereHas('cuota_actual', function ($query) use ($between_date) {
                    $query->whereBetween('fechaVencimiento', $between_date);
                });
            }
 
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('cliente', function ($Data) {
                    if (!isset($Data->solicitud)) {
                        return $Data->prestamo_id;
                    }
                    return $Data->solicitud->solicitud_nombre;
                })
                ->addColumn('analista', static function ($Data) {
                    return "{$Data->analista->name}";
                })
                ->addColumn('domicilio', static function ($Data) {
                    if (!isset($Data->solicitud)) {
                        return "sin direccion";
                    }
                    return $Data->solicitud->solicitud_domicilio;
                })
                ->addColumn('telefono', static function ($Data) {
                    if (!isset($Data->solicitud)) {
                        return "sin nombre";
                    }
                    $contactosArray = array_column($Data->solicitud->cliente->ContactosCliente->toArray(), 'ccliente_contacto');

                    // Ordena el array para asegurar que los números estén en el orden deseado
                    sort($contactosArray);

                    // Une los elementos del array en un string separados por '/'
                    $contactosString = implode('/', $contactosArray);
                    return $contactosString;
                })
                ->editColumn('created_at', static function ($Data) {
                    return Carbon::parse($Data->created_at)->format('Y-m-d');
                })
               ->make(true);
        }
    }

    public function load_table_caja(Request $request)
    {

        if ($request->ajax()) {

            if (Auth::user()->rol == "gerente") {
                $query = caja::with([
                    "usuario"
                ])->get();
            } else {
                $query = caja::with([
                    "usuario"
                ])->where("created_user", Auth::user()->id)->get();
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('usuario', static function ($Data) {
                    return "{$Data->usuario->name} {$Data->usuario->lastname}";
                })
                ->addColumn('fecha_cierre', static function ($Data) {
                    if ($Data->status == "A") {
                        return "Caja Abierta";
                    }
                    return Carbon::parse($Data->updated_at)->format('d/m/Y');
                })

                ->addColumn('saldo_final', static function ($Data) {
                    if ($Data->status == "A") {
                        return "Caja Abierta";
                    }
                    return number_format($Data->saldo_final, 2, ".", ",");
                })
                ->editColumn('created_at', static function ($Data) {
                    return Carbon::parse($Data->created_at)->format('d/m/Y');
                })
                ->toJson();
        }
    }


    public function load_table_cuentas(Request $request)
    {

        if ($request->ajax()) {

            $query = cuentas::with([
                "usuario"
            ]);

            $query = $query->get();

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('created_at', static function ($Data) {
                    return Carbon::parse($Data->created_at)->format('Y-m-d');
                })
                ->toJson();
        }
    }


    public function load_table_tipo_gasto(Request $request)
    {

        if ($request->ajax()) {

            $query = tipo_gasto::with([
                "usuario"
            ])->where("yes_default", "N");

            $query = $query->get();

            return DataTables::of($query)
                ->addIndexColumn()

                ->toJson();
        }
    }

    // // cargar de datos a la tabla gasto
    public function load_table_gasto(Request $request)
    {
        if ($request->ajax()) {

            $query = gastos::with([
                "usuario",
                "tipo_gasto",
                "sucursal"
            ]);

            if (!empty($request->all()["get_id"])) {
                $query = $query->where('caja_chica_id', Encryptor::decrypt($request->all()['get_id']));
            }

            $query = $query->get();

            return DataTables::of($query)
                ->addIndexColumn()
                ->toJson();
        }
    }

    // // cargar de datos a la tabla ingresos
    public function load_table_ingresos(Request $request)
    {
        if ($request->ajax()) {

            $query = ingreso::with([
                "usuario",
                "pagos" => function ($query) {
                    $query->with(["cuenta"]);
                },
                "prestamo" => function ($query) {
                    $query->with(["analista", "cliente"]);
                }
            ]);

            if (!empty($request->all()["get_caja_id"])) {
                $query = $query->where('caja_chica_id', Encryptor::decrypt($request->all()['get_caja_id']));
            }

            $query = $query->get();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('cliente', static function ($Data) {


                    switch ($Data->tipo_ingreso) {
                        case 'C':
                            $cliente = $Data->prestamo->cliente;
                            return "{$cliente->cli_nombre} {$cliente->cli_apellido}";
                            break;

                        case 'A':
                            return "ingresos varios";
                            break;
                        case 'G':

                            $cliente_g = Cliente::find($Data->cli_id);
                            return "{$cliente_g->cli_nombre} {$cliente_g->cli_apellido}";
                            break;
                    }
                })

                ->addColumn('analista', static function ($Data) {

                    switch ($Data->tipo_ingreso) {
                        case 'C':
                            $analista = $Data->prestamo->analista->name;
                            return "{$analista}";
                            break;

                        case 'A':
                            return "ingresos varios";
                            break;

                        case 'G':
                            return "Dinero guardado";
                    }
                })->addColumn('cuenta_varios', static function ($Data) {

                    switch ($Data->tipo_ingreso) {
                        case 'C':
                            return "ingresos normales";
                            break;

                        case 'A':
                            $cuenta = $Data->cuenta;
                            return "{ $cuenta->cuentas_entidad }";
                            break;

                        case 'G':
                            return "Dinero guardado";
                    }
                })
                ->toJson();
        }
    }


    // // cargar de datos a la tabla  solicitud de trabajadores
    public function tabla_solicitud_trabajadores(Request $request)
    {
        if ($request->ajax()) {

            $query = solicitud_trabajador::with([
                "usuario",
                "salario" => function ($query) {
                    $query->with(["trabajador"]);
                }
            ])->where("sucursal_id", Auth::user()->sucursal_id);


            $query = $query->get();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('trabajador', static function ($Data) {
                    if (!isset($Data->salario->trabajador) || is_null($Data->salario->trabajador)) {
                        return "Sin trabajador";
                    } else {
                        $trabajador = $Data->salario->trabajador->name;
                        return "{$trabajador}";
                    }
                })
                ->toJson();
        }
    }



    public function tabla_solicitud_trabajadores_procesado_by_trabajador(Request $request)
    {
        if ($request->ajax()) {

            $Param = $request->all();

            if (isset($Param["is_created_solicitud"])) {

                $query = solicitud_trabajador::with([
                    "usuario",
                    "gasto",
                    "salario" => function ($query) {
                        $query->with(["trabajador"]);
                    }
                ])->where("sucursal_id", Auth::user()->sucursal_id)->where("status", "G")->where("salario_id", Encryptor::decrypt($Param["salario_id"]));
            } else {
                $query = solicitud_trabajador::with([
                    "usuario",
                    "gasto",
                    "salario" => function ($query) {
                        $query->with(["trabajador"]);
                    }
                ])->where("sucursal_id", Auth::user()->sucursal_id)->where("status", "G")->where("salario_id",  $Param["salario_id"]);
            }


            $query = $query->get();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('trabajador', static function ($Data) {
                    $trabajador = $Data->salario->trabajador->name;
                    return "{$trabajador}";
                })
                ->toJson();
        }
    }
}
