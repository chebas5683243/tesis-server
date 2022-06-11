<?php

namespace Database\Seeders;

use App\Models\Parameter;
use Illuminate\Database\Seeder;

class ParameterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createWQI('pH', 'pH', 1, 7, 8.5);
        $this->createWQI('Cloruros', 'Cloruros', 20, 0, 250);
        $this->createWQI('Sulfatos', 'Sulfatos', 20, 0, 250);
        $this->createWQI('Dureza', 'Sulfatos', 20, 0, 500);
        $this->createWQI('Nitratos', 'NO₃-', 20, 0, 50);
        $this->createWQI('Hierro', 'Fe', 20, 0, 0.3);
        $this->createWQI('Manganeso', 'Mn', 20, 0, 0.2);
        $this->createWQI('Aluminio', 'Mn', 20, 0, 0.2);
        $this->createWQI('Cobre', 'Cu', 20, 0, 3);
        $this->createWQI('Plomo', 'Pb', 20, 0, 0.1);
        $this->createWQI('Cadmio', 'Cd', 20, 0, 0.003);
        $this->createWQI('Arsénico', 'As', 20, 0, 0.1);
        $this->createWQI('Mercurio', 'Hg', 20, 0, 0.001);
        $this->createWQI('Cromo', 'Cr', 20, 0, 0.05);
        $this->createWQI('Flúor', 'F', 20, 0, 2);
        $this->createWQI('Selenio', 'Se', 20, 0, 0.05);
        $this->createAQI('Material particulado', 'PM-10', 13, 50, 150, 250, 350, 420);
        $this->createAQI('Dióxido de azufre', 'SO₂', 13, 80, 365, 500, 1500, 2500);
        $this->createAQI('Monóxido de carbono', 'CO', 13, 10000, 15000, 20000, 30000, 35000);
        $this->createAQI('Sulfuro de hidrógeno', 'H₂S', 13, 50, 150, 1500, 3000, 5000);
        $this->createEstandar('Zinc sólido', 'Zn-s', 11, 1, 140, 0, null);
        $this->createEstandar('Cadmio sólido', 'Cd-s', 11, 1, 140, 0, null);
        $this->createEstandar('Níquel', 'Cd-s', 11, 1, 140, 1, 10);
        $this->createNoAplica('Color', 'Color');
    }

    public function createWQI($nombre, $nombre_corto, $unit_id, $valor_ideal, $valor_estandar_permisible) {
        Parameter::create([
            'nombre' => $nombre,
            'nombre_corto' => $nombre_corto,
            'tiene_maximo' => 0,
            'valor_maximo' => null,
            'tiene_minimo' => 0,
            'valor_minimo' => null,
            'unit_id' => $unit_id,
            'aqi_1' => null,
            'aqi_2' => null,
            'aqi_3' => null,
            'aqi_4' => null,
            'aqi_5' => null,
            'valor_ideal' => $valor_ideal,
            'valor_estandar_permisible' => $valor_estandar_permisible,
            'usa_estandar' => 0,
            'usa_aqi' => 0,
            'usa_wqi' => 1,
            'no_aplica' => 0
        ]);
    }

    public function createAQI($nombre, $nombre_corto, $unit_id, $aqi_1, $aqi_2, $aqi_3, $aqi_4, $aqi_5) {
        Parameter::create([
            'nombre' => $nombre,
            'nombre_corto' => $nombre_corto,
            'tiene_maximo' => 0,
            'valor_maximo' => null,
            'tiene_minimo' => 0,
            'valor_minimo' => null,
            'unit_id' => $unit_id,
            'aqi_1' => $aqi_1,
            'aqi_2' => $aqi_2,
            'aqi_3' => $aqi_3,
            'aqi_4' => $aqi_4,
            'aqi_5' => $aqi_5,
            'valor_ideal' => null,
            'valor_estandar_permisible' => null,
            'usa_estandar' => 0,
            'usa_aqi' => 1,
            'usa_wqi' => 0,
            'no_aplica' => 0
        ]);
    }

    public function createEstandar($nombre, $nombre_corto, $unit_id, $tiene_maximo, $valor_maximo, $tiene_minimo, $valor_minimo) {
        Parameter::create([
            'nombre' => $nombre,
            'nombre_corto' => $nombre_corto,
            'tiene_maximo' => $tiene_maximo,
            'valor_maximo' => $valor_maximo,
            'tiene_minimo' => $tiene_minimo,
            'valor_minimo' => $valor_minimo,
            'unit_id' => $unit_id,
            'aqi_1' => null,
            'aqi_2' => null,
            'aqi_3' => null,
            'aqi_4' => null,
            'aqi_5' => null,
            'valor_ideal' => null,
            'valor_estandar_permisible' => null,
            'usa_estandar' => 1,
            'usa_aqi' => 0,
            'usa_wqi' => 0,
            'no_aplica' => 0
        ]);
    }

    public function createNoAplica($nombre, $nombre_corto) {
        Parameter::create([
            'nombre' => $nombre,
            'nombre_corto' => $nombre_corto,
            'tiene_maximo' => 0,
            'valor_maximo' => null,
            'tiene_minimo' => 0,
            'valor_minimo' => null,
            'unit_id' => 1,
            'aqi_1' => null,
            'aqi_2' => null,
            'aqi_3' => null,
            'aqi_4' => null,
            'aqi_5' => null,
            'valor_ideal' => null,
            'valor_estandar_permisible' => null,
            'usa_estandar' => 0,
            'usa_aqi' => 0,
            'usa_wqi' => 0,
            'no_aplica' => 1
        ]);
    }
}
