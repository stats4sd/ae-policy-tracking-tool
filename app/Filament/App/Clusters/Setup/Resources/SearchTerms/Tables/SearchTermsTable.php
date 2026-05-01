<?php

namespace App\Filament\App\Clusters\Setup\Resources\SearchTerms\Tables;

use App\Models\Language;
use App\Models\PriorityAction;
use App\Models\SearchTerm;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class SearchTermsTable
{
    public static function configure(Table $table): Table
    {
        $assessment = Filament::getTenant();
        $languageId = $assessment->language_id;

        return $table
            ->paginated(false)
            ->groups([
                Group::make('recommendation_id')
                    ->label('Recommendation')
                    ->getTitleFromRecordUsing(fn (PriorityAction $record): string => $record->recommendation->code_and_short_title),
            ])
            ->defaultGroup('recommendation_id')
            ->groupingSettingsHidden()
            ->columns([
                TextColumn::make('code_and_short_name')
                    ->wrap()
                    ->label('Priority Action'),
                TextColumn::make('phrases')
                    ->label('Search Terms')
                    ->getStateUsing(function (PriorityAction $record) use ($languageId): string {
                        return $record->searchTerms
                            ->map(fn (SearchTerm $st) => $st->getTranslation('phrase', $languageId ?? 'en', useFallbackLocale: true))
                            ->filter()
                            ->join('; ');
                    })
                    ->wrap(),
            ])
            ->recordActions([
                Action::make('editSearchTerms')
                    ->label('Edit')
                    ->icon(Heroicon::OutlinedPencilSquare)
                    ->modalWidth('2xl')
                    ->mountUsing(function (Schema $form, PriorityAction $record) use ($assessment): void {
                        $terms = SearchTerm::where('priority_action_id', $record->id)
                            ->where('assessment_id', $assessment->id)
                            ->get()
                            ->map(fn (SearchTerm $st) => [
                                'id' => $st->id,
                                'phrase' => $st->getTranslations('phrase'),
                            ])
                            ->toArray();

                        $form->fill(['searchTerms' => $terms]);
                    })
                    ->schema(function (): array {
                        $assessmentLanguageId = Filament::getTenant()->language_id;
                        $requiredLocales = collect([$assessmentLanguageId, 'en'])->filter()->unique();

                        $languages = Language::all()->sortBy(function (Language $language) use ($assessmentLanguageId): int {
                            if ($language->id === $assessmentLanguageId) {
                                return 0;
                            }
                            if ($language->id === 'en') {
                                return 1;
                            }

                            return 2;
                        })->values();

                        $phraseInputs = $languages->map(fn (Language $language) => TextInput::make('phrase.'.$language->id)
                            ->label($language->getTranslation('name', 'en'))
                            ->inlineLabel()
                            ->required($requiredLocales->contains($language->id))
                            ->maxLength(255)
                        )->toArray();

                        return [
                            Repeater::make('searchTerms')
                                ->label('Search Terms')
                                ->schema([
                                    Hidden::make('id'),
                                    ...$phraseInputs,
                                ])
                                ->addActionLabel('Add Search Term')
                                ->reorderable(false)
                                ->columns(2),
                        ];
                    })
                    ->action(function (array $data, PriorityAction $record) use ($assessment): void {
                        $submitted = collect($data['searchTerms']);

                        $keptIds = $submitted->pluck('id')->filter()->values();

                        // Delete removed terms
                        SearchTerm::where('priority_action_id', $record->id)
                            ->where('assessment_id', $assessment->id)
                            ->whereNotIn('id', $keptIds)
                            ->delete();

                        // Update or create
                        foreach ($submitted as $termData) {
                            if (! empty($termData['id'])) {
                                SearchTerm::find($termData['id'])?->update(['phrase' => $termData['phrase']]);
                            } else {
                                SearchTerm::create([
                                    'assessment_id' => $assessment->id,
                                    'priority_action_id' => $record->id,
                                    'phrase' => $termData['phrase'],
                                ]);
                            }
                        }
                    })
                    ->modalHeading(fn (PriorityAction $record) => 'Edit Search Terms: '.$record->code_and_short_name)
                    ->modalWidth('2xl'),
            ]);
    }
}
