<?php

namespace App\Http\Controllers;

use App\Models\Record;
use App\Utils\ApiUtils;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Imports\RecordDataImport;
use Illuminate\Support\Facades\Log;
use App\Exports\RecordTemplateExport;
use Maatwebsite\Excel\HeadingRowImport;
use App\Models\MonitoringPointParameter;

class RecordController extends Controller
{
    public function exportTemplate($puntoId) {
        return (new RecordTemplateExport($puntoId))->download('plantilla_registro_punto_' . $puntoId . '.xlsx');
    }

    public function importRecordData(Request $request) {
        $headings = (new HeadingRowImport)->toCollection(request()->file('recordFile'));
        $parametros = $this->getParametrosPunto($request->puntoId);
        $errors = $this->validateFileHeaders($headings, $parametros);

        if (count($errors) !== 0) return ApiUtils::respuesta(false, ['errors' => $errors]);

        $import = new RecordDataImport($request->registradorId, $request->puntoId, $parametros);
        $import->import(request()->file('recordFile'));

        $rowValidations = $import->rowValidations;
        $rowValidations = $import->rowValidations;
        $rowValidations = $import->rowValidations;

        return ApiUtils::respuesta(true, [
            'rowValidations' => $import->rowValidations,
            'rowsInserted' => $import->rowsInserted,
            'rowsOmitted' => $import->rowsOmitted,
        ]);
    }

    private function getParametrosPunto($puntoId) {
        $parametros = MonitoringPointParameter::with('parametro.unidad')->where('monitoring_point_id', $puntoId)->get();

        foreach($parametros as $parametro) {
            $parametro->nombre = $parametro->parametro->nombre;
            $parametro->nombre = $parametro->parametro->nombre;
            $parametro->nombre_slug = Str::slug($parametro->nombre, '_');
            $parametro->nombre_corto = $parametro->parametro->nombre_corto;
            $parametro->unidad_corto = $parametro->parametro->unidad->nombre_corto;
        }

        return $parametros;
    }

    private function validateFileHeaders($headings, $parametros) {
        $headings = $headings[0][0];
        $errors = [];

        foreach($headings as $key=>$heading) {
            if ($key === 0) {
                if ($heading !== 'fecha_del_registro') {
                    $errors[] = "No se encuentra la columna de Fecha del registro";
                }
            }
            else if ($key === 1) {
                if ($heading !== 'hora_del_registro') {
                    $errors[] = "No se encuentra la columna de Hora del registro";
                }
            }
            else if ($heading !== $parametros[$key-2]->nombre_slug) {
                $errors[] = "No se encuentra la columnda del parámetro " . $parametros[$key-2]->nombre;
            }
        }

        return $errors;
    }

    public function reporteRegistro($id) {
        $registro = Record::with([
            'registrador:id,dni,primer_nombre,segundo_nombre,primer_apellido,segundo_apellido',
            // 'valoresParametros:id,record_id,valor_cuantitativo,valor_cualitativo',
            'valoresParametros.puntoParametro.parametro:id,nombre,nombre_corto,unit_id',
            'valoresParametros.puntoParametro.parametro.unidad:id,nombre_corto',
            'puntoMonitoreo:id,codigo,nombre,project_id',
            'puntoMonitoreo.proyecto:id,codigo,nombre,empresa_ejecutora_id',
            'puntoMonitoreo.proyecto.empresa_ejecutora:id,ruc,razon_social',
        ])->where('id', $id)->first();

        // nombre del proyecto
        $registro->proyecto = $this->getNombreProyecto($registro->puntoMonitoreo->proyecto);
        // nombre del registrador
        $registro->registrado_por = $this->getNombreRegistrador($registro->registrador);
        // nombre de la empresa
        $registro->empresa = $this->getNombreEmpresa($registro->puntoMonitoreo->proyecto->empresa_ejecutora);
        // nombre del punto
        $registro->punto = $this->getNombrePuntoMonitoreo($registro->puntoMonitoreo);

        unset($registro->registrador, $registro->puntoMonitoreo);
        unset($registro->registrador_id, $registro->monitoring_point_id, $registro->created_at, $registro->updated_at);

        [
            $registro->parametros_considerados,
            $registro->total_parametros,
            $parametros,
            $aqi,
            $wqi,
            $estandar,
            $no_aplica
        ] = $this->getDataParametros($registro->valoresParametros);



        unset($registro->valoresParametros);

        return ApiUtils::respuesta(true, [
            'registro' => $registro,
            'parametros' => $parametros,
            'aqi' => $aqi,
            'wqi' => $wqi,
            'estandar' => $estandar,
            'no_aplica' => $no_aplica,
        ]);
    }

