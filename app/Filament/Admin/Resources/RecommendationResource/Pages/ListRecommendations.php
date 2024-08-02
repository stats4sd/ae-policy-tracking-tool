<?php

namespace App\Filament\Admin\Resources\RecommendationResource\Pages;

use App\Filament\Admin\Resources\RecommendationResource;
use Filament\Resources\Pages\ListRecords;

class ListRecommendations extends ListRecords
{
    protected static string $resource = RecommendationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

}
