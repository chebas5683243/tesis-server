<?php

namespace App\Exports;

use App\Models\Parameter;
use Illuminate\Contracts\View\View;
use App\Models\MonitoringPointParameter;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RecordTemplateExport implements FromView, WithStyles, WithEvents
{
    use Exportable;

    public function __construct(int $puntoId)
    {
        $this->puntoId = $puntoId;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => [
                        'argb' => Color::COLOR_WHITE,
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => '1BA82F',
                    ],
                ],
            ],
            2 => [
                'font' => [
                    'regular' => true,
                    'color' => [
                        'argb' => Color::COLOR_BLACK,
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'E1E1E1',
                    ],
                ],
            ],
            3 => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getRowDimension('1')->setRowHeight(20);
                $event->sheet->getDelegate()->getRowDimension('2')->setRowHeight(20);
                $event->sheet->getDelegate()->getDefaultColumnDimension()->setWidth(25);
     
            },
        ];
    }

    public function view(): View
    {
        $parametros = MonitoringPointParameter::with('parametro.unidad')->where('monitoring_point_id', $this->puntoId)->get();

        foreach($parametros as $parametro) {
            $parametro->nombre = $parametro->parametro->nombre;
            $parametro->nombre_corto = $parametro->parametro->nombre_corto;
            $parametro->unidad_corto = $parametro->parametro->unidad->nombre_corto;
        }

        return view('exports.record_template', [
            'parametros' => $parametros
        ]);
    }
}
