<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Hosting: detectar raiz de Laravel
|--------------------------------------------------------------------------
|
| Estructura normal:  /proyecto/vendor + /proyecto/public/index.php
| Hosting restringido: /public/vendor + /public/app (todo dentro de public)
|
*/

$laravelBase = is_file(__DIR__.'/vendor/autoload.php')
    ? __DIR__
    : dirname(__DIR__);

if (! is_file($laravelBase.'/vendor/autoload.php')) {
    http_response_code(500);
    exit('No se encontro vendor/autoload.php. Sube vendor dentro de public/ o corrige la ruta del dominio.');
}

$_ENV['APP_BASE_PATH'] = $laravelBase;
putenv('APP_BASE_PATH='.$laravelBase);

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists($maintenance = $laravelBase.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require $laravelBase.'/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

$app = require_once $laravelBase.'/bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
