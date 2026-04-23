<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

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
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFB0C4DE',
                ],
            ],
        ];
    }

    // Apply a green styling in a heatmap style to the document count columns, with the intensity of the green color corresponding to the value in the cell (darker green for higher values, lighter green for lower values)
    public function applyHeatMapStyles(Worksheet $sheet): void
    {
        // highlight the document count columns with increasing shades of green based on the value:
        // brightest green for the highest value, and lightest green for the lowest value
        $highest = null;
        $lowest = null;
        $columnCount = $this->assessment->policyDocuments->count();

        // find the highest count across all document count columns
        foreach (range(4, 3 + $columnCount) as $colIndex) {
            $columnValues = $sheet->rangeToArray($sheet->getCellByColumnAndRow($colIndex, 2)->getCoordinate().':'.$sheet->getCellByColumnAndRow($colIndex, $sheet->getHighestRow())->getCoordinate());
            foreach ($columnValues as $valueRow) {
                $value = $valueRow[0];
                if ($highest === null || $value > $highest) {
                    $highest = $value;
                }
                if ($lowest === null || $value < $lowest) {
                    $lowest = $value;
                }
            }
        }

        // 4 shades of green from light to dark
        $shades = [
            'FFE2EFDA',
            'FFC6E0B4',
            'FFA9D08E',
            'FF548235',
        ];

        // apply shading - darkest green for numbers in the highest quartile, lightest green for numbers in the lowest quartile
        foreach (range(3, 2 + $columnCount) as $colIndex) {
            foreach (range(2, $sheet->getHighestRow()) as $rowIndex) {
                $cell = $sheet->getCellByColumnAndRow($colIndex, $rowIndex);
                $value = $cell->getValue();

                // for 0 values, shade white
                if ($value == 0) {
                    $cell->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('FFFFFFFF');

                    continue;
                }

                $range = (int) $highest - (int) $lowest;
                if ($range > 0) {
                    $quartile = (int) (4 * ((int) $value - (int) $lowest) / $range);
                } else {
                    $quartile = 0;
                }
                $shadeIndex = min($quartile, 3); // ensure index is within bounds
                $cell->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB($shades[$shadeIndex]);
            }
        }
    }
}
