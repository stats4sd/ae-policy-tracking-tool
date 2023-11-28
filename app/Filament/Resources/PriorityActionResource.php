<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PriorityActionResource\Pages;
use App\Filament\Resources\PriorityActionResource\RelationManagers;
use App\Models\PriorityAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PriorityActionResource extends Resource
{
    protected static ?string $model = PriorityAction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('recommendation.name')->wrap(),
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
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPriorityActions::route('/'),
            // 'create' => Pages\CreatePriorityAction::route('/create'),
            'edit' => Pages\EditPriorityAction::route('/{record}/edit'),
        ];
    }    
}
