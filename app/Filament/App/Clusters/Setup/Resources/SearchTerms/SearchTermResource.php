<?php

namespace App\Filament\App\Clusters\Setup\Resources\SearchTerms;

use App\Filament\App\Clusters\Setup\Resources\SearchTerms\Pages\ListSearchTerms;
use App\Filament\App\Clusters\Setup\Resources\SearchTerms\Schemas\SearchTermForm;
use App\Filament\App\Clusters\Setup\Resources\SearchTerms\Tables\SearchTermsTable;
use App\Filament\App\Clusters\Setup\SetupCluster;
use App\Models\PriorityAction;
use App\Models\SearchTerm;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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

    // override the default query; we want search terms by Priority Action for the table display
    public static function getEloquentQuery(): Builder
    {
        return PriorityAction::query()
            ->with(['recommendation', 'searchTerms' => fn ($q) => $q->where('assessment_id', Filament::getTenant()->id)])
            ->orderBy('id');
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
