<?php

namespace App\Exports;

use App\Models\Assessment;
use App\Models\Recommendation;
use App\Models\Type;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AllRecommendationsExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStrictNullComparison, WithStyles, WithTitle
{
    use ExportStyles;

    public function __construct(public Assessment $assessment) {}

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $types = Type::all();
        $recommendations = Recommendation::all();
        $documents = $this->assessment->policyDocuments;

        return $recommendations->flatMap(function ($recommendation) use ($types, $documents) {
            return $types->map(function ($type) use ($recommendation, $documents) {
                $documentCountRows = $documents->mapWithKeys(function ($doc) use ($recommendation, $type) {
                    return [
                        $doc->short_title => $doc->highlights()
                            ->whereHas('priorityActions', fn ($query) => $query->where('priority_actions.recommendation_id', $recommendation->id))
                            ->whereHas('type', fn ($query) => $query->where('types.id', $type->id))
                            ->count(),
                    ];
                });

                return [
                    'recommendation' => $recommendation->code_and_short_title,
                    'type' => $type->name,
                    ...$documentCountRows,
                    'total' => $documentCountRows->sum(),
                ];
            });
        });
    }

    public function headings(): array
    {
        $headings = [
            'Recommendation',
            'Type',
        ];

        $documentShortTitles = $this->assessment->policyDocuments->pluck('short_title')->toArray();
        $headings = array_merge($headings, $documentShortTitles);
        $headings[] = 'Total';

        return $headings;
    }

    public function map($row): array
    {
        $map = [
            $row['recommendation'],
            $row['type'],
        ];

        $this->assessment->policyDocuments->each(function ($doc) use (&$map, $row) {
            $map[] = $row[$doc->short_title] ?? 0;
        });

        $map[] = $row['total'];

        return $map;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('1')->applyFromArray($this->headingStyle());

        // highlight the document count columns with increasing shades of green based on the value:
        // brightest green for the highest value, and lightest green for the lowest value
        $highest = null;
        $lowest = null;
        $columnCount = $this->assessment->policyDocuments->count();

        // find the highest count across all document count columns
        foreach (range(3, 2 + $columnCount) as $colIndex) {
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
                    $cell->getStyle()->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('FFFFFFFF');

                    continue;
                }

                if ($highest !== $lowest) {
                    $quartile = (int) (4 * ($value - $lowest) / ($highest - $lowest));
                } else {
                    $quartile = 0;
                }
                $shadeIndex = min($quartile, 3); // ensure index is within bounds
                $cell->getStyle()->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB($shades[$shadeIndex]);
            }
        }
    }

    public function title(): string
    {
        return 'All Recommendations - Summary';
    }
}
