<?php

namespace App\Filament\Admin\Resources\PriorityActions\RelationManagers;

use App\Jobs\TranslateSearchTerm;
use App\Models\Language;
use App\Models\SearchTerm;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

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
                Action::make('auto_translate')
                    ->label('Auto Translate')
                    ->icon('heroicon-o-language')
                    ->requiresConfirmation()
                    ->modalDescription('This will automatically fill in any missing translations using the English phrase and Google Translate. Existing translations will not be overwritten.')
                    ->action(function (SearchTerm $record): void {
                        dispatch_sync(new TranslateSearchTerm($record));

                        Notification::make()
                            ->title('Translations updated')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->groupedBulkActions([
                BulkActionGroup::make([
                    BulkAction::make('auto_translate_selected')
                        ->label('Auto Translate')
                        ->icon('heroicon-o-language')
                        ->requiresConfirmation()
                        ->modalDescription('This will automatically fill in any missing translations for the selected search terms using the English phrase and Google Translate. Existing translations will not be overwritten.')
                        ->action(function (Collection $records): void {
                            foreach ($records as $record) {
                                dispatch_sync(new TranslateSearchTerm($record));
                            }

                            Notification::make()
                                ->title('Translations updated')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
