
<?php
use Illuminate\Support\Facades\Route;

 Route::post('/generar_gasto_adelanto_trabajador', [App\Http\Controllers\gastos_controller::class, 'generar_gasto_adelanto_trabajador'])->name('generar_gasto_adelanto_trabajador');
  
 Route::post('/print_voucher_gasto', [App\Http\Controllers\gastos_controller::class, 'print_voucher_gasto'])->name('print_voucher_gasto');
 
 