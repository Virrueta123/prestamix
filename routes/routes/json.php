<?php

use App\Http\Controllers\json_controller;
use Illuminate\Support\Facades\Route;
  
Route::get('/leer_json', [ json_controller::class, 'leer_json' ]);