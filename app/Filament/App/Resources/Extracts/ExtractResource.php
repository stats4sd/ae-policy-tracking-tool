<?php

namespace App\Filament\App\Resources\Extracts;

use App\Filament\App\Resources\Extracts\Pages\ListExtracts;
use App\Filament\App\Resources\Extracts\Schemas\ExtractForm;
use App\Filament\App\Resources\Extracts\Tables\ExtractTable;
use App\Models\Extract;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExtractResource extends Resource
{
    protected static ?string $model = Extract::class;

    protected static ?string $navigationLabel = '2. Review Extracts';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckBadge;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::CheckBadge;

    protected static ?string $recordTitleAttribute = 'extract';

    public static function form(Schema $schema): Schema
    {
        return ExtractForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExtractTable::configure($table);
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
            'index' => ListExtracts::route('/'),
        ];
    }

    // # Custom tenancy query filter due to complex relationship between Extract and Assessment.
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if ($tenant = Filament::getTenant()) {
            $query->whereHas('policyDocument', fn (Builder $q) => $q->where('assessment_id', $tenant->getKey()));
        }

        return $query;
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
