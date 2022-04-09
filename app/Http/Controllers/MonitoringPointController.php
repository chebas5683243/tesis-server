<?php

namespace App\Http\Controllers;

use App\Models\MonitoringPoint;
use App\Models\MonitoringPointParameter;
use App\Utils\ApiUtils;
use Illuminate\Http\Request;

class MonitoringPointController extends Controller
{
    public function detalle($id) {
        $punto = MonitoringPoint::find($id);

        return ApiUtils::respuesta(true, ['punto' => $punto]);
    }

    public function crear(Request $request) {
        $punto = new MonitoringPoint;

        $punto->nombre = $request->nombre;
        $punto->longitud = $request->longitud;
        $punto->latitud = $request->latitud;
        $punto->altitud = $request->latitud;
        $punto->project_id = $request->project_id;
        $punto->estado = 1;

        $punto->save();

        $punto->codigo = "EV-PMA-" . $punto->id;

        $punto->save();

        return ApiUtils::respuesta(true, ['punto' => $punto]);
    }

    public function editar(Request $request) {
        $punto = MonitoringPoint::find($request->id);

        $punto->nombre = $request->nombre;
        $punto->longitud = $request->longitud;
        $punto->latitud = $request->latitud;
        $punto->altitud = $request->altitud;

        $punto->save();

        return ApiUtils::respuesta(true, ['punto' => $punto]);
    }

    public function listarParametros($id) {
        $parametros = MonitoringPointParameter::with('parametro')->where('monitoring_point_id', $id)->get();

        foreach($parametros as $parametro) {
            unset($parametro->updated_at, $parametro->created_at, $parametro->id, $parametro->monitoring_point_id);
            $parametro->id = $parametro->parameter_id;
            unset($parametro->parameter_id);

            $parametro->nombre = $parametro->parametro->nombre;
            $parametro->nombre_corto = $parametro->parametro->nombre_corto;
            unset($parametro->parametro);

            if($parametro->usa_estandar) $parametro->modo_parametros = "usa_estandar";
            else if($parametro->usa_aqi) $parametro->modo_parametros = "usa_aqi";
            else if($parametro->usa_wqi) $parametro->modo_parametros = "usa_wqi";
            else $parametro->modo_parametros = "no_aplica";
        }

        return ApiUtils::respuesta(true, ['parametros' => $parametros]);
    }

    public function modificarParametro(Request $request) {
        $punto = MonitoringPoint::find($request->puntoId);

        $parametrizacion = [
            'aqi_1' => $request->aqi_1,
            'aqi_2' => $request->aqi_2,
            'aqi_3' => $request->aqi_3,
            'aqi_4' => $request->aqi_4,
            'aqi_5' => $request->aqi_5,
            'valor_ideal' => $request->valor_ideal,
            'tiene_maximo' => $request->tiene_maximo,
            'valor_maximo' => $request->valor_maximo,
            'tiene_minimo' => $request->tiene_minimo,
            'valor_minimo' => $request->valor_minimo,
            'usa_estandar' => $request->usa_estandar,
            'usa_aqi' => $request->usa_aqi,
            'usa_wqi' => $request->usa_wqi,
            'no_aplica' => $request->no_aplica,
        ];

        $update_result = $punto->parametros()->updateExistingPivot($request->id, $parametrizacion);

        if($update_result === 0) {
            $punto->parametros()->attach($request->id, $parametrizacion);
        }

        return ApiUtils::respuesta(true);
    }
}
