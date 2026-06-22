<?php

namespace App\Http\Controllers;

use App\Helpers\Encryptor;
use App\Models\Cliente;
use App\Models\cuentas;
use App\Models\Sucursal;
use App\Models\ubigueo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SelectController extends Controller
{
    public function search_cliente_select(Request $request)
    {
        try {
            $Params = $request->all();

            $searchTerm = trim($request->input('search', ''));
            $query = Cliente::select("*", DB::raw('cli_id AS id'), DB::raw("CONCAT(cli_nombre,' ', cli_apellido,'-',cli_dni) AS name"));

            if ($searchTerm !== '') {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('cli_nombre', 'like', '%' . $searchTerm . '%')
                        ->orWhere('cli_apellido', 'like', '%' . $searchTerm . '%')
                        ->orWhere('cli_dni', 'like', '%' . $searchTerm . '%');
                });
            }

            $search = $query->orderBy('created_at', 'desc')->limit(10)->get();

            return response()->json([
                'message' => 'Datos cargados correctamente',
                'error' => '',
                'success' => true,
                'data' => $search,
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

    public function get_ubigeo(Request $request)
    {
        try {
            $Params = $request->all();
            
            $ubigeo = ubigueo::find( Encryptor::decrypt($Params['urlapi']) );

            return response()->json([
                'message' => 'Datos cargados correctamente',
                'error' => '',
                'success' => true,
                'data' => $ubigeo,
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

    public function search_ubigeo_select(Request $request)
    {
        try {
            $Params = $request->all();

            $search = ubigueo::where('Distrito', 'like', '%' . $request->all()['search'] . '%')
                ->orWhere('Provincia', 'like', '%' . $request->all()['search'] . '%')
                ->orWhere('Departamento', 'like', '%' . $request->all()['search'] . '%')
                ->limit(10)
                ->get();

            return response()->json([
                'message' => 'Datos cargados correctamente',
                'error' => '',
                'success' => true,
                'data' => $search,
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

    public function load_cuentas(Request $request)
    {
        try {

            $cuentas = cuentas::where("status", "A")->get();

            return response()->json([
                'message' => 'Cuentas cargados correctamente',
                'error' => '',
                'success' => true,
                'data' => $cuentas,
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

    public function load_sucursal(Request $request)
    {
        try {

            $sucursal = Sucursal::where("status", "A")->get();

            return response()->json([
                'message' => 'Cuentas cargados correctamente',
                'error' => '',
                'success' => true,
                'data' => $sucursal,
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
