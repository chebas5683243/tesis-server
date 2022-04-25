<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringPointParameterRegister extends Model
{
    use HasFactory;

    protected $table = 'monitoring_point_parameter_register';

    public function registro() {
        return $this->belongsTo('App\Models\Record','record_id');
    }

    public function puntoParametro() {
        return $this->belongsTo('App\Models\MonitoringPointParameter','mpp_id');
    }
}
