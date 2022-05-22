<?php

namespace App\Http\Controllers;

use App\Utils\ApiUtils;
use App\Models\CauseType;
use Illuminate\Http\Request;

class CauseTypeController extends Controller
{
    public function listar() {
        $tipos_causa = CauseType::select(['id', 'descripcion as label'])->get();

        $tipos_causa->prepend([
            "id" => 0,
            "label" => "Selecciona un tipo de causa inmediata"
        ]);

        return ApiUtils::respuesta(true, ['tipos_causa' => $tipos_causa]);
    }
}
