<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition()
    {
        return [
            'ruc' => $this->faker->numerify('###########'),
            'razon_social' => $this->faker->company,
            'tipo_contribuyente' => $this->faker->randomElement(["Sociedad Civil","Asociación en Participacion","Asociación","Fundación","Instituciones Públicas"]),
            'direccion_fiscal' => $this->faker->streetAddress,
            'distrito_ciudad' => $this->faker->city,
            'departamento' => $this->faker->state,
            'email' => $this->faker->email,
            'numero_telefonico' => $this->faker->tollFreePhoneNumber,
            'es_propia' => 0,
            'estado' => 1
        ];
    }
}
