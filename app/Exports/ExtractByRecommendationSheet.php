<?php

namespace App\Exports;

use App\Models\Assessment;
use App\Models\Extract;
use App\Models\PriorityAction;
use App\Models\Recommendation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExtractByRecommendationSheet implements FromCollection, WithColumnWidths, WithHeadings, WithMapping, WithStrictNullComparison, WithStyles, WithTitle
{
    use ExportStyles;

    public Collection $priorityActions;

    public function __construct(public Assessment $assessment, public Recommendation $recommendation)
    {
        $this->priorityActions = PriorityAction::where('recommendation_id', $recommendation->id)
            ->with(['extracts.score', 'extracts.policyDocument', 'extracts.theme'])
            ->get();
    }

    /**
     * @return SupportCollection
     */
    public function collection()
    {
        return $this->priorityActions->flatMap(fn ($pa) => $this->getExtractsForPriorityAction($pa));
    }

    public function getExtractsForPriorityAction(PriorityAction $priorityAction): SupportCollection
    {
        return $priorityAction->extracts
            ->filter(fn (Extract $extract) => $extract->policyDocument->assessment_id === $this->assessment->id)
            ->map(fn (Extract $extract) => [
                'priority_action' => $priorityAction,
                'type' => $extract->score,
                'policyDocument' => $extract->policyDocument,
                'page_number' => $extract->page_number,
                'extract' => $extract->formatted_extract,
                'automatic' => $extract->automatic,
                'theme' => $extract->theme,
            ]);
    }

    public function headings(): array
    {
        return [
            'Priority Action',
            'Type',
            'Policy Document',
            'Page',
            'Extract',
            'From Search Terms',
            'Themes Linked',
        ];
    }

    public function map($row): array
    {
        // row is an array due to the way we constructed it in getExtractsForPriorityAction
        // did this to make sure we get 1 row per priority_action x extract.

        return [
            $row['priority_action']->code_and_short_name,
            $row['type']?->name,
            $row['policyDocument']->short_title,
            $row['page_number'],
            $row['extract'],
            $row['automatic'] ? 'Yes' : 'No',
            $row['theme']?->name,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('1')->applyFromArray($this->headingStyle());

        // wrap text for the Extract column (E) and Priority Action Column (A)
        $sheet->getStyle('A')->getAlignment()->setWrapText(true);
        $sheet->getStyle('E')->getAlignment()->setWrapText(true);
    }

    public function title(): string
    {
        return 'Extracts for '.$this->recommendation->code_and_short_title;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,  // Priority Action
            'B' => 30,  // Type
            'C' => 30,  // Policy Document
            'D' => 10,  // Page
            'E' => 70,  // Extract
            'F' => 20,  // From Search Terms
            'G' => 25,  // Themes Linked
        ];
    }
}
