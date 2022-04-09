<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringPoint extends Model
{
    use HasFactory;

    protected $table = 'monitoring_point';
    
    public function proyecto() {
        return $this->belongsTo('App\Models\Project');
    }

    public function parametros() {
        return $this->belongsToMany('App\Models\Parameter', 'monitoring_point_parameter');
    }
}
