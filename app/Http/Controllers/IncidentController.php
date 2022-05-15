<?php

namespace App\Http\Controllers;

use App\Utils\ApiUtils;
use App\Models\Incident;
use Illuminate\Http\Request;

class IncidentController extends Controller
{
    public function listar() {
        $incidentes = Incident::all();

        return ApiUtils::respuesta(true, [ 'incidentes' => $incidentes]);
    }
}
