<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\Models\Action;
use App\Utils\ApiUtils;
use App\Models\Incident;
use Illuminate\Http\Request;
use App\Models\Investigation;
use App\Models\AffectedPerson;
use App\Models\ImmediateCause;
use App\Models\ImmediateAction;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\EnvironmentalImpact;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\ActionController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ImmediateCauseController;
use App\Http\Controllers\ImmediateActionController;
use App\Http\Controllers\EnvironmentalImpactController;

class InvestigationController extends Controller
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

        $investigaciones = Investigation::with([
            'proyecto:id,nombre',
            'tipoIncidente:id,nombre'
        ])->where($whereParams)->get();

        foreach ($investigaciones as $investigacion) {
            $nombre_proyecto = $investigacion->proyecto->nombre;
            unset($investigacion->proyecto);
            $investigacion->proyecto = $nombre_proyecto;
            $nombre_tipo_incidente = $investigacion->tipoIncidente->nombre;
            unset($investigacion->tipoIncidente);
            $investigacion->tipo_incidente = $nombre_tipo_incidente;
            unset($investigacion->detalle_evento, $investigacion->localidad, $investigacion->zona_sector, $investigacion->distrito, $investigacion->provincia);
            unset($investigacion->departamento, $investigacion->coordenada_este, $investigacion->coordenada_norte, $investigacion->detalle_ubicacion);
            unset($investigacion->project_id, $investigacion->incident_type_id, $investigacion->created_at, $investigacion->updated_at, $investigacion->responsable_propio_id);
            unset($investigacion->responsable_externo_id, $investigacion->monitoring_point_id, $investigacion->detalle_pre_evento, $investigacion->detalle_post_evento);
            unset($investigacion->fecha_inicio_investigacion, $investigacion->fecha_fin_investigacion);
        }

        return ApiUtils::respuesta(true, [ 'investigaciones' => $investigaciones]);
    }

    public function crear(Request $request) {
        $investigacion = new Investigation;

        $investigacion->codigo = $request->codigo;
        $investigacion->fecha_inicio_investigacion = $request->fecha_inicio_investigacion;
        $investigacion->fecha_fin_investigacion = $request->fecha_fin_investigacion;
        $investigacion->detalle_evento = $request->detalle_evento;
        $investigacion->detalle_pre_evento = $request->detalle_pre_evento;
        $investigacion->detalle_post_evento = $request->detalle_post_evento;
        $investigacion->fecha_incidente = $request->fecha_incidente;
        $investigacion->hora_incidente = $request->hora_incidente;
        $investigacion->localidad = $request->localidad;
        $investigacion->zona_sector = $request->zona_sector;
        $investigacion->distrito = $request->distrito;
        $investigacion->provincia = $request->provincia;
        $investigacion->departamento = $request->departamento;
        $investigacion->coordenada_norte = $request->coordenada_norte;
        $investigacion->coordenada_este = $request->coordenada_este;
        $investigacion->detalle_ubicacion = $request->detalle_ubicacion;
        $investigacion->project_id = $request->proyecto['id'];
        $investigacion->monitoring_point_id = $request->punto['id'] !== -1 ? $request->punto['id'] : null;
        $investigacion->responsable_propio_id = $request->proyecto['responsable_propio_id'];
        $investigacion->responsable_externo_id = $request->proyecto['responsable_externo_id'];
        $investigacion->incident_type_id = $request->tipoIncidente['id'];
        $investigacion->estado = 0;

        $investigacion->save();

        $incidente = Incident::find($request->incidente_id);
        $incidente->investigation_id = $investigacion->id;
        $incidente->estado = 1;
        $incidente->save();

        $causas = (new ImmediateCauseController())->getImmediateCausesByIncidentId($request->incidente_id);
        $acciones_inmediatas = (new ImmediateActionController())->getImmediateActionsByIncidentId($request->incidente_id);

        foreach($causas as $causa) {
            $nuevaCausa = new ImmediateCause;
            $nuevaCausa->descripcion = $causa['descripcion']; 
            $nuevaCausa->cause_type_id = $causa['tipo']['id'];
            $nuevaCausa->investigation_id = $investigacion->id;

            $nuevaCausa->save();
        }

        foreach($acciones_inmediatas as $accion) {
            $nuevaAccion = new ImmediateAction;
            $nuevaAccion->descripcion = $accion['descripcion']; 
            $nuevaAccion->responsable = $accion['responsable'];
            $nuevaAccion->investigation_id = $investigacion->id;

            $nuevaAccion->save();
        }

        return ApiUtils::respuesta(true, [ 'investigacion' => $investigacion]);
    }

    public function detalle($id) {
        $investigacion = Investigation::with([
            'proyecto:id,codigo,nombre,responsable_propio_id,responsable_externo_id',
            'proyecto.puntos:id,codigo,nombre,longitud,latitud,project_id',
            'proyecto.responsable_propio:id,primer_nombre,segundo_nombre,primer_apellido,segundo_apellido',
            'proyecto.responsable_externo:id,primer_nombre,segundo_nombre,primer_apellido,segundo_apellido',
            'tipoIncidente:id,nombre as label',
            'punto:id,codigo,nombre,longitud as utmx,latitud as utmy'
        ])->where('id',$id)->first();

        (new ProjectController())->setProyectoInfo($investigacion->proyecto);

        $investigacion->causas = (new ImmediateCauseController())->getImmediateCausesByInvestigationId($investigacion->id);
        $investigacion->impactos = (new EnvironmentalImpactController())->getEnvironmentalImpactByInvestigationId($investigacion->id);
        $investigacion->personas = (new AffectedPersonController())->getAffectedPeopleByInvestigationId($investigacion->id);
        $investigacion->acciones = (new ActionController())->getActionsByInvestigationId($investigacion->id);

        $punto = $investigacion->punto;

        if ($punto !== null) {
            $punto->label = $punto->codigo . ' - ' . $punto->nombre;
            unset($punto->codigo, $punto->nombre);
        }
        else {
            unset($investigacion->punto);
            $investigacion->punto = [
                'id' => -1,
                'label' => 'Fuera de puntos de monitoreos',
                'utmx' => null,
                'utmy' => null
            ];
        }

        $tipoIncidente = $investigacion->tipoIncidente;
        unset($investigacion->tipoIncidente);
        $investigacion->tipoIncidente = $tipoIncidente;
        
        return ApiUtils::respuesta(true, ['investigacion' => $investigacion]);
    }

    public function guardarDatosGenerales(Request $request) {
        $investigacion = Investigation::find($request->id);

        $investigacion->detalle_evento = $request->detalle_evento;
        $investigacion->detalle_pre_evento = $request->detalle_pre_evento;
        $investigacion->detalle_post_evento = $request->detalle_post_evento;
        $investigacion->fecha_incidente = $request->fecha_incidente;
        $investigacion->hora_incidente = $request->hora_incidente;
        if ($request->nextStep) {
            $investigacion->step = 2;
        }
        $investigacion->save();

        return ApiUtils::respuesta(true);
    }

    public function guardarConsecuencias(Request $request) {
        $investigacion = Investigation::find($request->id);
        
        foreach($request->impactos as $impacto) {
            if (isset($impacto['created'])) {
                $nuevoImpacto = new EnvironmentalImpact;
                $nuevoImpacto->descripcion = $impacto['descripcion']; 
                $nuevoImpacto->impact_type_id = $impacto['tipo']['id'];
                $nuevoImpacto->investigation_id = $request->id;
                $nuevoImpacto->save();
            }
            else if (isset($impacto['deleted'])) {
                EnvironmentalImpact::destroy($impacto['id']);
            }
            else if (isset($impacto['edited'])) {
                $impactoEditado = EnvironmentalImpact::find($impacto['id']);
                $impactoEditado->descripcion = $impacto['descripcion']; 
                $impactoEditado->impact_type_id = $impacto['tipo']['id'];
                $impactoEditado->save();
            }
        }

        foreach($request->personas as $persona) {
            if (isset($persona['created'])) {
                $nuevaPersona = new AffectedPerson;
                $nuevaPersona->nombre_completo = $persona['nombre_completo']; 
                $nuevaPersona->dni = $persona['dni'];
                $nuevaPersona->descripcion = $persona['descripcion'];
                $nuevaPersona->investigation_id = $request->id;
                $nuevaPersona->save();
            }
            else if (isset($persona['deleted'])) {
                AffectedPerson::destroy($persona['id']);
            }
            else if (isset($persona['edited'])) {
                $personaEditada = AffectedPerson::find($persona['id']);
                $personaEditada->nombre_completo = $persona['nombre_completo']; 
                $personaEditada->dni = $persona['dni'];
                $personaEditada->descripcion = $persona['descripcion'];
                $personaEditada->save();
            }
        }
        if ($request->nextStep) {
            $investigacion->step = 3;
        }
        $investigacion->save();

        return ApiUtils::respuesta(true);
    }

    public function guardarCausasAcciones (Request $request) {
        $investigacion = Investigation::find($request->id);

        foreach($request->causas as $causa) {
            if (isset($causa['created'])) {
                $nuevaCausa = new ImmediateCause;
                $nuevaCausa->descripcion = $causa['descripcion'];
                $nuevaCausa->cause_type_id = $causa['tipo']['id'];
                $nuevaCausa->investigation_id = $request->id;
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

        if ($request->nextStep) {
            $investigacion->step = 4;
        }
        $investigacion->save();

        return ApiUtils::respuesta(true);
    }

    public function guardarPlanAcciones (Request $request) {
        $investigacion = Investigation::find($request->id);

        foreach($request->acciones as $accion) {
            if (isset($accion['created'])) {
                $nuevaAccion = new Action;
                $nuevaAccion->responsable = $accion['responsable'];
                $nuevaAccion->fecha_planeada = $accion['fecha_planeada'];
                $nuevaAccion->estado = $accion['estado'];
                $nuevaAccion->descripcion = $accion['descripcion'];
                $nuevaAccion->action_type_id = $accion['tipo']['id'];
                $nuevaAccion->investigation_id = $request->id;
                $nuevaAccion->save();
            }
            else if (isset($accion['deleted'])) {
                Action::destroy($accion['id']);
            }
            else if (isset($accion['edited'])) {
                $accionEditada = Action::find($accion['id']);
                $accionEditada->responsable = $accion['responsable'];
                $accionEditada->fecha_planeada = $accion['fecha_planeada'];
                $accionEditada->estado = $accion['estado'];
                $accionEditada->descripcion = $accion['descripcion'];
                $accionEditada->action_type_id = $accion['tipo']['id'];
                $accionEditada->save();
            }
        }

        if ($request->nextStep) {
            $investigacion->step = 5;
            $investigacion->estado = 1;
        }
        $investigacion->save();

        $acciones = (new ActionController())->getActionsByInvestigationId($investigacion->id);

        $hasInProgressActions = false;
        $hasExecutedActions = false;
        
        foreach ($acciones as $accion) {
            if ($accion->estado === 0) $hasInProgressActions = true;
            if ($accion->estado === 1) $hasExecutedActions = true;
        }

        if ($hasInProgressActions) {
            $investigacion->estado = 1;
        }
        else if (!$hasExecutedActions) {
            $investigacion->estado = 2;
        }

        $investigacion->save();
        return ApiUtils::respuesta(true);
    }

    public function validarInvestigacion($id) {
        $investigacion = Investigation::find($id);
        $investigacion->estado = 3;
        $investigacion->fecha_fin_investigacion = date("Y-m-d");
        $investigacion->save();
        return ApiUtils::respuesta(true);
    }

    public function exportarInvestigacion($id) {
        $investigacion = Investigation::with([
            'proyecto:id,responsable_externo_id,empresa_ejecutora_id',
            'proyecto.empresa_ejecutora:id,ruc,razon_social,tipo_contribuyente,direccion_fiscal,distrito_ciudad,departamento',
            'proyecto.responsable_externo:id,dni,primer_nombre,segundo_nombre,primer_apellido,segundo_apellido,email,numero_celular,cargo',
        ])->where('id',$id)->first();

        $investigacion->causas = (new ImmediateCauseController())->getImmediateCausesByInvestigationId($investigacion->id);
        $investigacion->acciones_inmediatas = (new ImmediateActionController())->getImmediateActionsByInvestigationId($investigacion->id);
        $investigacion->impactos = (new EnvironmentalImpactController())->getEnvironmentalImpactByInvestigationId($investigacion->id);
        $investigacion->personas = (new AffectedPersonController())->getAffectedPeopleByInvestigationId($investigacion->id);
        $investigacion->acciones = (new ActionController())->getActionsByInvestigationId($investigacion->id);

        $pdf = PDF::loadView('exports.investigation_report', [
            'company' => $investigacion->proyecto->empresa_ejecutora,
            'reporter' => $investigacion->proyecto->responsable_externo,
            'incident' => $investigacion,
            'causes' => $investigacion->causas,
            'immediate_actions' => $investigacion->acciones_inmediatas,
            'environmental_impacts' => $investigacion->impactos,
            'affected_people' => $investigacion->personas,
            'actions' => $investigacion->acciones
        ]);
        return $pdf->download('prueba.pdf');
    }

    public function listarConFiltro(Request $request) {

        $investigacionesQuery = Investigation::select([
                'investigation.*',
                'project.nombre as proyecto',
                'incident_type.nombre as tipo_incidente'
            ])
            ->join('project', 'project.id', '=', 'investigation.project_id')
            ->join('incident_type', 'incident_type.id', '=', 'investigation.incident_type_id')
            ->leftJoin('environmental_impact', 'environmental_impact.investigation_id', '=', 'investigation.id')
            ->leftJoin('immediate_cause', 'immediate_cause.investigation_id', '=', 'investigation.id')
            ->where('investigation.estado', 3);

        if ($request->has('tiposIncidente')) {
            $investigacionesQuery->whereIn('investigation.incident_type_id', $request->tiposIncidente);
        }

        if ($request->has('fechaInicio')) {
            $investigacionesQuery->whereDate('investigation.fecha_incidente', '>=', $request->fechaInicio);
        }

        if ($request->has('fechaFin')) {
            $investigacionesQuery->whereDate('investigation.fecha_incidente', '<=', $request->fechaFin);
        }

        if ($request->has('tiposImpacto')) {
            $investigacionesQuery->whereIn('environmental_impact.impact_type_id', $request->tiposImpacto);
        }

        if ($request->has('tiposCausa')) {
            $investigacionesQuery->whereIn('immediate_cause.cause_type_id', $request->tiposCausa);
        }

        $investigaciones = $investigacionesQuery->groupBy('investigation.id')->get();

        foreach ($investigaciones as $investigacion) {
            unset($investigacion->detalle_evento, $investigacion->localidad, $investigacion->zona_sector, $investigacion->distrito, $investigacion->provincia);
            unset($investigacion->departamento, $investigacion->coordenada_este, $investigacion->coordenada_norte, $investigacion->detalle_ubicacion);
            unset($investigacion->project_id, $investigacion->incident_type_id, $investigacion->created_at, $investigacion->updated_at, $investigacion->responsable_propio_id);
            unset($investigacion->responsable_externo_id, $investigacion->monitoring_point_id, $investigacion->detalle_pre_evento, $investigacion->detalle_post_evento);
            unset($investigacion->fecha_inicio_investigacion, $investigacion->fecha_fin_investigacion);
        }

        return ApiUtils::respuesta(true, [ 'investigaciones' => $investigaciones]);
    }
}