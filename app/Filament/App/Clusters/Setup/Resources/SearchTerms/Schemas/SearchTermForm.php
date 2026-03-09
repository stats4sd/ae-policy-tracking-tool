<?php

namespace App\Filament\App\Clusters\Setup\Resources\SearchTerms\Schemas;

use App\Models\Language;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SearchTermForm
{
    public static function configure(Schema $schema): Schema
    {
        $assessmentLanguageId = Filament::getTenant()->language_id;

        $requiredLocales = collect([$assessmentLanguageId, 'en'])->filter()->unique();

        $languages = Language::all();

        // Order: assessment language first, then English (if different), then others alphabetically.
        $orderedLanguages = $languages
            ->sortBy(function (Language $language) use ($assessmentLanguageId): int {
                if ($language->id === $assessmentLanguageId) {
                    return 0;
                }

                if ($language->id === 'en') {
                    return 1;
                }

                return 2;
            })
            ->values();

        $phraseInputs = $orderedLanguages->map(fn (Language $language) => TextInput::make('phrase.'.$language->id)
            ->label($language->getTranslation('name', 'en'))
            ->required($requiredLocales->contains($language->id))
            ->maxLength(255)
        )->toArray();

        return $schema
            ->columns(1)
            ->components([
                Select::make('priority_action_id')
                    ->relationship('priorityAction', 'code_and_short_name')
                    ->required(),
                ...$phraseInputs,
            ]);
    }
}
