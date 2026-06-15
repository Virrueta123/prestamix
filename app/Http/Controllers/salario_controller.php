<?php

namespace App\Http\Controllers;

use App\Helpers\Encryptor;
use App\Jobs\enviar_mail_notificacion_gerente;
use App\Jobs\enviar_mail_solicitud;
use App\Models\caja;
use App\Models\gastos;
use App\Models\pagos;
use App\Models\salario;
use App\Models\solicitud_trabajador;
use App\Models\User;
use App\Utils\funciones;
use App\Utils\ticketera;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class salario_controller extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
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
    public function crear_salario($id)
    {
        $user = User::find(Encryptor::decrypt($id));

        return view('modules.salario.create', [
            'user' => $user
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $Params = $request->all();
            $salario = new salario();
            $salario->monto = $Params['monto'];
            $salario->fecha_inicio = Carbon::parse($Params['fecha_inicio'])->format('Y-m-d');
            $salario->fecha_final = Carbon::parse($Params['fecha_final'])->format('Y-m-d');
            $salario->created_user = Auth::user()->id;
            $salario->sucursal_id =  Auth::user()->sucursal_id;
            $salario->trabajador_id = Encryptor::decrypt($Params['urlapi']);

            if ($salario->save()) {
                session()->flash('success', 'Registrar salario');
                return response()->json([
                    'message' => 'Registrar salario',
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function tabla_solicitud_show($id)
    {
        // $query = solicitud_trabajador::with([
        //     "usuario",
        //     "gasto",
        //     "salario" => function ($query) {
        //         $query->with(["trabajador"]);
        //     }
        // ])->where("sucursal_id", Auth::user()->sucursal_id)->where("status", "G")->where("salario_id", 10);
        // dd($query->get());
        $solicitud_trabajador = solicitud_trabajador::with([
            "usuario", "sucursal",
            "salario" => function ($query) {
                $query->with(["trabajador"]);
            }
        ])->find(Encryptor::decrypt($id));
        return view("modules.salario.show_solicitud_trabajador", compact("solicitud_trabajador"));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        try {

            $salario = salario::with([
                "trabajador",
                "usuario"
            ])->find(Encryptor::decrypt($id));

            return view("modules.salario.show", compact("salario"));
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return view("errors.404");
        }
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

    // crear solicitud 

    public function crear_solicitud_plantilla()
    {
        return view('modules.salario.crear_solicitud_plantilla');
    }

    public function load_salario()
    {
        try {

            $salario = salario::with(["usuario", "trabajador", "sucursal"])->where('sucursal_id', Auth::user()->sucursal_id)->where("status", "P")->get();

            if ($salario) {
                return response()->json([
                    'message' => 'datos cargados correctamente',
                    'error' => '',
                    'success' => true,
                    'data' => $salario,
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

    public function get_salario(Request $request)
    {
        try {
            $Datax = $request->all();

            $salario = salario::with(["usuario", "trabajador", "sucursal"])->where('salario_id',  Encryptor::decrypt($Datax["salario_id"]))->first();

            if ($salario) {
                return response()->json([
                    'message' => 'salarios cargados correctamente',
                    'error' => '',
                    'success' => true,
                    'data' => $salario,
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

    // funcion para pagar el sueldo restante de un trabajador
    public function pagar_salario(Request $request)
    { 
        try {
            $Params = $request->all();

            $solicitud_trabajador = new solicitud_trabajador();
            $solicitud_trabajador->tipo = "S";
            $solicitud_trabajador->monto = $Params["monto"];
            $solicitud_trabajador->sucursal_id =  Auth::user()->sucursal_id;
            $solicitud_trabajador->salario_id = Encryptor::decrypt($Params['salario_id']);
            $solicitud_trabajador->created_user = Auth::user()->id;

            $solicitud_trabajador_get = salario::with(["trabajador"])->where('salario_id', Encryptor::decrypt($Params['salario_id']))->first();
 
            $solicitud_trabajador->descripcion = "Pago sueldo restante de " . $solicitud_trabajador_get->trabajador->name . " " . $solicitud_trabajador_get->trabajador->lastname;
  
            $salario = salario::find(Encryptor::decrypt($Params['salario_id']));
            $salario->status = "C";
            $salario->save();

            if ($solicitud_trabajador->save()) {

                $gasto = new gastos();
                $gasto->gastos_descripcion = "Pago sueldo restante de " . $solicitud_trabajador->salario->trabajador->name . " " . $solicitud_trabajador_get->trabajador->lastname;
                $gasto->monto = $solicitud_trabajador->monto;
                $gasto->tipo_gasto_id = 6;
                $gasto->created_user = auth()->user()->id;
                $gasto->sucursal_id = auth()->user()->sucursal_id;
                $gasto->caja_chica_id = caja::where("created_user", auth()->user()->id)->where("status", "A")->first()->caja_chica_id;
                $gasto->codigo = "Ga" . funciones::generar_codigo($solicitud_trabajador->monto);
                $gasto->solicitud_trabajador_id = $solicitud_trabajador->solicitud_trabajador_id;

                $solicitud_trabajador->status = "G";
                $solicitud_trabajador->save();
                $gasto->save();

                $show_gasto = gastos::with(["tipo_gasto"])->find($gasto->gastos_id);
 
                ticketera::imprimir_gasto(
                    $show_gasto->gastos_descripcion,
                    $show_gasto->tipo_gasto->nombre,
                    $show_gasto->codigo,
                    $show_gasto->usuario->name,
                    $show_gasto->monto
                );
                
                $pagos = new pagos();
                $pagos->gastos_id = $show_gasto->gastos_id;
                $pagos->monto = $show_gasto->monto;
                $pagos->cuentas_id = 1;
                $pagos->tipo = "G";
                $pagos->caja_chica_id = caja::where("created_user", auth()->user()->id)->where("status", "A")->first()->caja_chica_id;
                $pagos->created_user  = auth()->user()->id;
                $pagos->sucursal_id  = auth()->user()->sucursal_id;
                $pagos->save();

                return response()->json([
                    'message' => 'Pago de sueldo registado con exito',
                    'error' => '',
                    'success' => true,
                    'data' => "",
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

    public function solicitud_trabajador(Request $request)
    {
        try {
            $Params = $request->all();

            DB::beginTransaction();
 
 
            $solicitud_trabajador = new solicitud_trabajador();
            $solicitud_trabajador->tipo = $Params["tipo"];
            $solicitud_trabajador->monto = $Params["monto"];
            $solicitud_trabajador->sucursal_id =  Auth::user()->sucursal_id;
            $solicitud_trabajador->salario_id = Encryptor::decrypt($Params['salario_id']);
            $solicitud_trabajador->created_user = Auth::user()->id;
            $solicitud_trabajador->tipo = $Params["tipo"];
            $solicitud_trabajador->descripcion = $Params["descripcion"];

            if ($solicitud_trabajador->save()) {
                $salario = salario::find($solicitud_trabajador->salario_id);
                $user = User::find($salario->trabajador_id);

                $solicitud = $Params["tipo"] == "A" ? "Adelanto" : "Descuento";
                $ruta = route('aprobar_solicitud_trabajador', Encryptor::encrypt($solicitud_trabajador->solicitud_trabajador_id));
                //enviar a la cola de trabajo
                // enviar_mail_notificacion_gerente::dispatch(
                //     $ruta,
                //     Auth::user()->name . " esta solicitado un " . $solicitud . " de " . $user->name
                // );


                // si el tipo de usuario es gerente
                if(Auth::user()->rol == "gerente"){

                $solicitud_trabajador = solicitud_trabajador::with([
                                "usuario",
                                "salario" => function ($query) {
                                    $query->with(["trabajador"]);
                                }
                            ])->find($solicitud_trabajador->solicitud_trabajador_id);

                            $caja_chica = caja::where("created_user", 1)->where("status", "A")->first()->caja_chica_id;
 
                            $gasto = new gastos();
                            $gasto->gastos_descripcion = "Pago adelanto de " . $solicitud_trabajador->salario->trabajador->name;
                            $gasto->monto = $solicitud_trabajador->monto;
                            $gasto->tipo_gasto_id = 6;
                            $gasto->created_user = 1;
                            $gasto->sucursal_id = auth()->user()->sucursal_id;
                            $gasto->caja_chica_id = $caja_chica;
                            $gasto->codigo = "Ga" . funciones::generar_codigo($solicitud_trabajador->monto);
                            $gasto->solicitud_trabajador_id = $solicitud_trabajador->solicitud_trabajador_id;

                            $solicitud_trabajador->status = "G";
                            $solicitud_trabajador->save();
 
                            if ($gasto->save()) {
                                $show_gasto = gastos::with(["tipo_gasto"])->find($gasto->gastos_id);
                
                                ticketera::imprimir_gasto(
                                    $show_gasto->gastos_descripcion,
                                    $show_gasto->tipo_gasto->nombre,
                                    $show_gasto->codigo,
                                    $show_gasto->usuario->name,
                                    $show_gasto->monto
                                );

                                $pagos = new pagos();
                                $pagos->gastos_id = $show_gasto->gastos_id;
                                $pagos->monto = $show_gasto->monto;
                                $pagos->cuentas_id = 1;
                                $pagos->tipo = "G";
                                $pagos->caja_chica_id = $caja_chica;
                                $pagos->created_user  = 1;
                                $pagos->sucursal_id  = auth()->user()->sucursal_id;
                                $pagos->save();

                                DB::commit();
                                session()->flash('success', 'Adelanto registado con exito');
                                return response()->json([
                                    'message' => 'Adelanto registado con exito',
                                    'error' => '',
                                    'success' => true,
                                    'data' => route("gastos.index"),
                                ]);

                               
                            } else {
                                DB::rollBack();
                                return response()->json([
                                    'message' => '',
                                    'error' => '',
                                    'success' => false,
                                    'data' => '',
                                ]);
                            }

                }

              DB::commit();
               
                return response()->json([
                    'message' => 'salario cargado correctamente',
                    'error' => '',
                    'success' => true,
                    'data' => "",
                ]);
            } else {
                  DB::rollBack();
                return response()->json([
                    'message' => '',
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

    // aprobar solicitud trabajador
    public function aprobar_solicitud_trabajador($id)
    {
        $solicitud_trabajador = solicitud_trabajador::with([
            "usuario",
            "sucursal",
            "salario" => function ($query) {
                $query->with("trabajador");
            }
        ])->find(Encryptor::decrypt($id));

        return view('modules.salario.aprobar_solicitud_trabajador', compact("solicitud_trabajador"));
    }

    //   tabla_solicitud
    public function tabla_solicitud()
    {
        return view('modules.salario.tabla_solicitud_trabajadores');
    }

    public function actualizar_estado_solicitud_trabajador(Request $request)
    {
        try {
            $Params = $request->all();

            $solicitud_trabajador = solicitud_trabajador::find(Encryptor::decrypt($Params["urlapi"]));

            $solicitud_trabajador->status = $Params["estado"];
            if ($solicitud_trabajador->save()) {
                session()->flash('success',  $Params["estado"] == 'A' ? 'La solicitud ha sido aprobado' : 'La solicitud ha sido rechazada');
                return response()->json([
                    'message' => $Params["estado"] == 'A' ? 'La solicitud ha sido aprobado' : 'La solicitud ha sido rechazada',
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
}
