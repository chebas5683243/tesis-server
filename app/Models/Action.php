<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    use HasFactory;
    
    protected $table = 'action';

    public function tipo () {
        return $this->belongsTo('App\Models\ActionType','action_type_id');
    }
}
