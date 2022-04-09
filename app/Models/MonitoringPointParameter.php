<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringPointParameter extends Model
{
    use HasFactory;

    protected $table = 'monitoring_point_parameter';

    public function parametro() {
        return $this->belongsTo('App\Models\Parameter', 'parameter_id');
    }
}
