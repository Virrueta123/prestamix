<?php

use Illuminate\Support\Facades\Route;

Route::post('/crear_cliente', [App\Http\Controllers\ClienteController::class, 'crear_cliente'])->name('crear_cliente');

Route::post('/tabla_solicitud_pendiente_data', [App\Http\Controllers\tables_controller::class, 'tabla_solicitud_pendiente_data']);

Route::post('/tabla_solicitud_rechazado_data', [App\Http\Controllers\tables_controller::class, 'tabla_solicitud_rechazado_data']);

Route::get('/tabla_solicitud_rechazado_data', [App\Http\Controllers\tables_controller::class, 'tabla_solicitud_rechazado_data']); 

Route::post('/tabla_solicitud_procesado_data', [App\Http\Controllers\tables_controller::class, 'tabla_solicitud_procesado_data']);

Route::post('/table_sueldo', [App\Http\Controllers\tables_controller::class, 'table_sueldo']);

Route::post('/tabla_solicitud_aprobado_data', [App\Http\Controllers\tables_controller::class, 'tabla_solicitud_aprobado_data']);

Route::post('/load_table_cuotas', [App\Http\Controllers\tables_controller::class, 'load_table_cuotas']);

Route::post('/load_table_cuotas_prestamo', [App\Http\Controllers\tables_controller::class, 'load_table_cuotas_prestamo']);

Route::post('/load_table_caja', [App\Http\Controllers\tables_controller::class, 'load_table_caja']);

Route::post('/load_table_cuentas', [App\Http\Controllers\tables_controller::class, 'load_table_cuentas']);

Route::post('/load_table_gasto', [App\Http\Controllers\tables_controller::class, 'load_table_gasto'])->name('load_table_gasto');

Route::post('/load_table_ingresos', [App\Http\Controllers\tables_controller::class, 'load_table_ingresos'])->name('load_table_ingresos');

Route::post('/load_table_tipo_gasto', [App\Http\Controllers\tables_controller::class, 'load_table_tipo_gasto'])->name('load_table_tipo_gasto');

Route::post('/tabla_solicitud_trabajadores', [App\Http\Controllers\tables_controller::class, 'tabla_solicitud_trabajadores'])->name('tabla_solicitud_trabajadores');

Route::post('/tabla_solicitud_trabajadores_procesado_by_trabajador', [App\Http\Controllers\tables_controller::class, 'tabla_solicitud_trabajadores_procesado_by_trabajador'])->name('tabla_solicitud_trabajadores_procesado_by_trabajador');
 
Route::post('/tabla_cliente_data', [App\Http\Controllers\tables_controller::class, 'tabla_cliente_data'])->name('tabla_cliente_data');

Route::post('/tabla_ingresos_cliente_data', [App\Http\Controllers\tables_controller::class, 'tabla_ingresos_cliente_data']);
