<?php

namespace App\Filament\Admin\Resources\PriorityActions;

use App\Filament\Admin\Resources\PriorityActions\Pages\ListPriorityActions;
use App\Filament\Admin\Resources\PriorityActions\Pages\ViewPriorityAction;
use App\Filament\Admin\Resources\PriorityActions\RelationManagers\SearchTermsRelationManager;
use App\Models\PriorityAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PriorityActionResource extends Resource
{
    protected static ?string $model = PriorityAction::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->label('ID')
                    ->disabledOn('edit'),
                Forms\Components\TextInput::make('short_name'),
                Forms\Components\Textarea::make('name')
                    ->label('Long Name / Description')
                    ->rows(4),
            ]);
    }

    // Add an infolist method to display the long name/description when viewing a record
    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Details')
                    ->schema([
                        TextEntry::make('id')->label('ID')->inlineLabel(),
                        TextEntry::make('short_name')->label('Short Name')->inlineLabel(),
                        TextEntry::make('name')->label('Description')->inlineLabel(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->wrap(),
                TextColumn::make('short_name')->wrap(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            SearchTermsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPriorityActions::route('/'),
            'view' => ViewPriorityAction::route('/{record}'),
        ];
    }
}
