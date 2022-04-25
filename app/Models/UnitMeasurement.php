<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitMeasurement extends Model
{
    use HasFactory;

    protected $table = 'unit_measurement';

    public function parametros() {
        return $this->hasMany('App\Models\Parameter', 'unit_id');
    }
}
