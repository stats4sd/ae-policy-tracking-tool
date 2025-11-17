<?php

namespace App\Filament\App\Resources\PolicyDocumentResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\App\Resources\PolicyDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPolicyDocument extends EditRecord
{
    protected static string $resource = PolicyDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
