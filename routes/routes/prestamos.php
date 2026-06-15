<?php

use Illuminate\Support\Facades\Route;

// todo los gets

Route::get('/planilla_prestamos', [App\Http\Controllers\PrestamoController::class, 'planilla_prestamos'])->name('prestamo.planilla_prestamos');
 
Route::get('/planilla_prestamos/{nombre?}', [App\Http\Controllers\PrestamoController::class, 'planilla_prestamos'])->name('planilla_prestamos');

Route::get('/reprogramacion/{urlapi}', [App\Http\Controllers\PrestamoController::class, 'reprogramacion'])->name('reprogramacion');

Route::get('/reprogramacion_cuota/{urlapi}', [App\Http\Controllers\PrestamoController::class, 'reprogramacion_cuota'])->name('reprogramacion_cuota');

Route::get('/refinanciamiento/{urlapi}', [App\Http\Controllers\PrestamoController::class, 'refinanciamiento'])->name('refinanciamiento');

Route::get('/ampliacion/{urlapi}', [App\Http\Controllers\PrestamoController::class, 'ampliacion'])->name('ampliacion');

Route::get('/desaparecer_prestamo/{urlapi}', [App\Http\Controllers\PrestamoController::class, 'desaparecer_prestamo'])->name('desaparecer_prestamo');

Route::get('/cancelar_prestamo/{urlapi}', [App\Http\Controllers\PrestamoController::class, 'cancelar_prestamo'])->name('cancelar_prestamo');

Route::post('/prestamo_por_cliente', [App\Http\Controllers\PrestamoController::class, 'prestamo_por_cliente'])->name('prestamo_por_cliente');

//  todo los post

Route::post('/crear_prestamo', [App\Http\Controllers\PrestamoController::class, 'crear_prestamo'])->name('crear_prestamo');

Route::post('/crear_reporgramacion', [App\Http\Controllers\PrestamoController::class, 'crear_reporgramacion'])->name('crear_reporgramacion');

Route::post('/crear_refinanciamiento', [App\Http\Controllers\PrestamoController::class, 'crear_refinanciamiento'])->name('crear_refinanciamiento');

Route::post('/crear_ampliacion', [App\Http\Controllers\PrestamoController::class, 'crear_ampliacion'])->name('crear_ampliacion');

Route::post('/procesar_cancelar_prestamo', [App\Http\Controllers\PrestamoController::class, 'procesar_cancelar_prestamo'])->name('procesar_cancelar_prestamo');


Route::post('/data_load_planilla', [App\Http\Controllers\PrestamoController::class, 'data_load_planilla'])->name('data_load_planilla');

Route::post('/cancelar_cronograma', [App\Http\Controllers\PrestamoController::class, 'cancelar_cronograma']);

Route::post('/get_cuotas', [App\Http\Controllers\PrestamoController::class, 'get_cuotas'])->name('get_cuotas');

Route::put('/updated_yes_interes', [App\Http\Controllers\PrestamoController::class, 'updated_yes_interes'])->name('updated_yes_interes');

Route::put('/updated_yes_mora', [App\Http\Controllers\PrestamoController::class, 'updated_yes_mora'])->name('updated_yes_mora');

// crear un ingreso de una cuota
Route::post('/ingreso_cuota', [App\Http\Controllers\PrestamoController::class, 'ingreso_cuota'])->name('ingreso_cuota');
 
Route::post('/ingreso_cuota_grupal', [App\Http\Controllers\PrestamoController::class, 'ingreso_cuota_grupal'])->name('ingreso_cuota_grupal');

Route::post('/ingreso_cuota_por_parte', [App\Http\Controllers\PrestamoController::class, 'ingreso_cuota_por_parte'])->name('ingreso_cuota_por_parte');

Route::post('/load_ingresos_prestamo', [App\Http\Controllers\PrestamoController::class, 'load_ingresos_prestamo'])->name('load_ingresos_prestamo');

Route::post('/load_desemboloso', [App\Http\Controllers\PrestamoController::class, 'load_desemboloso'])->name('load_desemboloso');

Route::post('/comprobar_pago_cuotas_reprogramacion',[App\Http\Controllers\PrestamoController::class, 'comprobar_pago_cuotas_reprogramacion'])->name("comprobar_pago_cuotas_reprogramacion");

Route::post('/comprobar_pago_cuotas_reprogramacion_store',[App\Http\Controllers\PrestamoController::class, 'comprobar_pago_cuotas_reprogramacion_store'])->name("comprobar_pago_cuotas_reprogramacion");


// rutas para los mensajes de los prestamos para los acuerdos

Route::get('/table_mensaje_prestamo', [App\Http\Controllers\PrestamoController::class,'mensaje_prestamo'])->name("prestamo.table_mensajes_prestamo");
 
Route::post('/load_data_msj_prestamo', [App\Http\Controllers\PrestamoController::class,'load_data_msj_prestamo']);

Route::post('/mensaje_prestamo', [App\Http\Controllers\PrestamoController::class, 'mensajes_prestamo'])->name('mensaje_prestamo'); 
 
Route::post('/load_mensaje_prestamo', [App\Http\Controllers\PrestamoController::class, 'load_mensaje_prestamo'])->name('load_mensaje_prestamo');

Route::post('/load_message_by_user_before', [App\Http\Controllers\PrestamoController::class, 'load_message_by_user_before'])->name('load_message_by_user_before');

Route::get('/do_no_show_message_before', [App\Http\Controllers\PrestamoController::class, 'do_no_show_message_before'])->name('do_no_show_message_before');

Route::post('/load_message_by_user_after', [App\Http\Controllers\PrestamoController::class, 'load_message_by_user_after'])->name('load_message_by_user_after');

Route::get('/do_no_show_message_after', [App\Http\Controllers\PrestamoController::class, 'do_no_show_message_after'])->name('do_no_show_message_after');

