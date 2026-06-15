<?php

namespace App\Http\Controllers;

use App\Imports\planilla;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class importar_controller extends Controller
{
    public function importar_planilla_semanal(){ 
        return Excel::import(new planilla(), 'import/planilla_semanal.xlsx');
    }
}
