<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\UnitMeasurement;
use App\Utils\ApiUtils;

class UnitMeasurementController extends Controller
{
    public function listar() {
        $unidades = UnitMeasurement::with('parametros:id,unit_id')->orderBy('nombre')->get();

        foreach($unidades as $unidad) {
            $unidad->can_delete = count($unidad->parametros) === 0;
            unset($unidad->parametros);
        }

        return ApiUtils::respuesta(true, ['unidades' => $unidades]);
    }

    public function simpleListar() {
        try {
            $unidades = UnitMeasurement::select('id','nombre','nombre_corto')->get();
            foreach($unidades as $unidad) {
                $unidad->label = $unidad->nombre . " (" . $unidad->nombre_corto . ")";
                if($unidad->nombre_corto === "-") $unidad->nombre_corto = "";
                unset($unidad->nombre);
            }
            $unidades->prepend([
                "nombre_corto" => "",
                "label" => "Selecciona una unidad",
                "id" => 0
            ]);
        }
        catch (Exception $ex) {
            return ApiUtils::respuesta(false);
        }
        return ApiUtils::respuesta(true, ['unidades' => $unidades]);
    }

    public function crear(Request $request) {
        $unidad = new UnitMeasurement;

        $unidad->nombre = $request->nombre;
        $unidad->nombre_corto = $request->nombre_corto;
        $unidad->timestamps = false;

        $unidad->save();

        return ApiUtils::respuesta(true, ['unidad' => $unidad]);
    }

    public function detalle($id) {
        $unidad = UnitMeasurement::find($id);

        return ApiUtils::respuesta(true, ['unidad' => $unidad]);
    }

    public function editar(Request $request) {
        $unidad = UnitMeasurement::find($request->id);

        $unidad->nombre = $request->nombre;
        $unidad->nombre_corto = $request->nombre_corto;
        $unidad->timestamps = false;

        $unidad->save();

        return ApiUtils::respuesta(true, ['unidad' => $unidad]);
    }

    public function eliminar($id) {
        $unidad = UnitMeasurement::find($id);
        $unidad->delete();
        return ApiUtils::respuesta(true);
    }
}
