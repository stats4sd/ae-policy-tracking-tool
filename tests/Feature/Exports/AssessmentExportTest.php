<?php

use App\Exports\AssessmentDataExport\AssessmentExport;
use App\Exports\AssessmentDataExport\ExtractExport;
use App\Exports\AssessmentDataExport\PolicyDocumentExport;
use App\Exports\AssessmentDataExport\StatementExport;
use App\Models\Assessment;
use App\Models\Extract;
use App\Models\PolicyDocument;
use App\Models\PriorityAction;
use App\Models\Recommendation;
use App\Models\Statement;

describe('AssessmentExport', function () {

    it('has five sheets', function () {
        $assessment = Assessment::factory()->create();
        $export = new AssessmentExport($assessment);

        expect($export->sheets())->toHaveCount(5);
    });

});

describe('ExtractExport', function () {

    it('exports extracts for the assessment', function () {
        $assessment = Assessment::factory()->create();
        $document = PolicyDocument::factory()->create(['assessment_id' => $assessment->id]);
        Extract::factory()->count(3)->create(['policy_document_id' => $document->id]);

        $export = new ExtractExport($assessment);

        expect($export->collection())->toHaveCount(3);
    });

    it('has correct headings', function () {
        $assessment = Assessment::factory()->create();
        $export = new ExtractExport($assessment);
        $headings = $export->headings();

        expect($headings)->toContain('Extract')
            ->and($headings)->toContain('Policy Document');
    });

    it('has title Document Extracts', function () {
        $assessment = Assessment::factory()->create();
        $export = new ExtractExport($assessment);

        expect($export->title())->toBe('Document Extracts');
    });

});

describe('PolicyDocumentExport', function () {

    it('exports policy documents for the assessment', function () {
        $assessment = Assessment::factory()->create();
        PolicyDocument::factory()->count(2)->create(['assessment_id' => $assessment->id]);

        $export = new PolicyDocumentExport($assessment);

        expect($export->collection())->toHaveCount(2);
    });

});

describe('StatementExport', function () {

    it('exports statements for the assessment', function () {
        $assessment = Assessment::factory()->create();
        $recommendation = Recommendation::factory()->create();
        $priorityAction = PriorityAction::factory()->create(['recommendation_id' => $recommendation->id]);
        Statement::factory()->count(4)->create([
            'assessment_id' => $assessment->id,
            'priority_action_id' => $priorityAction->id,
        ]);

        $export = new StatementExport($assessment);

        expect($export->collection()->count())->toBe(4);
    });

});
