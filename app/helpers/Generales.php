<?php

namespace App\Helpers;

use Carbon\Carbon;

class Generales
{
    public static function saludo()
    {
        $horaActual = Carbon::now();

        if ($horaActual->hour >= 6 && $horaActual->hour < 12) {
            // Estamos en la mañana
            return 'Buenos días';
        } elseif ($horaActual->hour >= 12 && $horaActual->hour < 18) {
            // Estamos en la tarde
            return 'Buenas tardes';
        } else {
            // Estamos en la noche
            return 'Buenas noches';
        }
    }
}
