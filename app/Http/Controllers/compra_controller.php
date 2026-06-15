<?php

namespace App\Http\Controllers;

use App\Helpers\Encryptor;
use App\Models\caja;
use App\Models\compra;
use App\Models\gastos;
use App\Models\pagos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class compra_controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('modules.compra.create');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('modules.compra.create');
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
            $compra = new compra();
            $compra->compra_correlativo = $Params['compra_correlativo'];
            $compra->compra_serie = $Params['compra_serie'];
            $compra->compra_total = $Params['total'];
            $compra->t_comprobante = $Params['t_comprobante'];
            $compra->caja_chica_id = caja::where("created_user", auth()->user()->id)->where("status", "A")->first()->caja_chica_id;
            $compra->created_user = auth()->user()->id;
            $compra->sucursal_id = auth()->user()->sucursal_id;
            $compra->ruc =$Params['ruc'];
            
 

            if ($compra->save()) {
  
                foreach ($Params["items"] as $item) {
                    $gasto = new gastos(); 
                    $gasto->gastos_descripcion = $item["descripcion"];
                    $gasto->monto = $item["importe"];
                    $gasto->cantidad = $item["cantidad"]; 
                    $gasto->precio = $item["precio"]; 
                    $gasto->tipo_gasto_id = Encryptor::decrypt($item["tipo_gasto_id"]);
                    $gasto->created_user = auth()->user()->id;
                    $gasto->sucursal_id = auth()->user()->sucursal_id;
                    $gasto->caja_chica_id = caja::where("created_user", auth()->user()->id)->where("status", "A")->first()->caja_chica_id;
                    $gasto->compra_id = $compra->compra_id;
                    $gasto->yes_compra = "Y";
                    $gasto->save();
                }
            

                foreach ($Params["pagos"] as $pago) {
                    $pagos = new pagos();
                    $pagos->compra_id = $compra->compra_id;
                    $pagos->monto = $pago["monto"];
                    $pagos->cuentas_id = Encryptor::decrypt($pago["cuentas_id"]);
                    $pagos->tipo = "C";
                    $pagos->caja_chica_id = caja::where("created_user", auth()->user()->id)->where("status", "A")->first()->caja_chica_id;
                    $pagos->created_user  = auth()->user()->id;
                    $pagos->sucursal_id  = auth()->user()->sucursal_id;
                    $pagos->save();
                }
                session()->flash('success', 'Compra creada correctamente');
                return response()->json([
                    'message' => 'Compra creada correctamente',
                    'error' => '',
                    'success' => true,
                    'data' => route("compra.index"),
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
}
