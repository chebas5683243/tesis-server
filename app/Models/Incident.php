<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    use HasFactory;

    protected $table = 'incident';

    public function proyecto() {
        return $this->belongsTo('App\Models\Project','project_id');
    }

    public function reportante() {
        return $this->belongsTo('App\Models\User','reportante_id');
    }

    public function tipoIncidente() {
        return $this->belongsTo('App\Models\IncidentType','incident_type_id');
    }

    public function investigacion() {
        return $this->belongsTo('App\Models\Investigation','investigation_id');
    }

    public function causas() {
        return $this->hasMany('App\Models\ImmediateCause','incident_id');
    }

    public function accionesInmediatas() {
        return $this->hasMany('App\Models\ImmediateAction','incident_id');
    }

    public function punto() {
        return $this->belongsTo('App\Models\MonitoringPoint','monitoring_point_id');
    }
}
