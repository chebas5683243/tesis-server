<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parameter extends Model
{
    use HasFactory;

    protected $table = 'parameter';
    public $timestamps = false;

    public function unidad() {
        return $this->belongsTo('App\Models\UnitMeasurement','unit_id');
    }

    public function tipos_incidentes() {
        return $this->belongsToMany('App\Models\IncidentType', 'incident_type_parameter');
    }

    public function parametros_monitoreo() {
        return $this->hasMany('App\Models\MonitoringPointParameter', 'parameter_id');
    }
}
