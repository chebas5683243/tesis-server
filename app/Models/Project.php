<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'project';

    public function empresa_ejecutora() {
        return $this->belongsTo('App\Models\Company','empresa_ejecutora_id');
    }

    public function responsable_externo() {
        return $this->belongsTo('App\Models\User','responsable_externo_id');
    }

    public function responsable_propio() {
        return $this->belongsTo('App\Models\User','responsable_propio_id');
    }

    public function fases() {
        return $this->hasMany('App\Models\Phase','project_id');
    }

    public function puntos() {
        return $this->hasMany('App\Models\MonitoringPoint', 'project_id');
    }

    public function incidentes() {
        return $this->hasMany('App\Models\Incident', 'project_id');
    }
}
