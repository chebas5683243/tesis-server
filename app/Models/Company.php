<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'company';
    // protected $fillable = [];

    public function usuarios() {
        return $this->hasMany('App\Models\User');
    }

}
