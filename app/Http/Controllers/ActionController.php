<?php

namespace App\Http\Controllers;

use App\Models\Action;
use Illuminate\Http\Request;

class ActionController extends Controller
{
    public function getActionsByInvestigationId($id) {
        $acciones = Action::select([
                'id',
                'responsable',
                'fecha_planeada',
                'estado',
                'descripcion',
                'action_type_id'
            ])
            ->with('tipo:id,descripcion as label')
            ->where('investigation_id', $id)
            ->get();

        foreach($acciones as $accion) {
            unset($accion->action_type_id);
        }

        return $acciones;
    }
}
