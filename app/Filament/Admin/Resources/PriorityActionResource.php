<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PriorityActionResource\Pages\EditPriorityAction;
use App\Filament\Admin\Resources\PriorityActionResource\Pages\ListPriorityActions;
use App\Filament\Admin\Resources\PriorityActionResource\RelationManagers\SearchTermsRelationManager;
use App\Models\PriorityAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
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
                Forms\Components\Textarea::make('name')
                    ->rows(4),
                Forms\Components\TextInput::make('short_name'),
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
            'edit' => EditPriorityAction::route('/{record}/edit'),
        ];
    }
}
