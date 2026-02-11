<?php

namespace App\Filament\Admin\Resources\Scores\Pages;

use App\Filament\Admin\Resources\Scores\ScoreResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditScore extends EditRecord
{
    protected static string $resource = ScoreResource::class;

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
