<?php

namespace App\Exports;

use App\Models\Assessment;
use App\Models\Recommendation;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExtractByRecommendationExport implements WithMultipleSheets
{
    public function __construct(public Assessment $assessment) {}

    public function sheets(): array
    {
        $sheets = [];
        $recommendations = Recommendation::all();

        foreach ($recommendations as $recommendation) {
            $sheets[] = new ExtractByRecommendationSheet($this->assessment, $recommendation);
        }

        return $sheets;
    }
}
