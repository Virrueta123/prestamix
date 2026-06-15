<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\cliente>
 */
class clienteFactory extends Factory
{
    protected $model = Cliente::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $faker =  Faker::create('es_PE');
        return [
            "cli_nombre"=>$faker->firstName(),
            "cli_apellido"=>$faker->lastName(),
            "cli_dni"=>$faker->numberBetween(45259751, 98975989),
            "cli_domicilio"=>$faker->address(),
            "cli_direccion_trabajo"=>$faker->streetAddress(),
            "cli_sexo"=>$faker->randomElement(['M','F']),
            "tipo_cliente"=>$faker->randomElement(['particular','tarjeta privada','tarjeta estado']),
            "sucursal_id"=>1,
            "fecha_nacimiento"=>$faker->date('Y-m-d')
        ];
    }
}
