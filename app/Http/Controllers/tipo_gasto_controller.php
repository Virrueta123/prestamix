<?php

namespace App\Http\Controllers;

use App\Helpers\Encryptor;
use App\Models\tipo_gasto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class tipo_gasto_controller extends Controller
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
        return view('modules.tipo_gasto.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('modules.tipo_gasto.create');
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
            $tipo_gasto = new tipo_gasto();
            $tipo_gasto->nombre = $Params['nombre'];
            $tipo_gasto->descripcion = $Params['descripcion'];
            $tipo_gasto->created_user = Auth::user()->id;

            if ($tipo_gasto->save()) {
                return response()->json([
                    'message' => 'Tipo de gasto creado correctamente',
                    'error' => '',
                    'success' => true,
                    'data' => route("tipo_gasto.index"),
                ]);
            } else {
                return response()->json([
                    'message' => 'error al registrar Tipo de gasto',
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

    // cargar todo los tipos de gastos
    public function load_tipo_de_gato(){
        try {

            $tipo_gasto = tipo_gasto::where("status","A")->whereIn("yes_default",["N","P"])->get();

            return response()->json([
                'message' => 'tipo de gasto cargado exitosamente',
                'error' => '',
                'success' => true,
                'data' => $tipo_gasto,
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

    // editar un tipo de gasto
    public function tipo_gasto_edit(Request $request){
        try {
            $Params = $request->all();
            
           


            $tipo_gasto = tipo_gasto::find( Encryptor::decrypt($Params['urlapi']) );

     
            $tipo_gasto->nombre = $Params['nombre_edit'];
            $tipo_gasto->descripcion = $Params['descripcion_edit'];
            $tipo_gasto->created_user = Auth::user()->id;
            $tipo_gasto->status = $Params['status'] ? "A" : "D";

            if ($tipo_gasto->save()) {
                $query = tipo_gasto::with([
                    "usuario"
                ])->find($tipo_gasto->tipo_gasto_id);

                return response()->json([
                    'message' => 'Tipo de gasto creado correctamente',
                    'error' => '',
                    'success' => true,
                    'data' => $query,
                ]);
            } else {
                return response()->json([
                    'message' => 'error al registrar Tipo de gasto',
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

    public function get_tipo_gasto( Request $request ){
        try {
            $Params = $request->all();
             
            $tipo_gasto = tipo_gasto::find(Encryptor::decrypt($Params['tipo_gasto_id']));

            return response()->json([
                'message' => 'tipo de gasto cargado exitosamente',
                'error' => '',
                'success' => true,
                'data' => $tipo_gasto,
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
