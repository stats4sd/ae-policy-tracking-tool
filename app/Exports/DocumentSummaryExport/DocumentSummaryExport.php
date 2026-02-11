<?php

namespace App\Exports\DocumentSummaryExport;

use App\Models\Assessment;
use App\Models\Recommendation;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DocumentSummaryExport implements WithMultipleSheets
{
    public function __construct(public Assessment $assessment) {}

    public function sheets(): array
    {

        $recommendations = Recommendation::with('priorityActions')->get();

        $sheets = [
            new DocumentSummaryList($this->assessment),
        ];

        foreach ($recommendations as $recommendation) {
            $sheets[] = new RecommendationExport($this->assessment, $recommendation);
        }

        $sheets[] = new AllRecommendationsExport($this->assessment);

        return $sheets;

    }
}
