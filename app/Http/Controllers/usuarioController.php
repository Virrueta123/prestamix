<?php

namespace App\Http\Controllers;

use App\Helpers\Encryptor;
use App\Models\ingreso;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class usuarioController extends Controller
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
        $users = User::with(["sucursal"])->get();

        return view("modules.users.index", compact("users"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("modules.users.create");
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

            $usuario = new User();
            $usuario->name = $Params["name"];
            $usuario->lastname = $Params["lastname"];
            $usuario->email = $Params["email"];
            $usuario->celular = $Params["celular"];
            $usuario->sexo = $Params["sexo"];
            $usuario->dni = $Params["dni"];
            $usuario->password =  Hash::make($Params["dni"]);
            $usuario->sucursal_id = Encryptor::decrypt($Params["sucursal_id"]);

            if ($usuario->save()) {
                $usuario->assignRole($Params["rol"]);

                session()->flash('success', 'Usuario registrado correctamente');

                return response()->json([
                    'message' => 'Usuario registrado correctamente',
                    'error' => '',
                    'success' => true,
                    'data' =>  $ruta = asset("/") . 'usuario',
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

    // funciones para axios

    public function load_usuario()
    {
        try {

            $usuario = User::with(["sucursal"])->find(Auth::user()->id);

            return response()->json([
                'message' => 'usuario cargado exitosamente',
                'error' => '',
                'success' => true,
                'data' => $usuario,
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

    public function load_analistas()
    {
        try {

            $analistas = User::whereHas('roles', function ($query) {
                $query->where('name', 'analista');
            })->get();

            return response()->json([
                'message' => 'analistas cargado exitosamente',
                'error' => '',
                'success' => true,
                'data' => $analistas,
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

    public function load_trabajadores()
    {
        try {
            $trabajadores = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['analista',"recepcionista"]);
            })->get();

            if ($trabajadores) {
                return response()->json([
                    'message' => 'Datos cargado exitosamente',
                    'error' => '',
                    'success' => true,
                    'data' => $trabajadores,
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

    // desactivar usuario
    public function desactivar_usuario(Request $request)
    {
        try {
            $Params = $request->all();

            $usuario = User::find(Encryptor::decrypt($Params['urlapi']));

            if ($usuario->status == "D") {
                $usuario->status = "A";
            } else {
                $usuario->status = "D";
            }

            if ($usuario->save()) {
                return response()->json([
                    'message' => 'Usuario desactivado correctamente',
                    'error' => '',
                    'success' => true,
                    'data' => $usuario->status,
                ]);
            } else {
                return response()->json([
                    'message' => 'no se puedo desactivar este usuario',
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

    // cargar datos para el reporte de analistas por caja
    public function report_analista_caja(Request $request)
    {
        try {
            $Params = $request->all();

            $caja_chica_id = Encryptor::decrypt($Params['urlapi']);

            $analistas = User::whereHas('roles', function ($query) {
                $query->where('name', 'analista');
            })->get();

            $response_analistas = [];
            $response_analistas_office = [];

            foreach ($analistas as $analista) {
                $amount = ingreso::with([
                    "prestamo" 
                ])
                    ->where("caja_chica_id", $caja_chica_id)
                    ->whereHas('prestamo', function ($query) use ($analista) {
                        $query->where('analista_id', $analista->id);
                    })->sum("monto");

                $response_analistas[] = array(
                    "asset" => $analista->name,
                    "amount" => floatval($amount),
                );
                // response_analistas_office
                $amount_office = ingreso::with([
                    "prestamo" 
                ])->whereHas('prestamo', function ($query) use ($analista) {
                    $query->where('analista_id', $analista->id);
                })->where("caja_chica_id", $caja_chica_id)->where("yes_office", "Y")->sum("monto");

                $amount_campo = ingreso::with([
                    "prestamo" 
                ])->where("caja_chica_id", $caja_chica_id)
                    ->whereHas('prestamo', function ($query) use ($analista) {
                        $query->where('analista_id', $analista->id);
                    })->where("yes_office", "N")->sum("monto");


                $response_analistas_office[] = array(
                    "asset" => $analista->name . " Campo",
                    "amount" => floatval($amount_campo),
                );

                $response_analistas_office[] = array(
                    "asset" => $analista->name . " Officina",
                    "amount" => floatval($amount_office),
                );
            }

            $data_response = array(
                "response_analistas" => $response_analistas,
                "response_analistas_office" => $response_analistas_office
            );


            if (true) {
                return response()->json([
                    'message' => '',
                    'error' => '',
                    'success' => true,
                    'data' =>  $data_response,
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

    // actualizar usuario
    public function edit_usuario(Request $request)
    {
        try {
            $Params = $request->all();


            $usuario = User::find(Encryptor::decrypt($Params['urlapi']));
            $usuario->name = $Params["name"];
            $usuario->lastname = $Params["lastname"];
            $usuario->email = $Params["email"];
            $usuario->celular = $Params["celular"];
            $usuario->sexo = $Params["sexo"];
            $usuario->dni = $Params["dni"];
            $usuario->sucursal_id = Encryptor::decrypt($Params["sucursal_id"]);

            if ($usuario->save()) {
                // Buscar el rol por su nombre
                $role = Role::where('name', $Params["rol"])->firstOrFail();

                // Actualizar el rol del usuario
                $usuario->roles()->sync([$role->id]);

                session()->flash('success', 'Usuario editado correctamente');

                return response()->json([
                    'message' => 'Usuario editado correctamente',
                    'error' => '',
                    'success' => true,
                    'data' =>  $ruta = asset("/") . 'usuario',
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

    // blade para crear una adelanto
    public function crear_adelanto(Request $request)
    {
        return view('modules.users.crear_adelanto');
    }

    // cargar todo los recepcionista
    public function load_recepcionista(){
        try {

            $recepcionista = User::whereHas('roles', function ($query) {
                $query->where('name', 'recepcionista');
            })->get();

            return response()->json([
                'message' => 'recepcionistas cargado exitosamente',
                'error' => '',
                'success' => true,
                'data' => $recepcionista,
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
