<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Parameter;
use App\Utils\ApiUtils;

class ParameterController extends Controller
{
    public function listar() {
        $parametros = Parameter::with(['unidad'])->orderBy('nombre')->get();

        foreach($parametros as $parametro) {
            $parametro->unidadMedida = $parametro->unidad->nombre . " (" . $parametro->unidad->nombre_corto . ")";
            unset($parametro->unidad);
            unset($parametro->tiene_maximo,$parametro->tiene_minimo,$parametro->valor_maximo,$parametro->valor_minimo);
        }

        return ApiUtils::respuesta(true, ['parametros' => $parametros]);
    }

    public function crear(Request $request) {
        $parametro = new Parameter;

        $parametro->nombre = $request->nombre;
        $parametro->nombre_corto = $request->nombre_corto;
        $parametro->unidad()->associate($request->unidad["id"]);
        $parametro->usa_estandar = $request->usa_estandar;
        $parametro->usa_aqi = $request->usa_aqi;
        $parametro->usa_wqi = $request->usa_wqi;
        $parametro->no_aplica = $request->no_aplica;
        $parametro->tiene_maximo = false;
        $parametro->tiene_minimo = false;

        if($parametro->usa_estandar){
            $parametro->tiene_maximo = $request->tiene_maximo;
            if($request->tiene_maximo) $parametro->valor_maximo = $request->valor_maximo;
            $parametro->tiene_minimo = $request->tiene_minimo;
            if($request->tiene_minimo) $parametro->valor_minimo = $request->valor_minimo;
        }
        else if($parametro->usa_aqi) {
            $parametro->aqi_1 = $request->aqi_1;
            $parametro->aqi_2 = $request->aqi_2;
            $parametro->aqi_3 = $request->aqi_3;
            $parametro->aqi_4 = $request->aqi_4;
            $parametro->aqi_5 = $request->aqi_5;
        } else if($parametro->usa_wqi) {
            $parametro->valor_ideal = $request->valor_ideal;
        }

        $parametro->timestamps = false;

        $parametro->save();

        return ApiUtils::respuesta(true, ['parametro' => $parametro]);
    }

    public function detalle($id) {
        $parametro = Parameter::with(['unidad'])->where('id',$id)->first();
        if($parametro->usa_estandar) $parametro->modo_parametros = "usa_estandar";
        else if($parametro->usa_aqi) $parametro->modo_parametros = "usa_aqi";
        else if($parametro->usa_wqi) $parametro->modo_parametros = "usa_wqi";
        else $parametro->modo_parametro = "no_aplica";

        $parametro->unidad->label = $parametro->unidad->nombre . " (" . $parametro->unidad->nombre_corto . ")";
        
        unset($parametro->unidad->nombre, $parametro->unidad->nombre_corto);

        return ApiUtils::respuesta(true, ['parametro' => $parametro]);
    }

    public function editar(Request $request) {
        $parametro = Parameter::find($request->id);

        $parametro->nombre = $request->nombre;
        $parametro->nombre_corto = $request->nombre_corto;
        $parametro->unidad()->associate($request->unidad["id"]);

        $parametro->usa_estandar = false;
        $parametro->usa_aqi = false;
        $parametro->usa_wqi = false;
        $parametro->no_aplica = false;

        $parametro->aqi_1 = null;
        $parametro->aqi_2 = null;
        $parametro->aqi_3 = null;
        $parametro->aqi_4 = null;
        $parametro->aqi_5 = null;
        $parametro->valor_ideal = null;
        $parametro->tiene_maximo = false;
        $parametro->valor_maximo = null;
        $parametro->tiene_minimo = false;
        $parametro->valor_minimo = null;

        if($request->usa_estandar){
            $parametro->usa_estandar = true;
            $parametro->tiene_maximo = $request->tiene_maximo;
            if($request->tiene_maximo) $parametro->valor_maximo = $request->valor_maximo;
            else $parametro->valor_maximo = null;
            $parametro->tiene_minimo = $request->tiene_minimo;
            if($request->tiene_minimo) $parametro->valor_minimo = $request->valor_minimo;
            else $parametro->valor_minimo = null;
        }
        else if($request->usa_aqi) {
            $parametro->usa_aqi = true;
            $parametro->aqi_1 = $request->aqi_1;
            $parametro->aqi_2 = $request->aqi_2;
            $parametro->aqi_3 = $request->aqi_3;
            $parametro->aqi_4 = $request->aqi_4;
            $parametro->aqi_5 = $request->aqi_5;
        }
        else if($request->usa_wqi) {
            $parametro->usa_wqi = true;
            $parametro->valor_ideal = $request->valor_ideal;
        }
        else {
            $parametro->no_aplica = true;
        }
        
        $parametro->timestamps = false;

        $parametro->save();

        return ApiUtils::respuesta(true, ['parametro' => $parametro]);
    }

    public function simpleListar() {
        try { 
            $parametros = Parameter::select('id','nombre')->get();
            foreach($parametros as $parametro) {
                $parametro->label = $parametro->nombre;
                unset($parametro->nombre);
            }
            $parametros[] = [
                "label" => "Selecciona un parámetro",
                "id" => 0
            ];
        }
        catch (Exception $ex) {
            return ApiUtils::respuesta(false);
        }
        return ApiUtils::respuesta(true, ['parametros' => $parametros]);
    }

    public function listarConParametrizacion() {
        try { 
            $parametros = Parameter::with('unidad')->get();
            foreach($parametros as $parametro) {
                if($parametro->usa_estandar) $parametro->modo_parametros = "usa_estandar";
                else if($parametro->usa_aqi) $parametro->modo_parametros = "usa_aqi";
                else if($parametro->usa_wqi) $parametro->modo_parametros = "usa_wqi";
                else $parametro->modo_parametros = "no_aplica";

                $parametro->nombre_unidad = $parametro->unidad->nombre . " (" . $parametro->unidad->nombre_corto . ")";
                if($parametro->unidad->nombre_corto === "-") $parametro->unidad->nombre_corto = "";

                $parametro->label = $parametro->nombre;
            }
            $parametros[] = [
                "label" => "Selecciona un parámetro",
                "id" => 0
            ];
        }
        catch (Exception $ex) {
            return ApiUtils::respuesta(false);
        }
        return ApiUtils::respuesta(true, ['parametros' => $parametros]);
    }
}
