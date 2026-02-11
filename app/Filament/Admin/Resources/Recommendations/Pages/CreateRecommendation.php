<?php

namespace App\Filament\Admin\Resources\Recommendations\Pages;

use App\Filament\Admin\Resources\Recommendations\RecommendationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRecommendation extends CreateRecord
{
    protected static string $resource = RecommendationResource::class;
}
