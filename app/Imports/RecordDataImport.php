<?php

namespace App\Imports;

use DateTime;
use App\Models\Record;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Jobs\ProcessIncidentNotification;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\MonitoringPointParameterRegister;

class RecordDataImport implements ToCollection, WithHeadingRow
{
    use Importable;

    public $rowValidations;
    public $rowsInserted = 0;
    public $rowsOmitted = 0;

    public function __construct(int $registradorId, int $puntoId, Collection $parametros)
    {
        $this->registradorId = $registradorId;
        $this->puntoId = $puntoId;
        $this->parametros = $parametros;
    }
    
    public function collection(Collection $records)
    {
        $records->shift();
        $errors = [];

        foreach($records as $index => $record) {
            //validamos los datos de la fila
            $errorsRow = $this->validateRow($record, $index+3);
            $errors = array_merge($errors, $errorsRow);
            //si tiene errores, se pasa al siguiente
            if (count($errorsRow) !== 0) {
                $this->rowsOmitted++;
                continue;
            }
            //si no tiene errores, se inserta
            $this->rowsInserted++;
            $this->insertRecord($record);
        }

        $this->rowValidations = $errors;
    }

    private function validateRow($record, $fila)
    {
        $errors = [];
        //validar fecha
        if (!is_numeric($record['fecha_del_registro'])) {
            $errors[] = [
                'fila' => $fila,
                'comentario' => 'Formato incorrecto de la fecha'
            ];
        }
        //validar hora
        if (!is_numeric($record['hora_del_registro'])) {
            $errors[] = [
                'fila' => $fila,
                'comentario' => 'Formato incorrecto de la hora'
            ];
        }
        //validar valores de los parámetros
        foreach($this->parametros as $parametro) {
            $valor = $record[$parametro->nombre_slug];
            if ($valor === null) continue;
            //si es un parametro cuantitativo y no es numérico
            if (!$parametro->no_aplica && !is_numeric($valor)) {
                $errors[] = [
                    'fila' => $fila,
                    'comentario' => 'El valor del parámetro ' . $parametro->nombre_corto . ' debe ser numérico'
                ];
            }
        }

        return $errors;
    }

    private function insertRecord($row)
    {
        //inicializamos el arreglo de parámetros que se excedieron
        $incidentParameters = [];
        //inicializamos el nuevo registro
        $newRecord = new Record;
        $newRecord->registrador_id = $this->registradorId;
        $newRecord->monitoring_point_id = $this->puntoId;
        $newRecord->fecha_registro = $this->getExcelDateTime($row['fecha_del_registro'], $row['hora_del_registro']);
        $newRecord->save();
        $newRecord->codigo = 'EV-REG-' . str_pad($newRecord->id, 6, '0', STR_PAD_LEFT);
        $newRecord->save();
        //insertar valores de parámetros
        foreach($this->parametros as $parametro) {
            $newValue = new MonitoringPointParameterRegister;
            $newValue->record_id = $newRecord->id;
            $newValue->mpp_id = $parametro->id;
            if (!$parametro->no_aplica) {
                $newValue->valor_cuantitativo = $row[$parametro->nombre_slug];
                if ($newValue->valor_cuantitativo !== null) {
                    $resultado = $this->checkIfOutOfRange($parametro, $newValue->valor_cuantitativo);
                    if ($resultado['out']) {
                        $incidentParameters[] = $resultado;
                    }
                }
            }
            else {
                $newValue->valor_cualitativo = $row[$parametro->nombre_slug];
            }
            $newValue->save();
        }
        if (count($incidentParameters) > 0) {
            ProcessIncidentNotification::dispatch($this->puntoId, $newRecord->id, $this->registradorId, $incidentParameters);
        }
    }

    private function getExcelDateTime($fechaExcel, $horaExcel) {
        $datetime = gmdate("Y-m-d H:i:s",($fechaExcel + $horaExcel - 25569) * 86400);
        return $datetime;
    }

    private function checkIfOutOfRange($parametro, $valor) {
        $resultado = [
            'out' => false,
            'parametro' => $parametro,
            'parametrizacion' => '',
            'etiqueta' => null,
            'valor' => $valor . $parametro->unidad_corto
        ];

        if ($parametro->usa_estandar) {
            $resultado['parametrizacion'] = 'Estándar';
            [ $resultado['etiqueta'], $resultado['out'] ] = $this->getEtiquetaEstandar($parametro, $valor);
        }
        else if ($parametro->usa_wqi) {
            $resultado['parametrizacion'] = 'WQI';
            [ $resultado['etiqueta'], $resultado['out'] ] = $this->getEtiquetaWQI($parametro, $valor);
        }
        else {
            $resultado['parametrizacion'] = 'AQI';
            [ $resultado['etiqueta'], $resultado['out'] ] = $this->getEtiquetaAQI($parametro, $valor);
        }

        return $resultado;
    }

