<?php

namespace App\Exports;

use App\Models\Assessment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AssessmentExport implements WithMultipleSheets
{

    public function __construct(public Assessment $assessment)
    {
    }

    public function sheets(): array
    {
        return [
            new AssessmentSummaryExport($this->assessment),
            new PolicyDocumentExport($this->assessment),
            new HighlightExport($this->assessment),
            new StatementExport($this->assessment),
            new PriorityActionExport($this->assessment),
        ];
    }
}
