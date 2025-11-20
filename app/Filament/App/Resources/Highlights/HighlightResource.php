<?php

namespace App\Filament\App\Resources\Highlights;

use App\Filament\App\Resources\Highlights\Pages\CreateHighlight;
use App\Filament\App\Resources\Highlights\Pages\EditHighlight;
use App\Filament\App\Resources\Highlights\Pages\ListHighlights;
use App\Filament\App\Resources\Highlights\Schemas\HighlightForm;
use App\Filament\App\Resources\Highlights\Tables\HighlightsTable;
use App\Models\Highlight;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HighlightResource extends Resource
{
    protected static ?string $model = Highlight::class;

    protected static ?string $navigationLabel = "2. Review Highlights";
    protected static string | BackedEnum | null $navigationIcon = "heroicon-o-check-badge";

    protected static ?string $recordTitleAttribute = 'extract';

    public static function form(Schema $schema): Schema
    {
        return HighlightForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HighlightsTable::configure($table);
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
            'index' => ListHighlights::route('/'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
