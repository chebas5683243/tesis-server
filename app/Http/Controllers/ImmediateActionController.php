<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ImmediateAction;

class ImmediateActionController extends Controller
{
    public function getImmediateActionsByIncidentId($id) {
        $causas = ImmediateAction::select([
                'id',
                'descripcion',
                'responsable'
            ])
            ->where('incident_id', $id)
            ->get();
                
        return $causas;
    }

    public function getImmediateActionsByInvestigationId($id) {
        $causas = ImmediateAction::select([
                'id',
                'descripcion',
                'responsable'
            ])
            ->where('investigation_id', $id)
            ->get();

        return $causas;
    }
}
