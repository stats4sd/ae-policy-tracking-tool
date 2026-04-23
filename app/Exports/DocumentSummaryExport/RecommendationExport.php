<?php

namespace App\Exports\DocumentSummaryExport;

use App\Exports\ExportStyles;
use App\Models\Assessment;
use App\Models\PolicyDocument;
use App\Models\PriorityAction;
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

class RecommendationExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStrictNullComparison, WithStyles, WithTitle
{
    use ExportStyles;

    public function __construct(public Assessment $assessment, public Recommendation $recommendation) {}

    public function collection(): Collection
    {
        // get list of each Priority Action x Type
        $types = Score::all();
        $documents = $this->assessment->policyDocuments;

        return $this->recommendation->priorityActions
            ->flatMap(function (PriorityAction $pa) use ($types, $documents) {
                return $types->map(function ($type) use ($pa, $documents) {

                    $documentCountRows = $documents->mapWithKeys(function (PolicyDocument $doc) use ($pa, $type) {
                        return [
                            $doc->short_title => $doc->extracts()
                                ->whereHas('priorityActions', fn ($query) => $query->where('priority_actions.id', $pa->id))
                                ->whereHas('score', fn ($query) => $query->where('scores.id', $type->id))
                                ->count(),
                        ];
                    });

                    return [
                        'priority_action' => $pa,
                        'type' => $type,
                        ...$documentCountRows->toArray(),
                        'total' => $documentCountRows->sum(),
                    ];
                });
            });
    }

    public function headings(): array
    {
        $headings = [
            'Priority Action',
            'Type',
        ];

        $this->assessment->policyDocuments->each(function (PolicyDocument $doc) use (&$headings) {
            $headings[] = $doc->short_title;
        });

        $headings[] = 'Total';

        return $headings;
    }

    public function map($row): array
    {
        $map = [
            $row['priority_action']->code_and_short_name,
            $row['type']->name,
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
        return 'Recommendation '.$this->recommendation->code;
    }
}
