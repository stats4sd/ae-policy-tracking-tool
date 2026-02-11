<?php

namespace App\Filament\Admin\Resources\AePrinciples\Pages;

use App\Filament\Admin\Resources\AePrinciples\AePrincipleResource;
use Filament\Actions\DeleteAction;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAePrinciple extends EditRecord
{
    protected static string $resource = AePrincipleResource::class;

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
