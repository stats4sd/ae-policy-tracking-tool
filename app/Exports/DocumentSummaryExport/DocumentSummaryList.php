<?php

namespace App\Exports\DocumentSummaryExport;

use App\Exports\ExportStyles;
use App\Models\Assessment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DocumentSummaryList implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    use ExportStyles;

    public function __construct(public Assessment $assessment) {}

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->assessment->policyDocuments;
    }

    public function headings(): array
    {
        return [
            'Document Name',
            'Document Short name',
            'Year(s)',
            'Comments',
            'Number of Extracts',
            'Number of Statements',
        ];
    }

    public function map($row): array
    {
        return [
            $row->name,
            $row->short_title,
            $row->year_string,
            $row->comments,
            $row->extracts()->count(),
            $row->statements()->count(),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('1')->applyFromArray($this->headingStyle());
    }

    public function title(): string
    {
        return 'Document List';
    }
}
