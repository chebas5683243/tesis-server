<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use DateTime;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition()
    {
        $usuariosPropios = User::where('company_id',1)->get();
        $usuarioResponsablePropio = $this->faker->randomElement($usuariosPropios);

        $usuariosExternos = User::where('company_id','!=',1)->get();
        $usuarioResponsableExterno = $this->faker->randomElement($usuariosExternos);

        return [
            'nombre' => $this->faker->sentence(10, true),
            'descripcion' => $this->faker->text(400) ,
            'codigo' => $this->faker->numerify('EV-PRO-######'),
            'fecha_inicio' => new DateTime('2021-09-25 00:00:00'),
            'fecha_fin_tentativa' => new DateTime('2021-11-25 00:00:00'),
            'fecha_fin' => null,
            'ubicacion' => $this->faker->city . ', '. $this->faker->state,
            'estado' => 0,
            'empresa_ejecutora_id' => $usuarioResponsableExterno->company_id,
            'responsable_propio_id' => $usuarioResponsablePropio->id,
            'responsable_externo_id' => $usuarioResponsableExterno->id,
        ];
    }
}
