<?php

use Illuminate\Support\Facades\Route;



Route::post('/load_usuario', [App\Http\Controllers\usuarioController::class, 'load_usuario'])->name('load_usuario');

Route::post('/load_trabajadores', [App\Http\Controllers\usuarioController::class, 'load_trabajadores'])->name('load_trabajadores');

Route::post('/load_analistas', [App\Http\Controllers\usuarioController::class, 'load_analistas'])->name('load_analistas');

Route::put('/desactivar_usuario', [App\Http\Controllers\usuarioController::class, 'desactivar_usuario'])->name('desactivar_usuario');

Route::put('/edit_usuario', [App\Http\Controllers\usuarioController::class, 'edit_usuario'])->name('edit_usuario');

Route::post('/report_analista_caja', [App\Http\Controllers\usuarioController::class, 'report_analista_caja'])->name('report_analista_caja');


Route::post('/load_recepcionista', [App\Http\Controllers\usuarioController::class, 'load_recepcionista'])->name('load_recepcionista');