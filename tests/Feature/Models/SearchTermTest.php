<?php

use App\Models\Assessment;
use App\Models\PriorityAction;
use App\Models\Recommendation;
use App\Models\SearchTerm;

describe('SearchTerm model', function () {

    it('phrase field is translatable', function () {
        $assessment = Assessment::factory()->create();
        $recommendation = Recommendation::factory()->create();
        $priorityAction = PriorityAction::factory()->create(['recommendation_id' => $recommendation->id]);

        $searchTerm = SearchTerm::factory()->create([
            'assessment_id' => $assessment->id,
            'priority_action_id' => $priorityAction->id,
            'phrase' => ['en' => 'soil health', 'fr' => 'santé des sols'],
        ]);

        expect($searchTerm->getTranslation('phrase', 'en', useFallbackLocale: false))->toBe('soil health')
            ->and($searchTerm->getTranslation('phrase', 'fr', useFallbackLocale: false))->toBe('santé des sols');
    });

    it('belongs to an assessment', function () {
        $assessment = Assessment::factory()->create();
        $recommendation = Recommendation::factory()->create();
        $priorityAction = PriorityAction::factory()->create(['recommendation_id' => $recommendation->id]);
        $searchTerm = SearchTerm::factory()->create([
            'assessment_id' => $assessment->id,
            'priority_action_id' => $priorityAction->id,
        ]);

        expect($searchTerm->assessment->id)->toBe($assessment->id);
    });

    it('belongs to a priority action', function () {
        $assessment = Assessment::factory()->create();
        $recommendation = Recommendation::factory()->create();
        $priorityAction = PriorityAction::factory()->create(['recommendation_id' => $recommendation->id]);
        $searchTerm = SearchTerm::factory()->create([
            'assessment_id' => $assessment->id,
            'priority_action_id' => $priorityAction->id,
        ]);

        expect($searchTerm->priorityAction->id)->toBe($priorityAction->id);
    });

    it('can access recommendation through priority action', function () {
        $assessment = Assessment::factory()->create();
        $recommendation = Recommendation::factory()->create();
        $priorityAction = PriorityAction::factory()->create(['recommendation_id' => $recommendation->id]);
        $searchTerm = SearchTerm::factory()->create([
            'assessment_id' => $assessment->id,
            'priority_action_id' => $priorityAction->id,
        ]);

        expect($searchTerm->recommendation->id)->toBe($recommendation->id);
    });

    it('getTranslations returns all available translations', function () {
        $assessment = Assessment::factory()->create();
        $recommendation = Recommendation::factory()->create();
        $priorityAction = PriorityAction::factory()->create(['recommendation_id' => $recommendation->id]);
        $searchTerm = SearchTerm::factory()->create([
            'assessment_id' => $assessment->id,
            'priority_action_id' => $priorityAction->id,
            'phrase' => ['en' => 'crop rotation', 'es' => 'rotación de cultivos'],
        ]);

        $translations = $searchTerm->getTranslations('phrase');

        expect($translations)->toHaveKeys(['en', 'es']);
    });

});
