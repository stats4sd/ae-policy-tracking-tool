<?php

namespace App\Filament\Admin\Resources\AePrincipleResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Admin\Resources\AePrincipleResource;
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
