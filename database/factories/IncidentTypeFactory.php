<?php

namespace Database\Factories;

use App\Models\IncidentType;
use Illuminate\Database\Eloquent\Factories\Factory;

class IncidentTypeFactory extends Factory
{
    protected $model = IncidentType::class;

    public function definition()
    {
        return [
            'nombre' => $this->faker->sentence(1, true),
            'estado' => 1
        ];
    }
}
