<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\Utils\ApiUtils;
use App\Models\Incident;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Investigation;
use App\Models\ImmediateCause;
use App\Models\ImmediateAction;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\ImmediateCauseController;
use App\Http\Controllers\ImmediateActionController;

class IncidentController extends Controller
{
    protected $user;

    public function __construct() {
        $this->middleware('jwt.auth');
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function listar() {

        $userType = $this->user->tipo;
        $whereParams = [];
        if ($userType === 2) {
            $whereParams[] = ['responsable_propio_id', $this->user->id];
        }
        else if ($userType === 3) {
            $whereParams[] = ['responsable_externo_id', $this->user->id];
        }

        $incidentes = Incident::with([
            'proyecto:id,nombre',
            'tipoIncidente:id,nombre'
        ])->where($whereParams)->get();

        Log::debug($this->user);

        foreach ($incidentes as $incidente) {
            $nombre_proyecto = $incidente->proyecto->nombre;
            unset($incidente->proyecto);
            $incidente->proyecto = $nombre_proyecto;
            $nombre_tipo_incidente = $incidente->tipoIncidente->nombre;
            unset($incidente->tipoIncidente);
            $incidente->tipo_incidente = $nombre_tipo_incidente;
            unset($incidente->detalle_evento, $incidente->localidad, $incidente->zona_sector, $incidente->distrito, $incidente->provincia);
            unset($incidente->departamento, $incidente->coordenada_este, $incidente->coordenada_norte, $incidente->detalle_ubicacion);
            unset($incidente->project_id, $incidente->incident_type_id, $incidente->created_at, $incidente->updated_at, $incidente->responsable_propio_id);
            unset( $incidente->responsable_externo_id, $incidente->monitoring_point_id);
        }

        return ApiUtils::respuesta(true, [ 'incidentes' => $incidentes]);
    }

    public function crear(Request $request) {
        $incidente = new Incident;

        $incidente->detalle_evento = $request->detalle_evento;
        $incidente->fecha_incidente = $request->fecha_incidente;
        $incidente->hora_incidente = $request->hora_incidente;
        $incidente->localidad = $request->localidad;
        $incidente->zona_sector = $request->zona_sector;
        $incidente->distrito = $request->distrito;
        $incidente->provincia = $request->provincia;
        $incidente->departamento = $request->departamento;
        $incidente->coordenada_este = $request->coordenada_este;
        $incidente->coordenada_norte = $request->coordenada_norte;
        $incidente->detalle_ubicacion = $request->detalle_ubicacion;
        $incidente->project_id = $request->proyecto['id'];
        $incidente->monitoring_point_id = $request->punto['id'] !== -1 ? $request->punto['id'] : null;
        $incidente->responsable_propio_id = $request->proyecto['responsable_propio_id'];
        $incidente->responsable_externo_id = $request->proyecto['responsable_externo_id'];
        $incidente->incident_type_id = $request->tipoIncidente['id'];
        $incidente->estado = 0;

        $incidente->save();

        $incidente->codigo = 'EV-IRP-' . str_pad($incidente->id, 6, '0', STR_PAD_LEFT);

        foreach($request->causas as $causa) {
            $nuevaCausa = new ImmediateCause;
            $nuevaCausa->descripcion = $causa['descripcion']; 
            $nuevaCausa->cause_type_id = $causa['tipo']['id'];
            $nuevaCausa->incident_id = $incidente->id;

            $nuevaCausa->save();
        }

        foreach($request->acciones_inmediatas as $accion) {
            $nuevaAccion = new ImmediateAction;
            $nuevaAccion->descripcion = $accion['descripcion']; 
            $nuevaAccion->responsable = $accion['responsable'];
            $nuevaAccion->incident_id = $incidente->id;

            $nuevaAccion->save();
        }

        $incidente->save();

        return ApiUtils::respuesta(true, [ 'incidente' => $incidente]);
    }

    public function detalle($id){
        $incidente = Incident::with([
            'proyecto:id,codigo,nombre,responsable_propio_id,responsable_externo_id',
            'proyecto.puntos:id,codigo,nombre,longitud,latitud,project_id',
            'proyecto.responsable_propio:id,primer_nombre,segundo_nombre,primer_apellido,segundo_apellido',
            'proyecto.responsable_externo:id,primer_nombre,segundo_nombre,primer_apellido,segundo_apellido',
            'tipoIncidente:id,nombre as label',
            'punto:id,codigo,nombre,longitud as utmx,latitud as utmy'
        ])->where('id',$id)->first();

        (new ProjectController())->setProyectoInfo($incidente->proyecto);

        $incidente->causas = (new ImmediateCauseController())->getImmediateCausesByIncidentId($incidente->id);
        $incidente->acciones_inmediatas = (new ImmediateActionController())->getImmediateActionsByIncidentId($incidente->id);

        $punto = $incidente->punto;

        if ($punto !== null) {
            $punto->label = $punto->codigo . ' - ' . $punto->nombre;
            unset($punto->codigo, $punto->nombre);
        }
        else {
            unset($incidente->punto);
            $incidente->punto = [
                'id' => -1,
                'label' => 'Fuera de puntos de monitoreos',
                'utmx' => null,
                'utmy' => null
            ];
        }

        $tipoIncidente = $incidente->tipoIncidente;
        unset($incidente->tipoIncidente);
        $incidente->tipoIncidente = $tipoIncidente;
        
        return ApiUtils::respuesta(true, ['incidente' => $incidente]);
    }

    public function editar(Request $request) {
        $incidente = Incident::find($request->id);

        $incidente->detalle_evento = $request->detalle_evento;
        $incidente->fecha_incidente = $request->fecha_incidente;
        $incidente->hora_incidente = $request->hora_incidente;
        $incidente->localidad = $request->localidad;
        $incidente->zona_sector = $request->zona_sector;
        $incidente->distrito = $request->distrito;
        $incidente->provincia = $request->provincia;
        $incidente->departamento = $request->departamento;
        $incidente->coordenada_este = $request->coordenada_este;
        $incidente->coordenada_norte = $request->coordenada_norte;
        $incidente->detalle_ubicacion = $request->detalle_ubicacion;
        $incidente->project_id = $request->proyecto['id'];
        $incidente->monitoring_point_id = $request->punto['id'] !== -1 ? $request->punto['id'] : null;
        $incidente->responsable_propio_id = $request->proyecto['responsable_propio_id'];
        $incidente->responsable_externo_id = $request->proyecto['responsable_externo_id'];
        $incidente->incident_type_id = $request->tipoIncidente['id'];

        $incidente->save();
        
        foreach($request->causas as $causa) {
            if (isset($causa['created'])) {
                $nuevaCausa = new ImmediateCause;
                $nuevaCausa->descripcion = $causa['descripcion'];
                $nuevaCausa->cause_type_id = $causa['tipo']['id'];
                $nuevaCausa->incident_id = $incidente->id;
                $nuevaCausa->save();
            }
            else if (isset($causa['deleted'])) {
                ImmediateCause::destroy($causa['id']);
            }
            else if (isset($causa['edited'])) {
                $causaEditada = ImmediateCause::find($causa['id']);
                $causaEditada->descripcion = $causa['descripcion'];
                $causaEditada->cause_type_id = $causa['tipo']['id'];
                $causaEditada->save();
            }
        }

        foreach($request->acciones_inmediatas as $accion) {
            if (isset($accion['created'])) {
                $nuevaAccion = new ImmediateAction;
                $nuevaAccion->descripcion = $accion['descripcion']; 
                $nuevaAccion->responsable = $accion['responsable'];
                $nuevaAccion->incident_id = $incidente->id;
                $nuevaAccion->save();
            }
            else if (isset($accion['deleted'])) {
                ImmediateAction::destroy($accion['id']);
            }
            else if (isset($accion['edited'])) {
                $accionEditada = ImmediateAction::find($accion['id']);
                $accionEditada->descripcion = $accion['descripcion']; 
                $accionEditada->responsable = $accion['responsable'];
                $accionEditada->save();
            }
        }

        return ApiUtils::respuesta(true, [ 'incidente' => $incidente]);
    }

    public function getInfoCrearInvestigacion ($id) {
        $incidente = Incident::with([
            'proyecto:id,codigo,nombre,responsable_propio_id,responsable_externo_id',
            'proyecto.puntos:id,codigo,nombre,longitud,latitud,project_id',
            'proyecto.responsable_propio:id,primer_nombre,segundo_nombre,primer_apellido,segundo_apellido',
            'proyecto.responsable_externo:id,primer_nombre,segundo_nombre,primer_apellido,segundo_apellido',
            'tipoIncidente:id,nombre as label',
            'punto:id,codigo,nombre,longitud as utmx,latitud as utmy'
        ])->where('id',$id)->first();

        (new ProjectController())->setProyectoInfo($incidente->proyecto);

        $punto = $incidente->punto;
        if ($punto !== null) {
            $punto->label = $punto->codigo . ' - ' . $punto->nombre;
            unset($punto->codigo, $punto->nombre);
        }
        else {
            unset($incidente->punto);
            $incidente->punto = [
                'id' => -1,
                'label' => 'Fuera de puntos de monitoreos',
                'utmx' => null,
                'utmy' => null
            ];
        }

        $tipoIncidente = $incidente->tipoIncidente;
        unset($incidente->tipoIncidente);
        $incidente->tipoIncidente = $tipoIncidente;

        $investigacion = new Investigation;

        $investigacion->codigo = 'EV-IRF-' . str_pad($incidente->id, 6, '0', STR_PAD_LEFT);
        $investigacion->fecha_inicio_investigacion = date("Y-m-d");
        $investigacion->fecha_fin_investigacion = null;
        $investigacion->detalle_evento = $incidente->detalle_evento;
        $investigacion->detalle_pre_evento = '';
        $investigacion->detalle_post_evento = '';
        $investigacion->fecha_incidente = $incidente->fecha_incidente;
        $investigacion->hora_incidente = $incidente->hora_incidente;
        $investigacion->localidad = $incidente->localidad;
        $investigacion->zona_sector = $incidente->zona_sector;
        $investigacion->distrito = $incidente->distrito;
        $investigacion->provincia = $incidente->provincia;
        $investigacion->departamento = $incidente->departamento;
        $investigacion->coordenada_norte = $incidente->coordenada_norte;
        $investigacion->coordenada_este = $incidente->coordenada_este;
        $investigacion->detalle_ubicacion = $incidente->detalle_ubicacion;
        $investigacion->tipoIncidente = $incidente->tipoIncidente;
        $investigacion->proyecto = $incidente->proyecto;
        $investigacion->punto = $incidente->punto;
        $investigacion->incidente_id = $incidente->id;

        return ApiUtils::respuesta(true, ['investigacion' => $investigacion]);
    }

    public function exportarIncidente($id) {
        $incidente = Incident::with([
            'proyecto:id,responsable_externo_id,empresa_ejecutora_id',
            'proyecto.empresa_ejecutora:id,ruc,razon_social,tipo_contribuyente,direccion_fiscal,distrito_ciudad,departamento',
            'proyecto.responsable_externo:id,dni,primer_nombre,segundo_nombre,primer_apellido,segundo_apellido,email,numero_celular,cargo',
        ])->where('id',$id)->first();

        $incidente->causas = (new ImmediateCauseController())->getImmediateCausesByIncidentId($incidente->id);
        $incidente->acciones_inmediatas = (new ImmediateActionController())->getImmediateActionsByIncidentId($incidente->id);

        $pdf = PDF::loadView('exports.incident_report', [
            'company' => $incidente->proyecto->empresa_ejecutora,
            'reporter' => $incidente->proyecto->responsable_externo,
            'incident' => $incidente,
            'causes' => $incidente->causas,
            'immediate_actions' => $incidente->acciones_inmediatas
        ]);
        return $pdf->download('prueba.pdf');
    }
}
