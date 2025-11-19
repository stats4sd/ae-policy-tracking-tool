<?php

namespace App\Filament\App\Resources\PolicyDocuments\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\App\Resources\PolicyDocuments\PolicyDocumentResource;
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
