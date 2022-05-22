<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImmediateCause extends Model
{
    use HasFactory;
    
    protected $table = 'immediate_cause';
    public $timestamps = false;

    public function tipo () {
        return $this->belongsTo('App\Models\CauseType','cause_type_id');
    }
}
