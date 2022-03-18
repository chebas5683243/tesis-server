<?php

namespace Database\Factories;

use App\Models\Personal;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonalFactoyFactory extends Factory
{
    protected $model = Personal::class;
    
    public function definition()
    {
        return [
            'nombre_completo' => $this->faker->name,
            'email' => $this->faker->email
        ];
    }
}
