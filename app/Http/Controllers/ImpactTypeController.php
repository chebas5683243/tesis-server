<?php

namespace App\Http\Controllers;

use App\Utils\ApiUtils;
use App\Models\ImpactType;
use Illuminate\Http\Request;

class ImpactTypeController extends Controller
{
    public function listar() {
        $tipos_impacto = ImpactType::select(['id', 'descripcion as label'])->get();

        $tipos_impacto->prepend([
            "id" => 0,
            "label" => "Selecciona un tipo de impacto"
        ]);

        return ApiUtils::respuesta(true, ['tipos_impacto' => $tipos_impacto]);
    }
}
