<?php

namespace App\Http\Controllers;

use App\Helpers\Encryptor;
use App\Helpers\Generales;
use App\Models\caja;
use App\Models\cronograma;
use App\Models\detalle_ingreso;
use App\Models\gastos;
use App\Models\ingreso;
use App\Models\mensajes_prestamo;
use App\Models\pagos;
use App\Models\prestamo;
use App\Models\Solicitud;
use App\Models\User;
use App\Utils\Formulas;
use App\Utils\funciones;
use App\Utils\ticketera;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;

class PrestamoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except("impresion_prueba");
        $this->middleware('bloqueado');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("modules.prestamo.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $destinatario = "jhoanybartra@gmail.com";
        // $ruta = 'https://laravel.com/docs/10.x/mail';
        // $mensaje = 'Somos de Repuestos & Servicios Chong. Le enviamos esta cotizacion de su moto.Lo puede revisar en la siguiente ruta ';

        // $mensaje = Mail::send('correo.prestamo_pediente', ['mensaje' => $mensaje,"ruta"=>$ruta], function ($message) use ($destinatario) {
        //     $message->to($destinatario)->subject('Asunto del mensaje');
        // });

        return view("modules.prestamo.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    public function reprogramacion($urlapi)
    {
        // dd($urlapi);

        if (Auth::user()->rol == "recepcionista") {
            return view("errors.desautorizacion");
        }

        $solicitud = Solicitud::with([
            "cliente" => function ($query) {
                return $query->with(["ContactosCliente"]);
            },
            "analista",
            "prestamo" => function ($query) {
                return $query->with(["cronograma"]);
            },
            "guardar_documento" => function ($query) {
                return $query->with(["sucursal", "usuario"]);
            },
        ])->find(Encryptor::decrypt($urlapi));


        $ingreso = ingreso::where("prestamo_id", $solicitud->prestamo->prestamo_id)->sum("monto");
        $monto_credito_nuevo = funciones::redondear($solicitud->prestamo->d_t - $ingreso);

        // ultima fecha de pago
        if ($solicitud->prestamo->frecuencia_pagos == "Mensual") {
            $cronograma = cronograma::where("prestamo_id", $solicitud->prestamo->prestamo_id)->where("yes_pago", "N")->orderBy("periodo", "ASC")->first();
            $ultima_fecha =  Carbon::parse($cronograma->fechaVencimiento)->format('Y-m-d');
        } else {
            $cronograma = cronograma::where("prestamo_id", $solicitud->prestamo->prestamo_id)->orderBy("periodo", "desc")->first();
            $ultima_fecha =  Carbon::parse($cronograma->fechaVencimiento)->format('Y-m-d');
        }




        return view("modules.prestamo.reprogramacion", ["solicitud" => $solicitud, "monto_credito_nuevo" =>  $monto_credito_nuevo, "ultima_fecha" => $ultima_fecha]);
    }

    public function desaparecer_prestamo($urlapi)
    {
        try {

            $solicitud = Solicitud::find(Encryptor::decrypt($urlapi));
            $prestamo = prestamo::find($solicitud->prestamo_id);
            $prestamo->status = "N";

            if ($prestamo->save()) {
                session()->flash('success', 'prestamos finalizado exitosamente');
                return redirect()->back();
            } else {
                session()->flash('success', 'operacion fallida');
                return redirect()->back();
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

    public function cancelar_prestamo($urlapi)
    {
        /**/
        $solicitud = Solicitud::with([
            "cliente" => function ($query) {
                return $query->with(["ContactosCliente"]);
            },
            "analista",
            "prestamo" => function ($query) {
                return $query->with(["cronograma"]);
            },
            "guardar_documento" => function ($query) {
                return $query->with(["sucursal", "usuario"]);
            },
        ])->find(Encryptor::decrypt($urlapi));

        /**/
        $prestamo = prestamo::find($solicitud->prestamo_id);

        if ($prestamo->intervalo == 1) {

            $cuota = cronograma::where("prestamo_id", $prestamo->prestamo_id)->where("periodo", 1)->first();
            $interes_por_dia = ($prestamo->moto_credito * ($prestamo->interes / 100)) / 30;

            // necesito saber cuanto es el interes diario del prestamo 
            /**/
            $interes_por_dia = ($prestamo->moto_credito * ($prestamo->interes / 100)) / 30;

            // necesito saber cuantos dias hay desde la fecha de vencimiento de la ultima cuota hasta el de actual

            $fechaDada = $cuota->fechaVencimiento; // Cambia esta fecha por la que necesites
            $fechaDadaCarbon = Carbon::parse($fechaDada);
            $fechaActual = Carbon::now();
            /**/
            $diferenciaDias = $fechaDadaCarbon->diffInDays($fechaActual);

            // necesito calcular los dias acumulados * el interes diario
            /**/
            $monto_interes = $diferenciaDias * $interes_por_dia;

            // necesito tambien saber si hay un ingreso en la cuota siguiente de la fecha ultima pagada si es asi obtener el monto   
            $detalle_ingreso = detalle_ingreso::where("cronograma_id", $cuota->cronograma_id)->get();
            /**/
            $is_ingresos = false;

            $amortizacion = 0;

            if ($detalle_ingreso->count() > 0) {
                $is_ingresos = true;
                $amortizacion = 0;
                foreach ($detalle_ingreso as $d_i) {
                    $amortizacion +=  ingreso::where("ingreso_id", $d_i->ingreso_id)->first()->monto;
                }
            } else {
                $is_ingresos = false;
                $amortizacion = 0;
            }

            $saldo_capital = cronograma::where("prestamo_id", $prestamo->prestamo_id)->where('periodo', 1)->get()->sum("amortizacion");

            $cuota_anterior = $cuota;
            $cuota_actual = $cuota;
        } else {
            // primero necesito la ultimo ultima cuota cancelada en sus totalidad
            /**/
            $cuota_actual = $prestamo->cuota_actual;
            $cuota_anterior = cronograma::where("prestamo_id", $prestamo->prestamo_id)->where("periodo", $cuota_actual->periodo - 1)->first();

            // necesito saber cuanto es el interes diario del prestamo 
            /**/
            $interes_por_dia = ($prestamo->moto_credito * ($prestamo->interes / 100)) / 30;

            // necesito saber cuantos dias hay desde la fecha de vencimiento de la ultima cuota hasta el de actual

            $fechaDada = $cuota_anterior->fechaVencimiento; // Cambia esta fecha por la que necesites
            $fechaDadaCarbon = Carbon::parse($fechaDada);
            $fechaActual = Carbon::now();

            /**/
            $diferenciaDias = $fechaDadaCarbon->diffInDays($fechaActual);


            // necesito calcular los dias acumulados * el interes diario
            /**/
            $monto_interes = $diferenciaDias * $interes_por_dia;

            // necesito tambien saber si hay un ingreso en la cuota siguiente de la fecha ultima pagada si es asi obtener el monto   
            $detalle_ingreso = detalle_ingreso::where("cronograma_id", $cuota_actual->cronograma_id)->get();
            /**/
            $is_ingresos = false;

            /**/
            $amortizacion = 0;

            if ($detalle_ingreso->count() > 0) {
                $is_ingresos = true;
                $amortizacion = 0;
                foreach ($detalle_ingreso as $d_i) {
                    $amortizacion +=  ingreso::where("ingreso_id", $d_i->ingreso_id)->first()->monto;
                }
            } else {
                $is_ingresos = false;
                $amortizacion = 0;
            }

            $saldo_capital = cronograma::where("prestamo_id", $prestamo->prestamo_id)->where('periodo', '>', $cuota_anterior->periodo)->get()->sum("amortizacion");
        }

        return view(
            "modules.prestamo.cancelar_prestamo",
            compact(
                "solicitud",
                "prestamo",
                "interes_por_dia",
                "diferenciaDias",
                "monto_interes",
                "is_ingresos",
                "amortizacion",
                "cuota_actual",
                "saldo_capital",
                "fechaDada",
                "cuota_anterior"
            )
        );
    }

    public function prestamo_por_cliente(Request $request)
    {

        $Params = $request->all();
        $cli_id =  Encryptor::decrypt($Params["urlapi"]);

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
            ])->where("cli_id", $cli_id)->where("status", "A");



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

            $query = $query->get();

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
                ->toJson();
        }
    }

    public function procesar_cancelar_prestamo(Request $request)
    {
        try {
            $is_caja = caja::where("created_user", auth()->user()->id)->where("status", "A")->first();



            if (is_null($is_caja)) {
                return response()->json([
                    'message' => 'No hay una caja abierta',
                    'error' => '',
                    'success' => false,
                    'data' => '',
                ]);
            }

            $Datax = $request->all();

            $fecha_inicio = Carbon::parse($Datax["fecha_inicio"])->format('Y-m-d');
            $fecha_final = Carbon::parse($Datax["fecha_final"])->format('Y-m-d');

            DB::beginTransaction();

            // primero tengo que actulizar el prestamo a cancelado
            $prestamo_id = Encryptor::decrypt($Datax["prestamo"]);
            $prestamo = prestamo::find($prestamo_id);
            $prestamo->status = "C";
            $prestamo->prestamo_cancelado = "Y";
            $prestamo->fecha_inicio = $fecha_inicio;
            $prestamo->fecha_final = $fecha_final;
            $prestamo->total = $Datax["total"];
            $prestamo->interespordia = $Datax["interespordia"];
            $prestamo->diferenciadias = $Datax["diferenciadias"];
            $prestamo->montointeres = $Datax["montointeres"];
            $prestamo->amortizacion = $Datax["amortizacion"];


            $cuota_inicio = cronograma::where("prestamo_id", $prestamo->prestamo_id)->where("yes_pago", "N")->orderBy("periodo", "ASC")->first()->periodo;
            $cuota_final = cronograma::where("prestamo_id", $prestamo->prestamo_id)->where("yes_pago", "N")->orderBy("periodo", "DESC")->first()->periodo;

            $prestamo->cuota_inicio = $cuota_inicio;
            $prestamo->cuota_final = $cuota_final;
            $prestamo->save();


            // despues cambiar la ultima cuota como cancelado y despues las otras restantes


            $cronogramas_restantes = cronograma::where("prestamo_id", $prestamo->prestamo_id)->where('periodo', '>', $Datax["periodo_anterior"])->get();


            foreach ($cronogramas_restantes as $c_r) {

                $cronograma = cronograma::find($c_r->cronograma_id);
                $cronograma->yes_pago = "Y";
                $cronograma->save();
            }

            // crear el ingreso
            $ingreso = new ingreso();
            $ingreso->monto = $Datax["total"];
            $ingreso->descripcion = "Cancelacion definitiva del prestamo de la solicitud N " . $Datax["nsolicitud"];
            $ingreso->created_user = auth()->user()->id;
            $ingreso->sucursal_id = auth()->user()->sucursal_id;
            $ingreso->yes_office = "Y";
            $ingreso->caja_chica_id = caja::where("created_user", auth()->user()->id)->where("status", "A")->first()->caja_chica_id;
            $ingreso->codigo = funciones::generar_codigo($Datax["total"]);
            $ingreso->prestamo_id = $prestamo_id;
            $ingreso->created_at = Carbon::now();
            $ingreso->updated_at = Carbon::now();
            $ingreso->prestamo_cancelado = "Y";
            $ingreso->save();

            // crear el pagos del ingreso
            if ($ingreso->save()) {
                foreach ($Datax["pagos"] as $pago) {

                    $pagos = new pagos();
                    $pagos->ingreso_id = $ingreso->ingreso_id;
                    $pagos->monto = $pago["monto"];
                    $pagos->cuentas_id = Encryptor::decrypt($pago["cuentas_id"]);
                    $pagos->tipo = "I";
                    $pagos->caja_chica_id = caja::where("created_user", auth()->user()->id)->where("status", "A")->first()->caja_chica_id;
                    $pagos->created_user  = auth()->user()->id;
                    $pagos->sucursal_id  = auth()->user()->sucursal_id;
                    $pagos->save();
                }
            }

            $ingreso = ingreso::with([
                "usuario",
                "prestamo" => function ($query) {
                    $query->with([
                        "solicitud" => function ($query) {
                            return $query->with("analista", "cliente");
                        }
                    ]);
                }
            ])->find($ingreso->ingreso_id);


            // imprimir tickets
            ticketera::impresion_voucher_prestamo_cancelado(
                Carbon::now()->format('Y-m-d h:is:'),
                $ingreso->prestamo->solicitud->code,
                $ingreso->prestamo->solicitud->solicitud_nombre,
                $ingreso->prestamo->solicitud->solicitud_documento,
                $ingreso->prestamo->moto_credito,
                $ingreso->prestamo->frecuencia_pagos,
                $ingreso->prestamo->intervalo,
                $ingreso->prestamo->interes,
                $Datax["interespordia"],
                $Datax["diferenciadias"],
                $Datax["montointeres"],
                $Datax["amortizacion"],
                $Datax["total"],
                $fecha_inicio,
                $fecha_final,
            );
 

            DB::commit();

            return response()->json([
                'message' => 'Datos cargados exitosamente',
                'error' => '',
                'success' => true,
                'data' => ''
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

    public function reprogramacion_cuota($urlapi)
    {

        if (Auth::user()->rol == "recepcionista") {
            return view("errors.desautorizacion");
        }



        $solicitud = Solicitud::with([
            "cliente" => function ($query) {
                return $query->with(["ContactosCliente"]);
            },
            "analista",
            "prestamo" => function ($query) {
                return $query->with(["cronograma"]);
            },
            "guardar_documento" => function ($query) {
                return $query->with(["sucursal", "usuario"]);
            },
        ])->find(Encryptor::decrypt($urlapi));

        $ingreso = ingreso::where("prestamo_id", $solicitud->prestamo->prestamo_id)->sum("monto");
        $monto_credito_nuevo = funciones::redondear($solicitud->prestamo->d_t - $ingreso);

        return view("modules.prestamo.reprogramacion_cuota", ["solicitud" => $solicitud, "monto_credito_nuevo" =>  $monto_credito_nuevo]);
    }


    public function ampliacion($urlapi)
    {
        if (Auth::user()->rol == "recepcionista") {
            return view("errors.desautorizacion");
        }
        // dd($urlapi);
        $solicitud = Solicitud::with([
            "cliente" => function ($query) {
                return $query->with(["ContactosCliente"]);
            },
            "analista",
            "prestamo" => function ($query) {
                return $query->with(["cronograma"]);
            },
            "guardar_documento" => function ($query) {
                return $query->with(["sucursal", "usuario"]);
            },
        ])->find(Encryptor::decrypt($urlapi));



        if ($solicitud->prestamo->frecuencia_pagos == "Mensual") {


            $cronograma = cronograma::where("prestamo_id", $solicitud->prestamo->prestamo_id)->where("yes_pago", "N")->first();
            $monto_credito_nuevo = $cronograma->saldoCapital;
        } else {
            $ingreso = ingreso::where("prestamo_id", $solicitud->prestamo->prestamo_id)->sum("monto");
            $monto_credito_nuevo = funciones::redondear($solicitud->prestamo->d_t - $ingreso);
        }

        return view("modules.prestamo.ampliacion", ["solicitud" => $solicitud, "monto_credito_nuevo" => $monto_credito_nuevo]);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function simulacion()
    {
        return view("modules.prestamo.simulacion");
    }


    // tabla de planilla
    public function planilla_prestamos($nombre = "")
    {

        $url = url('/');
        return view("modules.prestamo.planilla_prestamos", compact("url", "nombre"));
    }
    // cargar los datos del cronograma
    public function get_cuotas(Request $request)
    {
        try {
            $Params = $request->all();

            $cronograma = cronograma::where("prestamo_id", Encryptor::decrypt($Params["urlapi"]))->get();


            if ($cronograma) {
                return response()->json([
                    'message' => 'datos cargados exitosamente',
                    'error' => '',
                    'success' => true,
                    'data' => $cronograma,
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

    //enviar data a axios
    public function data_load_planilla()
    {

        try {
            $cronograma = cronograma::with([
                "prestamo" => function ($query) {
                    $query->with(["solicitud", "analista"]);
                }
            ])
                ->where('status', 'P')
                ->get();
            if ($cronograma) {
                return response()->json([
                    'message' => 'Datos cargados exitosamente',
                    'error' => '',
                    'success' => true,
                    'data' => $cronograma
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



    // actulizar el estado del interes por cuota
    public function updated_yes_interes(Request $request)
    {
        try {
            if (Auth::user()->rol == "gerente") {
                $Params = $request->all();
                $cronograma = cronograma::find(Encryptor::decrypt($Params["urlapi"]));

                $cronograma->yes_interes = $Params["yes_interes"];



                if ($cronograma->save()) {
                    return response()->json([
                        'message' => $cronograma->yes_interes == "Y" ? 'Esta cuota se cobrara interes' : 'Esta cuota no tiene interes',
                        'error' => '',
                        'success' => true,
                        'data' => $cronograma
                    ]);
                } else {
                    return response()->json([
                        'message' => 'error al actualizar el interes',
                        'error' => '',
                        'success' => true,
                        'data' => ""
                    ]);
                }
            } else {
                return response()->json([
                    'message' => 'error no tiene acceso a esta funcion',
                    'error' => '',
                    'success' => true,
                    'data' => ""
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

    // actulizar el estado del interes por cuota
    public function updated_yes_mora(Request $request)
    {
        try {
            if (Auth::user()->rol == "gerente") {
                $Params = $request->all();
                $cronograma = cronograma::find(Encryptor::decrypt($Params["urlapi"]));

                $cronograma->yes_mora = $Params["yes_mora"];
                $cronograma->monto_mora = $Params["yes_mora"] == "Y" ? $cronograma->interes : 0;

                if ($cronograma->save()) {
                    return response()->json([
                        'message' => $cronograma->yes_mora == "Y" ? 'Esta cuota se cobrara mora' : 'Esta cuota no se cobrara mora',
                        'error' => '',
                        'success' => true,
                        'data' => $cronograma
                    ]);
                } else {
                    return response()->json([
                        'message' => 'error al actualizar el interes',
                        'error' => '',
                        'success' => true,
                        'data' => ""
                    ]);
                }
            } else {
                return response()->json([
                    'message' => 'error no tiene acceso a esta funcion',
                    'error' => '',
                    'success' => true,
                    'data' => ""
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

    // insertar el ingreso de una cuota
    public function ingreso_cuota(Request $request)
    {
        try {
            $Params = $request->all();
            $ingreso = new ingreso();
            $ingreso->monto = $Params["monto"];
            $ingreso->descripcion = $Params["descripcion"];
            $ingreso->cuentas_id = Encryptor::decrypt($Params["cuentas_id"]);
            $ingreso->caja_chica_id = caja::where("created_user", auth()->user()->id)->where("status", "A")->first()->caja_chica_id;
            $ingreso->created_user = auth()->user()->id;
            $ingreso->cronograma_id = Encryptor::decrypt($Params["urlapi"]);
            $ingreso->sucursal_id = auth()->user()->sucursal_id;
            $ingreso->yes_office = $Params["yes_office"] ? "Y" : "N";

            if ($ingreso->save()) {
                session()->flash('success', 'Ingreso creado correctamente');
                return response()->json([
                    'message' => 'Ingreso creado correctamente',
                    'error' => '',
                    'success' => true,
                    'data' => route("planilla_prestamos"),
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

    // crear ingresos de cuotras grupales y moras
    public function ingreso_cuota_grupal(Request $request)
    {
        try {
            $Params = $request->all();


            // cuando paga sus cuota completo
            if ($Params["adelanto"] == 0) {

                $periodo_string = [];
                $mora_string = [];

                foreach ($Params["pago_grupal"] as $p_g) {

                    array_push($periodo_string, $p_g["periodo"]);

                    if ($p_g["yes_mora"] == "Y") {

                        array_push($mora_string, $p_g["periodo"]);
                    }
                }

                $string_descripcion = "Pago de cuota periodo( " . implode(',', $periodo_string) . " ) de la solicitud n° {$Params['get_prestamo']['solicitud']['code']}";

                if (count($mora_string) != 0) {
                    $string_descripcion .= " mora(" . implode(',', $mora_string) . ")";
                }


                $ingreso = new ingreso();
                $ingreso->monto = array_sum(array_column($Params["pagos"], "monto"));
                $ingreso->descripcion = $string_descripcion;
                $ingreso->created_user = auth()->user()->id;
                $ingreso->sucursal_id = auth()->user()->sucursal_id;
                $ingreso->yes_office = $Params["yes_office"] ? "Y" : "N";
                $ingreso->caja_chica_id = caja::where("created_user", 1)->where("status", "A")->first()->caja_chica_id;
                $ingreso->codigo = funciones::generar_codigo($Params["totalGeneral"]);
                $ingreso->prestamo_id = $Params['get_prestamo']["prestamo_id"];
                $ingreso->created_at = Carbon::now();
                $ingreso->updated_at = Carbon::now();
                $ingreso->save();

                if (!$ingreso->save()) {
                    return response()->json([
                        'message' => 'error al registrar los datos, inetentar de nuevo',
                        'error' => '',
                        'success' => false,
                        'data' => '',
                    ]);
                }

                foreach ($Params["pagos"] as $pago) {

                    $pagos = new pagos();
                    $pagos->ingreso_id = $ingreso->ingreso_id;
                    $pagos->monto = $pago["monto"];
                    $pagos->cuentas_id = Encryptor::decrypt($pago["cuentas_id"]);
                    $pagos->tipo = "I";
                    $pagos->caja_chica_id = caja::where("created_user", 1)->where("status", "A")->first()->caja_chica_id;
                    $pagos->created_user  = auth()->user()->id;
                    $pagos->sucursal_id  = auth()->user()->sucursal_id;
                    $pagos->save();
                }

                $ingreso = ingreso::with([
                    "usuario",
                    "prestamo" => function ($query) {
                        $query->with([
                            "solicitud" => function ($query) {
                                return $query->with("analista", "cliente");
                            }
                        ]);
                    }
                ])->find($ingreso->ingreso_id);

                $cronograma_id = Encryptor::decrypt($p_g["urlapi"]);

                $actualiza_cronograma = cronograma::find($cronograma_id);

          

                foreach ($Params["pago_grupal"] as $p_g) {

                    $actualiza_cronograma->yes_pago = "Y";

                    $detalle_ingresos[] = [
                        "ingreso_id" => $ingreso->ingreso_id,
                        "cronograma_id" => $cronograma_id,
                        "created_at" => Carbon::now(),
                        "updated_at" => Carbon::now(),
                        "created_user" => auth()->user()->id,
                        "sucursal_id" => auth()->user()->sucursal_id,
                        "yes_mora" => "N",
                        "descripcion"  => "Pago cuota {$p_g["periodo"]} de la solicitud n° {$Params['get_prestamo']['solicitud']['code']}",
                        "monto"  => $ingreso->monto,
                        "cuota_cancelada" => "Y"
                    ];

                    $actualiza_cronograma->save();
                }

                $detalle_ingreso = detalle_ingreso::insert($detalle_ingresos);
                $prestamo = prestamo::find($ingreso->prestamo_id);

                $periodo = cronograma::where("prestamo_id",  $prestamo->prestamo_id)->orderBy("periodo", "Desc")->first()->periodo;


                if ($periodo == $actualiza_cronograma->periodo) {
                    $prestamo->status = "C";
                    $prestamo->save();
                }

                if ($detalle_ingreso) {
                    session()->flash('success', 'Ingresos creado correctamente');
                    return response()->json([
                        'message' => 'Ingreso creado correctamente',
                        'error' => '',
                        'success' => true,
                        'data' => route("planilla_prestamos"),
                    ]);
                } else {
                    return response()->json([
                        'message' => '',
                        'error' => '',
                        'success' => false,
                        'data' => '',
                    ]);
                }
            } else {
                $periodo_string = [];
                $mora_string = [];

                foreach ($Params["pago_grupal"] as $p_g) {

                    array_push($periodo_string, $p_g["periodo"]);
                }

                $string_descripcion =  implode(',', $periodo_string);


                $ingreso = new ingreso();
                $ingreso->monto = $Params["totalCuota_pagos"];
                $ingreso->descripcion = "Pago adelanto de la cuota " . $string_descripcion . " de la solicitud n° {$Params['get_prestamo']['solicitud']['code']}";
                $ingreso->created_user = auth()->user()->id;
                $ingreso->sucursal_id = auth()->user()->sucursal_id;
                $ingreso->yes_office = $Params["yes_office"] ? "Y" : "N";
                $ingreso->caja_chica_id = caja::where("created_user", 1)->where("status", "A")->first()->caja_chica_id;
                $ingreso->codigo = funciones::generar_codigo($Params["totalGeneral"]);
                $ingreso->prestamo_id = $Params['get_prestamo']["prestamo_id"];
                $ingreso->created_at = Carbon::now();
                $ingreso->updated_at = Carbon::now();
                $ingreso->save();

                if (!$ingreso->save()) {
                    return response()->json([
                        'message' => 'error al registrar los datos, inetentar de nuevo',
                        'error' => '',
                        'success' => false,
                        'data' => '',
                    ]);
                }

                foreach ($Params["pagos"] as $pago) {
                    $pagos = new pagos();
                    $pagos->ingreso_id = $ingreso->ingreso_id;
                    $pagos->monto = $pago["monto"];
                    $pagos->cuentas_id = Encryptor::decrypt($pago["cuentas_id"]);
                    $pagos->tipo = "I";
                    $pagos->caja_chica_id = caja::where("created_user", 1)->where("status", "A")->first()->caja_chica_id;
                    $pagos->created_user  = auth()->user()->id;
                    $pagos->sucursal_id  = auth()->user()->sucursal_id;
                    $pagos->save();
                }

                $ingreso = ingreso::with([
                    "usuario",
                    "prestamo" => function ($query) {
                        $query->with([
                            "solicitud" => function ($query) {
                                return $query->with("analista", "cliente");
                            }
                        ]);
                    }
                ])->find($ingreso->ingreso_id);

                $cronograma_id = Encryptor::decrypt($p_g["urlapi"]);

                $actualiza_cronograma = cronograma::find($cronograma_id);
 

                foreach ($Params["pago_grupal"] as $p_g) {

                    $actualiza_cronograma->yes_pago = "N";

                    $detalle_ingresos[] = [
                        "ingreso_id" => $ingreso->ingreso_id,
                        "cronograma_id" => $cronograma_id,
                        "created_at" => Carbon::now(),
                        "updated_at" => Carbon::now(),
                        "created_user" => auth()->user()->id,
                        "sucursal_id" => auth()->user()->sucursal_id,
                        "yes_mora" => "N",
                        "descripcion"  => "Pago adelanto periodo " . $string_descripcion . "solicitud N°de la solicitud n° {$Params['get_prestamo']['solicitud']['code']}",
                        "monto"  =>  $Params['get_prestamo']["prestamo_id"],
                    ];


                    $actualiza_cronograma->save();
                }

                $detalle_ingreso = detalle_ingreso::insert($detalle_ingresos);

                if ($detalle_ingreso) {
                    session()->flash('success', 'Ingresos creado correctamente');
                    return response()->json([
                        'message' => 'Ingreso creado correctamente',
                        'error' => '',
                        'success' => true,
                        'data' => route("planilla_prestamos"),
                    ]);
                } else {
                    return response()->json([
                        'message' => '',
                        'error' => '',
                        'success' => false,
                        'data' => '',
                    ]);
                }
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

    public function ingreso_cuota_por_parte(Request $request)
    {
        try {
            $Params = $request->all();

            $ingreso = new ingreso();
            $ingreso->monto = $Params["monto_total"];
            $ingreso->descripcion = "amortizacion de la solicitud n° {$Params['get_prestamo']['solicitud']['code']}";
            $ingreso->created_user = auth()->user()->id;
            $ingreso->sucursal_id = auth()->user()->sucursal_id;
            $ingreso->yes_office = $Params["yes_office"] ? "Y" : "N";
            $ingreso->caja_chica_id = caja::where("created_user", auth()->user()->id)->where("status", "A")->first()->caja_chica_id;
            $ingreso->codigo = funciones::generar_codigo($Params["monto_total"]);
            $ingreso->created_at = Carbon::now();
            $ingreso->is_amortizacion = "Y";
            $ingreso->prestamo_id = $Params['get_prestamo']["prestamo_id"];
            $ingreso->updated_at = Carbon::now();
            $ingreso->save();

            if ($Params["is_cancelado"]) {
                $prestamo = prestamo::find($Params['get_prestamo']['prestamo_id']);
                $prestamo->status = "C";
                $prestamo->save();
            }

            if (!$ingreso->save()) {
                return response()->json([
                    'message' => 'error al registrar los datos, inetentar de nuevo',
                    'error' => '',
                    'success' => false,
                    'data' => '',
                ]);
            }

            foreach ($Params["pagos"] as $pago) {
                $pagos = new pagos();
                $pagos->ingreso_id = $ingreso->ingreso_id;
                $pagos->monto = $pago["monto"];
                $pagos->cuentas_id = Encryptor::decrypt($pago["cuentas_id"]);
                $pagos->tipo = "I";
                $pagos->caja_chica_id = caja::where("created_user", auth()->user()->id)->where("status", "A")->first()->caja_chica_id;
                $pagos->created_user  = auth()->user()->id;
                $pagos->sucursal_id  = auth()->user()->sucursal_id;
                $pagos->save();
            }


            if ($ingreso) {
                $ingreso = ingreso::with([
                    "usuario",
                    "prestamo" => function ($query) {
                        $query->with([
                            "solicitud" => function ($query) {
                                return $query->with("analista", "cliente");
                            }
                        ]);
                    }
                ])->find($ingreso->ingreso_id);

                send_email_controller::send_email_ingreso_creado(
                    Auth::user()->name . " " . Auth::user()->last_name . " genero en un ingreso de S./" . $ingreso->monto . " del Cliente :" .
                        $ingreso->prestamo->solicitud->cliente->cli_nombre . " " . $ingreso->prestamo->solicitud->cliente->cli_apellido
                        . " con codigo: " . $ingreso->codigo,
                    "Ingreso generado",

                );

                if ($Params["yes_office"] == "Y") {
                    ticketera::imprimir_ingreso(
                        $Params['saldo_pendiente'],
                        $ingreso->monto,
                        $ingreso->codigo,
                        $ingreso->prestamo->solicitud->cliente->cli_nombre . " " . $ingreso->prestamo->solicitud->cliente->cli_apellido,
                        $Params['get_prestamo']['solicitud']['code'],
                        $ingreso->prestamo->solicitud->analista->name,
                        $ingreso->usuario->name,
                        $Params['saldo_pendiente'] - $ingreso->monto
                    );

                    ticketera::imprimir_ingreso(
                        $Params['saldo_pendiente'],
                        $ingreso->monto,
                        $ingreso->codigo,
                        $ingreso->prestamo->solicitud->cliente->cli_nombre . " " . $ingreso->prestamo->solicitud->cliente->cli_apellido,
                        $Params['get_prestamo']['solicitud']['code'],
                        $ingreso->prestamo->solicitud->analista->name,
                        $ingreso->usuario->name,
                        $Params['saldo_pendiente'] - $ingreso->monto,
                        "Recepcion"
                    );
                } else {
                    ticketera::imprimir_ingreso(
                        $Params['saldo_pendiente'],
                        $ingreso->monto,
                        $ingreso->codigo,
                        $ingreso->prestamo->solicitud->cliente->cli_nombre . " " . $ingreso->prestamo->solicitud->cliente->cli_apellido,
                        $Params['get_prestamo']['solicitud']['code'],
                        $ingreso->prestamo->solicitud->analista->name,
                        $ingreso->usuario->name,
                        $Params['saldo_pendiente'] - $ingreso->monto,
                        "Recepcion"
                    );

                    ticketera::imprimir_ingreso(
                        $Params['saldo_pendiente'],
                        $ingreso->monto,
                        $ingreso->codigo,
                        $ingreso->prestamo->solicitud->cliente->cli_nombre . " " . $ingreso->prestamo->solicitud->cliente->cli_apellido,
                        $Params['get_prestamo']['solicitud']['code'],
                        $ingreso->prestamo->solicitud->analista->name,
                        $ingreso->usuario->name,
                        $Params['saldo_pendiente'] - $ingreso->monto,
                        "Analista"
                    );
                }


                session()->flash('success', 'Ingresos creado correctamente');
                return response()->json([
                    'message' => 'Ingreso creado correctamente',
                    'error' => '',
                    'success' => true,
                    'data' => route("planilla_prestamos"),
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


    // crear reprogramacion
    public function crear_reporgramacion(Request $request)
    {
        try {
            $Params = $request->all();

            $solicitud_anterior = Solicitud::find(Encryptor::decrypt($Params['urlapi']));

            // Validar si YA TIENE reprogramación creada
            $existe = Solicitud::where('solicitud_id_relacionado', $solicitud_anterior->solicitud_id)
                ->where('tipo_solicitud', 'Reprogramacion')
                ->where('status', '!=', 'C') // ejemplo: si quieres evitar duplicar activas
                ->first();

            if ($existe) {
                return response()->json([
                    'message' => 'Ya existe una reprogramación activa para esta solicitud',
                    'error' => 'ya existe una reprogramación activa para esta solicitud',
                    'success' => false,
                    'data' => '',
                ]);
            }

            DB::beginTransaction();

            $solicitud = new Solicitud();
            // Establece los valores de los atributos del modelo
            $solicitud->tipo_solicitud =  "Reprogramacion"; // O 'Recurrente' o 'Refinanciamiento'
            $solicitud->analista_id =  $solicitud_anterior->analista_id; // ID del analista
            $solicitud->cli_id =  $solicitud_anterior->cli_id; // ID del cliente (bigint unsigned)
            $solicitud->solicitud_nombre =  $solicitud_anterior->solicitud_nombre;
            $solicitud->solicitud_documento =  $solicitud_anterior->solicitud_documento;
            $solicitud->solicitud_conyugue_dni =  $solicitud_anterior->solicitud_conyugue_dni; // Opcional
            $solicitud->solicitud_nombre_conyuge =  $solicitud_anterior->solicitud_nombre_conyuge; // Opcional
            $solicitud->destino =  $solicitud_anterior->destino; // Opcional
            $solicitud->tipo_vivienda =  $solicitud_anterior->tipo_vivienda; // O 'Alquilada', etc.
            $solicitud->solicitud_direccion_negocio =  $solicitud_anterior->solicitud_direccion_negocio; // Opcional
            $solicitud->solicitud_referencia_negocio =  $solicitud_anterior->solicitud_referencia_negocio; // Opcional
            $solicitud->solicitud_domicilio =  $solicitud_anterior->solicitud_domicilio;
            $solicitud->solicitud_antiguedad =  $solicitud_anterior->solicitud_antiguedad; // Opcional
            $solicitud->sucursal_id = Auth::user()->sucursal_id; // ID de la sucursal
            $solicitud->solicitud_giro =  $solicitud_anterior->solicitud_giro; // Opcional
            $solicitud->solicitud_lugar =  $solicitud_anterior->solicitud_lugar; // Opcional
            $solicitud->solicitud_referencia_cliente =  $solicitud_anterior->solicitud_referencia_cliente; // Opcional
            $solicitud->solicitud_conyugue_ruc =  $solicitud_anterior->solicitud_conyugue_ruc; // Opcional
            $solicitud->created_user = Auth::user()->id; // Opcional
            $solicitud->solicitud_id_relacionado = $solicitud_anterior->solicitud_id;
            $solicitud->status = "G";

            if ($solicitud->save()) {

                $prestamo = new Prestamo();

                $prestamo->cli_id = $solicitud_anterior->cli_id;
                $prestamo->analista_id = $solicitud_anterior->analista_id;
                $prestamo->cuotas =  $request->input('cuotas'); // Número de cuotas
                $prestamo->interes = $request->input('interes'); // Tasa de interés (opcional)
                $prestamo->user_id = Auth::user()->id; // ID del usuario  // ID del analista 
                $prestamo->sucursal_id = Auth::user()->sucursal_id; // ID de la sucursal
                $prestamo->intervalo =  $request->input('intervalo'); // Días del plazo (opcional)
                $prestamo->moto_credito = $request->input('monto_credito'); // Monto del crédito 
                $prestamo->frecuencia_pagos =  $request->input('frecuencia_pagos'); // Frecuencia de pago
                $prestamo->status =  "A"; // Frecuencia de pago
                $prestamo->solicitud_id = $solicitud->solicitud_id; // ID de la solicitud relacionada (opcional)
                $prestamo->tasa_diaria = ($request->input('intervalo') / 30);
                $prestamo->d_t =  $request->input('d_t');
                $prestamo->save();

                $solicitud->prestamo_id =  $prestamo->prestamo_id;
                $solicitud->save();

                $prestamo_anterior = prestamo::find($solicitud_anterior->prestamo_id);
                $prestamo_anterior->status = "N";
                $prestamo_anterior->save();

                foreach ($Params["cronograma"] as $cronograma) {

                    $fecha = $cronograma["fechaVencimiento"];

                    // Crear un objeto DateTime a partir de la fecha en el formato de entrada
                    $fecha_objeto = DateTime::createFromFormat('d/n/Y', $fecha);

                    // Formatear la fecha al formato deseado "15/04/2024"
                    $fecha_formateada = $fecha_objeto->format('Y-m-d');

                    $cronogramasData[] = [
                        'amortizacion' => $cronograma["amortizacion"],
                        'cuota' => $cronograma["cuota"],
                        'fechaVencimiento' => $fecha_formateada,
                        'interes' => $cronograma["interes"],
                        'periodo' => $cronograma["periodo"],
                        'saldoCapital' => $cronograma["saldoCapital"],
                        'prestamo_id' => $prestamo->prestamo_id,
                        'status' => "N",
                    ];
                }
                // Insertar los datos del cronograma en la base de datos 
                cronograma::insert($cronogramasData);

                DB::commit();

                $ruta = asset("/") . 'solicitud/' . Encryptor::encrypt($solicitud->solicitud_id);

                session()->flash('success', 'Reprogramacion creada correctamente correctamente');

                return response()->json([
                    'message' => 'Reprogramacion creada correctamente correctamente',
                    'error' => '',
                    'success' => true,
                    'data' => $ruta,
                ]);
            } else {
                DB::rollback();
                return response()->json([
                    'message' => '',
                    'error' => '',
                    'success' => false,
                    'data' => '',
                ]);
            }
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
            return response()->json([
                'message' => 'error del servidor',
                'error' => $th->getMessage(),
                'success' => false,
                'data' => '',
            ]);
        }
    }


    // crear reprogramacion
    public function crear_refinanciamiento(Request $request)
    {
        try {
            $Params = $request->all();

            $solicitud_anterior = Solicitud::find(Encryptor::decrypt($Params['urlapi']));

            $solicitud = new Solicitud();

            // Establece los valores de los atributos del modelo
            $solicitud->tipo_solicitud =  "Refinanciamiento"; // O 'Recurrente' o 'Refinanciamiento'
            $solicitud->analista_id =  $solicitud_anterior->analista_id; // ID del analista
            $solicitud->cli_id =  $solicitud_anterior->cli_id; // ID del cliente (bigint unsigned)
            $solicitud->solicitud_nombre =  $solicitud_anterior->solicitud_nombre;
            $solicitud->solicitud_documento =  $solicitud_anterior->solicitud_documento;
            $solicitud->solicitud_conyugue_dni =  $solicitud_anterior->solicitud_conyugue_dni; // Opcional
            $solicitud->solicitud_nombre_conyuge =  $solicitud_anterior->solicitud_nombre_conyuge; // Opcional
            $solicitud->destino =  $solicitud_anterior->destino; // Opcional
            $solicitud->tipo_vivienda =  $solicitud_anterior->tipo_vivienda; // O 'Alquilada', etc.
            $solicitud->solicitud_direccion_negocio =  $solicitud_anterior->solicitud_direccion_negocio; // Opcional
            $solicitud->solicitud_referencia_negocio =  $solicitud_anterior->solicitud_referencia_negocio; // Opcional
            $solicitud->solicitud_domicilio =  $solicitud_anterior->solicitud_domicilio;
            $solicitud->solicitud_antiguedad =  $solicitud_anterior->solicitud_antiguedad; // Opcional
            $solicitud->sucursal_id = Auth::user()->sucursal_id; // ID de la sucursal
            $solicitud->solicitud_giro =  $solicitud_anterior->solicitud_giro; // Opcional
            $solicitud->solicitud_lugar =  $solicitud_anterior->solicitud_lugar; // Opcional
            $solicitud->solicitud_referencia_cliente =  $solicitud_anterior->solicitud_referencia_cliente; // Opcional
            $solicitud->solicitud_conyugue_ruc =  $solicitud_anterior->solicitud_conyugue_ruc; // Opcional
            $solicitud->created_user = Auth::user()->id; // Opcional
            $solicitud->solicitud_id_relacionado = $solicitud_anterior->solicitud_id;
            $solicitud->status = "G";

            if ($solicitud->save()) {

                $prestamo = new Prestamo();

                $prestamo->cli_id = $solicitud_anterior->cli_id;
                $prestamo->analista_id = $solicitud_anterior->analista_id;
                $prestamo->cuotas =  $request->input('cuotas'); // Número de cuotas
                $prestamo->interes = $request->input('interes'); // Tasa de interés (opcional)
                $prestamo->user_id = Auth::user()->id; // ID del usuario  // ID del analista 
                $prestamo->sucursal_id = Auth::user()->sucursal_id; // ID de la sucursal
                $prestamo->intervalo =  $request->input('intervalo'); // Días del plazo (opcional)
                $prestamo->moto_credito = $request->input('monto_credito'); // Monto del crédito 
                $prestamo->frecuencia_pagos =  $request->input('frecuencia_pagos'); // Frecuencia de pago
                $prestamo->solicitud_id = $solicitud->solicitud_id; // ID de la solicitud relacionada (opcional)

                $prestamo->save();

                $solicitud->prestamo_id =  $prestamo->prestamo_id;
                $solicitud->save();

                $prestamo_anterior = prestamo::find($solicitud_anterior->prestamo_id);
                $prestamo_anterior->status = "N";
                $prestamo_anterior->save();


                foreach ($Params["cronograma"] as $cronograma) {

                    $fecha = $cronograma["fechaVencimiento"];

                    // Crear un objeto DateTime a partir de la fecha en el formato de entrada
                    $fecha_objeto = DateTime::createFromFormat('d/n/Y', $fecha);

                    // Formatear la fecha al formato deseado "15/04/2024"
                    $fecha_formateada = $fecha_objeto->format('Y-m-d');

                    $cronogramasData[] = [
                        'amortizacion' => $cronograma["amortizacion"],
                        'cuota' => $cronograma["cuota"],
                        'fechaVencimiento' => $fecha_formateada,
                        'interes' => $cronograma["interes"],
                        'periodo' => $cronograma["periodo"],
                        'saldoCapital' => $cronograma["saldoCapital"],
                        'prestamo_id' => $prestamo->prestamo_id,
                        'status' => "R",
                    ];
                }
                // Insertar los datos del cronograma en la base de datos 
                cronograma::insert($cronogramasData);


                $ruta = asset("/") . 'solicitud/' . Encryptor::encrypt($solicitud->solicitud_id);

                session()->flash('success', 'Reprogramacion creada correctamente correctamente');

                return response()->json([
                    'message' => 'Reprogramacion creada correctamente correctamente',
                    'error' => '',
                    'success' => true,
                    'data' => $ruta,
                ]);
            } else {
                return response()->json([
                    'message' => '',
                    'error' => '',
                    'success' => false,
                    'data' => '',
                ]);
            }
            if (true) {
                return response()->json([
                    'message' => '',
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

    // crear reprogramacion
    public function crear_ampliacion(Request $request)
    {
        try {
            $Params = $request->all();


            $solicitud_anterior = Solicitud::find(Encryptor::decrypt($Params['urlapi']));

            $solicitud = new Solicitud();

            // Establece los valores de los atributos del modelo
            $solicitud->tipo_solicitud =  "Ampliacion";
            $solicitud->analista_id =  $solicitud_anterior->analista_id; // ID del analista
            $solicitud->cli_id =  $solicitud_anterior->cli_id; // ID del cliente (bigint unsigned)
            $solicitud->solicitud_nombre =  $solicitud_anterior->solicitud_nombre;
            $solicitud->solicitud_documento =  $solicitud_anterior->solicitud_documento;
            $solicitud->solicitud_conyugue_dni =  $solicitud_anterior->solicitud_conyugue_dni; // Opcional
            $solicitud->solicitud_nombre_conyuge =  $solicitud_anterior->solicitud_nombre_conyuge; // Opcional
            $solicitud->destino = $solicitud_anterior->destino; // Opcional
            $solicitud->tipo_vivienda = $solicitud_anterior->tipo_vivienda; // O 'Alquilada', etc.
            $solicitud->solicitud_direccion_negocio = $solicitud_anterior->solicitud_direccion_negocio; // Opcional
            $solicitud->solicitud_referencia_negocio = $solicitud_anterior->solicitud_referencia_negocio; // Opcional
            $solicitud->solicitud_domicilio = $solicitud_anterior->solicitud_domicilio;
            $solicitud->solicitud_antiguedad = $solicitud_anterior->solicitud_antiguedad; // Opcional
            $solicitud->sucursal_id = Auth::user()->sucursal_id; // ID de la sucursal
            $solicitud->solicitud_giro = $solicitud_anterior->solicitud_giro; // Opcional
            $solicitud->solicitud_lugar = $solicitud_anterior->solicitud_lugar; // Opcional
            $solicitud->solicitud_referencia_cliente =  $solicitud_anterior->solicitud_referencia_cliente; // Opcional
            $solicitud->solicitud_conyugue_ruc = $solicitud_anterior->solicitud_conyugue_ruc; // Opcional
            $solicitud->created_user = Auth::user()->id; // Opcional
            $solicitud->solicitud_id_relacionado = $solicitud_anterior->solicitud_id;
            $solicitud->status = "A";


            if ($solicitud->save()) {

                $prestamo = new Prestamo();

                $prestamo->cli_id = $solicitud_anterior->cli_id;
                $prestamo->analista_id = $solicitud_anterior->analista_id;
                $prestamo->cuotas =  $request->input('cuotas'); // Número de cuotas
                $prestamo->interes = $request->input('interes'); // Tasa de interés (opcional)
                $prestamo->user_id = Auth::user()->id; // ID del usuario  // ID del analista 
                $prestamo->sucursal_id = Auth::user()->sucursal_id; // ID de la sucursal
                $prestamo->intervalo =  $request->input('intervalo'); // Días del plazo (opcional)
                $prestamo->moto_credito = $request->input('monto_credito'); // Monto del crédito 
                $prestamo->frecuencia_pagos =  $request->input('frecuencia_pagos'); // Frecuencia de pago
                $prestamo->solicitud_id = $solicitud->solicitud_id; // ID de la solicitud relacionada (opcional)
                $prestamo->monto_ampliacion = $request->input('monto_credito_nueva');
                $prestamo->tasa_diaria = ($request->input('intervalo') / 30);
                $prestamo->d_t =  $request->input('d_t');
                $prestamo->is_fecha_pago = $request->input('is_fecha_pago') ? "A" : "N";
                $prestamo->fecha_de_pago_cuota = $request->input('is_fecha_pago') ? Carbon::parse($request->input('fecha_de_pago_cuota'))->format("Y-m-d") : NULL;
                $prestamo->save();

                $solicitud->prestamo_id =  $prestamo->prestamo_id;
                $solicitud->save();

                // $prestamo_anterior = prestamo::find($solicitud_anterior->prestamo_id);
                // $prestamo_anterior->status = "N";
                // $prestamo_anterior->save();


                // foreach ($Params["cronograma"] as $cronograma) {

                //     $fecha = $cronograma["fechaVencimiento"];

                //     // Crear un objeto DateTime a partir de la fecha en el formato de entrada
                //     $fecha_objeto = DateTime::createFromFormat('d/n/Y', $fecha);

                //     // Formatear la fecha al formato deseado "15/04/2024"
                //     $fecha_formateada = $fecha_objeto->format('Y-m-d');

                //     $cronogramasData[] = [
                //         'amortizacion' => $cronograma["amortizacion"],
                //         'cuota' => $cronograma["cuota"],
                //         'fechaVencimiento' => $fecha_formateada,
                //         'interes' => $cronograma["interes"],
                //         'periodo' => $cronograma["periodo"],
                //         'saldoCapital' => $cronograma["saldoCapital"],
                //         'prestamo_id' => $prestamo->prestamo_id,
                //         'status' => "R",
                //     ];
                // }
                // // Insertar los datos del cronograma en la base de datos 
                // cronograma::insert($cronogramasData); 

                $ruta = asset("/") . 'solicitud/' . Encryptor::encrypt($solicitud->solicitud_id);

                session()->flash('success', 'Ampliacion creada correctamente correctamente');

                return response()->json([
                    'message' => 'Reprogramacion creada correctamente correctamente',
                    'error' => '',
                    'success' => true,
                    'data' => $ruta,
                ]);
            } else {
                return response()->json([
                    'message' => '',
                    'error' => '',
                    'success' => false,
                    'data' => '',
                ]);
            }
            if (true) {
                return response()->json([
                    'message' => '',
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

    //refinanciamiento
    public function refinanciamiento($urlapi)
    {
        if (Auth::user()->rol == "recepcionista") {
            return view("errors.desautorizacion");
        }

        $solicitud = Solicitud::with([
            "cliente" => function ($query) {
                return $query->with(["ContactosCliente"]);
            },
            "analista",
            "prestamo" => function ($query) {
                return $query->with(["cronograma"]);
            },
            "guardar_documento" => function ($query) {
                return $query->with(["sucursal", "usuario"]);
            },
        ])->find(Encryptor::decrypt($urlapi));

        if ($solicitud->prestamo->frecuencia_pagos == "Mensual") {
            $monto_credito_nuevo = cronograma::where("prestamo_id", $solicitud->prestamo->prestamo_id)->where("yes_pago", "Y")->sum("amortizacion");
        } else {
            $monto_credito_nuevo = cronograma::where("prestamo_id", $solicitud->prestamo->prestamo_id)->where("yes_pago", "Y")->sum("amortizacion");
        }

        return view("modules.prestamo.refinanciamiento", ["solicitud" => $solicitud, "monto_credito_nuevo" => $monto_credito_nuevo]);
    }

    // crear mensajes para los prestamo
    function mensajes_prestamo(Request $request)
    {
        try {
            $Params = $request->all();

            $mensaje_prestamo = new mensajes_prestamo();
            $mensaje_prestamo->msj_descripcion = $Params['msj_descripcion'];
            $mensaje_prestamo->fecha_programada = Carbon::parse($Params['fecha_programada'])->format("Y-m-d");
            $mensaje_prestamo->fecha_anticipada = Carbon::parse($Params['fecha_programada'])->subDay()->format("Y-m-d");
            $mensaje_prestamo->prestamo_id = Encryptor::decrypt($Params['prestamo_urlapi']);
            $mensaje_prestamo->created_user = Auth::user()->id;
            $mensaje_prestamo->sucursal_id = Auth::user()->sucursal_id;

            if ($mensaje_prestamo->save()) {

                $prestamo = prestamo::find(Encryptor::decrypt($Params['prestamo_urlapi']));
                $prestamo->is_mensaje = "Y";
                $prestamo->save();

                return response()->json([
                    'message' => 'Mensaje creado correctamente',
                    'error' => '',
                    'success' => true,
                    'data' => $prestamo->is_mensaje,
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

    // tabla de todo los mensajes
    public function table_mensaje_prestamo() {}

    // cargar mensajes
    public function load_mensaje_prestamo(Request $request)
    {
        try {
            $Params = $request->all();
            $mensaje_prestamo = mensajes_prestamo::where("prestamo_id", Encryptor::decrypt($Params['prestamo_urlapi']))->first();

            if ($mensaje_prestamo) {
                return response()->json([
                    'message' => 'datos cargados exitosamente',
                    'error' => '',
                    'success' => true,
                    'data' => $mensaje_prestamo,
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


    public function load_message_by_user_before(Request $request)
    {
        try {
            $id = Auth::user()->id;

            $mensaje_prestamo = mensajes_prestamo::with(["prestamo" => function ($query) {
                return $query->with(["solicitud" => function ($query) {
                    return $query->with(["cliente"]);
                }]);
            }])
                ->where("check_one_alert", "A")
                ->where("check_two_alert", "A")
                ->where("created_user", $id)
                ->where("fecha_anticipada", "=", Carbon::now()->format("Y-m-d"))
                ->get();


            return response()->json([
                'message' => 'datos cargados exitosamente',
                'error' => '',
                'success' => true,
                'data' => $mensaje_prestamo,
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

    public function load_message_by_user_after(Request $request)
    {
        try {
            $id = Auth::user()->id;

            $mensaje_prestamo = mensajes_prestamo::with(["prestamo" => function ($query) {
                return $query->with(["solicitud" => function ($query) {
                    return $query->with(["cliente"]);
                }]);
            }])
                ->where("check_one_alert", "N")
                ->where("check_two_alert", "A")
                ->where("created_user", $id)
                ->where("fecha_programada", "=", Carbon::now()->format("Y-m-d"))
                ->get();

            return response()->json([
                'message' => 'datos cargados exitosamente',
                'error' => '',
                'success' => true,
                'data' => $mensaje_prestamo,
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


    public function do_no_show_message_before(Request $request)
    {
        try {

            $Datax = $request->all();

            $mensaje_prestamo = mensajes_prestamo::where("msj_prestamo_id", Encryptor::decrypt($Datax["id"]))->first();
            $mensaje_prestamo->check_one_alert = "N";

            if ($mensaje_prestamo->save()) {
                return response()->json([
                    'message' => 'El mensaje no volvera a aparecer',
                    'error' => '',
                    'success' => true,
                    'data' => [],
                ]);
            } else {
                return response()->json([
                    'message' => 'El mensaje volvera a aparecer',
                    'error' => '',
                    'success' => true,
                    'data' =>  [],
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

    public function do_no_show_message_after(Request $request)
    {
        try {

            $Datax = $request->all();

            $mensaje_prestamo = mensajes_prestamo::where("msj_prestamo_id", Encryptor::decrypt($Datax["id"]))->first();
            $mensaje_prestamo->check_two_alert = "N";
            $mensaje_prestamo->status = "A";

            if ($mensaje_prestamo->save()) {
                return response()->json([
                    'message' => 'El mensaje no volvera a aparecer',
                    'error' => '',
                    'success' => true,
                    'data' => [],
                ]);
            } else {
                return response()->json([
                    'message' => 'El mensaje volvera a aparecer',
                    'error' => '',
                    'success' => true,
                    'data' =>  [],
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

    // cargar ingresos a un prestamo
    public function load_ingresos_prestamo(Request $request)
    {
        try {
            $Params = $request->all();

            $solicitud_id = Encryptor::decrypt($Params['urlapi']);
  

            $solicitud_get = solicitud::find($solicitud_id);


            Log::info($solicitud_get);

            $ingresos_prestamo = ingreso::with([
                "pagos" => function ($query) {
                    return $query->with(["cuenta"]);
                }
            ])->where("prestamo_id", $solicitud_get->prestamo_id)->get();


            if ($ingresos_prestamo) {
                return response()->json([
                    'message' => 'datos cargados exitosamente',
                    'error' => '',
                    'success' => true,
                    'data' => $ingresos_prestamo,
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


    // esta funcion se encargara de cancelar el cronograma de pagos de un prestamo
    public function cancelar_cronograma(Request $request)
    {
        try {


            if (Auth::user()->rol != "gerente") {
                return response()->json([
                    'message' => 'No puedes cancelar esta cuota',
                    'error' => '',
                    'success' => false,
                    'data' => '',
                ]);
            }

            $Params = $request->all();

            $cronograma_id = Encryptor::decrypt($Params['cronograma_id']);

            if ($Params['ncuota'] == $Params['periodo']) {
                $prestamo = prestamo::find(Encryptor::decrypt($Params['prestamo_id']));
                $prestamo->status = "C";
                $prestamo->save();
            }

            $cronograma = cronograma::find($cronograma_id);
            $cronograma->yes_pago = "Y";



            if ($cronograma->save()) {
                return response()->json([
                    'message' => 'se actulizo correctamente',
                    'error' => '',
                    'success' => true,
                    'data' => []
                ]);
            } else {
                return response()->json([
                    'message' => 'error al actulizar este cronograma',
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


    public function load_desemboloso(Request $request)
    {
        try {
            $Params = $request->all();

            $solicitud_id = Encryptor::decrypt($Params['solicitud_id']);

            $gasto = gastos::with([
                "usuario"
            ])->where("solicitud_id", $solicitud_id)->first();

            if ($gasto) {
                return response()->json([
                    'message' => 'datos creados exitosamente',
                    'error' => '',
                    'success' => true,
                    'data' => $gasto
                ]);
            } else {
                return response()->json([
                    'message' => 'esta solicitud no tiene un desembolso asociado',
                    'error' => '',
                    'success' => false,
                    'data' => [],
                ]);
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json([
                'message' => 'error del servidor',
                'error' => $th->getMessage(),
                'success' => false,
                'data' => [],
            ]);
        }
    }

    public function comprobar_pago_cuotas_reprogramacion(Request $request)
    {
        try {
            $Params = $request->all();

            $solicitud_id = Encryptor::decrypt($Params['urlapi']);

            $solicitud = solicitud::with([
                "prestamo"
            ])->find($solicitud_id);

            // solo se puede aplicar para prestamos mensuales
            if ($solicitud->prestamo->frecuencia_pagos != "Mensual") {
                return response()->json([
                    'message' => 'No se puede realizar el pago porque no es un prestamo mensual',
                    'error' => "No se puede realizar el pago porque no es un prestamo mensual",
                    'success' => false,
                    'data' => [],
                ]);
            }

            $cuotas = cronograma::where("prestamo_id", $solicitud->prestamo->prestamo_id)->orderBy("periodo", "ASC")->get();
            // comprobar si la ultima cuota pago su interes y si su cuota tiene mora y pago la mora
            $cuota_actual =  $solicitud->prestamo->cuota_actual;

            $fechaVencimiento = Carbon::createFromFormat('Y-m-d', $cuota_actual->fechaVencimiento); // Convierte la fecha a Carbon
            $fechaActual = Carbon::now()->format('Y-m-d'); // Obtiene la fecha actual en el mismo formato

            if (!$fechaActual > $fechaVencimiento->format('Y-m-d')) {
                return response()->json([
                    'message' => 'No se puede realizar el pago porque no hay cuotas vencidas',
                    'error' => "No se puede realizar el pago porque no hay cuotas vencidas",
                    'success' => false,
                    'data' => [],
                ]);
            }

            // cargar ingresos de la cuota actual por su cronograma sacados de la tabla detalle_ingresos
            $ingresos_cuota_actual = detalle_ingreso::with([
                "ingreso",
            ])->where("cronograma_id", $cuota_actual->cronograma_id)->get()->sum("ingreso.monto");

            $ingresos_cuota_actual = floatval($ingresos_cuota_actual);
            $interes_cuota = floatval($cuota_actual->interes);

            if ($ingresos_cuota_actual <  $interes_cuota) {
                return response()->json([
                    'message' => 'No se puede realizar el pago porque que no pago el interes ',
                    'error' => "No se puede realizar el pago porque que no pago el interes",
                    'success' => false,
                    'data' => [],
                ]);
            }

            $data_output = [
                "cuotas" => $cuotas,
                "cuota_actual" => $cuota_actual,
                "prestamo" => $solicitud->prestamo,
                "ingresos_cuota_actual" => $ingresos_cuota_actual
            ];

            return response()->json([
                'message' => 'datos creados exitosamente',
                'error' => '',
                'success' => true,
                'data' => $data_output
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json([
                'message' => 'error del servidor',
                'error' => $th->getMessage(),
                'success' => false,
                'data' => [],
            ]);
        }
    }


    public function comprobar_pago_cuotas_reprogramacion_store(Request $request)
    {

        try {
            $Datax = $request->all();
            $solicitud = Solicitud::find(Encryptor::decrypt($Datax["urlapi"]));

            $prestamo = prestamo::find($solicitud->prestamo_id);
            $cronograma = cronograma::where("prestamo_id", $prestamo->prestamo_id)
                ->orderBy('periodo') // Asegurar orden correcto
                ->get();

            $index_cambiado = null;
            $nueva_cuota_data = null;

            // Paso 1: Encontrar la cuota a reprogramar y preparar los datos
            foreach ($cronograma as $index => $c_g) {
                if ($c_g->periodo == $prestamo->cuota_actual->periodo) {
                    $nueva_cuota_data = [
                        'prestamo_id' => $prestamo->prestamo_id,
                        'periodo' => $c_g->periodo + 1,
                        'fechaVencimiento' => Carbon::parse($c_g->fechaVencimiento)->addMonths(1)->format('Y-m-d'),
                        'saldoCapital' => $c_g->saldoCapital,
                        'amortizacion' => $c_g->amortizacion,
                        'interes' => $c_g->interes,
                        'cuota' => $c_g->cuota,
                        'yes_reprogramacion_duplicate' => 'Y'
                    ];
                    $index_cambiado = $index;
                    break; // Salir del bucle una vez encontrada
                }
            }

            // Iniciar transacción de base de datos
            DB::beginTransaction();

            // Paso 2: Si se encontró la cuota, proceder con la reprogramación
            if (!is_null($index_cambiado) && $nueva_cuota_data) {
                // Primero actualizar las cuotas posteriores en la colección
                foreach ($cronograma as $index => $c_g) {
                    if ($index > $index_cambiado) {
                        $c_g->periodo += 1;
                        $c_g->fechaVencimiento = Carbon::parse($c_g->fechaVencimiento)->addMonths(1)->format('Y-m-d');
                        $c_g->save(); // Guardar los cambios en la base de datos
                    }
                }

                // Paso 3: Crear la nueva cuota reprogramada
                $nueva_cuota = new Cronograma();
                foreach ($nueva_cuota_data as $key => $value) {
                    $nueva_cuota->$key = $value;
                }
                $nueva_cuota->save();

                // Paso 4: Actualizar la cuota actual para que se cancele 
                $cuota_actual_editar = cronograma::find($prestamo->cuota_actual->cronograma_id);
                $cuota_actual_editar->yes_pago = "Y";
                $cuota_actual_editar->yes_reprogramacion = "Y";
                $cuota_actual_editar->save();


                Log::info("Cuota reprogramada exitosamente. Período original: " . $prestamo->cuota_actual->periodo);
            } else {
                DB::rollBack();
                Log::warning("No se encontró la cuota a reprogramar. Período buscado: " . $prestamo->cuota_actual->periodo);
            }

            // Finalizar transacción de base de datos
            DB::commit();
            return response()->json([
                'message' => 'Datos cargados exitosamente',
                'error' => '',
                'success' => true,
                'data' => route("solicitud.show", $request->input('urlapi')),
            ]);
        } catch (\Throwable $th) {
            Log::error("Error en reprogramación de cuota: " . $th->getMessage());
            Log::error("Stack trace: " . $th->getTraceAsString());

            // En caso de error, realizar rollback de la transacción
            DB::rollBack();
            return response()->json([
                'message' => 'error del servidor',
                'error' => $th->getMessage(),
                'success' => false,
                'data' => [],
            ]);
        }
    }

    public function mensaje_prestamo()
    {
        return view("modules.prestamo.table_msj_prestamos");
    }

    public function load_data_msj_prestamo(Request $request)
    {
        if ($request->ajax()) {

            $query = mensajes_prestamo::with([
                "usuario"
            ])
                ->orderBy('created_at', "desc")
                ->get();


            return DataTables::of($query)
                ->addIndexColumn()
                ->toJson();
        }
    }

    public function probar_correo()
    {
        // return view("correo.send_correo");
        $destinatario = "juan_AVC789@hotmail.com";
        $ruta = 'https://laravel.com/docs/10.x/mail';
        $mensaje = 'prueba ';

        $mensaje = Mail::send('correo.send_correo', ['cliente' => $mensaje, 'nombre' => $mensaje, "ruta" => $ruta, "saludo" => "dasddas"], function ($message) use ($destinatario) {
            $message->to($destinatario)->subject('Asunto del mensaje');
        });
    }
    public function impresion_prueba()
    {

        // echo "https://github.com/Virrueta123/print_script.git";
        ticketera::imprimir_gasto(
            "prueba",
            "prueba",
            "prueba",
            "prueba",
            100.00
        );
    }
}
