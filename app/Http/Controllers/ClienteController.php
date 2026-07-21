<?php

namespace App\Http\Controllers;

use App\Helpers\Encryptor;
use App\Models\Cliente;
use App\Models\ContactosCliente;
use App\Models\ingreso;
use App\Models\Prestamo;
use App\Models\Solicitud;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    public function prestamos_activos_cliente(Request $request)
    {
        try {
            $cliId = Encryptor::decrypt($request->input('urlapi'));
            $prestamos = Prestamo::with('solicitud')
                ->where('cli_id', $cliId)
                ->where('status', 'A')
                ->get()
                ->map(function ($p) {
                    return [
                        'code' => $p->solicitud->code ?? $p->code,
                        'monto' => $p->moto_credito,
                        'estado' => 'Activo',
                    ];
                });

            return response()->json([
                'message' => 'Préstamos activos del cliente',
                'success' => true,
                'data' => [
                    'total' => $prestamos->count(),
                    'prestamos' => $prestamos,
                ],
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json([
                'message' => 'error del servidor',
                'success' => false,
                'data' => '',
            ]);
        }
    }

    public function eliminar_cliente(Request $request)
    {
        try {
            $Params = $request->all();
            $cliId = Encryptor::decrypt($Params['urlapi']);
            $cliente = Cliente::find($cliId);

            if (!$cliente) {
                return response()->json([
                    'message' => 'Cliente no encontrado',
                    'error' => '',
                    'success' => false,
                    'data' => '',
                ]);
            }

            if (Solicitud::where('cli_id', $cliId)->exists()) {
                return response()->json([
                    'message' => 'No se puede eliminar: el cliente tiene solicitudes registradas',
                    'error' => '',
                    'success' => false,
                    'data' => '',
                ]);
            }

            if (Prestamo::where('cli_id', $cliId)->exists()) {
                return response()->json([
                    'message' => 'No se puede eliminar: el cliente tiene préstamos registrados',
                    'error' => '',
                    'success' => false,
                    'data' => '',
                ]);
            }

            if (ingreso::where('cli_id', $cliId)->exists()) {
                return response()->json([
                    'message' => 'No se puede eliminar: el cliente tiene ingresos registrados',
                    'error' => '',
                    'success' => false,
                    'data' => '',
                ]);
            }

            ContactosCliente::where('cli_id', $cliId)->delete();
            $cliente->delete();

            return response()->json([
                'message' => 'Cliente eliminado correctamente',
                'error' => '',
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
     * Vista: clientes que van pagando + gráfico de intereses.
     */
    public function clientes_que_pagan()
    {
        return view('modules.cliente.clientes_que_pagan');
    }

    /** @deprecated usar clientes_que_pagan */
    public function beneficiarios_pagos()
    {
        return $this->clientes_que_pagan();
    }

    /**
     * API: pagos de clientes (más recientes primero) + intereses mensuales.
     */
    public function load_clientes_que_pagan(Request $request)
    {
        try {
            $fechaInicio = $request->input('fecha_inicio');
            $fechaFin = $request->input('fecha_fin');
            $sucursalId = Auth::user()->sucursal_id;

            $base = DB::table('ingreso as i')
                ->join('detalle_ingreso as di', 'di.ingreso_id', '=', 'i.ingreso_id')
                ->join('cronograma as c', 'c.cronograma_id', '=', 'di.cronograma_id')
                ->join('prestamos as p', 'p.prestamo_id', '=', 'i.prestamo_id')
                ->join('solicitud as s', 's.solicitud_id', '=', 'p.solicitud_id')
                ->join('cliente as cl', 'cl.cli_id', '=', 's.cli_id')
                ->whereNull('i.deleted_at')
                ->whereNull('di.deleted_at')
                ->where('i.sucursal_id', $sucursalId)
                ->whereNotNull('i.prestamo_id')
                ->where('i.prestamo_id', '>', 0);

            if (!empty($fechaInicio)) {
                $base->whereDate('i.created_at', '>=', $fechaInicio);
            }
            if (!empty($fechaFin)) {
                $base->whereDate('i.created_at', '<=', $fechaFin);
            }

            $pagos = (clone $base)
                ->select([
                    'i.ingreso_id',
                    'di.monto as monto_detalle',
                    'i.codigo',
                    'i.created_at as fecha_pago',
                    'i.descripcion',
                    'cl.cli_id',
                    'cl.cli_nombre',
                    'cl.cli_apellido',
                    'cl.cli_dni',
                    'c.periodo',
                    'c.interes',
                    'c.cuota',
                    'c.yes_interes',
                    'c.amortizacion',
                    'c.yes_pago',
                    's.serie as solicitud_serie',
                    's.solicitud_id',
                    'p.frecuencia_pagos',
                    'p.prestamo_id',
                ])
                ->orderByDesc('i.created_at')
                ->get()
                ->map(function ($row) {
                    $interes = ($row->yes_interes === 'Y') ? (float) $row->interes : 0.0;

                    return [
                        'ingreso_id' => $row->ingreso_id,
                        'cliente' => trim(($row->cli_nombre ?? '') . ' ' . ($row->cli_apellido ?? '')),
                        'cli_dni' => $row->cli_dni,
                        // Número de solicitud (serie), no el de préstamo
                        'solicitud' => sprintf('%06d', $row->solicitud_serie ?? 0),
                        'periodo' => $row->periodo,
                        'cuota' => (float) $row->cuota,
                        'monto_pagado' => (float) $row->monto_detalle,
                        'interes' => round($interes, 2),
                        'amortizacion' => (float) $row->amortizacion,
                        'yes_interes' => $row->yes_interes,
                        'frecuencia_pagos' => $row->frecuencia_pagos,
                        'descripcion' => $row->descripcion,
                        'fecha_pago' => $row->fecha_pago
                            ? Carbon::parse($row->fecha_pago)->format('Y-m-d H:i:s')
                            : null,
                        'fecha_pago_fmt' => $row->fecha_pago
                            ? Carbon::parse($row->fecha_pago)->format('d/m/Y H:i')
                            : '—',
                    ];
                })
                ->values();

            // Gráfico: interés cobrado por mes (mismo filtro de fechas)
            $mensualRaw = (clone $base)
                ->selectRaw("
                    YEAR(i.created_at) as anio,
                    MONTH(i.created_at) as mes,
                    SUM(CASE WHEN c.yes_interes = 'Y' THEN c.interes ELSE 0 END) as total_interes,
                    SUM(di.monto) as total_pagado,
                    COUNT(*) as cantidad
                ")
                ->groupBy(DB::raw('YEAR(i.created_at)'), DB::raw('MONTH(i.created_at)'))
                ->orderBy(DB::raw('YEAR(i.created_at)'))
                ->orderBy(DB::raw('MONTH(i.created_at)'))
                ->get();

            $mesesNombres = [
                1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr',
                5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
                9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic',
            ];

            $grafico = [
                'labels' => [],
                'intereses' => [],
                'pagos' => [],
            ];

            foreach ($mensualRaw as $m) {
                $label = ($mesesNombres[(int) $m->mes] ?? $m->mes) . ' ' . $m->anio;
                $grafico['labels'][] = $label;
                $grafico['intereses'][] = round((float) $m->total_interes, 2);
                $grafico['pagos'][] = round((float) $m->total_pagado, 2);
            }

            $totalInteres = $pagos->sum('interes');
            $totalPagado = $pagos->sum('monto_pagado');

            return response()->json([
                'message' => 'Datos cargados correctamente',
                'error' => '',
                'success' => true,
                'data' => [
                    'pagos' => $pagos,
                    'grafico' => $grafico,
                    'resumen' => [
                        'total_registros' => $pagos->count(),
                        'total_interes' => round($totalInteres, 2),
                        'total_pagado' => round($totalPagado, 2),
                    ],
                ],
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
