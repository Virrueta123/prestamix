<?php

namespace App\Http\Controllers;

use App\Helpers\Encryptor;
use App\Models\caja;
use App\Models\ingreso;
use App\Models\pagos;
use App\Utils\funciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ingresos_varios_controller extends Controller
{
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
    public function create()
    {
        return view("modules.ingresos_varios.create");
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
            $ingresos_varios = new ingreso();
            $ingresos_varios->descripcion = $Params['descripcion'];
            $ingresos_varios->cuentas_id = Encryptor::decrypt($Params['cuentas_id']);
            $ingresos_varios->monto = $Params['monto'];
            $ingresos_varios->created_user = Auth::user()->id;
            $ingresos_varios->sucursal_id = Auth::user()->sucursal_id;
            $ingresos_varios->yes_office = 'Y';
            $ingresos_varios->codigo = funciones::generar_codigo($Params["monto"]);

            if ($Params['yes_cliente']) {
                $ingresos_varios->cli_id = Encryptor::decrypt($Params['cli_id']);
                $ingresos_varios->tipo_ingreso = 'G';
            }else{
                $ingresos_varios->tipo_ingreso = 'A';
            }

            $ingresos_varios->caja_chica_id = caja::where("created_user", auth()->user()->id)->where("status", "A")->first()->caja_chica_id; 
            $ingresos_varios->save();
  
            $pagos = new pagos();
            $pagos->ingreso_id = $ingresos_varios->ingreso_id;
            $pagos->monto = $Params['monto'];
            $pagos->cuentas_id = 1;
            $pagos->tipo = "I";
            $pagos->caja_chica_id = caja::where("created_user", auth()->user()->id)->where("status", "A")->first()->caja_chica_id;
            $pagos->created_user  = auth()->user()->id;
            $pagos->sucursal_id  = auth()->user()->sucursal_id;
            $pagos->save();
 

            if ($pagos->save()) {

                return response()->json([
                    'message' => 'ingreso registrado correctamente',
                    'error' => '',
                    'success' => true,
                    'data' => '',
                ]);
            } else {
                return response()->json([
                    'message' => 'Error al registrar este grasto',
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
}
