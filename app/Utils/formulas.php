<?php

namespace App\Utils;

use DateInterval;
use DateTime;


class formulas
{
    public static function calcularAmortizacionFrancesMensual_cronograma($montoPrestamo, $plazoMeses, $tasaInteresMensual)
    {
        $montoPrestamo = floatval(str_replace(",", "", $montoPrestamo));
        $plazoMeses = floatval(str_replace(",", "", $plazoMeses));
        $tasaInteresMensual = floatval(str_replace(",", "", $tasaInteresMensual));
        // Tasa de interés diaria
        $tasa_diaria = ($tasaInteresMensual / $plazoMeses);

        // Convertir tasa mensual a porcentaje
        $tasaInteresMensual /= 100;

        // Cálculo de la cuota mensual
        $cuota = ($montoPrestamo * $tasaInteresMensual * pow((1 + $tasaInteresMensual), $plazoMeses)) /
            (pow((1 + $tasaInteresMensual), $plazoMeses) - 1);

        // Fecha de desembolso
        $fecha_desembolso = date("d/m/Y");

        // Interés inicial
        $interes_inicial = $montoPrestamo * $tasaInteresMensual;

        // Tabla de amortización
        $amortizacionFrances = [];
        $amortizacionPeriodo = 0;
        $saldo_capital = $montoPrestamo;

        for ($periodo = 1; $periodo <= $plazoMeses; $periodo++) {
            if ($periodo == 1) {
                // Primer periodo
                $amortizacionPeriodo = $cuota - $interes_inicial;
            } else {
                // Periodos posteriores
                $saldo_capital -= $amortizacionPeriodo;
                $interes_inicial = $saldo_capital * $tasaInteresMensual;
                $amortizacionPeriodo = $cuota - $interes_inicial;
            }

            // Fecha de vencimiento
            $fechaVencimiento = new DateTime('2024-03-14');
            $fechaVencimiento->setDate($fechaVencimiento->format("Y"), $fechaVencimiento->format("m") + $periodo, $fechaVencimiento->format("d"));

            // Ajuste por domingo
            if ($fechaVencimiento->format("w") == 0) {
                $fechaVencimiento->modify("+1 day");
            }

            // Agregar pago a la tabla
            if ($periodo == 1) {
                $pago = [
                    "status" => "P",
                    "periodo" => $periodo,
                    "fechaVencimiento" => $fechaVencimiento->format("Y-m-d"),
                    "saldoCapital" =>  number_format($saldo_capital, 2, ".", ""),
                    "amortizacion" => number_format($amortizacionPeriodo, 2, ".", ""),
                    "interes" => number_format($interes_inicial, 2, ".", ""),
                    "cuota" => number_format($cuota, 2, ".", ""),
                ];
            } else {
                $pago = [
                    "status" => "N",
                    "periodo" => $periodo,
                    "fechaVencimiento" => $fechaVencimiento->format("Y-m-d"),
                    "saldoCapital" =>  number_format($saldo_capital, 2, ".", ""),
                    "amortizacion" => number_format($amortizacionPeriodo, 2, ".", ""),
                    "interes" => number_format($interes_inicial, 2, ".", ""),
                    "cuota" => number_format($cuota, 2, ".", ""),
                ];
            }


            $amortizacionFrances[] = $pago;
        }

        return $amortizacionFrances;
    }

