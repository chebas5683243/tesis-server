<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnvironmentalImpact extends Model
{
    use HasFactory;
    
    protected $table = 'environmental_impact';

    public function tipo () {
        return $this->belongsTo('App\Models\ImpactType','impact_type_id');
    }
}
