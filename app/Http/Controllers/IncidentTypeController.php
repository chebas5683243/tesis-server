<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;

use App\Models\IncidentType;
use App\Utils\ApiUtils;

class IncidentTypeController extends Controller
{
    public function listar(){
        try {
            $tipos_incidente = IncidentType::with(['parametros:id','personas_alertas:id'])->orderBy('nombre','asc')->get();
            foreach($tipos_incidente as $tipo_incidente) {
                $tipo_incidente->nparametros = count($tipo_incidente->parametros);
                $tipo_incidente->npersonas = count($tipo_incidente->personas_alertas);
                unset($tipo_incidente->parametros);
                unset($tipo_incidente->personas_alertas);
            }
        }
        catch (Exception $ex) {
            return ApiUtils::respuesta(false);
        }

        return ApiUtils::respuesta(true, ['tipos_incidente' => $tipos_incidente]);
    }

    public function simpleListar() {
        try { 
            $tipos_incidente = IncidentType::select('id','nombre')->orderBy('nombre','asc')->get();
            foreach($tipos_incidente as $tipo_incidente) {
                $tipo_incidente->label = $tipo_incidente->nombre;
                unset($tipo_incidente->nombre);
            }
            $tipos_incidente[] = [
                "label" => "Selecciona un tipo de incidente",
                "id" => 0
            ];
        }
        catch (Exception $ex) {
            return ApiUtils::respuesta(false);
        }
        return ApiUtils::respuesta(true, ['tipos_incidente' => $tipos_incidente]);
    }

    public function crear(Request $request) {
        $tipo_incidente = new IncidentType;

        // $empresa->ruc = $request->ruc;
        // $empresa->razon_social = $request->razon_social;
        // $empresa->tipo_contribuyente = $request->tipo_contribuyente;
        // $empresa->direccion_fiscal = $request->direccion_fiscal;
        // $empresa->distrito_ciudad = $request->distrito_ciudad;
        // $empresa->departamento = $request->departamento;
        // $empresa->email = $request->email;
        // $empresa->numero_telefonico = $request->numero_telefonico;
        // $empresa->es_propia = 0;
        // $empresa->estado = 1;

        // $empresa->save();

        return ApiUtils::respuesta(true, ['tipo_incidente' => $tipo_incidente]);
    }

    public function detalle($id){
        try {
            $tipo_incidente = IncidentType::with(['parametros:id,nombre','personas_alertas'])->where('id',$id)->first();
            foreach($tipo_incidente->parametros as $parametro) {
                unset($parametro->pivot);
            }
            foreach($tipo_incidente->personas_alertas as $persona_alerta) {
                unset($persona_alerta->pivot);
            }
        }
        catch (Exception $ex) {
            return ApiUtils::respuesta(false);
        }
        
        return ApiUtils::respuesta(true, ['tipo_incidente' => $tipo_incidente]);
    }

    public function editar(Request $request) {
        $tipo_incidente = IncidentType::find($request->id);

        // $empresa->ruc = $request->ruc;
        // $empresa->razon_social = $request->razon_social;
        // $empresa->tipo_contribuyente = $request->tipo_contribuyente;
        // $empresa->direccion_fiscal = $request->direccion_fiscal;
        // $empresa->distrito_ciudad = $request->distrito_ciudad;
        // $empresa->departamento = $request->departamento;
        // $empresa->email = $request->email;
        // $empresa->numero_telefonico = $request->numero_telefonico;

        // $empresa->save();

        return ApiUtils::respuesta(true, ['tipo_incidente' => $tipo_incidente]);
    }

    public function activar($id) {
        try {
            $tipo_incidente = IncidentType::find($id);
        }
        catch (Exception $ex) {
            return ApiUtils::respuesta(false);
        }

        $tipo_incidente->estado_alerta = 1;

        $tipo_incidente->save();

        return ApiUtils::respuesta(true, ['tipo_incidente' => $tipo_incidente]);
    }

    public function desactivar($id) {
        try {
            $tipo_incidente = IncidentType::find($id);
        }
        catch (Exception $ex) {
            return ApiUtils::respuesta(false);
        }

        $tipo_incidente->estado_alerta = 0;

        $tipo_incidente->save();

        return ApiUtils::respuesta(true, ['tipo_incidente' => $tipo_incidente]);
    }
}
