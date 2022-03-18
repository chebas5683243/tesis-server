<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentType extends Model
{
    use HasFactory;

    protected $table = 'incident_type';

    public function parametros() {
        return $this->belongsToMany('App\Models\Parameter', 'incident_type_parameter');
    }

    public function personas_alertas() {
        return $this->belongsToMany('App\Models\Personal', 'incident_type_alert');
    }
}
