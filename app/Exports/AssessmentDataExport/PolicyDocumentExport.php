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

class PolicyDocumentExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles, WithTitle, WithMapping
{
    use ExportStyles;

    public function __construct(public Assessment $assessment) {}

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->assessment->policyDocuments()->get();
    }

    public function map($row): array
    {
        return [
            $this->assessment->id,
            $this->assessment->title,
            $row->name,
            $row->comments,
            $row->highlights()->count(),
            $row->statements()->count(),
        ];
    }

    public function headings(): array
    {
        return [
            'Assessment ID',
            'Assessment',
            'Name',
            'Comments',
            '# Highlights',
            '# Statements',
        ];
    }

    public function title(): string
    {
        return 'Policy Documents';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('1')->applyFromArray($this->headingStyle());
    }
}
