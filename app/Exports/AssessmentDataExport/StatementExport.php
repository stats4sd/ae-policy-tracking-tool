<?php

namespace App\Exports\AssessmentDataExport;

use App\Exports\ExportStyles;
use App\Models\Assessment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StatementExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
{
    use ExportStyles;

    public function __construct(public Assessment $assessment) {}

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->assessment->statements;
    }

    public function headings(): array
    {
        return [
            'Assessment ID',
            'Assessment',
            'Priority Action',
            'Type of Statement',
            'Theme',
            'Statement Text',
            '# Supporting Extracts',
        ];
    }

    public function map($row): array
    {
        return [
            $this->assessment->id,
            $this->assessment->title,
            $row->priorityAction->title,
            $row->type->name,
            $row->theme->name ?? 'N/A',
            $row->name,
            $row->extracts()->count(),
        ];
    }

    public function title(): string
    {
        return 'Summary Statements';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('1')->applyFromArray($this->headingStyle());
    }
}