    private function getNombreProyecto($proyecto) {
        return $proyecto->codigo . ' - ' . $proyecto->nombre;
    }

    private function getNombreRegistrador($registrador) {
        return $registrador->dni . ' - ' . $registrador->primer_nombre . ' ' . $registrador->segundo_nombre
            . ' ' . $registrador->primer_apellido . ' ' . $registrador->segundo_apellido;
    }

    private function getNombreEmpresa($empresa) {
        return $empresa->ruc . ' - ' . $empresa->razon_social;
    }

    private function getNombrePuntoMonitoreo($punto) {
        return $punto->codigo . ' - ' . $punto->nombre;
    }

    private function getDataParametros($valores) {
        $parametros_considerados = 0;
        $total_parametros = 0;
        $parametros = [];
        [ $aqi, $wqi, $estandar, $no_aplica ] = $this->inicializarParametrizaciones();
        
        foreach ($valores as $valor) {
            // contamos todos los parámetros
            $total_parametros++;
            // inicializamos el parametro
            $parametro = [];
            $parametro['id'] = $valor->puntoParametro->parametro->id;
            $parametro['nombre'] = $valor->puntoParametro->parametro->nombre;
            // primer caso, sin parametización
            if ($valor->puntoParametro->no_aplica) {
                $parametro['parametrizacion'] = 'No aplica';
                $parametro['valor'] = $valor->valor_cualitativo;
                if ($valor->valor_cualitativo !== null) {
                    $parametros_considerados++;
                    $parametro['numero'] = $parametros_considerados;
                    $no_aplica['cantidad']++;
                }
            }
            else {
                $parametro['valor'] = $valor->valor_cuantitativo . ' ' . $valor->puntoParametro->parametro->unidad->nombre_corto;
                if($valor->valor_cuantitativo !== null)  {
                    $parametros_considerados++;
                    $parametro['numero'] = $parametros_considerados;
                }
                // segundo caso, parametrización estándar
                if ($valor->puntoParametro->usa_estandar) {
                    if($valor->valor_cuantitativo !== null) $estandar['cantidad']++;
                    $parametro['parametrizacion'] = 'Estándar';
                    [ $parametro['etiqueta'], $estandar ] = $this->getEtiquetaEstandar($valor, $estandar);
                }
                // tercer caso, parametrización wqi
                else if ($valor->puntoParametro->usa_wqi) {
                    if($valor->valor_cuantitativo !== null) $wqi['cantidad']++;
                    $parametro['parametrizacion'] = 'WQI';
                    [ $parametro['etiqueta'], $wqi ] = $this->getEtiquetaWQI($valor, $wqi);
                }
                // cuarto caso, parametrización aqi
                else {
                    if($valor->valor_cuantitativo !== null) $aqi['cantidad']++;
                    $parametro['parametrizacion'] = 'AQI';
                    [ $parametro['etiqueta'], $aqi ] = $this->getEtiquetaAQI($valor, $aqi);
                }
            }
            $parametros[] = $parametro;
        }

        $aqi['cantidad_perc'] = $aqi['cantidad'] * 100 / $total_parametros;
        $wqi['cantidad_perc'] = $wqi['cantidad'] * 100 / $total_parametros;
        $estandar['cantidad_perc'] = $estandar['cantidad'] * 100 / $total_parametros;
        $no_aplica['cantidad_perc'] = $no_aplica['cantidad'] * 100 / $total_parametros;
        return [ $parametros_considerados, $total_parametros, $parametros, $aqi, $wqi, $estandar, $no_aplica ];
    }

