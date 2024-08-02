<?php

namespace App\Filament\App\Resources\PolicyResource\Pages;

use App\Filament\App\Resources\PolicyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPolicy extends EditRecord
{
    protected static string $resource = PolicyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
