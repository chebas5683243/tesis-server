<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phase extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'phase';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
        'inicio',
        'fin',
        'project_id'
    ];

    public function proyecto() {
        return $this->belongsTo('App\Models\Project','project_id');
    }
}
