<?php

namespace App\Filament\Admin\Resources\RecommendationResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Admin\Resources\RecommendationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRecommendation extends EditRecord
{
    protected static string $resource = RecommendationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
