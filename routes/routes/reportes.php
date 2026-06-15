<?php

use App\Http\Controllers\reporte_controller;
use Illuminate\Support\Facades\Route;

// todo los gets
// todo los posts

Route::post('/load_leyenda', [ reporte_controller::class, 'load_leyenda' ]);

Route::post('/report_limit_aprobados', [ reporte_controller::class, 'report_limit_aprobados' ]);

Route::post('/load_anual', [ reporte_controller::class, 'load_anual' ]);

Route::post('/load_mensual', [ reporte_controller::class, 'load_mensual' ]);

Route::post('/load_data_by_year', [ reporte_controller::class, 'load_data_by_year' ]);

Route::post('/load_data_by_month', [ reporte_controller::class, 'load_data_by_month' ]);

Route::post('/time_line_request', [ reporte_controller::class, 'time_line_request' ]);

