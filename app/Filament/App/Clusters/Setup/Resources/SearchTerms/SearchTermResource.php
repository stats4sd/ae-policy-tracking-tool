<?php

namespace App\Filament\App\Clusters\Setup\Resources\SearchTerms;

use App\Filament\App\Clusters\Setup\Resources\SearchTerms\Pages\CreateSearchTerm;
use App\Filament\App\Clusters\Setup\Resources\SearchTerms\Pages\EditSearchTerm;
use App\Filament\App\Clusters\Setup\Resources\SearchTerms\Pages\ListSearchTerms;
use App\Filament\App\Clusters\Setup\Resources\SearchTerms\Schemas\SearchTermForm;
use App\Filament\App\Clusters\Setup\Resources\SearchTerms\Tables\SearchTermsTable;
use App\Filament\App\Clusters\Setup\SetupCluster;
use App\Models\SearchTerm;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SearchTermResource extends Resource
{
    protected static ?string $cluster = SetupCluster::class;

    protected static ?string $model = SearchTerm::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMagnifyingGlassPlus;
    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'phrase';

    public static function form(Schema $schema): Schema
    {
        return SearchTermForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SearchTermsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSearchTerms::route('/'),
        ];
    }
}
