<?php

namespace App\Imports;

use DateTime;
use App\Models\Record;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
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
            }
            else {
                $newValue->valor_cualitativo = $row[$parametro->nombre_slug];
            }
            $newValue->save();
        }
    }

    private function getExcelDateTime($fechaExcel, $horaExcel)
    {
        $datetime = gmdate("Y-m-d H:i:s",($fechaExcel + $horaExcel - 25569) * 86400);
        return $datetime;
    }
}
