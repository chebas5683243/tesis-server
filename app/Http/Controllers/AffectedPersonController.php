<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AffectedPerson;

class AffectedPersonController extends Controller
{
    public function getAffectedPeopleByInvestigationId($id) {
        $personas = AffectedPerson::select([
                'id',
                'nombre_completo',
                'dni',
                'descripcion'
            ])
            ->where('investigation_id', $id)
            ->get();

        return $personas;
    }
}
