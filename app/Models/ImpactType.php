<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImpactType extends Model
{
    use HasFactory;
    
    protected $table = 'impact_type';
    public $timestamps = false;
}
