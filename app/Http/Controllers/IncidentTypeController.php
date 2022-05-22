<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;

use App\Models\IncidentType;
use App\Models\Personal;
use App\Utils\ApiUtils;

class IncidentTypeController extends Controller
{
    public function listar(){
        try {
            $tipos_incidente = IncidentType::with(['parametros:id','personas_alertas'])->orderBy('nombre','asc')->get();
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

            $tipos_incidente->prepend([
                "id" => 0,
                "label" => "Selecciona un tipo de incidente"
            ]);
        }
        catch (Exception $ex) {
            return ApiUtils::respuesta(false);
        }
        return ApiUtils::respuesta(true, ['tipos_incidente' => $tipos_incidente]);
    }

    public function crear(Request $request) {
        $tipo_incidente = new IncidentType;

        $tipo_incidente->nombre = $request->nombre;
        $tipo_incidente->estado_alerta = 1;
        $tipo_incidente->save();

        foreach($request->personas_alertas as $persona_alerta) {
            $nuevaPersona = new Personal;
            $nuevaPersona->nombre_completo = $persona_alerta["nombre_completo"];
            $nuevaPersona->email = $persona_alerta["email"];
            $nuevaPersona->incident_type_id = $tipo_incidente->id;
            $nuevaPersona->save();
        }
        
        foreach($request->parametros as $parametro) {
            $tipo_incidente->parametros()->attach($parametro["parametro"]["id"]);
        }

        return ApiUtils::respuesta(true, ['tipo_incidente' => $tipo_incidente]);
    }

    public function detalle($id){
        try {
            $tipo_incidente = IncidentType::with(['parametros:id,nombre','personas_alertas'])->where('id',$id)->first();
            $count = 1;
            foreach($tipo_incidente->parametros as $parametro) {
                $parametro->parametro = [
                    "id" => $parametro->id,
                    "label" => $parametro->nombre
                ];
                $parametro->id = $count;
                unset($parametro->pivot,$parametro->nombre);
                $parametro->existente = true;
                $count++;
            }
            foreach($tipo_incidente->personas_alertas as $persona) {
                $persona->existente = true;
            }
        }
        catch (Exception $ex) {
            return ApiUtils::respuesta(false);
        }
        
        return ApiUtils::respuesta(true, ['tipo_incidente' => $tipo_incidente]);
    }

    public function editar(Request $request) {
        $tipo_incidente = IncidentType::find($request->id);

        $tipo_incidente->nombre = $request->nombre;

        foreach($request->personas_alertas as $persona_alerta) {
            if(isset($persona_alerta["eliminado"])) {
                Personal::destroy($persona_alerta["id"]);
            }
            else if(isset($persona_alerta["creado"])){
                $nuevaPersona = new Personal;
                $nuevaPersona->nombre_completo = $persona_alerta["nombre_completo"];
                $nuevaPersona->email = $persona_alerta["email"];
                $nuevaPersona->incident_type_id = $tipo_incidente->id;
                $nuevaPersona->save();
            }
        }

        foreach($request->parametros as $parametro) {
            if(isset($parametro["eliminado"])) {
                $tipo_incidente->parametros()->detach($parametro["parametro"]["id"]);
            }
            else if(isset($parametro["creado"])){
                $tipo_incidente->parametros()->attach($parametro["parametro"]["id"]);
            }
        }

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
