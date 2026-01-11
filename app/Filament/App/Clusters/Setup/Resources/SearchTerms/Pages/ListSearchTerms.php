<?php

namespace App\Filament\App\Clusters\Setup\Resources\SearchTerms\Pages;

use App\Filament\App\Clusters\Setup\Resources\SearchTerms\SearchTermResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSearchTerms extends ListRecords
{
    protected static string $resource = SearchTermResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
