<?php

namespace App\Filament\Admin\Resources\PriorityActions\Pages;

use App\Filament\Admin\Resources\PriorityActions\PriorityActionResource;
use App\Filament\Admin\Resources\Recommendations\RecommendationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewPriorityAction extends ViewRecord
{
    protected static string $resource = PriorityActionResource::class;

    public function getBreadcrumbs(): array
    {
        $recommendation = $this->getRecord()->recommendation;

        return [
            RecommendationResource::getUrl('index') => RecommendationResource::getTitleCasePluralModelLabel(),
            RecommendationResource::getUrl('view', ['record' => $recommendation]) => $recommendation->code_and_short_title,
            $this->getTitle(),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->code_and_short_name;
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
