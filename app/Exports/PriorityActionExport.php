<?php

namespace App\Exports;

use App\Models\Assessment;
use App\Models\PriorityAction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PriorityActionExport implements FromCollection, WithTitle, WithHeadings, WithStyles, WithColumnWidths, WithMapping
{
    use ExportStyles;

    public function __construct(public Assessment $assessment)
    {
    }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return PriorityAction::with(['statements', 'highlights', 'themes'])->get();
    }

        public function headings(): array
    {
        return [
            'Assessment ID',
            'Assessment',
            'Priority Action',
            'Priority Action Text',
            '# Themes',
            '# Statements',
            '# Highlights',
        ];
    }

    public function map($row): array
    {
        return [
            $this->assessment->id,
            $this->assessment->id,
            $row->id,
            $row->name,
            $row->themes()->count(),
            $row->statements()->count(),
            $row->highlights()->count(),
        ];
    }

    public function title(): string
    {
        return 'Priority Actions';
    }


    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('1')->applyFromArray($this->headingStyle());
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,
            'B' => 12,
            'C' => 15,
            'D' => 30,
            'E' => 10,
            'F' => 10,
            'G' => 10,
        ];
    }
}
