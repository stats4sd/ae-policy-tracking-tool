<?php

namespace App\Filament\Admin\Resources\Recommendations\Pages;

use App\Filament\Admin\Resources\Recommendations\RecommendationResource;
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
