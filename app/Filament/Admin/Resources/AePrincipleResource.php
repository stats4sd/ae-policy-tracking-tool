<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AePrincipleResource\Pages\EditAePrinciple;
use App\Filament\Admin\Resources\AePrincipleResource\Pages\ListAePrinciples;
use App\Models\AePrinciple;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AePrincipleResource extends Resource
{
    protected static ?string $model = AePrinciple::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Agroecology Principles';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAePrinciples::route('/'),
            // 'create' => Pages\CreateAePrinciple::route('/create'),
            'edit' => EditAePrinciple::route('/{record}/edit'),
        ];
    }
}
