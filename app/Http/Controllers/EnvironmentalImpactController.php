<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EnvironmentalImpact;

class EnvironmentalImpactController extends Controller
{
    public function getEnvironmentalImpactByInvestigationId($id) {
        $impactos = EnvironmentalImpact::select([
                'id',
                'descripcion',
                'impact_type_id'
            ])
            ->with('tipo:id,descripcion as label')
            ->where('investigation_id', $id)
            ->get();

        foreach($impactos as $impacto) {
            unset($impacto->impact_type_id);
        }

        return $impactos;
    }
}
