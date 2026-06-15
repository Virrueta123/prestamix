<?php
use Illuminate\Support\Facades\Route;

 Route::put('/cerrar_caja', [App\Http\Controllers\caja_controller::class, 'cerrar_caja'])->name('cerrar_caja');

 Route::get('/caja_reporte/{id}', [App\Http\Controllers\caja_controller::class, 'caja_reporte'])->name('caja_reporte');

