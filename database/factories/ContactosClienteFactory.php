<?php

namespace Database\Factories;

use App\Models\ContactosCliente;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContactosCliente>
 */
class ContactosClienteFactory extends Factory
{
    protected $model = ContactosCliente::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $faker =  Faker::create('es_PE');
        return [
            "ccliente_contacto"=>$faker->numberBetween(912365896, 992995899),
            "ccliente_descripcion"=>"sin descripcion", 
        ];
    }
}
