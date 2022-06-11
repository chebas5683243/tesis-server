<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Record;
use App\Utils\ApiUtils;
use Illuminate\Http\Request;
use App\Models\MonitoringPoint;
use App\Models\MonitoringPointParameter;

class MonitoringPointController extends Controller
{
    public function detalle($id) {
        $punto = MonitoringPoint::with('registros')->where('id',$id)->first();

        $punto->longitud = floatval($punto->longitud);
        $punto->latitud = floatval($punto->latitud);
        $punto->altitud = floatval($punto->altitud);

        $fecha_mas_reciente = null;

        foreach($punto->registros as $registro) {
            $fecha = Carbon::createFromFormat('Y-m-d H:i:s', $registro->fecha_registro);
            if ($fecha_mas_reciente) {
                if ($fecha->gt($fecha_mas_reciente)) {
                    $fecha_mas_reciente = $fecha;
                }
            }
            else {
                $fecha_mas_reciente = $fecha;    
            }   
        }

        $punto->fecha_mas_reciente = null;

        if ($fecha_mas_reciente) {
            $punto->fecha_mas_reciente = [
                'fecha' => $fecha_mas_reciente->format('d-m-Y'),
                'hora' => $fecha_mas_reciente->format('H:i:s')
            ];
        }

        unset($punto->registros);

        return ApiUtils::respuesta(true, ['punto' => $punto]);
    }

    public function crear(Request $request) {
        $punto = new MonitoringPoint;

        $punto->nombre = $request->nombre;
        $punto->longitud = $request->longitud;
        $punto->latitud = $request->latitud;
        $punto->altitud = $request->altitud;
        $punto->project_id = $request->project_id;
        $punto->estado = 1;

        $punto->save();

        $punto->codigo = 'EV-PMA-' . str_pad($punto->id, 6, '0', STR_PAD_LEFT);

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
        $parametros = MonitoringPointParameter::with('parametro.unidad')->where('monitoring_point_id', $id)->get();

        foreach($parametros as $parametro) {
            unset($parametro->updated_at, $parametro->created_at, $parametro->id, $parametro->monitoring_point_id);
            $parametro->id = $parametro->parameter_id;
            unset($parametro->parameter_id);

            $parametro->nombre = $parametro->parametro->nombre;
            $parametro->nombre_corto = $parametro->parametro->nombre_corto;
            $parametro->unidad = $parametro->parametro->unidad;
            $parametro->nombre_unidad = $parametro->parametro->unidad->nombre . " (" . $parametro->parametro->unidad->nombre_corto . ")";
            if($parametro->parametro->unidad->nombre_corto === "-") $parametro->unidad->nombre_corto = "";
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
            'valor_estandar_permisible' => $request->valor_estandar_permisible,
            'tiene_maximo' => $request->tiene_maximo,
            'valor_maximo' => $request->valor_maximo,
            'tiene_minimo' => $request->tiene_minimo,
            'valor_minimo' => $request->valor_minimo,
            'usa_estandar' => $request->usa_estandar,
            'usa_aqi' => $request->usa_aqi,
            'usa_wqi' => $request->usa_wqi,
            'no_aplica' => $request->no_aplica,
            'updated_at' => Carbon::now()
        ];

        $update_result = $punto->parametros()->updateExistingPivot($request->id, $parametrizacion);

        if($update_result === 0) {
            $punto->parametros()->attach($request->id, $parametrizacion);
        }

        return ApiUtils::respuesta(true);
    }

    public function listarRegistros($id) {
        $registros = Record::with([
            'registrador:id,primer_nombre,segundo_nombre,primer_apellido,segundo_apellido',
            'valoresParametros:id,record_id,valor_cuantitativo,valor_cualitativo'
        ])->where('monitoring_point_id', $id)->get();

        foreach($registros as $registro) {
            $parametros_considerados = 0;
            $total_parametros = 0;
            foreach($registro->valoresParametros as $valorParametro) {
                $total_parametros++;
                if($valorParametro->valor_cuantitativo !== null || $valorParametro->valor_cualitativo !== null) {
                    $parametros_considerados++;
                }
            }
            $registro->parametros_considerados = $parametros_considerados;
            $registro->total_parametros = $total_parametros;
            $registro->registrado_por = $registro->registrador->primer_nombre . ' ' . $registro->registrador->segundo_nombre . ' ' .
                $registro->registrador->primer_apellido . ' ' . $registro->registrador->segundo_apellido;
            unset($registro->valoresParametros, $registro->registrador);
        }
    
        return ApiUtils::respuesta(true, ['registros' => $registros]);
    }

    public function desactivar($id) {
        $punto = MonitoringPoint::find($id);
        $punto->estado = 0;
        $punto->save();

        return ApiUtils::respuesta(true);
    }

    public function activar($id) {
        $punto = MonitoringPoint::find($id);
        $punto->estado = 1;
        $punto->save();

        return ApiUtils::respuesta(true);
    }
}
