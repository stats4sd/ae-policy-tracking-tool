<?php

namespace App\Filament\Admin\Resources\SearchTerms;

use App\Filament\Admin\Resources\SearchTerms\Pages;
use App\Models\SearchTerm;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class SearchTermResource extends Resource
{
    protected static ?string $model = SearchTerm::class;

    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('priority_action_id')
                    ->relationship('priorityAction', 'code_and_name')
                    ->required()
                    ->label('Select the related priority action'),
                Forms\Components\TextInput::make('phrase')
                    ->required()
                    ->label('Enter the search word or phrase'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('phrase'),
                Tables\Columns\TextColumn::make('priorityAction.name')->label('Related Priority Action'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('priority_action_id')
                    ->relationship('priorityAction', 'code_and_name')
                    ->label('Filter by Priority Action'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->groupedBulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListSearchTerms::route('/'),
            'create' => Pages\CreateSearchTerm::route('/create'),
            'edit' => Pages\EditSearchTerm::route('/{record}/edit'),
        ];
    }
}
