<?php

namespace App\Jobs;

use App\Models\DefaultSearchTerm;
use App\Models\Language;
use App\Models\SearchTerm;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Stichoza\GoogleTranslate\GoogleTranslate;

class TranslateSearchTerm implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly SearchTerm|DefaultSearchTerm $searchTerm) {}

    public function handle(): void
    {
        $englishPhrase = $this->searchTerm->getTranslation('phrase', 'en', useFallbackLocale: false);

        if (blank($englishPhrase)) {
            return;
        }

        $languages = Language::all()->reject(fn ($language) => $language->id === 'en');

        foreach ($languages as $language) {
            $existing = $this->searchTerm->getTranslation('phrase', $language->id, useFallbackLocale: false);

            if (filled($existing)) {
                continue;
            }

            $translated = app()->isProduction()
                ? $this->callGoogleTranslateApi($englishPhrase, $language->id)
                : $this->callGoogleTranslateFree($englishPhrase, $language->id);

            if ($translated === null) {
                // Return early if translation failed, to avoid saving incomplete translations to the database.
                Notification::make()
                    ->title('Translation Failed')
                    ->body("Failed to translate '{$englishPhrase}' to {$language->id}. The Translation API returned an error, so the translation task was halted.")
                    ->danger()
                    ->send();

                return;
            }

            $this->searchTerm->setTranslation('phrase', $language->id, $translated);
        }

        $this->searchTerm->save();
    }

    /**
     * Translate using the official Google Cloud Translation API v2.
     *
     * Requires a valid GOOGLE_TRANSLATE_API_KEY with the Cloud Translation API enabled.
     * This is the recommended method for production use.
     */
    private function callGoogleTranslateApi(string $text, string $targetLocale): ?string
    {
        $response = Http::get('https://translation.googleapis.com/language/translate/v2', [
            'key' => config('services.google_translate.key'),
            'q' => $text,
            'source' => 'en',
            'target' => $targetLocale,
            'format' => 'text',
        ]);

        if ($response->failed()) {
            Log::error('Google Translate API error', [
                'search_term_id' => $this->searchTerm->id,
                'target_locale' => $targetLocale,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            Notification::make()
                ->title('Translation Failed')
                ->body("Failed to translate '{$text}' to {$targetLocale}. Status {$response->status()}: {$response->body()}")
                ->danger()
                ->send();

            return null;
        }

        return $response->json('data.translations.0.translatedText');
    }

    /**
     * Translate using the stichoza/google-translate-php package.
     *
     * This uses Google Translate's unofficial free API — no API key required.
     * Suitable for local development and testing only. Do not use in production,
     * as it may be rate-limited or blocked by Google without warning.
     */
    private function callGoogleTranslateFree(string $text, string $targetLocale): ?string
    {
        try {
            return GoogleTranslate::trans($text, $targetLocale, 'en');
        } catch (\Exception $e) {
            Log::error('Google Translate (free) error', [
                'search_term_id' => $this->searchTerm->id,
                'target_locale' => $targetLocale,
                'message' => $e->getMessage(),
            ]);

            Notification::make()
                ->title('Translation Failed')
                ->body("Failed to translate '{$text}' to {$targetLocale}: {$e->getMessage()}")
                ->danger()
                ->send();

            return null;
        }
    }
}
