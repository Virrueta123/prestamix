<?php

use Illuminate\Support\Facades\Route;

// actualizar gasto
Route::put('/gasto_edit', [App\Http\Controllers\gastos_controller::class, 'gasto_edit'])->name('gasto_edit');

Route::post('/delete_gasto', [App\Http\Controllers\gastos_controller::class, 'delete_gasto'])->name('delete_gasto');

 Route::post('/actualizar_pago', [App\Http\Controllers\gastos_controller::class, 'actualizar_pago']);