<?php

namespace App\Exports;

use App\Models\Assessment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HighlightExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithColumnWidths
{
    use ExportStyles;

    public function __construct(public Assessment $assessment) {}

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->assessment->highlights;
    }

    public function headings(): array
    {
        return [
            'Assessment ID',
            'Assessment',
            'Policy Document',
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
            $row->extract,
            $row->automatic ? 'Yes' : 'No',
            $row->theme?->name,
            $row->statements()->count(),
            $row->priorityActions()->pluck('priority_actions.id')->join(', '),
        ];
    }

    public function title(): string
    {
        return 'Document Highlights';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('1')->applyFromArray($this->headingStyle());

        // Wrap text for the 'Extract' column (D)
        $sheet->getStyle('D')->getAlignment()->setWrapText(true);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,  // Assessment ID
            'B' => 30,  // Assessment
            'C' => 30,  // Policy Document
            'D' => 50,  // Extract
            'E' => 20,  // From Automatic Search
            'F' => 20,  // Theme
            'G' => 15,  // # Statements
            'H' => 25,  // Priority Action(s)
        ];
    }
}
