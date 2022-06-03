<?php

namespace App\Http\Controllers;

use App\Utils\ApiUtils;
use App\Models\ActionType;
use Illuminate\Http\Request;

class ActionTypeController extends Controller
{
    public function listar() {
        $tipos_accion = ActionType::select(['id', 'descripcion as label'])->get();

        $tipos_accion->prepend([
            "id" => 0,
            "label" => "Selecciona un tipo de acción"
        ]);

        return ApiUtils::respuesta(true, ['tipos_accion' => $tipos_accion]);
    }
}
