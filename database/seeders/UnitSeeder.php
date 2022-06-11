<?php

namespace Database\Seeders;

use App\Models\UnitMeasurement;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UnitMeasurement::create([
            'nombre' => 'Adimensional',
            'nombre_corto' => '-'
        ]);
        UnitMeasurement::create([
            'nombre' => 'Kilogramo',
            'nombre_corto' => 'Kg'
        ]);
        UnitMeasurement::create([
            'nombre' => 'Gramo',
            'nombre_corto' => 'g'
        ]);
        UnitMeasurement::create([
            'nombre' => 'Miligramo',
            'nombre_corto' => 'mg'
        ]);
        UnitMeasurement::create([
            'nombre' => 'Microgramo',
            'nombre_corto' => 'µg'
        ]);
        UnitMeasurement::create([
            'nombre' => 'Litro',
            'nombre_corto' => 'L'
        ]);
        UnitMeasurement::create([
            'nombre' => 'Mililitro',
            'nombre_corto' => 'mL'
        ]);
        UnitMeasurement::create([
            'nombre' => 'Partes por millón',
            'nombre_corto' => 'ppm'
        ]);
        UnitMeasurement::create([
            'nombre' => 'Partes por billón',
            'nombre_corto' => 'ppb'
        ]);
        UnitMeasurement::create([
            'nombre' => 'Kilogramo por metro cúbico',
            'nombre_corto' => 'Kg/m³'
        ]);
        UnitMeasurement::create([
            'nombre' => 'Gramo por metro cúbico',
            'nombre_corto' => 'g/m³'
        ]);
        UnitMeasurement::create([
            'nombre' => 'Miligramo por metro cúbico',
            'nombre_corto' => 'mg/m³'
        ]);
        UnitMeasurement::create([
            'nombre' => 'Microgramo por metro cúbico',
            'nombre_corto' => 'µg/m³'
        ]);
        UnitMeasurement::create([
            'nombre' => 'Kilogramo por centimetro cúbico',
            'nombre_corto' => 'Kg/cm³'
        ]);
        UnitMeasurement::create([
            'nombre' => 'Gramo por centimetro cúbico',
            'nombre_corto' => 'g/cm³'
        ]);
        UnitMeasurement::create([
            'nombre' => 'Miligramo por centimetro cúbico',
            'nombre_corto' => 'mg/cm³'
        ]);
        UnitMeasurement::create([
            'nombre' => 'Microgramo por centimetro cúbico',
            'nombre_corto' => 'µg/cm³'
        ]);
        UnitMeasurement::create([
            'nombre' => 'Kilogramo por litro',
            'nombre_corto' => 'Kg/L'
        ]);
        UnitMeasurement::create([
            'nombre' => 'Gramo por litro',
            'nombre_corto' => 'g/L'
        ]);
        UnitMeasurement::create([
            'nombre' => 'Miligramo por litro',
            'nombre_corto' => 'mg/L'
        ]);
        UnitMeasurement::create([
            'nombre' => 'Microgramo por litro',
            'nombre_corto' => 'µg/L'
        ]);
    }
}
