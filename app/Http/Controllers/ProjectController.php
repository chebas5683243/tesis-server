<?php

namespace App\Http\Controllers;

use Datetime;
use Exception;
use JWTAuth;
use App\Models\Phase;

use App\Models\Project;
use App\Utils\ApiUtils;
use Illuminate\Http\Request;
use App\Models\MonitoringPoint;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;

class ProjectController extends Controller
{
    protected $user;

    public function __construct() {
        $this->middleware('jwt.auth');
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function listar(){
        try {
            $proyectos = Project::with(['empresa_ejecutora'])->orderBy('codigo','asc')->get();

            foreach($proyectos as $proyecto) {
                $proyecto->empresa = $proyecto->empresa_ejecutora->razon_social;

                if($proyecto->fecha_inicio) {
                    $inicio = new Datetime($proyecto->fecha_inicio);
                    $proyecto->fecha_inicio = $inicio->format('Y-m-d');
                }
                else $proyecto->fecha_inicio = "-";

                if($proyecto->fecha_fin) {
                    $fin = new Datetime($proyecto->fecha_fin);
                    $proyecto->fecha_fin = $fin->format('Y-m-d');
                }
                else $proyecto->fecha_fin = "-";

                unset($proyecto->created_at,$proyecto->updated_at,$proyecto->deleted_at,$proyecto->descripcion);
                unset($proyecto->estado,$proyecto->fecha_fin_tentativa,$proyecto->responsable_externo_id);
                unset($proyecto->responsable_propio_id,$proyecto->empresa_ejecutora_id,$proyecto->empresa_ejecutora);
            }
        }
        catch (Exception $ex) {
            return ApiUtils::respuesta(false);
        }

        return ApiUtils::respuesta(true, ['proyectos' => $proyectos]);
    }

    public function listarMonitoreo(){
        $userType = $this->user->tipo;
        $whereParams = [];
        if ($userType === 2) {
            $whereParams[] = ['responsable_propio_id', $this->user->id];
        }
        else if ($userType === 3) {
            $whereParams[] = ['responsable_externo_id', $this->user->id];
        }
        $proyectos = Project::with(['empresa_ejecutora'])->where($whereParams)->orderBy('codigo','asc')->get();

        foreach($proyectos as $proyecto) {
            $proyecto->empresa = $proyecto->empresa_ejecutora->razon_social;

            if($proyecto->fecha_inicio) {
                $inicio = new Datetime($proyecto->fecha_inicio);
                $proyecto->fecha_inicio = $inicio->format('Y-m-d');
            }
            else $proyecto->fecha_inicio = "-";

            if($proyecto->fecha_fin) {
                $fin = new Datetime($proyecto->fecha_fin);
                $proyecto->fecha_fin = $fin->format('Y-m-d');
            }
            else $proyecto->fecha_fin = "-";

            unset($proyecto->created_at,$proyecto->updated_at,$proyecto->deleted_at,$proyecto->descripcion);
            unset($proyecto->estado,$proyecto->fecha_fin_tentativa,$proyecto->responsable_externo_id);
            unset($proyecto->responsable_propio_id,$proyecto->empresa_ejecutora_id,$proyecto->empresa_ejecutora);
        }

        return ApiUtils::respuesta(true, ['proyectos' => $proyectos]);
    }

    public function detalle($id){
        try {
            $proyecto = Project::with([
                'empresa_ejecutora:id,razon_social as label',
                'responsable_externo:id,primer_apellido,segundo_apellido,primer_nombre,segundo_nombre',
                'responsable_propio:id,primer_apellido,segundo_apellido,primer_nombre,segundo_nombre',
                'puntos:id,project_id',
                'puntos.registros:id,monitoring_point_id',
                'fases',
                'incidentes'
            ])->where('id',$id)->first();

            $proyecto->fecha_inicio = strtotime($proyecto->fecha_inicio);
            $proyecto->fecha_inicio = date('Y-m-d', $proyecto->fecha_inicio);

            $proyecto->fecha_fin_tentativa = strtotime($proyecto->fecha_fin_tentativa);
            $proyecto->fecha_fin_tentativa = date('Y-m-d', $proyecto->fecha_fin_tentativa);

            $proyecto->cantidad_puntos = count($proyecto->puntos);
            $proyecto->cantidad_registros = 0;

            foreach($proyecto->puntos as $punto) {
                $proyecto->cantidad_registros += count($punto->registros);
            }

            if($proyecto->fecha_fin) {
                $proyecto->fecha_fin = strtotime($proyecto->fecha_fin);
                $proyecto->fecha_fin = date('Y-m-d', $proyecto->fecha_fin); 
            } else {
                $proyecto->fecha_fin = "";
            }

            $proyecto->cantidad_incidentes = count($proyecto->incidentes);
            unset($proyecto->incidentes);
            unset($proyecto->empresa_ejecutora_id, $proyecto->responsable_externo_id, $proyecto->responsable_propio_id);
            unset($proyecto->created_at, $proyecto->updated_at, $proyecto->deleted_at, $proyecto->puntos);
            $proyecto->responsable_externo->label = $proyecto->responsable_externo->primer_nombre . " " . $proyecto->responsable_externo->segundo_nombre . " " . $proyecto->responsable_externo->primer_apellido . " " . $proyecto->responsable_externo->segundo_apellido;
            $proyecto->responsable_propio->label = $proyecto->responsable_propio->primer_nombre . " " . $proyecto->responsable_propio->segundo_nombre . " " . $proyecto->responsable_propio->primer_apellido . " " . $proyecto->responsable_propio->segundo_apellido;

            unset($proyecto->responsable_externo->primer_nombre, $proyecto->responsable_externo->segundo_nombre, $proyecto->responsable_externo->primer_apellido, $proyecto->responsable_externo->segundo_apellido);
            unset($proyecto->responsable_propio->primer_nombre, $proyecto->responsable_propio->segundo_nombre, $proyecto->responsable_propio->primer_apellido, $proyecto->responsable_propio->segundo_apellido);

            foreach($proyecto->fases as $fase) {
                unset($fase->created_at, $fase->updated_at, $fase->deleted_at, $fase->project_id);
            }
        }
        catch (Exception $ex) {
            return ApiUtils::respuesta(false);
        }
        
        return ApiUtils::respuesta(true, ['proyecto' => $proyecto]);
    }

    public function crear(Request $request) {
        $proyecto = new Project;
        
        $proyecto->nombre = $request->nombre;
        $proyecto->descripcion = $request->descripcion;
        $proyecto->fecha_inicio = $request->fecha_inicio;
        $proyecto->fecha_fin_tentativa = $request->fecha_fin_tentativa;
        $proyecto->ubicacion = $request->ubicacion;
        $proyecto->estado = 0;
        $proyecto->empresa_ejecutora()->associate($request->empresa_ejecutora["id"]);
        $proyecto->responsable_externo()->associate($request->responsable_externo["id"]);
        $proyecto->responsable_propio()->associate($request->responsable_propio["id"]);

        $proyecto->save();

        $proyectoEmpezo = $this->proyectoHaEmpezado($proyecto->fecha_inicio);
        if($proyectoEmpezo) {
            $proyecto->estado = 1;
        }

        $isFirst = true;
        foreach($request->fases as $fase) {
            $faseCreated = Phase::create([
                'nombre' => $fase["nombre"],
                'descripcion' => $fase["descripcion"],
                'estado' => $fase["estado"],
                'project_id' => $proyecto->id
            ]);
            if($isFirst) {
                $faseCreated->inicio = $proyecto->fecha_inicio;
                if($proyectoEmpezo) {
                    $faseCreated->estado = 2;
                }
                $faseCreated->save();
                $isFirst = false;
            }
        }

        $proyecto->codigo = 'EV-PRO-' . str_pad($proyecto->id, 6, '0', STR_PAD_LEFT);

        $proyecto->save();

        return ApiUtils::respuesta(true, ['proyecto' => $proyecto]);
    }

    public function editar(Request $request) {
        $proyecto = Project::find($request->id);
        
        $proyecto->nombre = $request->nombre;
        $proyecto->descripcion = $request->descripcion;
        $proyecto->fecha_inicio = $request->fecha_inicio;
        $proyecto->fecha_fin_tentativa = $request->fecha_fin_tentativa;
        $proyecto->ubicacion = $request->ubicacion;
        $proyecto->estado = $request->estado;
        $proyecto->empresa_ejecutora()->associate($request->empresa_ejecutora["id"]);
        $proyecto->responsable_externo()->associate($request->responsable_externo["id"]);
        $proyecto->responsable_propio()->associate($request->responsable_propio["id"]);

        $isFirst = true;
        $proyectoEmpezo = $this->proyectoHaEmpezado($proyecto->fecha_inicio);
        if($proyectoEmpezo && $proyecto->estado === 0) {
            $proyecto->estado = 1;
        }
        if($proyecto->estado === 2 && $proyecto->fecha_fin === null) {
            $proyecto->fecha_fin = date("Y-m-d");
        }

        foreach($request->fases as $fase) {
            if(isset($fase["created"])) {
                $faseCreated = Phase::create([
                    'nombre' => $fase["nombre"],
                    'descripcion' => $fase["descripcion"],
                    'estado' => $fase["estado"],
                    'project_id' => $proyecto->id
                ]);
                if($isFirst) {
                    $faseCreated->inicio = $proyecto->fecha_inicio;
                    if($proyectoEmpezo) {
                        $faseCreated->estado = 2;
                    }
                    $faseCreated->save();
                    $isFirst = false;
                }
            }
            else if(isset($fase["deleted"])) {
                Phase::destroy($fase["id"]);
            }
            else {
                $editedFase = Phase::find($fase["id"]);
                if($isFirst) {
                    $editedFase->inicio = $proyecto->fecha_inicio;
                    $isFirst = false;
                    if($fase["estado"] === 1 && $proyectoEmpezo) {
                        $fase["estado"] = 2;
                    }
                }
                $editedFase->estado = $fase["estado"];
                if($editedFase->estado === 2 && $editedFase->inicio === null) {
                    $editedFase->inicio = date("Y-m-d");
                }
                if($editedFase->estado === 3 && $editedFase->fin === null) {
                    $editedFase->fin = date("Y-m-d");
                }
                if($editedFase->estado === 3 && $editedFase->inicio === null) {
                    $editedFase->inicio = date("Y-m-d");
                }

                $editedFase->save();
            }
        }

        $proyecto->save();

        return ApiUtils::respuesta(true, ['proyecto' => $proyecto]);
    }

    private function proyectoHaEmpezado($fecha_inicio) {
        $inicioProyecto = strtotime($fecha_inicio);
        $today = date("Y-m-d");
        $today_time = strtotime($today);

        if ($inicioProyecto <= $today_time) {
            return true;
        }

        return false;
    }

    public function puntosMonitoreos($id) {
        $puntos = MonitoringPoint::where('project_id', $id)->get();

        foreach ($puntos as $punto) {
            $punto->longitud = floatval($punto->longitud);
            $punto->latitud = floatval($punto->latitud);
            $punto->altitud = floatval($punto->altitud);
        }
        
        return ApiUtils::respuesta(true, ['puntos' => $puntos]);
    }

    public function simpleListar() {
        $proyectos = Project::with([
            'puntos:id,codigo,nombre,longitud,latitud,project_id',
            'responsable_propio:id,primer_nombre,segundo_nombre,primer_apellido,segundo_apellido',
            'responsable_externo:id,primer_nombre,segundo_nombre,primer_apellido,segundo_apellido'
        ])->get();

        foreach($proyectos as $proyecto) {
            $this->setProyectoInfo($proyecto);
        }

        $proyectos->prepend([
            "id" => 0,
            "label" => "Selecciona un proyecto",
            "responsable_propio" => "",
            "responsable_externo" => "",
            "puntos" => []
        ]);
        return ApiUtils::respuesta(true, ['proyectos' => $proyectos]);
    }

    public function setProyectoInfo($proyecto) {
        $proyecto->label = $proyecto->codigo . ' - ' . $proyecto->nombre;
        $responsable_propio = $proyecto->responsable_propio->getCompleteName();
        $responsable_externo = $proyecto->responsable_externo->getCompleteName();
        unset($proyecto->responsable_propio, $proyecto->responsable_externo);
        $proyecto->responsable_propio = $responsable_propio;
        $proyecto->responsable_externo = $responsable_externo;
        unset($proyecto->nombre, $proyecto->codigo, $proyecto->fecha_inicio, $proyecto->fecha_fin_tentativa, $proyecto->fecha_fin);
        unset($proyecto->ubicacion, $proyecto->estado, $proyecto->created_at, $proyecto->updated_at, $proyecto->deleted_at);
        unset($proyecto->empresa_ejecutora_id, $proyecto->descripcion);

        foreach($proyecto->puntos as $punto) {
            $punto->label = $punto->codigo . ' - ' . $punto->nombre;
            $punto->utmx = $punto->longitud;
            $punto->utmy = $punto->latitud;
            unset($punto->codigo, $punto->nombre, $punto->latitud, $punto->longitud);
        }

        $proyecto->puntos->prepend([
            'id' => -1,
            'label' => 'Fuera de puntos de monitoreos',
            'utmx' => null,
            'utmy' => null
        ]);

        $proyecto->puntos->prepend([
            'id' => 0,
            'label' => 'Selecciona un punto de monitoreo',
            'utmx' => null,
            'utmy' => null
        ]);
    }
}
