<?php
use Illuminate\Support\Facades\Route;

 Route::put('/cuentas_edit', [App\Http\Controllers\cuentas_controller::class, 'cuentas_edit'])->name('cuentas_edit');
 

