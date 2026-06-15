<?php

namespace App\Http\Controllers;

use App\Helpers\Encryptor;
use App\Models\caja;
use App\Models\gastos;
use App\Models\pagos;
use App\Models\Solicitud;
use App\Models\solicitud_trabajador;
use App\Models\tipo_gasto;
use App\Utils\funciones;
use App\Utils\ticketera;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class gastos_controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('modules.gastos.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('modules.gastos.create');
    }

    public function actualizar_pago(Request $request){
        try {
            $Params = $request->all();
  
            $gasto = $Params["get_gasto"];
              
            $gasto = gastos::find(Encryptor::decrypt($gasto["urlapi"]));
            $gasto->monto = $Params["monto"];
            $gasto->save();
 
            $pagos = pagos::where('gastos_id', Encryptor::decrypt($gasto["urlapi"]))->get();
              
            foreach ($Params["pagos"] as $pago) {
             
                if(isset($pago["urlapi"])){
                  
                    $pag = pagos::where("pagos_id",Encryptor::decrypt($pago["urlapi"]))->first(); 
                    $pag->cuentas_id = Encryptor::decrypt($pago["cuentas_id"]);
                    $pag->monto = $pago["monto"];
                    $pag->save(); 
                    
                    
                }else{ 
                   
                    $pag = new pagos();
                    $pag->gastos_id = Encryptor::decrypt($gasto["urlapi"]);
                    $pag->monto = $pago["monto"];
                    $pag->cuentas_id = Encryptor::decrypt($pago["cuentas_id"]);
                    $pag->tipo = "G";
                    $pag->caja_chica_id = $gasto["caja_chica_id"];
                    $pag->created_user  = auth()->user()->id;
                    $pag->sucursal_id  = auth()->user()->sucursal_id;
                    $pag->save(); 
                } 
             
            }

            
            if(!empty($Params["delete_array"])){
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
 
            $gasto = new gastos();
            $gasto->gastos_descripcion = $Params["gastos_descripcion"];
            $gasto->monto = $Params["monto"];
            
            $gasto->created_user = auth()->user()->id;
            $gasto->sucursal_id = auth()->user()->sucursal_id;
            $gasto->caja_chica_id = caja::where("created_user", auth()->user()->id)->where("status", "A")->first()->caja_chica_id;
            $gasto->codigo = "Ga" . funciones::generar_codigo($Params["monto"]);

            if( $Params["yes_cliente"] ){
                $gasto->tipo_gasto = "G";
                $devolucion = tipo_gasto::where("yes_devolucion", "Y")->first();
                $gasto->tipo_gasto_id = $devolucion->tipo_gasto_id;
            }else{
                $gasto->tipo_gasto_id = Encryptor::decrypt($Params["tipo_gasto_id"]);
            }

            if (!is_null($Params["analista"])) {
                $gasto->analista_id = Encryptor::decrypt($Params["analista"]);
            }
 
            if ($gasto->save()) {
                
                send_email_controller::send_email_ingreso_creado(
                    Auth::user()->name . " " . Auth::user()->last_name . " genero un gasto de S./" . $gasto->monto . " con descripcion :" . 
                    $gasto->gastos_descripcion . " con codigo: " . $gasto->codigo, 
                    "Gasto generado", 
                );

                $show_pasto = gastos::with(["tipo_gasto"])->find($gasto->gastos_id);
  
                ticketera::imprimir_gasto(
                    $show_pasto->gastos_descripcion,
                    $show_pasto->tipo_gasto->nombre,
                    $show_pasto->codigo,
                    $show_pasto->usuario->name,
                    $show_pasto->monto
                );

                foreach ($Params["pagos"] as $pago) {
                    $pagos = new pagos();
                    $pagos->gastos_id = $gasto->gastos_id;
                    $pagos->monto = $pago["monto"];
                    $pagos->cuentas_id = Encryptor::decrypt($pago["cuentas_id"]);
                    $pagos->tipo = "G";
                    $pagos->caja_chica_id = caja::where("created_user", auth()->user()->id)->where("status", "A")->first()->caja_chica_id;
                    $pagos->created_user  = auth()->user()->id;
                    $pagos->sucursal_id  = auth()->user()->sucursal_id;
                    $pagos->save();
                }
                session()->flash('success', 'Gasto registado con exito');
                return response()->json([
                    'message' => 'Gasto registado con exito',
                    'error' => '',
                    'success' => true,
                    'data' => route("gastos.index"),
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
    public function show($id)
    {
        //
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
    public function gasto_edit(Request $request)
    {
        try {
            $Params = $request->all();

            $gasto = gastos::find(Encryptor::decrypt($Params['urlapi']));
            $gasto->gastos_descripcion = $Params["gastos_descripcion_edit"];
            $gasto->monto = $Params["monto_edit"];
            $gasto->tipo_gasto_id = Encryptor::decrypt($Params["tipo_gasto_id"]);

            if ($gasto->save()) {

                $query = gastos::with([
                    "usuario",
                    "tipo_gasto",
                    "sucursal"
                ])->find($gasto->gastos_id);

                return response()->json([
                    'message' => 'Gasto registrado correctamente',
                    'error' => '',
                    'success' => true,
                    'data' => $query,
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function generar_gasto_adelanto_trabajador(Request $request)
    {
        try {
            $Params = $request->all();

            $solicitud_trabajador = solicitud_trabajador::with([
                "usuario",
                "salario" => function ($query) {
                    $query->with(["trabajador"]);
                }
            ])->find(Encryptor::decrypt($Params['urlapi']));
 
            $gasto = new gastos();
            $gasto->gastos_descripcion = "Pago adelanto de " . $solicitud_trabajador->salario->trabajador->name;
            $gasto->monto = $solicitud_trabajador->monto;
            $gasto->tipo_gasto_id = 6;
            $gasto->created_user = auth()->user()->id;
            $gasto->sucursal_id = auth()->user()->sucursal_id;
            $gasto->caja_chica_id = caja::where("created_user", auth()->user()->id)->where("status", "A")->first()->caja_chica_id;
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
                $pagos->caja_chica_id = caja::where("created_user", auth()->user()->id)->where("status", "A")->first()->caja_chica_id;
                $pagos->created_user  = auth()->user()->id;
                $pagos->sucursal_id  = auth()->user()->sucursal_id;
                $pagos->save();


                session()->flash('success', 'Adelanto registado con exito');
                return response()->json([
                    'message' => 'Adelanto registado con exito',
                    'error' => '',
                    'success' => true,
                    'data' => route("gastos.index"),
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

    public function delete_gasto(Request $request)
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

            $gasto = gastos::with([
                "solicitud" 
                ])->find(Encryptor::decrypt($Params['urlapi']));
 
            $pago = pagos::where("gastos_id", Encryptor::decrypt($Params['urlapi']));
            $pago->delete();

            if( $gasto->tipo_gasto_id == 3 ){
                $solicitud = solicitud::find($gasto->solicitud_id);
                $solicitud->status = "A";
                $solicitud->save();
            }
  
            gastos::where("gastos_id", Encryptor::decrypt($Params['urlapi']))->delete();
  
            if (true) {
                return response()->json([
                    'message' => 'el gasto se elimino correctamente',
                    'error' => '',
                    'success' => true,
                    'data' => '',
                ]);
            } else {
                return response()->json([
                    'message' => 'error al eliminar el gasto',
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

    public function print_voucher_gasto(Request $request)
    {
        try {
            $Params = $request->all();
            $gasto = gastos::with(["tipo_gasto", "usuario"])->find(Encryptor::decrypt($Params['urlapi']));
            
            if ($gasto->tipo_gasto->tipo_gasto_id == 3) {

                $solicitud = Solicitud::with(["prestamo", "cliente"])->find($gasto->solicitud_id);

                ticketera::imprimir_gasto_duplicado(
                    Carbon::parse($gasto->created_at)->format('Y/m/d h:ia'),
                    "Cliente: " . $solicitud->cliente->cli_nombre . " " . $solicitud->cliente->cli_apellido . " \n **" . $gasto->gastos_descripcion,
                    $gasto->tipo_gasto->nombre,
                    $gasto->codigo,
                    $gasto->usuario->name,
                    $gasto->monto
                );

                ticketera::imprimir_desembolso(
                    $gasto->gastos_descripcion,
                    $gasto->tipo_gasto->nombre,
                    $gasto->codigo,
                    $gasto->usuario->name,
                    $gasto->monto,
                    "si",
                    $solicitud->cliente->cli_nombre . " " . $solicitud->cliente->cli_apellido,
                    "recepcionista"
                );
    
                ticketera::imprimir_desembolso(
                    $gasto->gastos_descripcion,
                    $gasto->tipo_gasto->nombre,
                    $gasto->codigo,
                    $gasto->usuario->name,
                    $gasto->monto,
                    "no",
                    $solicitud->cliente->cli_nombre . " " . $solicitud->cliente->cli_apellido,
                    "recepcionista"
                );
    
                 ticketera::imprimir_desembolso(
                    $gasto->gastos_descripcion,
                    $gasto->tipo_gasto->nombre,
                    $gasto->codigo,
                    $gasto->usuario->name,
                    $gasto->monto,
                    "si",
                    $solicitud->cliente->cli_nombre . " " . $solicitud->cliente->cli_apellido,
                    "cliente"
                ); 



            } else {

                ticketera::imprimir_gasto_duplicado(
                    Carbon::parse($gasto->created_at)->format('Y/m/d h:ia'),
                    $gasto->gastos_descripcion,
                    $gasto->tipo_gasto->nombre,
                    $gasto->codigo,
                    $gasto->usuario->name,
                    $gasto->monto
                );
                
            }
 
            if ($gasto) {
                
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
}
