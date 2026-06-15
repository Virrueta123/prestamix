<?php

namespace Database\Factories;

use App\Models\Solicitud;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\solicitud>
 */
class solicitudFactory extends Factory
{
    protected $model = Solicitud::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $faker =  Faker::create('es_PE');
        return [ 
            "tipo_solicitud" =>"Nuevo",
            "analista_id" =>$this->faker->randomElement([6,3]),  
            "solicitud_conyugue_dni"=>$faker->numberBetween(45259751, 98975989),
            "solicitud_nombre_conyuge" =>$this->faker->firstname() ."".$this->faker->lastName(), 
            "destino" => "no refiere",
            "tipo_vivienda" =>$this->faker->randomElement(['Propia','Alquilada','Familiar','Noble','Rustico']),
            "solicitud_direccion_negocio" =>$this->faker->address,
            "solicitud_referencia_negocio" =>$this->faker->streetAddress, 
            "solicitud_antiguedad" =>"antiguo", 
            "solicitud_giro" =>$this->faker->jobTitle,
            "solicitud_lugar" =>$this->faker->randomElement(['TARAPOTO','BANDA SHILCAYO','MORALES']),
            "solicitud_referencia_cliente" =>"REFERENCIA",
            "solicitud_conyugue_ruc" =>$faker->numberBetween(10236547895, 13236537895), 
            "status" => $this->faker->randomElement(['A','P','R',"G"]),
            "created_user" =>4,
            "sucursal_id"=>1,
            "descripcion_rechazo" =>"se rechazo por motivos desconocidos", 
        ];
       
    }
}
