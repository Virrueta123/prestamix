<?php

use Illuminate\Support\Facades\Route;

// todo los get
Route::get('/tabla_solicitud_pendiente', [App\Http\Controllers\SolicitudController::class, 'tabla_solicitud_pendiente'])->name('solicitud.tabla_solicitud_pendiente');
Route::get('/tabla_solicitud_rechazado', [App\Http\Controllers\SolicitudController::class, 'tabla_solicitud_rechazado'])->name('solicitud.tabla_solicitud_rechazado');
Route::get('/tabla_solicitud_aprobado', [App\Http\Controllers\SolicitudController::class, 'tabla_solicitud_aprobado'])->name('solicitud.tabla_solicitud_aprobado');
Route::get('/tabla_solicitud_procesado', [App\Http\Controllers\SolicitudController::class, 'tabla_solicitud_procesado'])->name('solicitud.tabla_solicitud_procesado');

Route::get('/modicarreprogramados', [App\Http\Controllers\SolicitudController::class, 'modicarreprogramados'])->name('solicitud.modicarreprogramados');

//imprimir contrato
Route::get('/solicitdu/{id}/imprimir', [App\Http\Controllers\SolicitudController::class, 'imprimir_solicitud'])->name('solicitud.imprimir_solicitud');

 
Route::get('/cambiar_contrato', [App\Http\Controllers\SolicitudController::class, 'cambiar_contrato'])->name('solicitud.cambiar_contrato');

Route::post('/print_parte_contrato', [App\Http\Controllers\SolicitudController::class, 'print_parte_contrato'])->name('solicitud.print_parte_contrato');

// todo los post
Route::post('/crear_solicitud', [App\Http\Controllers\SolicitudController::class, 'crear_solicitud'])->name('crear_solicitud');

Route::post('/guardar_documento', [App\Http\Controllers\SolicitudController::class, 'guardar_documento'])->name('guardar_documento');

Route::post('/guardar_edicion_solicitud', [App\Http\Controllers\SolicitudController::class, 'guardar_edicion_solicitud'])->name('guardar_edicion_solicitud');

Route::post('/restablecer_solicitud', [App\Http\Controllers\SolicitudController::class, 'restablecer_solicitud']);


// todo los put
Route::put("/send_rechazo_solicitud", [App\Http\Controllers\SolicitudController::class, 'send_rechazo_solicitud'])->name('send_rechazo_solicitud');
Route::put("/send_solicitud_aprobada", [App\Http\Controllers\SolicitudController::class, 'send_solicitud_aprobada'])->name('send_solicitud_aprobada');
Route::put("/send_procesar_solicitud", [App\Http\Controllers\SolicitudController::class, 'send_procesar_solicitud'])->name('send_procesar_solicitud');

Route::put("/editar_solicitud", [App\Http\Controllers\SolicitudController::class, 'editar_solicitud'])->name('editar_solicitud');


Route::put("/edit_documento", [App\Http\Controllers\SolicitudController::class, 'edit_documento'])->name('edit_documento'); 

// todo los delete
