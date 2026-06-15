<?php
use Illuminate\Support\Facades\Route;

Route::post('/search_cliente_select', [App\Http\Controllers\SelectController::class, 'search_cliente_select'])->name('search_cliente_select');

Route::post('/search_ubigeo_select', [App\Http\Controllers\SelectController::class, 'search_ubigeo_select'])->name('search_ubigeo_select');

Route::post('/load_cuentas', [App\Http\Controllers\SelectController::class, 'load_cuentas'])->name('load_cuentas');

Route::post('/get_ubigeo', [App\Http\Controllers\SelectController::class, 'get_ubigeo'])->name('get_ubigeo');

Route::post('/load_sucursal', [App\Http\Controllers\SelectController::class, 'load_sucursal'])->name('load_sucursal');