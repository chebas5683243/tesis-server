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
        $parametro->tiene_maximo = $request->tiene_maximo;
        if($request->tiene_maximo) $parametro->valor_maximo = $request->valor_maximo;
        $parametro->tiene_minimo = $request->tiene_minimo;
        if($request->tiene_minimo) $parametro->valor_minimo = $request->valor_minimo;

        $parametro->timestamps = false;

        $parametro->save();

        return ApiUtils::respuesta(true, ['parametro' => $parametro]);
    }

    public function detalle($id) {
        $parametro = Parameter::with(['unidad'])->where('id',$id)->first();

        $parametro->unidad->label = $parametro->unidad->nombre . " (" . $parametro->unidad->nombre_corto . ")";
        
        unset($parametro->unidad->nombre, $parametro->unidad->nombre_corto);

        return ApiUtils::respuesta(true, ['parametro' => $parametro]);
    }

    public function editar(Request $request) {
        $parametro = Parameter::find($request->id);

        $parametro->nombre = $request->nombre;
        $parametro->nombre_corto = $request->nombre_corto;
        $parametro->unidad()->associate($request->unidad["id"]);
        $parametro->tiene_maximo = $request->tiene_maximo;
        if($request->tiene_maximo) $parametro->valor_maximo = $request->valor_maximo;
        else $parametro->valor_maximo = null;
        $parametro->tiene_minimo = $request->tiene_minimo;
        if($request->tiene_minimo) $parametro->valor_minimo = $request->valor_minimo;
        else $parametro->valor_minimo = null;
        
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
}
