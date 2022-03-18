<?php

namespace Database\Factories;

use App\Models\Phase;
use Illuminate\Database\Eloquent\Factories\Factory;

class PhaseFactory extends Factory
{
    protected $model = Phase::class;

    public function definition()
    {
        return [
            'nombre' => $this->faker->sentence(1, true),
            'descripcion' => $this->faker->sentence(5, true),
            'estado' => 1,
            'project_id' => $this->faker->numberBetween(1, 100)
        ];
    }
}
