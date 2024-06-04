<?php

namespace App\Filament\App\Resources\StatementResource\Pages;

use App\Filament\App\Resources\StatementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStatements extends ListRecords
{
    protected static string $resource = StatementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
