<?php

namespace App\Filament\Admin\Resources\Countries;

use App\Filament\Admin\Resources\Countries\Pages\ListCountries;
use App\Models\Country;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CountryResource extends Resource
{
    protected static ?string $model = Country::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id')
                    ->label('ISO Alpha-3 Code')
                    ->required()
                    ->maxLength(3)
                    ->minLength(3)
                    ->readOnly(fn (string $operation): bool => $operation === 'edit'),
                TextInput::make('name')
                    ->required(),
                TextInput::make('iso2')
                    ->label('ISO Alpha-2 Code')
                    ->maxLength(2)
                    ->minLength(2),
                TextInput::make('un_code')
                    ->label('UN M49 Code')
                    ->maxLength(3)
                    ->minLength(3),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ISO-3')->sortable()->searchable(),
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('iso2')->label('ISO-2')->sortable(),
                TextColumn::make('un_code')->label('UN M49')->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                //
            ])
            ->emptyStateActions([
                CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCountries::route('/'),
        ];
    }
}
