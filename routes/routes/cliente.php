<?php
use Illuminate\Support\Facades\Route;

// get
Route::get('/cliente', [App\Http\Controllers\ClienteController::class, 'index'])->name('cliente.index'); 
Route::get('/cliente/{urlapi}', [App\Http\Controllers\ClienteController::class, 'show'])->name('cliente.show'); 

// post
 Route::post('/crear_cliente', [App\Http\Controllers\ClienteController::class, 'crear_cliente'])->name('crear_cliente');
 Route::post('/search_dni', [App\Http\Controllers\ClienteController::class, 'search_dni'])->name('search_dni');
 Route::post('/search_ruc', [App\Http\Controllers\ClienteController::class, 'search_ruc'])->name('search_ruc');
 Route::post('/get_ciente', [App\Http\Controllers\ClienteController::class, 'get_ciente'])->name('get_ciente');

//  put
 Route::put('/editar_cliente', [App\Http\Controllers\ClienteController::class, 'editar_cliente'])->name('editar_cliente');

// delete
Route::post('/eliminar_cliente', [App\Http\Controllers\ClienteController::class, 'eliminar_cliente'])->name('eliminar_cliente');
Route::post('/prestamos_activos_cliente', [App\Http\Controllers\ClienteController::class, 'prestamos_activos_cliente'])->name('prestamos_activos_cliente');