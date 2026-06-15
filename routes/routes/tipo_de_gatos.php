<?php

use Illuminate\Support\Facades\Route;

Route::post('/load_tipo_de_gato', [App\Http\Controllers\tipo_gasto_controller::class, 'load_tipo_de_gato'])->name('load_tipo_de_gato');

Route::put('/tipo_gasto_edit', [App\Http\Controllers\tipo_gasto_controller::class, 'tipo_gasto_edit'])->name('tipo_gasto_edit');

Route::put('/change_status', [App\Http\Controllers\tipo_gasto_controller::class, 'change_status'])->name('change_status');


Route::post('/get_tipo_gasto', [App\Http\Controllers\tipo_gasto_controller::class, 'get_tipo_gasto'])->name('get_tipo_gasto');