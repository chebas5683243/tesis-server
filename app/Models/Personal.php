<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personal extends Model
{
    use HasFactory;

    protected $table = 'personal';

    public function incidentes_alertas() {
        return $this->belongsToMany('App\Models\IncidentType', 'incident_type_alert');
    }
}
