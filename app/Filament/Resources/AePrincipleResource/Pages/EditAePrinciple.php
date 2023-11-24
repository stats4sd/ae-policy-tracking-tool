<?php

namespace App\Filament\Resources\AePrincipleResource\Pages;

use App\Filament\Resources\AePrincipleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAePrinciple extends EditRecord
{
    protected static string $resource = AePrincipleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
