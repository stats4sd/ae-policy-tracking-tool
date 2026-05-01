<?php

namespace App\Filament\App\Clusters\Setup\Resources\SearchTerms\Pages;

use App\Filament\App\Clusters\Setup\Resources\SearchTerms\SearchTermResource;
use App\Filament\App\Clusters\Setup\Resources\SearchTerms\Tables\SearchTermsTable;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;

class ListSearchTerms extends ListRecords
{
    protected static string $resource = SearchTermResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function table(Table $table): Table
    {
        return SearchTermsTable::configure($table);
    }
}
