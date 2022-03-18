<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Personal;

class PersonalSeeder extends Seeder
{
    public function run()
    {
        Personal::factory()->count(1000)->create();
    }
}
