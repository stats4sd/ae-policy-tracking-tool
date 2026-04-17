<?php

use App\Jobs\TranslateSearchTerm;
use App\Models\Assessment;
use App\Models\Language;
use App\Models\PriorityAction;
use App\Models\Recommendation;
use App\Models\SearchTerm;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

describe('TranslateSearchTerm', function () {

    beforeEach(function () {
        Language::factory()->create(['id' => 'en', 'name' => ['en' => 'English']]);
        Language::factory()->create(['id' => 'fr', 'name' => ['en' => 'French']]);
        Language::factory()->create(['id' => 'es', 'name' => ['en' => 'Spanish']]);
    });

    it('implements ShouldQueue', function () {
        expect(TranslateSearchTerm::class)->toImplement(ShouldQueue::class);
    });

    it('dispatches to queue', function () {
        Queue::fake();

        $assessment = Assessment::factory()->create();
        $recommendation = Recommendation::factory()->create();
        $priorityAction = PriorityAction::factory()->create(['recommendation_id' => $recommendation->id]);
        $searchTerm = SearchTerm::factory()->create([
            'assessment_id' => $assessment->id,
            'priority_action_id' => $priorityAction->id,
            'phrase' => ['en' => 'irrigation'],
        ]);

        TranslateSearchTerm::dispatch($searchTerm);

        Queue::assertPushed(TranslateSearchTerm::class);
    });

    it('skips languages that already have a translation', function () {
        Http::fake([
            'https://translation.googleapis.com/*' => Http::response([
                'data' => ['translations' => [['translatedText' => 'irrigation']]],
            ], 200),
        ]);

        $assessment = Assessment::factory()->create();
        $recommendation = Recommendation::factory()->create();
        $priorityAction = PriorityAction::factory()->create(['recommendation_id' => $recommendation->id]);
        $searchTerm = SearchTerm::factory()->create([
            'assessment_id' => $assessment->id,
            'priority_action_id' => $priorityAction->id,
            'phrase' => ['en' => 'irrigation', 'fr' => 'irrigation'],
        ]);

        // Run in production mode to use the real API call
        app()->detectEnvironment(fn () => 'production');

        (new TranslateSearchTerm($searchTerm))->handle();

        // fr was already set, so the API should only be called for 'es'
        Http::assertSentCount(1);
    });

    it('returns early when no English phrase is set', function () {
        Http::fake();

        $assessment = Assessment::factory()->create();
        $recommendation = Recommendation::factory()->create();
        $priorityAction = PriorityAction::factory()->create(['recommendation_id' => $recommendation->id]);
        $searchTerm = SearchTerm::factory()->create([
            'assessment_id' => $assessment->id,
            'priority_action_id' => $priorityAction->id,
            'phrase' => [],
        ]);

        (new TranslateSearchTerm($searchTerm))->handle();

        Http::assertNothingSent();
    });

    it('saves translated phrases using the paid API in production', function () {
        Http::fake([
            'https://translation.googleapis.com/*' => Http::response([
                'data' => ['translations' => [['translatedText' => 'irrigación']]],
            ], 200),
        ]);

        $assessment = Assessment::factory()->create();
        $recommendation = Recommendation::factory()->create();
        $priorityAction = PriorityAction::factory()->create(['recommendation_id' => $recommendation->id]);
        $searchTerm = SearchTerm::factory()->create([
            'assessment_id' => $assessment->id,
            'priority_action_id' => $priorityAction->id,
            'phrase' => ['en' => 'irrigation'],
        ]);

        // Force production environment
        app()->detectEnvironment(fn () => 'production');

        (new TranslateSearchTerm($searchTerm))->handle();

        $searchTerm->refresh();
        // Should have saved at least one translation (the mocked response)
        expect($searchTerm->getTranslations('phrase'))->toHaveKey('en');
    });

    it('halts when the API returns a failure response', function () {
        Http::fake([
            'https://translation.googleapis.com/*' => Http::response(['error' => 'API key missing'], 403),
        ]);

        $assessment = Assessment::factory()->create();
        $recommendation = Recommendation::factory()->create();
        $priorityAction = PriorityAction::factory()->create(['recommendation_id' => $recommendation->id]);
        $searchTerm = SearchTerm::factory()->create([
            'assessment_id' => $assessment->id,
            'priority_action_id' => $priorityAction->id,
            'phrase' => ['en' => 'seed variety'],
        ]);

        app()->detectEnvironment(fn () => 'production');

        // Should not throw, just halt silently (notification is sent)
        (new TranslateSearchTerm($searchTerm))->handle();

        // Neither fr nor es should be saved since the API failed on the first call
        $searchTerm->refresh();
        expect($searchTerm->getTranslation('phrase', 'fr', useFallbackLocale: false))->toBeEmpty();
    });

});
