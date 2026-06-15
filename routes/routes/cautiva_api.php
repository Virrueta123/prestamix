<?php
use Illuminate\Support\Facades\Route;
 
Route::post('/enviar_codigo', [App\Http\Controllers\pos_controller::class, 'enviar_codigo']);
 


