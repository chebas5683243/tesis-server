<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ImmediateCause;

class ImmediateCauseController extends Controller
{
    public function getImmediateCausesByIncidentId($id) {
        $causas = ImmediateCause::select([
                'id',
                'descripcion',
                'cause_type_id'
            ])
            ->with('tipo:id,descripcion as label')
            ->where('incident_id', $id)
            ->get();

        foreach($causas as $causa) {
            unset($causa->cause_type_id);
        }

        return $causas;
    }

    public function getImmediateCausesByInvestigationId($id) {
        $causas = ImmediateCause::select([
                'id',
                'descripcion',
                'cause_type_id'
            ])
            ->with('tipo:id,descripcion as label')
            ->where('investigation_id', $id)
            ->get();

        foreach($causas as $causa) {
            unset($causa->cause_type_id);
        }

        return $causas;
    }
}
