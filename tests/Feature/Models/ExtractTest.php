<?php

use App\Models\Assessment;
use App\Models\Extract;
use App\Models\PolicyDocument;
use App\Models\PriorityAction;
use App\Models\Recommendation;
use App\Models\SearchTerm;
use App\Models\Statement;

describe('Extract model', function () {

    it('formattedExtract removes newlines and collapses multiple spaces', function () {
        $assessment = Assessment::factory()->create();
        $document = PolicyDocument::factory()->create(['assessment_id' => $assessment->id]);
        $extract = Extract::factory()->create([
            'policy_document_id' => $document->id,
            'extract' => "Line one\nLine two\r\nLine three  with   extra spaces",
        ]);

        expect($extract->formattedExtract)->toBe('Line one Line two Line three with extra spaces');
    });

    it('global scope orders extracts by policy_document_id, page_number, start_offset', function () {
        $assessment = Assessment::factory()->create();
        $doc1 = PolicyDocument::factory()->create(['assessment_id' => $assessment->id]);
        $doc2 = PolicyDocument::factory()->create(['assessment_id' => $assessment->id]);

        // Create out of order
        Extract::factory()->create(['policy_document_id' => $doc2->id, 'page_number' => 1, 'start_offset' => 10]);
        Extract::factory()->create(['policy_document_id' => $doc1->id, 'page_number' => 2, 'start_offset' => 5]);
        Extract::factory()->create(['policy_document_id' => $doc1->id, 'page_number' => 1, 'start_offset' => 20]);
        Extract::factory()->create(['policy_document_id' => $doc1->id, 'page_number' => 1, 'start_offset' => 0]);

        $extracts = Extract::all();

        expect($extracts[0]->policy_document_id)->toBe($doc1->id)
            ->and($extracts[0]->page_number)->toBe(1)
            ->and($extracts[0]->start_offset)->toBe(0);
        expect($extracts[1]->start_offset)->toBe(20);
        expect($extracts[2]->page_number)->toBe(2);
        expect($extracts[3]->policy_document_id)->toBe($doc2->id);
    });

    it('can be soft deleted', function () {
        $assessment = Assessment::factory()->create();
        $document = PolicyDocument::factory()->create(['assessment_id' => $assessment->id]);
        $extract = Extract::factory()->create(['policy_document_id' => $document->id]);

        $extract->delete();

        $this->assertSoftDeleted('extracts', ['id' => $extract->id]);
        expect(Extract::find($extract->id))->toBeNull();
        expect(Extract::withTrashed()->find($extract->id))->not->toBeNull();
    });

    it('belongs to a policy document', function () {
        $assessment = Assessment::factory()->create();
        $document = PolicyDocument::factory()->create(['assessment_id' => $assessment->id]);
        $extract = Extract::factory()->create(['policy_document_id' => $document->id]);

        expect($extract->policyDocument->id)->toBe($document->id);
    });

    it('belongs to an assessment through policy document', function () {
        $assessment = Assessment::factory()->create();
        $document = PolicyDocument::factory()->create(['assessment_id' => $assessment->id]);
        $extract = Extract::factory()->create(['policy_document_id' => $document->id]);

        expect($extract->assessment->id)->toBe($assessment->id);
    });

    it('belongs to many priority actions', function () {
        $assessment = Assessment::factory()->create();
        $document = PolicyDocument::factory()->create(['assessment_id' => $assessment->id]);
        $extract = Extract::factory()->create(['policy_document_id' => $document->id]);
        $recommendation = Recommendation::factory()->create();
        $pa1 = PriorityAction::factory()->create(['recommendation_id' => $recommendation->id]);
        $pa2 = PriorityAction::factory()->create(['recommendation_id' => $recommendation->id]);

        $extract->priorityActions()->attach([$pa1->id, $pa2->id]);

        expect($extract->priorityActions)->toHaveCount(2);
    });

    it('belongs to many search terms', function () {
        $assessment = Assessment::factory()->create();
        $document = PolicyDocument::factory()->create(['assessment_id' => $assessment->id]);
        $extract = Extract::factory()->create(['policy_document_id' => $document->id]);
        $recommendation = Recommendation::factory()->create();
        $priorityAction = PriorityAction::factory()->create(['recommendation_id' => $recommendation->id]);
        $searchTerm = SearchTerm::factory()->create([
            'assessment_id' => $assessment->id,
            'priority_action_id' => $priorityAction->id,
        ]);

        $extract->searchTerms()->attach($searchTerm->id);

        expect($extract->searchTerms)->toHaveCount(1);
    });

    it('belongs to many statements', function () {
        $assessment = Assessment::factory()->create();
        $document = PolicyDocument::factory()->create(['assessment_id' => $assessment->id]);
        $extract = Extract::factory()->create(['policy_document_id' => $document->id]);
        $recommendation = Recommendation::factory()->create();
        $priorityAction = PriorityAction::factory()->create(['recommendation_id' => $recommendation->id]);
        $statement = Statement::factory()->create([
            'assessment_id' => $assessment->id,
            'priority_action_id' => $priorityAction->id,
        ]);

        $extract->statements()->attach($statement->id);

        expect($extract->statements()->withoutGlobalScopes()->count())->toBe(1);
    });

});
