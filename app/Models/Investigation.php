<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investigation extends Model
{
    use HasFactory;
    protected $table = 'investigation';

    public function proyecto() {
        return $this->belongsTo('App\Models\Project','project_id');
    }

    public function tipoIncidente() {
        return $this->belongsTo('App\Models\IncidentType','incident_type_id');
    }
    
    public function incidente() {
        return $this->hasOne('App\Models\Incident','investigation_id');
    }

    public function causas() {
        return $this->hasMany('App\Models\ImmediateCause','investigation_id');
    }

    public function impactos() {
        return $this->hasMany('App\Models\EnvironmentalImpact','investigation_id');
    }

    public function accionesInmediatas() {
        return $this->hasMany('App\Models\ImmediateAction','investigation_id');
    }

    public function punto() {
        return $this->belongsTo('App\Models\MonitoringPoint','monitoring_point_id');
    }
}
