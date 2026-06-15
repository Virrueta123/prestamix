<?php

namespace App\Http\Controllers;

use App\Helpers\Encryptor;
use App\Models\cuentas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class cuentas_controller extends Controller
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
        return view("modules.cuentas.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("modules.cuentas.create");
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

            $messages = [

                'cuentas_nombre.required' => 'Es obligatorio el campo nombre.',
                
                'cuentas_numero.required' => 'Es obligatorio el campo numero de cuenta.', 

                'cuentas_entidad.required' => 'Es obligatorio el campo entidad.',
 
            ];

            $validator = Validator::make($Params, [
                'cuentas_nombre' => 'required|string',
                'cuentas_numero' => 'required|numeric', 
                'cuentas_entidad' => 'required|string', 
            ], $messages);

            if ($validator->fails()) {
                // Retorna los errores en formato JSON 
                return response()->json([
                    'message' => 'Aun no se puede registrar por la siguientes observaciones',
                    'error' => 'validation',
                    'success' => false,
                    'data' => $validator->errors()->all(),
                ]);
            }
 
            $cuentas = new cuentas();

            // Establece los valores de los atributos del modelo
            $cuentas->cuentas_nombre = $request->input('cuentas_nombre');
            $cuentas->cuentas_numero = $request->input('cuentas_numero');
            $cuentas->cuentas_cci = $request->input('cuentas_cci');
            $cuentas->cuentas_entidad = $request->input('cuentas_entidad');
            $cuentas->created_user = Auth::user()->id;
            $cuentas->sucursal_id = Auth::user()->sucursal_id;

            if ($cuentas->save()) {
  
                session()->flash('success', 'Cuenta registrado correctamente');

                return response()->json([
                    'message' => 'cuenta registrado correctamente',
                    'error' => '',
                    'success' => true,
                    'data' => route("cuentas.index"),
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
    public function cuentas_edit(Request $request )
    {
        try {
            $Params = $request->all();

            $messages = [

                'cuentas_nombre_edit.required' => 'Es obligatorio el campo nombre.',
                
                'cuentas_numero_edit.required' => 'Es obligatorio el campo numero de cuenta.', 

                'cuentas_entidad_edit.required' => 'Es obligatorio el campo entidad.',
 
            ];

            $validator = Validator::make($Params, [
                'cuentas_nombre_edit' => 'required|string',
                'cuentas_numero_edit' => 'required|numeric', 
                'cuentas_entidad_edit' => 'required|string', 
            ], $messages);

            if ($validator->fails()) {
                // Retorna los errores en formato JSON 
                return response()->json([
                    'message' => 'Aun no se puede registrar por la siguientes observaciones',
                    'error' => 'validation',
                    'success' => false,
                    'data' => $validator->errors()->all(),
                ]);
            }
 
            $cuentas =  cuentas::find( Encryptor::decrypt($Params["urlapi"]) );

            // Establece los valores de los atributos del modelo
            $cuentas->cuentas_nombre = $request->input('cuentas_nombre_edit');
            $cuentas->cuentas_numero = $request->input('cuentas_numero_edit');
            $cuentas->cuentas_cci = $request->input('cuentas_cci_edit');
            $cuentas->cuentas_entidad = $request->input('cuentas_entidad_edit'); 

            if ($cuentas->save()) {

                $query = cuentas::with([
                    "usuario"
                ])->find($cuentas->cuentas_id);
   
                return response()->json([
                    'message' => 'Cuenta actualizado correctamente', 
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


   
}
