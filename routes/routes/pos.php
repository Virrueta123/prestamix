<?php
use Illuminate\Support\Facades\Route;

 Route::get('/pos', [App\Http\Controllers\pos_controller::class, 'create'])->name('cautiva.pos'); 
 