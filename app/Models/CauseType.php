<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CauseType extends Model
{
    use HasFactory;
    
    protected $table = 'cause_type';
    public $timestamps = false;
}