    private function inicializarParametrizaciones() {
        $aqi = [ 'cantidad' => 0, 'aqi_1' => 0, 'aqi_2' => 0, 'aqi_3' => 0, 'aqi_4' => 0, 'aqi_5' => 0, 'aqi_6' => 0 ];
        $wqi = [ 'cantidad' => 0, 'wqi_1' => 0, 'wqi_2' => 0, 'wqi_3' => 0, 'wqi_4' => 0, 'wqi_5' => 0 ];
        $estandar = [ 'cantidad' => 0, 'buena' => 0, 'inadecuada' => 0 ];
        $no_aplica = [ 'cantidad' => 0 ];

        return [ $aqi, $wqi, $estandar, $no_aplica ];
    }

    private function getEtiquetaEstandar($valor, $estandar) {
        $minimo = $valor->puntoParametro->valor_minimo;
        $maximo = $valor->puntoParametro->valor_maximo;
        $unidad = $valor->puntoParametro->parametro->unidad->nombre_corto;
        $etiqueta = [ 'tipo' => null, 'info' => null ];

        if($valor->valor_cuantitativo === null) return [ $etiqueta, $estandar ];

        //tiene ambos
        if ($minimo !== null && $maximo !== null) {
            if ($valor->valor_cuantitativo >= $minimo && $valor->valor_cuantitativo <= $maximo) {
                $estandar['buena']++;
                $etiqueta['tipo'] = 1;
                $etiqueta['info'] = 'Buena (' . $minimo . $unidad . ' - ' . $maximo . $unidad . ' )' ;
            }
            else if ($valor->valor_cuantitativo < $minimo) {
                $estandar['inadecuada']++;
                $etiqueta['tipo'] = 2;
                $etiqueta['info'] = 'Inadecuada ( menor a ' . $minimo . $unidad . ' )' ;
            }
            else {
                $estandar['inadecuada']++;
                $etiqueta['tipo'] = 2;
                $etiqueta['info'] = 'Inadecuada ( mayor a ' . $maximo . $unidad . ' )' ;
            }
        }
        // tiene minimo
        else if ($minimo !== null) {
            if ($valor->valor_cuantitativo >= $minimo) {
                $estandar['buena']++;
                $etiqueta['tipo'] = 1;
                $etiqueta['info'] = 'Buena ( mayor o igual a ' . $minimo . $unidad . ' )' ;
            }
            else {
                $estandar['inadecuada']++;
                $etiqueta['tipo'] = 2;
                $etiqueta['info'] = 'Inadecuada ( menor a ' . $minimo . $unidad . ' )' ;
            }
        }
        else if ($maximo !== null) {
            if ($valor->valor_cuantitativo <= $maximo) {
                $estandar['buena']++;
                $etiqueta['tipo'] = 1;
                $etiqueta['info'] = 'Buena ( menor o igual a ' . $maximo . $unidad . ' )' ;
            }
            else {
                $estandar['inadecuada']++;
                $etiqueta['tipo'] = 2;
                $etiqueta['info'] = 'Inadecuada ( mayor a ' . $maximo . $unidad . ' )' ;
            }
        }
        
        return [ $etiqueta, $estandar ];
    }