    public static function calcularAmortizacionFrancesSemanal_cronograma($fecha_inicio,$montoPrestamo, $plazoSemanas, $tasaInteresSemanal)
    {

        $montoPrestamo =  floatval(str_replace(",", "", $montoPrestamo));
        $plazoSemanas =  floatval(str_replace(",", "", $plazoSemanas));
        $tasaInteresSemanal =  floatval(str_replace(",", "", $tasaInteresSemanal));

        // Cálculo de la cuota semanal
        $cuotaSemanal = ($montoPrestamo * ($tasaInteresSemanal / 100) + $montoPrestamo) / $plazoSemanas;

        // Fecha de desembolso
        $fechaDesembolso = date("d/m/Y");

        // Interés semanal
        $interesSemanal = ($montoPrestamo * ($tasaInteresSemanal / 100)) / $plazoSemanas;

        // Amortización semanal
        $amortizacionSemanal = $cuotaSemanal - $interesSemanal;

        // Tabla de amortización
        $amortizacionFrances = [];
        $saldoPendiente = $montoPrestamo;

        for ($semana = 1; $semana <= $plazoSemanas; $semana++) {
            // Actualizar saldo pendiente
            $saldoPendiente -= $amortizacionSemanal;

            // Calcular fecha de vencimiento
            $fechaVencimiento = new DateTime($fecha_inicio);
            $fechaVencimiento->setDate($fechaVencimiento->format("Y"), $fechaVencimiento->format("m"), $fechaVencimiento->format("d"));
            $fechaVencimiento->add(new DateInterval("P{$semana}W"));

            // Ajustar por domingo
            if ($fechaVencimiento->format("w") == 0) {
                $fechaVencimiento->modify("+1 day");
            }

            // Agregar pago a la tabla
            if ($semana == 1) {
                $pago = [
                    "status" => "P",
                    "periodo" => $semana,
                    "fechaVencimiento" => $fechaVencimiento->format("Y-m-d"),
                    "saldoCapital" => number_format($saldoPendiente, 2, ".", ""),
                    "amortizacion" =>  number_format($amortizacionSemanal, 2, ".", ""),
                    "interes" => number_format($interesSemanal, 2, ".", ""),
                    "cuota" => number_format($cuotaSemanal, 2, ".", ""),
                ];
            } else {
                $pago = [ 
                    "status" => "N",
                    "periodo" => $semana,
                    "fechaVencimiento" => $fechaVencimiento->format("Y-m-d"),
                    "saldoCapital" => number_format($saldoPendiente, 2, ".", ""),
                    "amortizacion" => number_format($amortizacionSemanal, 2, ".", ""),
                    "interes" => number_format($interesSemanal, 2, ".", ""),
                    "cuota" => number_format($cuotaSemanal, 2, ".", ""),
                ];
            }


            $amortizacionFrances[] = $pago;
        }
        return $amortizacionFrances;
    }

    public static function calcularAmortizacionFrancesDiaria_cronograma($fecha_inicio,$montoPrestamo, $plazoDias, $tasaInteresDiaria)
    {
        $montoPrestamo = floatval(str_replace(",", "", $montoPrestamo));
        $plazoDias = floatval(str_replace(",", "", $plazoDias));
        $tasaInteresDiaria = floatval(str_replace(",", "", $tasaInteresDiaria));

        // Cálculo de la cuota diaria
        $cuotaDiaria = ($montoPrestamo * ($tasaInteresDiaria / 100) + $montoPrestamo) / $plazoDias;

        // Fecha de desembolso
        $fechaDesembolso = date("d/m/Y");

        // Interés diario
        $interesDiario = ($montoPrestamo * ($tasaInteresDiaria / 100)) / $plazoDias;

        // Amortización diaria
        $amortizacionDiaria = $cuotaDiaria - $interesDiario;

        // Tabla de amortización
        $amortizacionFrances = [];
        $saldoPendiente = $montoPrestamo;
        $fecha = 1;
        for ($dia = 1; $dia <= $plazoDias; $dia++) {
            // Actualizar saldo pendiente
            $saldoPendiente -= $amortizacionDiaria;

            // Calcular fecha de vencimiento
            $fechaVencimiento = new DateTime($fecha_inicio);
            $fechaVencimiento->setDate($fechaVencimiento->format("Y"), $fechaVencimiento->format("m"), $fechaVencimiento->format("d"));
            $fechaVencimiento->add(new DateInterval("P{$fecha}D"));

            $fecha++;
            // Ajustar por domingo
            if ($fechaVencimiento->format("w") == 0) {
                $fechaVencimiento->modify("+1 day");
                $fecha++;
            }

            // Agregar pago a la tabla
            if ($dia == 1) {
                $pago = [
                    "status" => "P",
                    "periodo" => $dia,
                    "fechaVencimiento" => $fechaVencimiento->format("Y-m-d"),
                    "saldoCapital" => number_format($saldoPendiente, 2, ".", ""),
                    "amortizacion" => number_format($amortizacionDiaria, 2, ".", ""),
                    "interes" => number_format($interesDiario, 2, ".", ""),
                    "cuota" => number_format($cuotaDiaria, 2, ".", ""),
                ];
            } else {
                $pago = [
                    "status" => "N",
                    "periodo" => $dia,
                    "fechaVencimiento" => $fechaVencimiento->format("Y-m-d"),
                    "saldoCapital" => number_format($saldoPendiente, 2, ".", ""),
                    "amortizacion" =>  number_format($amortizacionDiaria, 2, ".", ""),
                    "interes" => number_format($interesDiario, 2, ".", ""),
                    "cuota" => number_format($cuotaDiaria, 2, ".", ""),
                ];
            }

            $amortizacionFrances[] = $pago;
        }
        return $amortizacionFrances;
    }

