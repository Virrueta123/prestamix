<?php

use Illuminate\Support\Facades\Route;

 
Route::post('/delete_ingreso', [App\Http\Controllers\ingresos_controller::class, 'delete_ingreso'])->name('delete_ingreso');

Route::post('/load_ingresos_cliente', [App\Http\Controllers\ingresos_controller::class, 'load_ingresos_cliente'])->name('load_ingresos_cliente');

Route::post('/load_ingresos_por_cuota', [App\Http\Controllers\ingresos_controller::class, 'load_ingresos_por_cuota'])->name('load_ingresos_por_cuota');

Route::post('/print_voucher_ingreso', [App\Http\Controllers\ingresos_controller::class, 'print_voucher_ingreso'])->name('print_voucher_ingreso');
 
Route::post('/editar_pago_ingreso', [App\Http\Controllers\ingresos_controller::class, 'editar_pago_ingreso'])->name('editar_pago_ingreso');

