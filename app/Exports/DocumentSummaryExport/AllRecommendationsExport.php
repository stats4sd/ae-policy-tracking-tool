<?php

namespace App\Exports\DocumentSummaryExport;

use App\Exports\ExportStyles;
use App\Models\Assessment;
use App\Models\PolicyDocument;
use App\Models\Recommendation;
use App\Models\Score;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AllRecommendationsExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStrictNullComparison, WithStyles, WithTitle
{
    use ExportStyles;

    public function __construct(public Assessment $assessment) {}

    public function collection(): Collection
    {
        $types = Score::all();
        $recommendations = Recommendation::all();
        $documents = $this->assessment->policyDocuments;

        return $recommendations->flatMap(function ($recommendation) use ($types, $documents) {
            return $types->map(function (Score $type) use ($recommendation, $documents) {
                $documentCountRows = $documents->mapWithKeys(function (PolicyDocument $doc) use ($recommendation, $type) {
                    return [
                        $doc->highlights()
                            ->whereHas('priorityActions', fn ($query) => $query->where('priority_actions.recommendation_id', $recommendation->id))
                            ->whereHas('score', fn ($query) => $query->where('types.id', $type->id))
                            ->count(),
                    ];
                });

                return [
                    $recommendation->code_and_short_title,
                    $type->name,
                    ...$documentCountRows,
                    $documentCountRows->sum(),
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

        $this->assessment->policyDocuments->each(function (PolicyDocument $doc) use (&$map, $row) {
            $map[] = $row[$doc->short_title] ?? 0;
        });

        $map[] = $row['total'];

        return $map;
    }

    /**
     * @throws Exception
     */
    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle('1')->applyFromArray($this->headingStyle());
        $this->applyHeadMapStyles($sheet);
    }

    public function title(): string
    {
        return 'All Recommendations - Summary';
    }
}
