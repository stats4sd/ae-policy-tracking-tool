<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PriorityActionResource\RelationManagers\SearchTermsRelationManager;
use App\Models\PriorityAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PriorityActionResource extends Resource
{
    protected static ?string $model = PriorityAction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Forms\Components\TextInput::make('id')
                ->label('ID')
                ->disabledOn('edit'),
                Forms\Components\Textarea::make('name')
                                ->rows(4)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name')->wrap(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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
            'index' => \App\Filament\Admin\Resources\PriorityActionResource\Pages\ListPriorityActions::route('/'),
            // 'create' => Pages\CreatePriorityAction::route('/create'),
            'edit' => \App\Filament\Admin\Resources\PriorityActionResource\Pages\EditPriorityAction::route('/{record}/edit'),
        ];
    }
}
