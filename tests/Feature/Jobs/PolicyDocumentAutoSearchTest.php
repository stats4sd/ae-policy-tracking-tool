<?php

use App\Jobs\PolicyDocumentAutoSearch;
use App\Models\Assessment;
use App\Models\Extract;
use App\Models\Language;
use App\Models\PolicyDocument;
use App\Models\PolicyDocumentPage;
use App\Models\PriorityAction;
use App\Models\Recommendation;
use App\Models\SearchTerm;
use Illuminate\Contracts\Queue\ShouldQueue;

describe('PolicyDocumentAutoSearch', function () {

    beforeEach(function () {
        $this->language = Language::factory()->create(['id' => 'en', 'name' => ['en' => 'English']]);
        $this->assessment = Assessment::factory()->create(['language_id' => 'en']);
        $this->document = PolicyDocument::factory()->create([
            'assessment_id' => $this->assessment->id,
            'language_id' => 'en',
        ]);
    });

    it('implements ShouldQueue', function () {
        expect(PolicyDocumentAutoSearch::class)->toImplement(ShouldQueue::class);
    });

    it('creates extracts for matching search terms in a page', function () {
        $recommendation = Recommendation::factory()->create();
        $priorityAction = PriorityAction::factory()->create(['recommendation_id' => $recommendation->id]);
        SearchTerm::factory()->create([
            'assessment_id' => $this->assessment->id,
            'priority_action_id' => $priorityAction->id,
            'phrase' => ['en' => 'agricultural policy'],
        ]);

        PolicyDocumentPage::factory()->create([
            'policy_document_id' => $this->document->id,
            'page_number' => 1,
            'content' => 'This document covers agricultural policy reform in the region.',
        ]);

        (new PolicyDocumentAutoSearch($this->document))->handle();

        expect(Extract::count())->toBeGreaterThanOrEqual(1);
        $this->assertDatabaseHas('extracts', [
            'policy_document_id' => $this->document->id,
            'automatic' => true,
        ]);
    });

    it('matches search terms case insensitively', function () {
        $recommendation = Recommendation::factory()->create();
        $priorityAction = PriorityAction::factory()->create(['recommendation_id' => $recommendation->id]);
        SearchTerm::factory()->create([
            'assessment_id' => $this->assessment->id,
            'priority_action_id' => $priorityAction->id,
            'phrase' => ['en' => 'CLIMATE CHANGE'],
        ]);

        PolicyDocumentPage::factory()->create([
            'policy_document_id' => $this->document->id,
            'page_number' => 1,
            'content' => 'The government addresses climate change adaptation.',
        ]);

        (new PolicyDocumentAutoSearch($this->document))->handle();

        expect(Extract::count())->toBeGreaterThanOrEqual(1);
    });

    it('links the created extract to the correct priority action', function () {
        $recommendation = Recommendation::factory()->create();
        $priorityAction = PriorityAction::factory()->create(['recommendation_id' => $recommendation->id]);
        SearchTerm::factory()->create([
            'assessment_id' => $this->assessment->id,
            'priority_action_id' => $priorityAction->id,
            'phrase' => ['en' => 'food security'],
        ]);

        PolicyDocumentPage::factory()->create([
            'policy_document_id' => $this->document->id,
            'page_number' => 1,
            'content' => 'National food security strategies are being reviewed.',
        ]);

        (new PolicyDocumentAutoSearch($this->document))->handle();

        $extract = Extract::first();
        expect($extract->priorityActions->pluck('id')->toArray())->toContain($priorityAction->id);
    });

    it('links the created extract to the matched search term', function () {
        $recommendation = Recommendation::factory()->create();
        $priorityAction = PriorityAction::factory()->create(['recommendation_id' => $recommendation->id]);
        $searchTerm = SearchTerm::factory()->create([
            'assessment_id' => $this->assessment->id,
            'priority_action_id' => $priorityAction->id,
            'phrase' => ['en' => 'water management'],
        ]);

        PolicyDocumentPage::factory()->create([
            'policy_document_id' => $this->document->id,
            'page_number' => 1,
            'content' => 'Improved water management is a priority for rural communities.',
        ]);

        (new PolicyDocumentAutoSearch($this->document))->handle();

        $extract = Extract::first();
        expect($extract->searchTerms->pluck('id')->toArray())->toContain($searchTerm->id);
    });

    it('does not create extracts when no search terms match', function () {
        $recommendation = Recommendation::factory()->create();
        $priorityAction = PriorityAction::factory()->create(['recommendation_id' => $recommendation->id]);
        SearchTerm::factory()->create([
            'assessment_id' => $this->assessment->id,
            'priority_action_id' => $priorityAction->id,
            'phrase' => ['en' => 'xyz_nonexistent_term_12345'],
        ]);

        PolicyDocumentPage::factory()->create([
            'policy_document_id' => $this->document->id,
            'page_number' => 1,
            'content' => 'This page discusses irrigation and water access.',
        ]);

        (new PolicyDocumentAutoSearch($this->document))->handle();

        expect(Extract::count())->toBe(0);
    });

    it('skips pages with blank content', function () {
        $recommendation = Recommendation::factory()->create();
        $priorityAction = PriorityAction::factory()->create(['recommendation_id' => $recommendation->id]);
        SearchTerm::factory()->create([
            'assessment_id' => $this->assessment->id,
            'priority_action_id' => $priorityAction->id,
            'phrase' => ['en' => 'farming'],
        ]);

        PolicyDocumentPage::factory()->create([
            'policy_document_id' => $this->document->id,
            'page_number' => 1,
            'content' => null,
        ]);

        (new PolicyDocumentAutoSearch($this->document))->handle();

        expect(Extract::count())->toBe(0);
    });

    it('does not duplicate extracts when run twice with the same content', function () {
        $recommendation = Recommendation::factory()->create();
        $priorityAction = PriorityAction::factory()->create(['recommendation_id' => $recommendation->id]);
        SearchTerm::factory()->create([
            'assessment_id' => $this->assessment->id,
            'priority_action_id' => $priorityAction->id,
            'phrase' => ['en' => 'land reform'],
        ]);

        PolicyDocumentPage::factory()->create([
            'policy_document_id' => $this->document->id,
            'page_number' => 1,
            'content' => 'Land reform policies have been updated.',
        ]);

        (new PolicyDocumentAutoSearch($this->document))->handle();
        $countAfterFirst = Extract::count();

        (new PolicyDocumentAutoSearch($this->document))->handle();
        $countAfterSecond = Extract::count();

        expect($countAfterFirst)->toBe($countAfterSecond);
    });

    it('uses the document language to look up translatable search terms', function () {
        $frLanguage = Language::factory()->create(['id' => 'fr', 'name' => ['en' => 'French']]);
        $documentFr = PolicyDocument::factory()->create([
            'assessment_id' => $this->assessment->id,
            'language_id' => 'fr',
        ]);

        $recommendation = Recommendation::factory()->create();
        $priorityAction = PriorityAction::factory()->create(['recommendation_id' => $recommendation->id]);
        SearchTerm::factory()->create([
            'assessment_id' => $this->assessment->id,
            'priority_action_id' => $priorityAction->id,
            'phrase' => ['en' => 'agriculture', 'fr' => 'agriculture'],
        ]);

        PolicyDocumentPage::factory()->create([
            'policy_document_id' => $documentFr->id,
            'page_number' => 1,
            'content' => 'Le ministère de l\'agriculture a publié de nouvelles directives.',
        ]);

        // Override language_id on assessment to 'fr'
        $this->assessment->update(['language_id' => 'fr']);

        (new PolicyDocumentAutoSearch($documentFr))->handle();

        expect(Extract::where('policy_document_id', $documentFr->id)->count())->toBeGreaterThanOrEqual(1);
    });

});
