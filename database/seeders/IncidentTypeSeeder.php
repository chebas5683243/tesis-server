<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IncidentType;

class IncidentTypeSeeder extends Seeder
{
    public function run()
    {
        IncidentType::factory()->count(100)->create();
    }
}
