<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Prestamo;
use App\Utils\Formulas;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Prestamo>
 */
class PrestamoFactory extends Factory
{
    protected $model = Prestamo::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $faker =  Faker::create('es_PE');
        
        $interes =  $faker->randomElement([10, 13, 15]);

        $monto_credito = $faker->randomElement([1000, 2000, 3000,6000,8000,10000,20000,30000]);

        $frecuencia_pagos = 'Quincenal';

        $cuota = 0;
        switch ( $frecuencia_pagos) {
            case 'Diario':
                $intervalo = 30;
                $cuota = Formulas::calcularAmortizacionFrancesDiaria($monto_credito,$intervalo,$interes);
                break; 
                case 'Mensual':
                $monto_credito_mensual = $faker->randomElement([50000,20000,30000,90000]);
                $intervalo =  $faker->randomElement([9,12,15,10]);
                $cuota = Formulas::calcularAmortizacionFrancesMensual($monto_credito_mensual,$intervalo,$interes);
                break; 
                case 'Semanal':
                $intervalo = 4;
                $cuota = Formulas::calcularAmortizacionFrancesDiaria($monto_credito,$intervalo,$interes);
                break;
                case 'Quincenal':
                $intervalo = 6;
                $cuota = Formulas::calcularAmortizacionFrancesQuincenal($monto_credito, $intervalo, $interes);
                break;
        }

        return [  
            "user_id" => 5, 
            "sucursal_id" => 1, 
            "moto_credito" => $faker->randomFloat(),
            "frecuencia_pagos" => $frecuencia_pagos, 
            "cuotas" => $cuota,
            "intervalo" =>  $intervalo,
            "interes" => $interes, 
            "moto_credito" => $monto_credito,  
            "tasa_diaria" =>  number_format( ($interes / 30), 2),
        ];
        
    }
}