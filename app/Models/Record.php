<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    protected $table = 'record';

    public function puntoMonitoreo() {
        return $this->belongsTo('App\Models\MonitoringPoint','monitoring_point_id');
    }

    public function registrador() {
        return $this->belongsTo('App\Models\User','registrador_id');
    }

    public function valoresParametros() {
        return $this->hasMany('App\Models\MonitoringPointParameterRegister','record_id');
    }
}
