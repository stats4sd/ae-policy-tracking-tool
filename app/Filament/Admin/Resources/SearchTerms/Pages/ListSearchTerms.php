<?php

namespace App\Filament\Admin\Resources\SearchTerms\Pages;

use App\Filament\Admin\Resources\SearchTerms\SearchTermResource;
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
