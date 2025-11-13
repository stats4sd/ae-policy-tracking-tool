<?php

namespace App\Filament\Admin\Resources\SearchTermResource\Pages;

use App\Filament\Admin\Resources\SearchTermResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSearchTerm extends EditRecord
{
    protected static string $resource = SearchTermResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
