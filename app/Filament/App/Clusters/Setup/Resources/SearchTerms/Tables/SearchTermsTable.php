<?php

namespace App\Filament\App\Clusters\Setup\Resources\SearchTerms\Tables;

use App\Models\SearchTerm;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SearchTermsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('priorityAction.code_and_short_name')
                    ->searchable(),
                TextColumn::make('phrase')
                    ->label(function (): string {
                        $language = Filament::getTenant()->language;

                        return $language
                            ? 'Phrase ('.$language->getTranslation('name', 'en').')'
                            : 'Phrase';
                    })
                    ->getStateUsing(function (SearchTerm $record): string {
                        $languageId = Filament::getTenant()->language_id;

                        return $languageId
                            ? $record->getTranslation('phrase', $languageId, useFallbackLocale: true)
                            : $record->phrase;
                    })
                    ->searchable(),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->filters([
                SelectFilter::make('recommendation')
                    ->relationship('recommendation', 'code_and_short_title'),
                SelectFilter::make('priorityAction')
                    ->relationship('priorityAction', 'code_and_short_name'),
            ])
            ->recordActions([
                EditAction::make()
                    ->modalHeading('Edit Search Term'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
