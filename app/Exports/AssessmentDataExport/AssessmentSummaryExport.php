<?php

namespace App\Exports\AssessmentDataExport;

use App\Exports\ExportStyles;
use App\Models\Assessment;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AssessmentSummaryExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
{
    use ExportStyles;

    public function __construct(public Assessment $assessment) {}

    /**
     * @return Collection
     */
    public function collection()
    {
        return collect([
            [
                'Assessment ID' => $this->assessment->id,
                'Assessment' => $this->assessment->title,
                'Jurisdiction' => $this->assessment->jurisdiction?->name,
                'Year' => $this->assessment->year,
                'Created At' => $this->assessment->created_at,
                'Finalized At' => $this->assessment->finalized_at,
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'Assessment ID',
            'Assessment',
            'Jurisdiction',
            'Year',
            'Created At',
            'Finalized At',
        ];
    }

    public function title(): string
    {
        return 'Assessment Summary';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('1')->applyFromArray($this->headingStyle());
    }
}
