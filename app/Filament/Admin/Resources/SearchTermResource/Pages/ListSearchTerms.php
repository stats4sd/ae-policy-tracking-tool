<?php

namespace App\Filament\Admin\Resources\SearchTermResource\Pages;

use App\Filament\Admin\Resources\SearchTermResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSearchTerms extends ListRecords
{
    protected static string $resource = SearchTermResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
