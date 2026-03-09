<?php

namespace App\Filament\Admin\Resources\PriorityActions\RelationManagers;

use App\Models\Language;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class SearchTermsRelationManager extends RelationManager
{
    protected static string $relationship = 'searchTerms';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        $languages = Language::all();

        $inputs = $languages->map(function ($language) use ($languages) {
            return Forms\Components\TextInput::make('phrase.'.$language->id)
                ->label($language->getTranslation('name', 'en'))
                ->required($language->id === $languages->first()->id)
                ->maxLength(255);
        });

        return $schema->schema($inputs->toArray())
            ->columns(1);
    }

    public function table(Table $table): Table
    {
        $languages = Language::all();

        $columns = $languages->map(function ($language) {
            return Tables\Columns\TextColumn::make('phrase_'.$language->id)
                ->label($language->getTranslation('name', 'en'))
                ->getStateUsing(fn ($record) => $record->getTranslation('phrase', $language->id, useFallbackLocale: false));
        });

        return $table
            ->recordTitleAttribute('phrase')
            ->columns($columns->toArray())
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->groupedBulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
