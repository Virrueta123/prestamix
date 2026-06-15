<?php

namespace App\Http\Controllers;

use App\Helpers\Encryptor;
use App\Models\Cliente;
use App\Models\ContactosCliente;
use App\Models\ingreso;
use App\Models\Solicitud;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Peru\Jne\DniFactory;
use Peru\Sunat\RucFactory;

class ClienteController extends Controller
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
        return view("modules.cliente.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
    public function show($urlapi)
    {
          
        $cliente = Cliente::with(["ContactosCliente"])->where("cli_id", Encryptor::decrypt($urlapi))->first();

        return view("modules.cliente.show", compact("cliente"));
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

    // rutas axios
    public function crear_cliente(Request $request)
    {
        try {
            $Params = $request->all();
            // Crea un nuevo registro de cliente con los datos proporcionados


            $Created = new Cliente();

            $Created->cli_dni = $request->cli_dni;
            $Created->cli_nombre = $request->cli_nombre;
            $Created->cli_apellido = $request->cli_apellido;
            $Created->cli_domicilio = $request->cli_domicilio;
            $Created->cli_direccion_trabajo = $request->cli_direccion_trabajo;
            $Created->cli_sexo = $request->cli_sexo;
            $Created->tipo_cliente = $request->tipo_cliente;
            $Created->sucursal_id = Auth::user()->sucursal_id;
            $Created->fecha_nacimiento = Carbon::parse($Params["fecha_nacimiento"])->format("Y-m-d");
            $Created->cli_departamento = $request->cli_departamento;
            $Created->cli_distrito = $request->cli_distrito;
            $Created->cli_provincia = $request->cli_provincia;

            if ($Created->save()) {
                // agregar contactos a los clientes
                foreach ($request->cli_celular as $cc) {

                    $ContactosCliente = new ContactosCliente();
                    $ContactosCliente->ccliente_contacto = $cc['contacto'];
                    $ContactosCliente->ccliente_descripcion = $cc['descripcion'];
                    $ContactosCliente->cli_id = $Created->cli_id;
                    $ContactosCliente->save();
                }

                $cliente =  Cliente::with(["ContactosCliente"])->find($Created->cli_id);
                return response()->json([
                    'message' => 'Cliente registrado correctamente',
                    'error' => '',
                    'success' => true,
                    'data' => $cliente,
                ]);
            } else {
                return response()->json([
                    'message' => 'Cliente no se registro correctamente',
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

    public function search_dni(Request $request)
    {
       try {

        $dni = $request->dni;

        $response = Http::withToken('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI0MTI1MSIsImh0dHA6Ly9zY2hlbWFzLm1pY3Jvc29mdC5jb20vd3MvMjAwOC8wNi9pZGVudGl0eS9jbGFpbXMvcm9sZSI6ImNvbnN1bHRvciJ9.VYdMXqXBj8R25Q8PS9s_YemZMBTpuTV8i-lK2j1T9CU')
            ->get("https://api.factiliza.com/v1/dni/info/{$dni}");

        $result = $response->json();

        if (!$response->successful() || !$result['success']) {

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'No se encontró información',
                'data' => null
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'DNI encontrado',
            'data' => $result['data']
        ]);

    } catch (\Throwable $th) {

        return response()->json([
            'success' => false,
            'message' => 'Error del servidor',
            'error' => $th->getMessage()
        ]);
    }
    }

    // buscar por ruc
    public function search_ruc(Request $request)
    {
        try {
            $Params = $request->all();

            $ruc = $Params["ruc"];

            $factory = new RucFactory();
            $cs = $factory->create();

            $company = $cs->get($ruc);
            if (!$company) {
                return response()->json([
                    'message' => 'el Ruc( ' . $ruc . ' ) no existe,digite nuevamente',
                    'error' => '',
                    'success' => false,
                    'data' => '',
                ]);
            }

            return response()->json([
                'message' => 'Ruc( ' . $ruc . ' ) encontrado',
                'error' => '',
                'success' => true,
                'data' => $company,
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

    // cargar los datos de un cliente

    public function get_ciente(Request $request)
    {

        try {
            $Params = $request->all();

            $cliente = Cliente::with(["ContactosCliente", "solicitudes"])->find(Encryptor::decrypt($Params["urlapi"]));

            if ($cliente) {
                return response()->json([
                    'message' => 'Cliente encontrado',
                    'error' => '',
                    'success' => true,
                    'data' => $cliente,
                ]);
            } else {
                return response()->json([
                    'message' => 'No se encontro este cliente',
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

    public function editar_cliente(Request $request)
    {
        try {
            $Params = $request->all();
            $cliente = Cliente::find(Encryptor::decrypt($Params["urlapi"]));
            $cliente->cli_dni = $request->cli_dni;
            $cliente->cli_nombre = $request->cli_nombre;
            $cliente->cli_apellido = $request->cli_apellido;
            $cliente->cli_domicilio = $request->cli_domicilio;
            $cliente->cli_direccion_trabajo = $request->cli_direccion_trabajo;
            $cliente->cli_sexo = $request->cli_sexo;
            $cliente->tipo_cliente = $request->tipo_cliente;
            $cliente->sucursal_id = Auth::user()->sucursal_id;
            $cliente->fecha_nacimiento = Carbon::parse($Params["fecha_nacimiento"])->format("Y-m-d");
            $cliente->cli_departamento = $request->cli_departamento;
            $cliente->cli_distrito = $request->cli_distrito;
            $cliente->cli_provincia = $request->cli_provincia;
            if ($cliente->save()) {

                ContactosCliente::where('cli_id', $cliente->cli_id)->delete();

                foreach ($request->cli_celular as $cc) {

                    $ContactosCliente = new ContactosCliente();
                    $ContactosCliente->ccliente_contacto = $cc['contacto'];
                    $ContactosCliente->ccliente_descripcion = $cc['descripcion'];
                    $ContactosCliente->cli_id = $cliente->cli_id;
                    $ContactosCliente->save();
                }

                $cliente = Cliente::with(["ContactosCliente"])->find($cliente->cli_id);

                return response()->json([
                    'message' => 'Editar cliente correctamente',
                    'error' => '',
                    'success' => true,
                    'data' => $cliente,
                ]);
            } else {
                return response()->json([
                    'message' => 'fallo en la edicion',
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
