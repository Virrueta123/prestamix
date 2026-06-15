<?php
use Illuminate\Support\Facades\Route;

 Route::get('/importar_planilla_semanal', [App\Http\Controllers\importar_controller::class, 'importar_planilla_semanal'])->name('importar_planilla_semanal');
  
