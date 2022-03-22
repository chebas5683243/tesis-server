<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentType extends Model
{
    use HasFactory;

    protected $table = 'incident_type';
    public $timestamps = false;

    public function parametros() {
        return $this->belongsToMany('App\Models\Parameter', 'incident_type_parameter');
    }

    public function personas_alertas() {
        return $this->hasMany('App\Models\Personal');
    }
}
