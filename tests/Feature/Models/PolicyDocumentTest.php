<?php

use App\Jobs\PolicyDocumentAutoSearch;
use App\Models\Assessment;
use App\Models\Extract;
use App\Models\Language;
use App\Models\PolicyDocument;
use App\Models\PolicyDocumentPage;
use Illuminate\Support\Facades\Queue;

describe('PolicyDocument model', function () {

    it('dispatches PolicyDocumentAutoSearch job on runAutomaticSearch', function () {
        Queue::fake();

        $assessment = Assessment::factory()->create();
        $document = PolicyDocument::factory()->create(['assessment_id' => $assessment->id]);

        $document->runAutomaticSearch();

        Queue::assertPushed(PolicyDocumentAutoSearch::class, function ($job) use ($document) {
            return $job->policyDocument->id === $document->id;
        });
    });

    it('yearString returns the year when no end_year is set', function () {
        $assessment = Assessment::factory()->create();
        $document = PolicyDocument::factory()->create([
            'assessment_id' => $assessment->id,
            'year' => 2022,
            'end_year' => null,
        ]);

        expect($document->yearString)->toBe('2022');
    });

    it('yearString returns a range when end_year is set', function () {
        $assessment = Assessment::factory()->create();
        $document = PolicyDocument::factory()->create([
            'assessment_id' => $assessment->id,
            'year' => 2020,
            'end_year' => 2023,
        ]);

        expect($document->yearString)->toBe('2020 - 2023');
    });

    it('has many pages', function () {
        $assessment = Assessment::factory()->create();
        $document = PolicyDocument::factory()->create(['assessment_id' => $assessment->id]);
        PolicyDocumentPage::factory()->count(3)->create(['policy_document_id' => $document->id]);

        expect($document->pages)->toHaveCount(3);
    });

    it('has many extracts', function () {
        $assessment = Assessment::factory()->create();
        $document = PolicyDocument::factory()->create(['assessment_id' => $assessment->id]);
        Extract::factory()->count(4)->create(['policy_document_id' => $document->id]);

        expect($document->extracts)->toHaveCount(4);
    });

    it('automaticExtracts returns only unverified automatic extracts', function () {
        $assessment = Assessment::factory()->create();
        $document = PolicyDocument::factory()->create(['assessment_id' => $assessment->id]);

        // automatic + unverified — should be included
        Extract::factory()->create([
            'policy_document_id' => $document->id,
            'automatic' => true,
            'verified' => false,
        ]);
        // automatic + verified — should NOT be included
        Extract::factory()->create([
            'policy_document_id' => $document->id,
            'automatic' => true,
            'verified' => true,
        ]);
        // manual — should NOT be included
        Extract::factory()->create([
            'policy_document_id' => $document->id,
            'automatic' => false,
            'verified' => false,
        ]);

        expect($document->automaticExtracts()->count())->toBe(1);
    });

    it('verifiedExtracts returns extracts that are verified or manual', function () {
        $assessment = Assessment::factory()->create();
        $document = PolicyDocument::factory()->create(['assessment_id' => $assessment->id]);

        // verified — included
        Extract::factory()->create([
            'policy_document_id' => $document->id,
            'automatic' => true,
            'verified' => true,
        ]);
        // manual (not automatic) — included
        Extract::factory()->create([
            'policy_document_id' => $document->id,
            'automatic' => false,
            'verified' => false,
        ]);
        // automatic unverified — NOT included
        Extract::factory()->create([
            'policy_document_id' => $document->id,
            'automatic' => true,
            'verified' => false,
        ]);

        expect($document->verifiedExtracts()->count())->toBe(2);
    });

    it('belongs to an assessment', function () {
        $assessment = Assessment::factory()->create();
        $document = PolicyDocument::factory()->create(['assessment_id' => $assessment->id]);

        expect($document->assessment->id)->toBe($assessment->id);
    });

    it('belongs to a language when language_id is set', function () {
        $language = Language::factory()->create(['id' => 'en']);
        $assessment = Assessment::factory()->create(['language_id' => 'en']);
        $document = PolicyDocument::factory()->create([
            'assessment_id' => $assessment->id,
            'language_id' => 'en',
        ]);

        expect($document->language->id)->toBe('en');
    });

});
