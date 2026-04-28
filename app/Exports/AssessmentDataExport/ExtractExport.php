<?php

namespace App\Exports\AssessmentDataExport;

use App\Exports\ExportStyles;
use App\Models\Assessment;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExtractExport implements FromCollection, WithColumnWidths, WithHeadings, WithMapping, WithStyles, WithTitle
{
    use ExportStyles;

    public function __construct(public Assessment $assessment) {}

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->assessment->extracts;
    }

    public function headings(): array
    {
        return [
            'Assessment ID',
            'Assessment',
            'Policy Document',
            'Page',
            'Extract',
            'From Automatic Search',
            'Theme',
            '# Statements',
            'Priority Action(s)',
        ];
    }

    public function map($row): array
    {
        return [
            $this->assessment->id,
            $this->assessment->title,
            $row->policyDocument->name,
            $row->page_number,
            $row->formatted_extract,
            $row->automatic ? 'Yes' : 'No',
            $row->theme?->name,
            $row->statements()->count(),
            $row->priorityActions()->pluck('priority_actions.id')->join(', '),
        ];
    }

    public function title(): string
    {
        return 'Document Extracts';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('1')->applyFromArray($this->headingStyle());

        // Wrap text for the 'Extract' column (E)
        $sheet->getStyle('E')->getAlignment()->setWrapText(true);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,  // Assessment ID
            'B' => 30,  // Assessment
            'C' => 30,  // Policy Document
            'D' => 10,  // Page
            'E' => 50,  // Extract
            'F' => 20,  // From Automatic Search
            'G' => 20,  // Theme
            'H' => 15,  // # Statements
            'I' => 25,  // Priority Action(s)
        ];
    }
}
