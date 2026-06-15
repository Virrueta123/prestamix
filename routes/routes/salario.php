<?php

use App\Http\Controllers\reporte_controller;
use App\Http\Controllers\salario_controller;
use Illuminate\Support\Facades\Route;

// todo los gets
// todo los posts

Route::get('/crear_salario/{id}', [ salario_controller::class, 'crear_salario' ]); 

Route::get('/crear_solicitud_plantilla', [App\Http\Controllers\salario_controller::class, 'crear_solicitud_plantilla'])->name('crear_solicitud_plantilla');

Route::post('/load_salario', [App\Http\Controllers\salario_controller::class, 'load_salario'])->name('load_salario');

Route::post('/get_salario', [App\Http\Controllers\salario_controller::class, 'get_salario'])->name('get_salario');

Route::post('/pagar_salario', [App\Http\Controllers\salario_controller::class, 'pagar_salario']);

Route::post('/solicitud_trabajador', [App\Http\Controllers\salario_controller::class, 'solicitud_trabajador'])->name('solicitud_trabajador');

Route::get('/aprobar_solicitud_trabajador/{id}', [App\Http\Controllers\salario_controller::class, 'aprobar_solicitud_trabajador'])->name('aprobar_solicitud_trabajador');

Route::get('/tabla_solicitud', [App\Http\Controllers\salario_controller::class, 'tabla_solicitud'])->name('tabla_solicitud');

Route::get('/tabla_solicitud/{id}', [App\Http\Controllers\salario_controller::class, 'tabla_solicitud_show'])->name('tabla_solicitud_show');

Route::put('/actualizar_estado_solicitud_trabajador', [App\Http\Controllers\salario_controller::class, 'actualizar_estado_solicitud_trabajador'])->name('actualizar_estado_solicitud_trabajador');

Route::put('/salario/{id}', [App\Http\Controllers\salario_controller::class, 'show'])->name('show_salario');
