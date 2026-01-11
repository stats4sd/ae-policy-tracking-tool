<?php

namespace App\Exports;

trait ExportStyles
{
    public function headingStyle(): array
    {
        return [
            'font' => [
                'bold' => true,
            ],
            // fill light-blue
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFB0C4DE',
                ],
            ],
        ];
    }
}