    public static function calcularAmortizacionFrancesQuincenal_cronograma($fecha_inicio, $montoPrestamo, $plazoQuincenas, $tasaInteres)
    {
        $montoPrestamo = floatval(str_replace(",", "", $montoPrestamo));
        $plazoQuincenas = floatval(str_replace(",", "", $plazoQuincenas));
        $tasaInteres = floatval(str_replace(",", "", $tasaInteres));

        $cuotaQuincenal = ($montoPrestamo * ($tasaInteres / 100) + $montoPrestamo) / $plazoQuincenas;
        $interesQuincenal = ($montoPrestamo * ($tasaInteres / 100)) / $plazoQuincenas;
        $amortizacionQuincenal = $cuotaQuincenal - $interesQuincenal;

        $amortizacionFrances = [];
        $saldoPendiente = $montoPrestamo;

        for ($quincena = 1; $quincena <= $plazoQuincenas; $quincena++) {
            $saldoPendiente -= $amortizacionQuincenal;

            $fechaVencimiento = new DateTime($fecha_inicio);
            $fechaVencimiento->add(new DateInterval('P' . ($quincena * 15) . 'D'));

            if ($fechaVencimiento->format("w") == 0) {
                $fechaVencimiento->modify("+1 day");
            }

            $pago = [
                "status" => $quincena == 1 ? "P" : "N",
                "periodo" => $quincena,
                "fechaVencimiento" => $fechaVencimiento->format("Y-m-d"),
                "saldoCapital" => number_format($saldoPendiente, 2, ".", ""),
                "amortizacion" => number_format($amortizacionQuincenal, 2, ".", ""),
                "interes" => number_format($interesQuincenal, 2, ".", ""),
                "cuota" => number_format($cuotaQuincenal, 2, ".", ""),
            ];

            $amortizacionFrances[] = $pago;
        }

        return $amortizacionFrances;
    }

    //cuotas

    public static function calcularAmortizacionFrancesMensual($montoPrestamo, $plazoMeses, $tasaInteresMensual)
    {
        $montoPrestamo = floatval(str_replace(",", "", $montoPrestamo));
        $plazoMeses = floatval(str_replace(",", "", $plazoMeses));
        $tasaInteresMensual = floatval(str_replace(",", "", $tasaInteresMensual));
        // Tasa de interés diaria
        $tasa_diaria = ($tasaInteresMensual / $plazoMeses);

        // Convertir tasa mensual a porcentaje
        $tasaInteresMensual /= 100;

        // Cálculo de la cuota mensual
        $cuota = ($montoPrestamo * $tasaInteresMensual * pow((1 + $tasaInteresMensual), $plazoMeses)) /
            (pow((1 + $tasaInteresMensual), $plazoMeses) - 1);

        // Fecha de desembolso
        $fecha_desembolso = date("d/m/Y");

        // Interés inicial
        $interes_inicial = $montoPrestamo * $tasaInteresMensual;

        // Tabla de amortización
        $amortizacionFrances = [];
        $amortizacionPeriodo = 0;
        $saldo_capital = $montoPrestamo;

        for ($periodo = 1; $periodo <= $plazoMeses; $periodo++) {
            if ($periodo == 1) {
                // Primer periodo
                $amortizacionPeriodo = $cuota - $interes_inicial;
            } else {
                // Periodos posteriores
                $saldo_capital -= $amortizacionPeriodo;
                $interes_inicial = $saldo_capital * $tasaInteresMensual;
                $amortizacionPeriodo = $cuota - $interes_inicial;
            }

            // Fecha de vencimiento
            $fechaVencimiento = new DateTime();
            $fechaVencimiento->setDate($fechaVencimiento->format("Y"), $fechaVencimiento->format("m") + $periodo, $fechaVencimiento->format("d"));

            // Ajuste por domingo
            if ($fechaVencimiento->format("w") == 0) {
                $fechaVencimiento->modify("+1 day");
            }

            // Agregar pago a la tabla
            $pago = [
                "periodo" => $periodo,
                "fechaVencimiento" => $fechaVencimiento->format("d/m/Y"),
                "saldoCapital" => number_format($saldo_capital, 2),
                "amortizacion" => number_format($amortizacionPeriodo, 2),
                "interes" => number_format($interes_inicial, 2),
                "cuota" => number_format($cuota, 2),
            ];

            $amortizacionFrances[] = $pago;
        }

        return str_replace(",", "", number_format($cuota, 2));
    }

