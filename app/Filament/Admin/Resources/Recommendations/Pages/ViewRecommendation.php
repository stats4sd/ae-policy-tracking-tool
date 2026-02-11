<?php

namespace App\Filament\Admin\Resources\Recommendations\Pages;

use App\Filament\Admin\Resources\Recommendations\RecommendationResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewRecommendation extends ViewRecord
{
    protected static string $resource = RecommendationResource::class;

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->code_and_short_title;
    }

}
