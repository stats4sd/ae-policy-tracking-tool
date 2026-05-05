<?php

namespace App\Exports\AssessmentDataExport;

use App\Exports\ExportStyles;
use App\Models\Assessment;
use App\Models\Theme;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ThemeExport implements FromCollection, WithColumnWidths, WithHeadings, WithMapping, WithStyles, WithTitle
{
    use ExportStyles;

    public function __construct(public Assessment $assessment) {}

    public function collection(): Collection
    {
        return Theme::with('priorityAction')
            ->withCount(['statements', 'extracts'])
            ->where('assessment_id', $this->assessment->id)
            ->get();
    }

    public function headings(): array
    {
        return [
            'Assessment ID',
            'Assessment',
            'Theme',
            'Priority Action',
            '# Summary Statements',
            '# Extracts',
        ];
    }

    public function map($row): array
    {
        return [
            $this->assessment->id,
            $this->assessment->title,
            $row->name,
            $row->priorityAction->title,
            $row->statements_count,
            $row->extracts_count,
        ];
    }

    public function title(): string
    {
        return 'Themes';
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle('1')->applyFromArray($this->headingStyle());
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,  // Assessment ID
            'B' => 30,  // Assessment
            'C' => 30,  // Theme
            'D' => 25,  // Priority Action
            'E' => 20,  // # Summary Statements
            'F' => 15,  // # Extracts
        ];
    }
}
