<?php

namespace App\Http\Controllers;

use App\Helpers\Encryptor;
use App\Models\caja;
use App\Models\Cliente;
use App\Models\gastos;
use App\Models\ingreso;
use App\Models\pagos;
use App\Utils\funciones;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class caja_controller extends Controller
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

        return view("modules.caja.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $hay_caja = caja::where("created_user", auth()->user()->id)->where("status","A")->first();

        //  if(isset($hay_caja)){ 
        //     session()->flash('warning', 'Ya exite una caja abierta con tu usuario cierra la caja actual para crear una nueva');
        //     return redirect()->route("caja.index");
        //  }else{

        //     return view("modules.caja.create");
        //  }


        return view("modules.caja.create");
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

                'saldo_inicial.required' => 'Es obligatorio el campo saldo_inicial.',

                'referencia.required' => 'Es obligatorio el campo referencia.',

            ];

            $validator = Validator::make($Params, [
                'saldo_inicial' => 'required',
                'referencia' => 'required|string',
                'recepcionista_id' => 'required'
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

            $caja = new caja();

            // Establece los valores de los atributos del modelo
            $caja->saldo_inicial = number_format($request->input('saldo_inicial'), 2, ".", "");
            $caja->referencia = $request->input('referencia');
            $caja->fecha_apertura = Carbon::now()->format("Y-m-d");
            $caja->created_user = Encryptor::decrypt($request->input('recepcionista_id'));
            $caja->sucursal_id = Auth::user()->sucursal_id;

            if ($caja->save()) {

                session()->flash('success', 'Cuenta registrado correctamente');

                return response()->json([
                    'message' => 'cuenta registrado correctamente',
                    'error' => '',
                    'success' => true,
                    'data' => route("caja.index"),
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
        $get_tabla_tarjeta_ingresos = pagos::with(["cuenta"])
            ->select('cuentas_id', DB::raw('SUM(monto) as total'))
            ->where("caja_chica_id", Encryptor::decrypt($id))
            ->where("tipo", "I")
            ->whereHas("cuenta", function ($query) {
                $query->where("yes_efectivo", "N");
            })
            ->groupBy('cuentas_id')
            ->get();


        $get_caja = caja::find(Encryptor::decrypt($id));
        return view("modules.caja.show", compact("get_caja", "get_tabla_tarjeta_ingresos"));
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

    public function caja_reporte($id)
    {

        $get_tabla_tarjeta_ingresos = pagos::with(["cuenta"])
            ->select('cuentas_id', DB::raw('SUM(monto) as total'))
            ->where("caja_chica_id", Encryptor::decrypt($id))
            ->where("tipo", "I")
            ->whereHas("cuenta", function ($query) {
                $query->where("yes_efectivo", "N");
            })
            ->groupBy('cuentas_id')
            ->get();


        $get_caja = caja::find(Encryptor::decrypt($id));

        $query = ingreso::with([
            "usuario",
            "pagos" => function ($query) {
                $query->with(["cuenta"]);
            },
            "prestamo" => function ($query) {
                $query->with(["analista", "cliente"]);
            }
        ]);

        $query = $query->where('caja_chica_id', Encryptor::decrypt($id));


        $query = $query->get();

        $ingresos = DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('cliente', static function ($Data) {
                switch ($Data->tipo_ingreso) {
                    case 'C':
                        $cliente = $Data->prestamo->cliente;
                        return "{$cliente->cli_nombre} {$cliente->cli_apellido}";
                        break;

                    case 'A':
                        return "ingresos varios";
                        break;
                    case 'G':

                        $cliente_g = Cliente::find($Data->cli_id);
                        return "{$cliente_g->cli_nombre} {$cliente_g->cli_apellido}";
                        break;
                }
            })
            ->addColumn('analista', static function ($Data) {

                switch ($Data->tipo_ingreso) {
                    case 'C':
                        $analista = $Data->prestamo->analista->name;
                        return "{$analista}";
                        break;

                    case 'A':
                        return "ingresos varios";
                        break;

                    case 'G':
                        return "Dinero guardado";
                }
            })
            ->toJson();


        $query_gasto = gastos::with([
            "usuario",
            "tipo_gasto",
            "sucursal"
        ]);
 
        $query_gasto = $query_gasto->where('caja_chica_id', Encryptor::decrypt($id));
 
        $query_gasto = $query_gasto->get();

        $gasto = DataTables::of($query_gasto)
            ->addIndexColumn()
            ->toJson();


        $funciones =  new funciones(); 
        $carbon =  new Carbon();



        $pdf = Pdf::loadView(
            'pdf.reporte_caja',
            [
                "gasto" =>  $gasto,
                'funciones' => $funciones,
                'get_caja' => $get_caja,
                'get_tabla_tarjeta_ingresos' => $get_tabla_tarjeta_ingresos,
                "ingresos" => $ingresos->original["data"],
                "carbon" => $carbon,
            ]
        );



        return $pdf->stream('invoice.pdf');
    }


    // cerrar caja
    public function cerrar_caja(Request $request)
    {
        try {
            $Params = $request->all();

            $caja = caja::find(Encryptor::decrypt($Params["urlapi"]));
            $caja->status = "C";
            $caja->saldo_final = ($caja->saldo_inicial + $caja->ingresosefectivo) - $caja->gastosefectivo;

            session()->flash('success', 'caja cerrada correcta el reporte ya esta listo');

            if ($caja->save()) {
                return response()->json([
                    'message' => 'Caja cerrar correctamente',
                    'error' => '',
                    'success' => true,
                    'data' => "/caja/" . $Params["urlapi"],
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
