<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Project;

class ProjectSeeder extends Seeder
{
    public function run()
    {
        $company = Company::first();
        $company->es_propia = 1;
        $company->save();

        Project::factory()->count(100)->create();
    }
}
