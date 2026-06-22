<?php

namespace App\Utils;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Crypt;

class funciones
{
    public static function get_by_days_month($year, $month)
    {
        // Crear el primer día del mes
        $start_date = Carbon::create($year, $month, 1);

        // Obtener el último día del mes
        $end_date = $start_date->copy()->endOfMonth();

        // Generar un periodo de fechas desde el inicio hasta el fin del mes
        $period = CarbonPeriod::create($start_date, $end_date);

        // Filtrar los días que no sean domingo y devolver el array de fechas
        $weekdays = [];
        foreach ($period as $date) {
            if (!$date->isSunday()) {
                $weekdays[] = $date->toDateString(); // Formato Y-m-d
            }
        }

        return $weekdays;
    }

    public static function yes_mora($fechaVencimiento, $yes_interes)
    {

        $fechaDada = new Carbon($fechaVencimiento);
        $fechaActual = Carbon::now();

        // Comparar la fecha actual con la fecha dada
        if ($fechaDada->isBefore($fechaActual->startOfDay())) {
            if ($yes_interes == "Y") {
                return true;
            } else {
                return false;
            }
        } else if ($fechaDada->isSameDay($fechaActual)) {
            return false;
        } else {
            return false;
        }
    }

    public static function formatear_dinero_soles($value)
    {
        return 'S/.' . number_format($value, 2, '.', ',');
    }

    public static function colores_calendar($fechaVencimiento)
    {

        $fechaDada = new Carbon($fechaVencimiento);
        $fechaActual = Carbon::now();

        // Comparar la fecha actual con la fecha dada
        if ($fechaDada->isBefore($fechaActual->startOfDay())) {
            return "#DD0506";
        } else if ($fechaDada->isSameDay($fechaActual)) {
            return "#FF8000";
        } else {
            return "#0083D0";
        }
    }

    public static function frecuencia_a($frecuencia)
    {
        switch ($frecuencia) {
            case 'Diario':
                return 'Dias';
            case 'Semanal':
                return 'Semanas';
            case 'Mensual':
                return 'Meses';
            case 'Quincenal':
                return 'Quincenas';
        }
    }


    public static function generar_codigo($periodo)
    {
        // crear un generar_codigo

        // Obtenemos la fecha y hora actual en formato "YmdHis" (AñoMesDíaHoraMinutoSegundo)
        $fechaHora = date("YmdHis");

        // Convertimos la fecha y hora en un array de caracteres
        $caracteresFechaHora = str_split($fechaHora);

        // Generamos un string aleatorio de 6 caracteres adicionales
        $caracteresAdicionales = bin2hex(random_bytes(3));

        // Mezclamos ambos arrays para obtener una secuencia única
        $codigo = array_merge($caracteresFechaHora, str_split($caracteresAdicionales));

        // Barajamos el array para hacer el código impredecible
        shuffle($codigo);

        // Tomamos los primeros 12 caracteres para el código
        $codigo = implode('', array_slice($codigo, 0, 12));

        return $codigo . $periodo;
    }
    public static function redondear($valor)
    {
        // Redondear solo si el decimal es mayor o igual a 0.27
        if ($valor - floor($valor) >= 0.27) {
            // Redondear al siguiente múltiplo de 10
            return ceil($valor * 10) / 10;
        } else {
            // De lo contrario, simplemente redondear al entero más cercano
            return round($valor);
        }
    }
}
