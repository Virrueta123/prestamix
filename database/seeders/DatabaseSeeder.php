<?php

namespace Database\Seeders;

use App\Models\Cliente; 
use App\Models\ContactosCliente;
use App\Models\cronograma;
use App\Models\Prestamo;
use App\Models\Solicitud;
use App\Models\User;
use App\Utils\Formulas;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(3)->create();
        // Define los roles disponibles
        //$roles = ['recepcionista', 'administrator', 'analista'];

        // Crea 6 usuarios y asigna roles específicos
        // User::factory(6)->create()->each(function ($user, $index) use ($roles) {
        //     $user->assignRole($roles[$index % count($roles)]);
        // });
        
        for ($i=0; $i < 500; $i++) { 
            $created_cliente  = Cliente::factory( )->create();

            $created_solicitud = Solicitud::factory( )->create([
                'cli_id' => $created_cliente->cli_id,
                "solicitud_nombre" => $created_cliente->cli_nombre ." ".$created_cliente->cli_apellido,
                "solicitud_domicilio" =>  $created_cliente->cli_domicilio,
                "solicitud_documento" => $created_cliente->cli_dni,
            ]);

            $created_celular = ContactosCliente::factory( )->create([
                'cli_id' => $created_cliente->cli_id, 
            ]);
    
            if($created_solicitud->status == "G" ){
                $created_prestamo = Prestamo::factory( )->create([
                    "cli_id" => $created_solicitud->cli_id,
                    "analista_id" => $created_solicitud->analista_id,
                    "solicitud_id" => $created_solicitud->solicitud_id, 
                    "status" => "A"
                ]);
                $cronogramasData = [];
                $monto_credito_sin_comas = $created_prestamo->moto_credito;
                switch ( $created_prestamo->frecuencia_pagos) {
                    case 'Diario':
                       
                        $cronogramasData = Formulas::calcularAmortizacionFrancesDiaria_cronograma($monto_credito_sin_comas,$created_prestamo->intervalo,$created_prestamo->interes);
                        break; 
                        case 'Mensual':
                   
                        $cronogramasData = Formulas::calcularAmortizacionFrancesMensual_cronograma($monto_credito_sin_comas,$created_prestamo->intervalo,$created_prestamo->interes);
                        break; 
                        case 'Semanal':
                     
                        $cronogramasData = Formulas::calcularAmortizacionFrancesSemanal_cronograma($monto_credito_sin_comas,$created_prestamo->intervalo,$created_prestamo->interes);
                        break; 
                } 

                $cronogramasData = array_map(function ($item, $key) use ($created_prestamo) {
                    $item['prestamo_id'] = $created_prestamo->prestamo_id;
                    return $item;
                }, $cronogramasData, array_keys($cronogramasData));

                $get_prestamo = Prestamo::find($created_prestamo->prestamo_id);
                $get_prestamo->d_t = array_sum(array_column($cronogramasData, 'cuota'));
                $get_prestamo->save();

                cronograma::insert($cronogramasData);

                
            }else{
                $created_prestamo = Prestamo::factory( )->create([
                    "cli_id" => $created_solicitud->cli_id,
                    "analista_id" => $created_solicitud->analista_id,
                    "solicitud_id" => $created_solicitud->solicitud_id,
                ]); 

                $cronogramasData = [];
                $monto_credito_sin_comas = $created_prestamo->moto_credito;
                switch ( $created_prestamo->frecuencia_pagos) {
                    case 'Diario':
                       
                        $cronogramasData = Formulas::calcularAmortizacionFrancesDiaria_cronograma($monto_credito_sin_comas,$created_prestamo->intervalo,$created_prestamo->interes);
                        break; 
                        case 'Mensual':
                   
                        $cronogramasData = Formulas::calcularAmortizacionFrancesMensual_cronograma($monto_credito_sin_comas,$created_prestamo->intervalo,$created_prestamo->interes);
                        break; 
                        case 'Semanal':
                     
                        $cronogramasData = Formulas::calcularAmortizacionFrancesSemanal_cronograma($monto_credito_sin_comas,$created_prestamo->intervalo,$created_prestamo->interes);
                        break; 
                } 

                $cronogramasData = array_map(function ($item, $key) use ($created_prestamo) {
                    $item['prestamo_id'] = $created_prestamo->prestamo_id;
                    return $item;
                }, $cronogramasData, array_keys($cronogramasData));
                
                $get_prestamo = Prestamo::find($created_prestamo->prestamo_id);
                $get_prestamo->d_t = array_sum(array_column($cronogramasData, 'cuota'));
                $get_prestamo->save();
                
            }
            
            $created_solicitud->prestamo_id =$created_prestamo->prestamo_id;
            $created_solicitud->save();
        } 
        
    }
}