    public static function calcularAmortizacionFrancesSemanal($montoPrestamo, $plazoSemanas, $tasaInteresSemanal)
    {
        $montoPrestamo =  floatval(str_replace(",", "", $montoPrestamo));
        $plazoSemanas =  floatval(str_replace(",", "", $plazoSemanas));
        $tasaInteresSemanal =  floatval(str_replace(",", "", $tasaInteresSemanal));
        // Cálculo de la cuota semanal
        $cuotaSemanal = ($montoPrestamo * ($tasaInteresSemanal / 100) + $montoPrestamo) / $plazoSemanas;

        // Fecha de desembolso
        $fechaDesembolso = date("d/m/Y");

        // Interés semanal
        $interesSemanal = ($montoPrestamo * ($tasaInteresSemanal / 100)) / $plazoSemanas;

        // Amortización semanal
        $amortizacionSemanal = $cuotaSemanal - $interesSemanal;

        // Tabla de amortización
        $amortizacionFrances = [];
        $saldoPendiente = $montoPrestamo;

        for ($semana = 1; $semana <= $plazoSemanas; $semana++) {
            // Actualizar saldo pendiente
            $saldoPendiente -= $amortizacionSemanal;

            // Calcular fecha de vencimiento
            $fechaVencimiento = new DateTime();
            $fechaVencimiento->setDate($fechaVencimiento->format("Y"), $fechaVencimiento->format("m"), $fechaVencimiento->format("d"));
            $fechaVencimiento->add(new DateInterval("P{$semana}W"));

            // Ajustar por domingo
            if ($fechaVencimiento->format("w") == 0) {
                $fechaVencimiento->modify("+1 day");
            }

            // Agregar pago a la tabla
            $pago = [
                "periodo" => $semana,
                "fechaVencimiento" => $fechaVencimiento->format("d/m/Y"),
                "saldoCapital" => number_format($saldoPendiente, 2),
                "amortizacion" => $amortizacionSemanal,
                "interes" => number_format($interesSemanal, 2),
                "cuota" => number_format($cuotaSemanal, 2),
            ];

            $amortizacionFrances[] = $pago;
        }
        return str_replace(",", "", number_format($cuotaSemanal, 2)) ;
    }

    public static function calcularAmortizacionFrancesQuincenal($montoPrestamo, $plazoQuincenas, $tasaInteres)
    {
        $montoPrestamo = floatval(str_replace(",", "", $montoPrestamo));
        $plazoQuincenas = floatval(str_replace(",", "", $plazoQuincenas));
        $tasaInteres = floatval(str_replace(",", "", $tasaInteres));

        $cuotaQuincenal = ($montoPrestamo * ($tasaInteres / 100) + $montoPrestamo) / $plazoQuincenas;

        return str_replace(",", "", number_format($cuotaQuincenal, 2));
    }

    public static function calcularAmortizacionFrancesDiaria($montoPrestamo, $plazoDias, $tasaInteresDiaria)
    {
        $montoPrestamo = floatval(str_replace(",", "", $montoPrestamo));
        $plazoDias = floatval(str_replace(",", "", $plazoDias));
        $tasaInteresDiaria = floatval(str_replace(",", "", $tasaInteresDiaria));
        // Cálculo de la cuota diaria
        $cuotaDiaria = ($montoPrestamo * ($tasaInteresDiaria / 100) + $montoPrestamo) / $plazoDias;

        // Fecha de desembolso
        $fechaDesembolso = date("d/m/Y");

        // Interés diario
        $interesDiario = ($montoPrestamo * ($tasaInteresDiaria / 100)) / $plazoDias;

        // Amortización diaria
        $amortizacionDiaria =    $cuotaDiaria - $interesDiario;

        // Tabla de amortización
        $amortizacionFrances = [];
        $saldoPendiente = $montoPrestamo;

        for ($dia = 1; $dia <= $plazoDias; $dia++) {
            // Actualizar saldo pendiente
            $saldoPendiente -= $amortizacionDiaria;

            // Calcular fecha de vencimiento
            $fechaVencimiento = new DateTime();
            $fechaVencimiento->setDate($fechaVencimiento->format("Y"), $fechaVencimiento->format("m"), $fechaVencimiento->format("d"));
            $fechaVencimiento->add(new DateInterval("P{$dia}D"));

            // Ajustar por domingo
            if ($fechaVencimiento->format("w") == 0) {
                $fechaVencimiento->modify("+1 day");
            }

            // Agregar pago a la tabla
            $pago = [
                "periodo" => $dia,
                "fechaVencimiento" => $fechaVencimiento->format("d/m/Y"),
                "saldoCapital" => number_format($saldoPendiente, 2),
                "amortizacion" => $amortizacionDiaria,
                "interes" => number_format($interesDiario, 2),
                "cuota" => number_format($cuotaDiaria, 2),
            ];

            $amortizacionFrances[] = $pago;
        }
        return  str_replace(",", "", number_format($cuotaDiaria, 2));
    }
}
