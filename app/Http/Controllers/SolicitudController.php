<?php

namespace App\Http\Controllers;

use App\Helpers\Encryptor;
use App\Helpers\Generales;
use App\Jobs\enviar_mail_aprobada_solicitud;
use App\Jobs\enviar_mail_rechazo_solicitud;
use App\Jobs\enviar_mail_solicitud;
use App\Models\caja;
use App\Models\Cliente;
use App\Models\cronograma;
use App\Models\gastos;
use App\Models\guardar_documento;
use App\Models\ingreso;
use App\Models\pagos;
use App\Models\Prestamo;
use App\Models\Solicitud;
use App\Models\tipo_gasto;
use App\Models\User;
use App\Utils\funciones;
use App\Utils\ticketera;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

use Barryvdh\DomPDF\Facade\Pdf;
use DateTime;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Storage;
use Ilovepdf\Ilovepdf;
use Luecano\NumeroALetras\NumeroALetras;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Html\Column;

class SolicitudController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('bloqueado');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($parametro = null)
    {
        if ($parametro === null) {
            $solicitud = '';
        } else {
            $solicitud = Solicitud::with(["cliente", "prestamo"])->find(Encryptor::decrypt($parametro));
        }


        return View("modules.solicitud.create", compact('solicitud'));
    }

    // esta funcion sirve para restablecer a una solicitud anterior siempre y cuando no se haya generado ningun pago
    public function restablecer_solicitud(Request $request)
    {
        try {

            $Params = $request->all();


            $solicitud_actual = Solicitud::where("solicitud_id",Encryptor::decrypt($Params['urlapi']))->first();



            // ACTUALIZACION EL PRESTAMO ANTERIOR PARA SE RESTABLESCA
            $solicitud_anterior = Solicitud::where('solicitud_id', $solicitud_actual->solicitud_id_relacionado)->first();
            $prestamo_anterior = Prestamo::where("prestamo_id", $solicitud_anterior->prestamo_id)->first();


            $prestamo_anterior->status = "A";
            $prestamo_anterior->save();

            // ACTUALIZACION EL PRESTAMO ACTUAL PARA SE ELIMINE
            $prestamo_actual = Prestamo::where("prestamo_id",$solicitud_actual->prestamo_id)->first();
            $prestamo_actual->delete();
            $solicitud_actual->delete();

            return response()->json([
                'message' => 'Solicitud editado correctamente',
                'error' => '',
                'success' => true,
                'data' => Encryptor::encrypt($solicitud_anterior->solicitud_id),
            ]);


        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json([
                'message' => 'error del servidor, intentelo mas tarde',
                'error' => $th->getMessage(),
                'success' => false,
                'data' => '',
            ]);
        }
    }

    public function cambiar_contrato()
    {
        return view("modules.solicitud.cambiar_contrato");
    }

    public function print_parte_contrato(Request $request)
    {
        try {
            $Params = $request->all();

            $formatter = new NumeroALetras();

            $pdf = Pdf::loadView(
                'pdf.cambiar_contrato',
                [
                    'nombre' =>  $Params["nombre"],
                    'documento' =>  $Params["documento"],
                    'interes_deletreado' => strtolower($formatter->toWords($Params["interes"])),
                    'interes' => $Params["interes"],

                ]
            );

            $pdfContent = $pdf->output();
            $pdfBase64 = base64_encode($pdfContent);

            if (true) {
                return response()->json([
                    'message' => 'Contrato generado',
                    'error' => '',
                    'success' => true,
                    'data' => $pdfBase64,
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
        ])->find(Encryptor::decrypt($id));



        return view("modules.solicitud.show", compact("solicitud"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {}

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
    //otras rutas
    public function tabla_solicitud_aprobado(Request $request)
    {
        $fecha_actual = Carbon::now();

        return view("modules.solicitud.tables.tabla_solicitud_aprobado");
    }

    public function tabla_solicitud_pendiente(Request $request)
    {
        $fecha_actual = Carbon::now();

        return view("modules.solicitud.tables.tabla_solicitud_pendiente");
    }

    public function tabla_solicitud_procesado(Request $request)
    {
        $fecha_actual = Carbon::now();

        return view("modules.solicitud.tables.tabla_solictud_procesado");
    }

    public function modicarreprogramados()
    {

        $desembolosos = gastos::with(["solicitud"])->where('solicitud_id', '!=', 0)->get();

        foreach ($desembolosos as $ds) {
            if ($ds->solicitud->tipo_solicitud != 'Ampliacion') {
                $solicitud = Solicitud::find($ds->solicitud_id);
                $solicitud->tipo_solicitud = 'Recurrente';
                $solicitud->save();
                echo "Se ha modificado el estado de la solicitud: " . $ds->solicitud->serie;
            }
        }
    }

    public function tabla_solicitud_rechazado(Request $request)
    {
        $fecha_actual = Carbon::now();

        return view("modules.solicitud.tables.tabla_solicitud_rechazado");
    }

    // funciones para las rutas axios
    public function crear_solicitud(Request $request)
    {

        try {
            $Params = $request->all();

            $messages = [
                'analista.required' => 'Es necesario seleccionar un Analista es obligatorio.',

                'cliente.required' => 'Es necesario seleccionar un Cliente, es obligatorio.',

                'solicitud_nombre.required' => 'Es necesario ingresar el Nombre y apellido o Razón Social un Cliente, es obligatorio.',
                'solicitud_nombre.string' => 'El Nombre y apellido o Razón Social del Cliente debe ser una cadena de texto sin numeros.',

                'solicitud_conyugue_dni.required' => 'Es necesario ingresar un dni del conyugue, es obligatorio.',
                'solicitud_conyugue_dni.numeric' => 'El dni del conyugue debe ser numeros no texto.',

                'solicitud_nombre_conyuge.required' => 'Es necesario ingresar el nombre del conyugue, es obligatorio.',
                'solicitud_nombre_conyuge.string' => 'El dni del conyugue debe ser una cadena de texto sin numeros.',

                'solicitud_nombre_conyuge.required' => 'Es necesario ingresar el nombre del conyugue, es obligatorio.',
                'solicitud_nombre_conyuge.string' => 'El dni del conyugue debe ser una cadena de texto sin numeros.',

                'destino.required' => 'Es necesario ingresar el destino del prestamo, es obligatorio.',
                'destino.string' => 'El destino del prestamo debe ser una cadena de texto sin numeros.',

                'solicitud_direccion_negocio.required' => 'Es necesario ingresar el la direccion del negocio, es obligatorio.',

                'solicitud_referencia_negocio.required' => 'Es necesario ingresar la referencia del negocio, es obligatorio.',

                'solicitud_domicilio.required' => 'Es necesario ingresar el domicilio, es obligatorio.',
            ];

            $validator = Validator::make($Params, [
                'tipo_solicitud' => 'required|string',
                'analista' => 'required|string',
                'cliente' => 'required|string',
                'solicitud_nombre' => 'required|string',
                'solicitud_conyugue_dni' => 'nullable|numeric',
                'solicitud_nombre_conyuge' => 'nullable|string',
                'destino' => 'required|string',
                'tipo_vivienda' => 'required',
                'solicitud_direccion_negocio' => 'required',
                'solicitud_referencia_negocio' => 'required|string',
                'solicitud_domicilio' => 'required|string',
                'solicitud_antiguedad' => 'required|string',
                'solicitud_giro' => 'required|string',
                'solicitud_lugar' => 'required|string',
                'solicitud_referencia_cliente' => 'required|string',
                'solicitud_conyugue_ruc' => 'nullable|numeric',
                'solicitud_documento' => 'required|numeric',
                'cronograma' => 'required|array|min:1',
            ], $messages);

            if ($validator->fails()) {
                // Retorna los errores en formato JSON
                return response()->json([
                    'message' => 'Aun no se puede registrar por la siguientes obeservaciones',
                    'error' => 'validation',
                    'success' => false,
                    'data' => $validator->errors()->all(),
                ]);
            }


            $get_cliente = Cliente::find(Encryptor::decrypt($request->input('cliente')));
            // desencriptando id de analista
            $analista_id =  Encryptor::decrypt($request->input('analista'));

            $solicitud = new Solicitud();

            // Establece los valores de los atributos del modelo
            $solicitud->tipo_solicitud = $request->input('tipo_solicitud'); // O 'Recurrente' o 'Refinanciamiento'
            $solicitud->analista_id = $analista_id; // ID del analista
            $solicitud->cli_id =  $get_cliente->cli_id; // ID del cliente (bigint unsigned)
            $solicitud->solicitud_nombre = $request->input('solicitud_nombre');
            $solicitud->solicitud_documento = $request->input('solicitud_documento');
            $solicitud->solicitud_conyugue_dni = $request->input('solicitud_conyugue_dni');
            $solicitud->solicitud_nombre_conyuge = $request->input('solicitud_nombre_conyuge');
            $solicitud->destino = $request->input('destino');
            $solicitud->tipo_vivienda = $request->input('tipo_vivienda'); // O 'Alquilada', etc.
            $solicitud->solicitud_direccion_negocio = $request->input('solicitud_direccion_negocio');
            $solicitud->solicitud_referencia_negocio = $request->input('solicitud_referencia_negocio');
            $solicitud->solicitud_domicilio = $request->input('solicitud_domicilio');
            $solicitud->solicitud_antiguedad = $request->input('solicitud_antiguedad');
            $solicitud->sucursal_id = Auth::user()->sucursal_id; // ID de la sucursal
            $solicitud->solicitud_giro = $request->input('solicitud_giro');
            $solicitud->solicitud_lugar = $request->input('solicitud_lugar');
            $solicitud->solicitud_referencia_cliente = $request->input('solicitud_referencia_cliente');
            $solicitud->solicitud_conyugue_ruc = $request->input('solicitud_conyugue_ruc');
            $solicitud->solicitud_tarjeta = $request->input('solicitud_tarjeta');
            $solicitud->solicitud_entidad_tarjeta = $request->input('solicitud_entidad_tarjeta');

            if ($request->input('solicitud_paralelo') != "") {
                $solicitud->solicitud_id_relacionado = Encryptor::decrypt($request->input('solicitud_paralelo'));
            }
            $solicitud->created_user = Auth::user()->id;

            if ($solicitud->save()) {

                $prestamo = new Prestamo();

                // Establece los valores de los atributos del modelo
                $prestamo->cli_id = $get_cliente->cli_id;
                $prestamo->analista_id = $analista_id;
                $prestamo->cuotas = $request->input('cuotas'); // Número de cuotas
                $prestamo->interes = $request->input('interes'); // Tasa de interés (opcional)
                $prestamo->user_id = Auth::user()->id; // ID del usuario  // ID del analista
                $prestamo->sucursal_id = Auth::user()->sucursal_id; // ID de la sucursal
                $prestamo->intervalo =  $request->input('intervalo'); // Días del plazo (opcional)
                $prestamo->moto_credito = $request->input('monto_credito'); // Monto del crédito
                $prestamo->frecuencia_pagos =  $request->input('frecuencia_pagos'); // Frecuencia de pago
                $prestamo->solicitud_id = $solicitud->solicitud_id; // ID de la solicitud relacionada (opcional)
                $prestamo->d_t = $request->input('d_t');
                $prestamo->tasa_diaria = $request->input('tasa_diaria');
                $prestamo->is_fecha_pago = $request->input('is_fecha_pago') ? "A" : "N";
                $prestamo->fecha_de_pago_cuota = $request->input('is_fecha_pago') ? $request->input('fecha_de_pago_cuota') : NULL;
                $prestamo->status = "A";
                $prestamo->save();

                $solicitud->prestamo_id = $prestamo->prestamo_id;
                $solicitud->status = "G";
                $solicitud->save();

                $this->procesarAmpliacionSiAplica($solicitud);

                if (!$this->insertarCronograma($prestamo->prestamo_id, $Params['cronograma'])) {
                    return response()->json([
                        'message' => 'Error al generar el cronograma de pagos',
                        'error' => '',
                        'success' => false,
                        'data' => '',
                    ]);
                }

                $ruta = asset("/") . 'solicitud/' . Encryptor::encrypt($solicitud->solicitud_id);

                send_email_controller::send_email_ingreso_creado(
                    Auth::user()->name . " " . Auth::user()->last_name . " genero un prestamo con un monto de credito de"
                        . $prestamo->moto_credito .
                        " con "
                        . $prestamo->interes .
                        "% de interes en un intervalo de "
                        . $prestamo->intervalo .
                        " "
                        . funciones::frecuencia_a($prestamo->frecuencia_pagos) .
                        "",
                    "Prestamo creado",
                );

                session()->flash('success', 'Prestamo creado correctamente');

                return response()->json([
                    'message' => 'Prestamo creado correctamente',
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

    // edicion simplre de una solicitud
    public function editar_solicitud(Request $request)
    {

        try {

            $Params = $request->all();

            $solicitud = Solicitud::find(Encryptor::decrypt($Params['urlapi']));

            // Establece los valores de los atributos del modelo
            $solicitud->tipo_solicitud = $request->input('tipo_solicitud'); // O 'Recur
            $solicitud->solicitud_nombre = $request->input('solicitud_nombre');
            $solicitud->solicitud_documento = $request->input('solicitud_documento');
            $solicitud->solicitud_conyugue_dni = $request->input('solicitud_conyugue_dni'); // Opcional
            $solicitud->solicitud_nombre_conyuge = $request->input('solicitud_nombre_conyuge'); // Opcional
            $solicitud->destino = $request->input('destino'); // Opcional
            $solicitud->tipo_vivienda = $request->input('tipo_vivienda'); // O 'Alquilada', etc.
            $solicitud->solicitud_direccion_negocio = $request->input('solicitud_direccion_negocio'); // Opcional
            $solicitud->solicitud_referencia_negocio = $request->input('solicitud_referencia_negocio'); // Opcional
            $solicitud->solicitud_domicilio = $request->input('solicitud_domicilio');
            $solicitud->solicitud_antiguedad = $request->input('solicitud_antiguedad'); // Opcional
            $solicitud->sucursal_id = Auth::user()->sucursal_id; // ID de la sucursal
            $solicitud->solicitud_giro = $request->input('solicitud_giro'); // Opcional
            $solicitud->solicitud_lugar = $request->input('solicitud_lugar'); // Opcional
            $solicitud->solicitud_referencia_cliente = $request->input('solicitud_referencia_cliente'); // Opcional
            $solicitud->solicitud_conyugue_ruc = $request->input('solicitud_conyugue_ruc'); // Opcional
            $solicitud->solicitud_tarjeta = $request->input('solicitud_tarjeta');
            $solicitud->solicitud_entidad_tarjeta = $request->input('solicitud_entidad_tarjeta'); // Opcional


            if ($solicitud->save()) {

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
                ])->find($solicitud->solicitud_id);



                return response()->json([
                    'message' => 'Solicitud editado correctamente',
                    'error' => '',
                    'success' => true,
                    'data' => $solicitud,
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

    // rechazar solicitud
    public function send_rechazo_solicitud(Request $request)
    {
        try {
            $Params = $request->all();

            $solicitud =  Solicitud::findOrFail(Encryptor::decrypt($request->input('solicitud')));
            $solicitud->status = "R"; //R significa rechazado
            $solicitud->descripcion_rechazo = $request->input('descripcion_solicitud');

            if ($solicitud->save()) {
                $user =  User::find($solicitud->created_user);

                // enviar correo a la cola de trabajo
                // enviar_mail_rechazo_solicitud::dispatch($user, route("solicitud.show", $request->input('solicitud')), $solicitud->cli_id);
                // session()->flash('success_img_rechazo', 'Esta solicitud fue rechazado correctamente | se envio al correo del usuario ' . $user->name . " el estado de la solicitud");
                // return response()->json([
                //     'message' => 'operacion exitosa',
                //     'error' => '',
                //     'success' => true,
                //     'data' => '',
                // ]);
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

    //aprobar solicitud
    public function send_solicitud_aprobada(Request $request)
    {
        try {
            $Params = $request->all();
            // // dd(route("solicitud.show", $request->input('solicitud')));
            $solicitud =  Solicitud::findOrFail(Encryptor::decrypt($request->input('solicitud')));
            $solicitud->status = "A"; //A significa Aceptado

            if ($solicitud->save()) {
                $user =  User::find($solicitud->created_user);

                // enviar correo a la cola de trabajo
                // enviar_mail_aprobada_solicitud::dispatch(
                //     $user,
                //     route("solicitud.show", $request->input('solicitud')),
                //     $solicitud->cli_id,
                //     $solicitud->serie
                // );
                session()->flash('success_img_aprobada', 'Esta solicitud fue aprobada con exito correctamente | se envio al correo del usuario ' . $user->name . " el estado de la solicitud");
                return response()->json([
                    'message' => 'operacion exitosa',
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


    // procesar la solicitud para generar un contrato
    public function send_procesar_solicitud(Request $request)
    {
        try {
            // Obtener todos los parámetros de la solicitud
            $Params = $request->all();

            // Buscar la solicitud en la base de datos
            $solicitud =  Solicitud::with("prestamo", "cliente")->findOrFail(Encryptor::decrypt($request->input('solicitud')));
            // Actualizar el estado de la solicitud a "G" (Aceptado)
            $solicitud->status = "G";

            $prestamo = Prestamo::where("solicitud_id", $solicitud->solicitud_id)->first();
            $prestamo->status = "A";

            if (!$solicitud->save()) {
                return response()->json([
                    'message' => 'error al procesar esta solicitud',
                    'error' => '',
                    'success' => false,
                    'data' => '',
                ]);
            }
            $prestamo->save();

            $this->procesarAmpliacionSiAplica($solicitud);

            $created_cronograma = $this->insertarCronograma($solicitud->prestamo->prestamo_id, $Params["cronograma"]);

            // Si la inserción del cronograma tiene éxito, devolver un mensaje de éxito
            if ($created_cronograma) {
                session()->flash(
                    'success_img_aprobada',
                    'Solicitud procesada correctamente = Podras imprimir los siguientes documentos : Contrato, conograma y solicitud'
                );
                return response()->json([
                    'message' => 'Solicitud procesado correctamente puedes imprimir los documentos',
                    'error' => '',
                    'success' => true,
                    'data' => ''
                ]);
            } else {
                $solicitud->status = "A";
                $solicitud->save();
                return response()->json([
                    'message' => 'error al procesar esta solicitud',
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
    // imprimir solicitud
    public function imprimir_solicitud($id)
    {
        $solicitud = Solicitud::with([
            "cliente" => function ($query) {
                $query->with(["ContactosCliente"]);
            },
            "analista",
            "prestamo" => function ($query) {
                $query->with(["cronograma"]);
            }
        ])->find(Encryptor::decrypt($id));

        $contactosArray = array_column($solicitud->cliente->ContactosCliente->toArray(), 'ccliente_contacto');

        // Ordena el array para asegurar que los números estén en el orden deseado
        sort($contactosArray);

        // Une los elementos del array en un string separados por '/'
        $contactosString = implode('/', $contactosArray);

        $marca_agua = Blade::compileString(view(
            'modules.solicitud.marca_agua',
            ["code" => $solicitud->code]
        )->render());

        $fecha_actual = Carbon::parse($solicitud->updated_at);

        $frecuencia_pago_a = "";
        $frecuencia_pago_b = "";

        switch ($solicitud->prestamo->frecuencia_pagos) {
            case 'Mensual':
                $frecuencia_pago_a = "MESES";
                $frecuencia_pago_b = "MENSUALES";
                break;
            case 'Diario':
                $frecuencia_pago_a = "DIAS";
                $frecuencia_pago_b = "DIARIOS";
                break;
            case 'Semanal':
                $frecuencia_pago_a = "SEMANAS";
                $frecuencia_pago_b = "SEMANALES";
                break;
            case 'Quincenal':
                $frecuencia_pago_a = "QUINCENAS";
                $frecuencia_pago_b = "QUINCENALES";
                break;
        }

        $solicitud_documento = Blade::compileString(view(
            'modules.solicitud.solicitud_documento',
            [
                // variables documentos
                'solicitud_nombre' => $solicitud->solicitud_nombre,
                'solicitud_documento' => $solicitud->solicitud_documento,
                'antiguedad' => $solicitud->solicitud_antiguedad,
                'giro_negocio' => $solicitud->solicitud_giro,
                'cel' => $contactosString,
                'lugar' => $solicitud->solicitud_lugar,
                'direccion_negocio' =>  $solicitud->solicitud_direccion_negocio,
                'referencia_negocio' => $solicitud->solicitud_referencia_negocio,
                'referencia_cliente' => $solicitud->solicitud_referencia_cliente,
                'direccion_domicilio' => $solicitud->solicitud_domicilio,
                'fecha' => $fecha_actual->format("d/m/Y"),
                'monto' => $solicitud->prestamo->moto_credito,
                'frecuencia_pago' =>  $frecuencia_pago_a,
                'intervalo' => $solicitud->prestamo->intervalo,
                'frecuencia_pago_a' => $frecuencia_pago_b,
                'cuotas' => $solicitud->prestamo->cuotas,
                'destino' =>  $solicitud->prestamo->destino,
                'analista' => $solicitud->analista->name . " " . $solicitud->analista->lastname,
                'nombre_conyugue' => $solicitud->solicitud_nombre_conyuge,
                'nombre_empresa' => 'HORIZON FINANCE EIRL',
                'direccion_empresa' => 'Jirón Bolognesi  N° 523 ',
                'nombre_conyugue_empresa' => 'Olga Panduro Pinedo ',
                'documento_empresa' => '20608330284',
                'documento_conyugue_empresa' => '80210814',
                'lugar_empresa' =>  $solicitud->solicitud_lugar,
                'dni_conyugue' => $solicitud->solicitud_conyugue_dni,
                'ruc_conyugue' => $solicitud->solicitud_conyugue_ruc,
                'dia' => $fecha_actual->format("d"),
                'mes' => $fecha_actual->format("m"),
                'ano' => $fecha_actual->format("Y"),
                'tipo_solicitud' =>  $solicitud->tipo_solicitud,
                'tipo_vivienda' => $solicitud->tipo_vivienda,
                "serie" => $solicitud->code,
            ]
        )->render());

        $formatter = new NumeroALetras();
        $montoDeletreado = $formatter->toInvoice($solicitud->prestamo->moto_credito, 2, 'soles');

        $pdf = Pdf::loadView(
            'pdf.documentos_prestamo',
            [
                'marca_agua' => $marca_agua,
                'solicitud_documento' => $solicitud_documento,
                // variables documento
                'nombre' =>  $solicitud->cliente->cli_nombre . " " . $solicitud->cliente->cli_apellido,
                'documento' =>  $solicitud->solicitud_documento,
                'direccion' => $solicitud->solicitud_domicilio,
                'distrito' => $solicitud->cliente->cli_distrito,
                'provincia' => $solicitud->cliente->cli_provincia,
                'departamento' => $solicitud->cliente->cli_departamento,
                'monto_credito' => $solicitud->prestamo->moto_credito,
                'monto_credito_deletreado' => $montoDeletreado,
                'dia' => $fecha_actual->format("d"),
                'mes' => $fecha_actual->format("m"),
                'ano' => $fecha_actual->format("Y"),
                'nombre2' => $solicitud->cliente->cli_nombre . " " . $solicitud->cliente->cli_apellido,
                'interes_deletreado' => strtolower($formatter->toWords($solicitud->prestamo->interes)),
                'interes' => $solicitud->prestamo->interes,
                'ocupacion' => $solicitud->solicitud_giro,
                'solicitud' => $solicitud,
                "solicitud_tarjeta" => ($solicitud->solicitud_tarjeta == "" || $solicitud->solicitud_tarjeta == null) ?  "……………………………………………………" : $solicitud->solicitud_tarjeta,
                "solicitud_entidad_tarjeta" => ($solicitud->solicitud_entidad_tarjeta == "" || $solicitud->solicitud_entidad_tarjeta == null) ?  "" : "perteneciente a la entidad bancaria " . $solicitud->solicitud_entidad_tarjeta
            ]
        );

        return $pdf->stream('invoice.pdf');
    }

    // guardar documentos  prestamos
    public function guardar_documento(Request $request)
    {
        try {
            $Params = $request->all();

            $decodedData = base64_decode($Params['pdf_code']);
            $filename = 'doc_' . $Params["urlapi"] . '_' . Carbon::now()->format('Ymdhis') . '.pdf'; // Set the desired filename here
            $path = 'documentos/' . $filename;

            $add = Storage::disk('local')->put('public/' . $path, $decodedData);

            // $ilovepdf = new Ilovepdf('project_public_869e28a9f0fea7c47f3095e9d6bd505b_BM5Q7ac058200a6376f6fe3a6cb71a5adaf1d', 'secret_key_c6f7b5769f7c864b7fc84337b51d3d47_8fkDFbfcc7f769ec9ea37e4ad2e5bac6f1480');

            // $myTaskCompress = $ilovepdf->newTask('compress');
            // $myTaskCompress->addFile('storage/documentos/'.$filename);
            // // Execute the task
            // $myTaskCompress->execute();
            // $myTaskCompress->download('storage/documentos');

            $guardar_documento = new guardar_documento();
            $guardar_documento->solicitud_id = Encryptor::decrypt($Params["urlapi"]);
            $guardar_documento->url_destino =  'storage/documentos/' . $filename;
            $guardar_documento->size =  strlen($decodedData);
            $guardar_documento->sucursal_id =  Auth::user()->sucursal_id;
            $guardar_documento->created_user =  Auth::user()->id;
            $guardar_documento->descripcion = $Params["descripcion"];

            if ($add && $guardar_documento->save()) {
                $get_archivo =  guardar_documento::with(["sucursal", "usuario"])->find($guardar_documento->guardar_documento_id);
                return response()->json([
                    'message' => 'se guardo correctamente este documento',
                    'error' => '',
                    'success' => true,
                    'data' =>  $get_archivo,
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

    // editar documento exitente
    public function edit_documento(Request $request)
    {
        try {
            $Params = $request->all();

            $guardar_documento = guardar_documento::find(Encryptor::decrypt($Params["urlapi"]));

            $decodedData = base64_decode($Params['pdf_code']);
            $filename = $guardar_documento->url_destino; // Set the desired filename here
            $path = 'documentos/' . $filename;


            $remplace_filename = "public/" . str_replace("storage/", "", $filename);

            if (Storage::disk('local')->exists($remplace_filename)) {
                Storage::disk('local')->delete($remplace_filename);
            } else {
                return response()->json([
                    'message' => 'el archivo no existe',
                    'error' => '',
                    'success' => false,
                    'data' => '',
                ]);
            }

            $add = Storage::disk('local')->put($remplace_filename, $decodedData);

            $guardar_documento->size =  strlen($decodedData);
            $guardar_documento->sucursal_id =  Auth::user()->sucursal_id;
            $guardar_documento->created_user =  Auth::user()->id;
            $guardar_documento->descripcion = $Params["descripcion"];

            if ($add && $guardar_documento->save()) {

                $get_archivo =  guardar_documento::with(["sucursal", "usuario"])->find(Encryptor::decrypt($Params["urlapi"]));

                return response()->json([
                    'message' => 'se actualizo correctamente este documento',
                    'error' => '',
                    'success' => true,
                    'data' =>  $get_archivo,
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

    //editar documentos existentes
    public function guardar_edicion_solicitud(Request $request)
    {
        try {
            $Params = $request->all();
            $solicitud = Solicitud::find(Encryptor::decrypt($Params['urlapi']));

            $prestamo = Prestamo::find($solicitud->prestamo_id);

            $prestamo_get = $Params['prestamo'];

            $prestamo->moto_credito = $prestamo_get['moto_credito'];
            $prestamo->interes = $prestamo_get['interes'];
            $prestamo->frecuencia_pagos = $prestamo_get['frecuencia_pagos'];
            $prestamo->intervalo = $prestamo_get['intervalo'];
            $prestamo->cuotas = $prestamo_get['cuotas'];
            $prestamo->tasa_diaria = $prestamo_get['tasa_diaria'];
            $prestamo->d_t = $prestamo_get['d_t'];


            if ($prestamo->save()) {
                return response()->json([
                    'message' => '',
                    'error' => '',
                    'success' => true,
                    'data' => '/solicitud/' . $Params['urlapi'],
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

    private function procesarAmpliacionSiAplica(Solicitud $solicitud): void
    {
        if ($solicitud->tipo_solicitud == "Ampliacion" && $solicitud->solicitud_id_relacionado) {
            $solicitud_anterior = Solicitud::with("prestamo")->findOrFail($solicitud->solicitud_id_relacionado);
            $prestamo_anterior = Prestamo::find($solicitud_anterior->prestamo_id);
            if ($prestamo_anterior) {
                $prestamo_anterior->status = "N";
                $prestamo_anterior->save();
            }
        }
    }

    private function insertarCronograma(int $prestamoId, array $cronogramaParams): bool
    {
        $cronogramasData = [];
        $index = 0;

        foreach ($cronogramaParams as $cronograma) {
            $fecha = $cronograma["fechaVencimiento"];
            $fecha_objeto = DateTime::createFromFormat('d/n/Y', $fecha);

            if (!$fecha_objeto) {
                $fecha_objeto = DateTime::createFromFormat('d/m/Y', $fecha);
            }

            if (!$fecha_objeto) {
                return false;
            }

            $cronogramasData[] = [
                'amortizacion' => $cronograma["amortizacion"],
                'cuota' => $cronograma["cuota"],
                'fechaVencimiento' => $fecha_objeto->format('Y-m-d'),
                'interes' => $cronograma["interes"],
                'periodo' => $cronograma["periodo"],
                'saldoCapital' => $cronograma["saldoCapital"],
                'prestamo_id' => $prestamoId,
                'status' => $index === 0 ? "P" : "N",
            ];
            $index++;
        }

        return cronograma::insert($cronogramasData);
    }

    public function eliminar_solicitud(Request $request)
    {
        try {
            $solicitudId = Encryptor::decrypt($request->input('urlapi'));
            $solicitud = Solicitud::find($solicitudId);

            if (!$solicitud) {
                return response()->json([
                    'message' => 'Solicitud no encontrada',
                    'success' => false,
                    'data' => '',
                ]);
            }

            $prestamo = Prestamo::where('solicitud_id', $solicitudId)->first();

            if ($prestamo) {
                if (ingreso::where('prestamo_id', $prestamo->prestamo_id)->exists()) {
                    return response()->json([
                        'message' => 'No se puede eliminar: el préstamo tiene pagos registrados',
                        'success' => false,
                        'data' => '',
                    ]);
                }

                cronograma::where('prestamo_id', $prestamo->prestamo_id)->delete();
                $prestamo->delete();
            }

            if (gastos::where('solicitud_id', $solicitudId)->exists()) {
                return response()->json([
                    'message' => 'No se puede eliminar: tiene gastos de desembolso asociados',
                    'success' => false,
                    'data' => '',
                ]);
            }

            guardar_documento::where('solicitud_id', $solicitudId)->delete();
            $solicitud->delete();

            return response()->json([
                'message' => 'Solicitud eliminada correctamente',
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