    private function getEtiquetaWQI($valor, $wqi) {
        $valor_ideal = $valor->puntoParametro->valor_ideal;
        $valor_estandar_permisible = $valor->puntoParametro->valor_estandar_permisible;
        $unidad = $valor->puntoParametro->parametro->unidad->nombre_corto;
        $etiqueta = [ 'tipo' => null, 'info' => null ];

        if($valor->valor_cuantitativo === null) return [ $etiqueta, $wqi];

        $rating = abs(100 * ($valor->valor_cuantitativo - $valor_ideal) / ($valor_estandar_permisible - $valor_ideal));
        
        if ($rating <= 25) {
            $wqi['wqi_1']++;
            $etiqueta['tipo'] = 1;
            $etiqueta['info'] = 'Excelente ( rating: ' . $rating . '% )';
        }
        else if ($rating <= 50) {
            $wqi['wqi_2']++;
            $etiqueta['tipo'] = 2;
            $etiqueta['info'] = 'Buena ( rating: ' . $rating . '% )';
        }
        else if ($rating <= 75) {
            $wqi['wqi_3']++;
            $etiqueta['tipo'] = 3;
            $etiqueta['info'] = 'Baja ( rating: ' . $rating . '% )';
        }
        else if ($rating <= 100) {
            $wqi['wqi_4']++;
            $etiqueta['tipo'] = 4;
            $etiqueta['info'] = 'Muy baja ( rating: ' . $rating . '% )';
        }
        else {
            $wqi['wqi_5']++;
            $etiqueta['tipo'] = 5;
            $etiqueta['info'] = 'Inadecuada ( rating: ' . $rating . '% )';
        }

        return [ $etiqueta, $wqi];
    }

    private function getEtiquetaAQI($valor, $aqi) {
        $aqi_1 = $valor->puntoParametro->aqi_1;
        $aqi_2 = $valor->puntoParametro->aqi_2;
        $aqi_3 = $valor->puntoParametro->aqi_3;
        $aqi_4 = $valor->puntoParametro->aqi_4;
        $aqi_5 = $valor->puntoParametro->aqi_5;
        $unidad = $valor->puntoParametro->parametro->unidad->nombre_corto;
        $etiqueta = [ 'tipo' => null, 'info' => null ];

        if($valor->valor_cuantitativo === null) return [ $etiqueta, $aqi];

        if ($valor->valor_cuantitativo <= $aqi_1) {
            $aqi['aqi_1']++;
            $etiqueta['tipo'] = 1;
            $etiqueta['info'] = 'Buena ( 0' . $unidad. ' - ' . $aqi_1 . $unidad . ' )' ;
        }
        else if ($valor->valor_cuantitativo <= $aqi_2) {
            $aqi['aqi_2']++;
            $etiqueta['tipo'] = 2;
            $etiqueta['info'] = 'Media (' . $aqi_1 . $unidad . ' - ' . $aqi_2 . $unidad . ' )' ;
        }
        else if ($valor->valor_cuantitativo <= $aqi_3) {
            $aqi['aqi_3']++;
            $etiqueta['tipo'] = 3;
            $etiqueta['info'] = 'Dañina (' . $aqi_2 . $unidad . ' - ' . $aqi_3 . $unidad . ' )' ;
        }
        else if ($valor->valor_cuantitativo <= $aqi_4) {
            $aqi['aqi_4']++;
            $etiqueta['tipo'] = 4;
            $etiqueta['info'] = 'No saludable (' . $aqi_3 . $unidad . ' - ' . $aqi_4 . $unidad . ' )' ;
        }
        else if ($valor->valor_cuantitativo <= $aqi_5) {
            $aqi['aqi_5']++;
            $etiqueta['tipo'] = 5;
            $etiqueta['info'] = 'Muy insalubre (' . $aqi_4 . $unidad . ' - ' . $aqi_5 . $unidad . ' )' ;
        }
        else {
            $aqi['aqi_6']++;
            $etiqueta['tipo'] = 6;
            $etiqueta['info'] = 'Peligrosa ( mayor a ' . $aqi_5 . $unidad . ' )' ;
        }

        return [ $etiqueta, $aqi];
    }
}
