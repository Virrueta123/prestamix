<?php

namespace App\Imports;

use App\Models\cronograma;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;

class planilla implements ToCollection
{

    public function collection(Collection $rows)
    {
        
        try {
            return response()->json([
                'message' => 'se importo correctamente los datos del archivo excel',
                'error' => '',
                'success' => true,
                'data' => '',
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                'message' => 'error al importar verifique los datos en el archivo excel',
                'error' => $th,
                'success' => false,
                'data' => '',
            ]);
        }
    }
}
