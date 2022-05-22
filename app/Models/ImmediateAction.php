<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImmediateAction extends Model
{
    use HasFactory;
    
    protected $table = 'immediate_action';
    public $timestamps = false;
}
