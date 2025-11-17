<?php

namespace App\Filament\App\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use App\Filament\App\Resources\StatementResource\RelationManagers\EvidenceRelationManager;
use App\Filament\App\Resources\StatementResource\Pages\EditStatement;
use Filament\Forms;
use App\Models\Type;
use Filament\Tables;
use App\Models\Statement;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\App\Resources\StatementResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\App\Resources\StatementResource\RelationManagers;

class StatementResource extends Resource
{
    protected static ?string $model = Statement::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type_id')
                                ->label('Type')
                                ->options(Type::all()->pluck('name','id')->toArray())
                                ->required(),  
                Textarea::make('name')
                                ->rows(4)
                                ->label('Statement')
                                ->required(),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->recordActions([
               //
            ])
            ->toolbarActions([
                //
            ])
            ->emptyStateActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            EvidenceRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'edit' => EditStatement::route('/{record}/edit'),
        ];
    }
}
