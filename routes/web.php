<?php

use App\Http\Controllers\caja_controller;
use App\Http\Controllers\compra_controller;
use App\Http\Controllers\cuentas_controller;
use App\Http\Controllers\gastos_controller;
use App\Http\Controllers\ingresos_varios_controller;
use App\Http\Controllers\PrestamoController;
use App\Http\Controllers\salario_controller;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\tipo_gasto_controller;
use App\Http\Controllers\usuarioController;
use App\Models\compra;
use App\Models\tipo_gasto;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//Rutas para conectar la app de scanner de productos 
include_once "routes/cautiva_api.php";

//Rutas de cliente
include_once "routes/cliente.php";

include_once "routes/usuario.php";

// rutas prestamo
include_once "routes/prestamos.php";

// rutas solicitud
include_once "routes/solicitud.php";

// rutas solicitud
include_once "routes/calendar.php";

// importar cuentas
include_once "routes/cuentas.php";

// importar tipo_de_gatos
include_once "routes/tipo_de_gatos.php";

// importar gastos
include_once "routes/gastos.php";
 
// importar planilla
include_once "routes/planilla.php";

//services
include_once "routes/select.php";

//caja_chica
include_once "routes/caja_chica.php";

//reporte
include_once "routes/reportes.php"; 

//salario
include_once "routes/salario.php";

//gasto
include_once "routes/gasto.php";

//ingreso
include_once "routes/ingreso.php";

//json
include_once "routes/json.php";

include_once "routes/tables.php";

include_once "routes/pos.php";

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home.index');


Route::get('/simulacion', [App\Http\Controllers\PrestamoController::class, 'simulacion'])->name('simulacion');


Route::get('/probar_correo', [App\Http\Controllers\PrestamoController::class, 'probar_correo'])->name('probar_correo');

Route::get('/impresion_prueba', [App\Http\Controllers\PrestamoController::class, 'impresion_prueba'])->name('impresion_prueba');

Route::resource('usuario', usuarioController::class);

Route::resource('ingresos_varios', ingresos_varios_controller::class);

Route::resource('compra', compra_controller::class);

Route::resource('prestamo', PrestamoController::class);


Route::get('solicitud/create/{parametro?}', [SolicitudController::class, 'create'])
    ->name('solicitud.create');


Route::resource('solicitud', SolicitudController::class);

Route::resource('caja', caja_controller::class);

Route::resource('cuentas', cuentas_controller::class);

Route::resource('gastos', gastos_controller::class);

Route::resource('tipo_gasto', tipo_gasto_controller::class);

Route::resource('salario', salario_controller::class);

 
Route::get('/bloqueado', function () { 
    return view("bloqueado");
})->name('bloqueado')->middleware(['auth', 'bloqueado']);
