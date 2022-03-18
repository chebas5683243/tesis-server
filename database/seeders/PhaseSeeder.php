<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Phase;

class PhaseSeeder extends Seeder
{
    public function run()
    {
        Phase::factory()->count(400)->create();
    }
}
