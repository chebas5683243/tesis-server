<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        $company_id = $this->faker->numberBetween(1, 100);

        return [
            'primer_nombre' => $this->faker->firstName,
            'segundo_nombre' => $this->faker->firstName,
            'primer_apellido' => $this->faker->lastName,
            'segundo_apellido' => $this->faker->lastName,
            'dni' => $this->faker->numerify('########'),
            'codigo' => $this->faker->numerify('EV-USU-######'),
            'email' => $this->faker->email,
            'numero_celular' => $this->faker->tollFreePhoneNumber,
            'cargo' => substr($this->faker->jobTitle,0,45),
            'password' => 'sefles568',
            'tipo' => $company_id === 1 ? 2 : 3,
            'estado' => 1,
            'company_id' => $company_id
        ];
    }
}
