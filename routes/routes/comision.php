<?php

use App\Http\Controllers\comision_controller;
use Illuminate\Support\Facades\Route;

Route::get('/comisiones', [comision_controller::class, 'index'])->name('comision.index');
Route::get('/comisiones/cobrador/{trabajador_id}', [comision_controller::class, 'show_cobrador'])->name('comision.cobrador');
Route::post('/load_cobrador_comisiones', [comision_controller::class, 'load_cobrador_comisiones'])->name('load_cobrador_comisiones');
Route::post('/load_comisiones', [comision_controller::class, 'load_comisiones'])->name('load_comisiones');
Route::post('/load_comision_detalle', [comision_controller::class, 'load_detalle'])->name('load_comision_detalle');
Route::post('/recalcular_comision', [comision_controller::class, 'recalcular_comision'])->name('recalcular_comision');
Route::post('/procesar_comision', [comision_controller::class, 'procesar_comision'])->name('procesar_comision');
Route::get('/get_comision_config', [comision_controller::class, 'get_config'])->name('get_comision_config');
Route::post('/save_comision_config', [comision_controller::class, 'save_config'])->name('save_comision_config');