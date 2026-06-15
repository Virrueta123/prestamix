<?php
use Illuminate\Support\Facades\Route;

 Route::get('/calendario_planilla', [App\Http\Controllers\calendario_controller::class, 'calendario_planilla'])->name('calendario.planilla');
  Route::post('/load_calendar', [App\Http\Controllers\calendario_controller::class, 'load_calendar']);
 