    private function getEtiquetaEstandar($parametro, $valor) {
        $minimo = $parametro->valor_minimo;
        $maximo = $parametro->valor_maximo;
        $unidad = $parametro->unidad_corto;
        $fueraLimite = false;
        $etiqueta = [ 'tipo' => null, 'info' => null ];

        //tiene ambos
        if ($minimo !== null && $maximo !== null) {
            if ($valor >= $minimo && $valor <= $maximo) {
                $etiqueta['tipo'] = 1;
                $etiqueta['info'] = 'Buena (' . $minimo . $unidad . ' - ' . $maximo . $unidad . ' )' ;
            }
            else if ($valor < $minimo) {
                $etiqueta['tipo'] = 2;
                $etiqueta['info'] = 'Inadecuada ( menor a ' . $minimo . $unidad . ' )' ;
                $fueraLimite = true;
            }
            else {
                $etiqueta['tipo'] = 2;
                $etiqueta['info'] = 'Inadecuada ( mayor a ' . $maximo . $unidad . ' )' ;
                $fueraLimite = true;
            }
        }
        // tiene minimo
        else if ($minimo !== null) {
            if ($valor >= $minimo) {
                $etiqueta['tipo'] = 1;
                $etiqueta['info'] = 'Buena ( mayor o igual a ' . $minimo . $unidad . ' )' ;
            }
            else {
                $etiqueta['tipo'] = 2;
                $etiqueta['info'] = 'Inadecuada ( menor a ' . $minimo . $unidad . ' )' ;
                $fueraLimite = true;
            }
        }
        else if ($maximo !== null) {
            if ($valor <= $maximo) {
                $etiqueta['tipo'] = 1;
                $etiqueta['info'] = 'Buena ( menor o igual a ' . $maximo . $unidad . ' )' ;
            }
            else {
                $etiqueta['tipo'] = 2;
                $etiqueta['info'] = 'Inadecuada ( mayor a ' . $maximo . $unidad . ' )' ;
                $fueraLimite = true;
            }
        }
        
        return [ $etiqueta, $fueraLimite ];
    }

    private function getEtiquetaWQI($parametro, $valor) {
        $valor_ideal = $parametro->valor_ideal;
        $valor_estandar_permisible = $parametro->valor_estandar_permisible;
        $unidad = $parametro->unidad_corto;
        $fueraLimite = false;
        $etiqueta = [ 'tipo' => null, 'info' => null ];

        $rating = abs(100 * ($valor - $valor_ideal) / ($valor_estandar_permisible - $valor_ideal));
        
        if ($rating <= 25) {
            $etiqueta['tipo'] = 1;
            $etiqueta['info'] = 'Excelente ( rating: ' . $rating . '% )';
        }
        else if ($rating <= 50) {
            $etiqueta['tipo'] = 2;
            $etiqueta['info'] = 'Buena ( rating: ' . $rating . '% )';
        }
        else if ($rating <= 75) {
            $fueraLimite = true;
            $etiqueta['tipo'] = 3;
            $etiqueta['info'] = 'Baja ( rating: ' . $rating . '% )';
        }
        else if ($rating <= 100) {
            $fueraLimite = true;
            $etiqueta['tipo'] = 4;
            $etiqueta['info'] = 'Muy baja ( rating: ' . $rating . '% )';
        }
        else {
            $fueraLimite = true;
            $etiqueta['tipo'] = 5;
            $etiqueta['info'] = 'Inadecuada ( rating: ' . $rating . '% )';
        }

        return [ $etiqueta, $fueraLimite ];
    }

    private function getEtiquetaAQI($parametro, $valor) {
        $aqi_1 = $parametro->aqi_1;
        $aqi_2 = $parametro->aqi_2;
        $aqi_3 = $parametro->aqi_3;
        $aqi_4 = $parametro->aqi_4;
        $aqi_5 = $parametro->aqi_5;
        $unidad = $parametro->unidad_corto;
        $fueraLimite = false;
        $etiqueta = [ 'tipo' => null, 'info' => null ];

        if ($valor <= $aqi_1) {
            $etiqueta['tipo'] = 1;
            $etiqueta['info'] = 'Buena ( 0' . $unidad. ' - ' . $aqi_1 . $unidad . ' )' ;
        }
        else if ($valor <= $aqi_2) {
            $etiqueta['tipo'] = 2;
            $etiqueta['info'] = 'Media (' . $aqi_1 . $unidad . ' - ' . $aqi_2 . $unidad . ' )' ;
        }
        else if ($valor <= $aqi_3) {
            $fueraLimite = true;
            $etiqueta['tipo'] = 3;
            $etiqueta['info'] = 'Dañina (' . $aqi_2 . $unidad . ' - ' . $aqi_3 . $unidad . ' )' ;
        }
        else if ($valor <= $aqi_4) {
            $fueraLimite = true;
            $etiqueta['tipo'] = 4;
            $etiqueta['info'] = 'No saludable (' . $aqi_3 . $unidad . ' - ' . $aqi_4 . $unidad . ' )' ;
        }
        else if ($valor <= $aqi_5) {
            $fueraLimite = true;
            $etiqueta['tipo'] = 5;
            $etiqueta['info'] = 'Muy insalubre (' . $aqi_4 . $unidad . ' - ' . $aqi_5 . $unidad . ' )' ;
        }
        else {
            $fueraLimite = true;
            $etiqueta['tipo'] = 6;
            $etiqueta['info'] = 'Peligrosa ( mayor a ' . $aqi_5 . $unidad . ' )' ;
        }

        return [ $etiqueta, $fueraLimite ];
    }
}
