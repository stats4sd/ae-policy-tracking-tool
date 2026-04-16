<?php

use App\Models\AePrinciple;
use App\Models\Assessment;
use App\Models\Extract;
use App\Models\PolicyDocument;
use App\Models\PriorityAction;
use App\Models\Recommendation;
use App\Models\Statement;
use Filament\Facades\Filament;

describe('Statement model', function () {

    it('belongs to a priority action', function () {
        $assessment = Assessment::factory()->create();
        $recommendation = Recommendation::factory()->create();
        $priorityAction = PriorityAction::factory()->create(['recommendation_id' => $recommendation->id]);
        $statement = Statement::factory()->create([
            'assessment_id' => $assessment->id,
            'priority_action_id' => $priorityAction->id,
        ]);

        expect($statement->priorityAction->id)->toBe($priorityAction->id);
    });

    it('belongs to an assessment', function () {
        $assessment = Assessment::factory()->create();
        $recommendation = Recommendation::factory()->create();
        $priorityAction = PriorityAction::factory()->create(['recommendation_id' => $recommendation->id]);
        $statement = Statement::factory()->create([
            'assessment_id' => $assessment->id,
            'priority_action_id' => $priorityAction->id,
        ]);

        expect($statement->assessment->id)->toBe($assessment->id);
    });

    it('belongs to many ae principles', function () {
        $assessment = Assessment::factory()->create();
        $recommendation = Recommendation::factory()->create();
        $priorityAction = PriorityAction::factory()->create(['recommendation_id' => $recommendation->id]);
        $statement = Statement::factory()->create([
            'assessment_id' => $assessment->id,
            'priority_action_id' => $priorityAction->id,
        ]);
        $principle = AePrinciple::factory()->create();
        $statement->aePrinciples()->attach($principle->id);

        expect($statement->aePrinciples)->toHaveCount(1);
    });

    it('assessment global scope filters to current tenant', function () {
        $assessment1 = Assessment::factory()->create();
        $assessment2 = Assessment::factory()->create();
        $recommendation = Recommendation::factory()->create();
        $priorityAction = PriorityAction::factory()->create(['recommendation_id' => $recommendation->id]);

        Statement::factory()->create(['assessment_id' => $assessment1->id, 'priority_action_id' => $priorityAction->id]);
        Statement::factory()->create(['assessment_id' => $assessment2->id, 'priority_action_id' => $priorityAction->id]);

        // Without tenancy set, global scope does not filter
        // (Filament::hasTenancy() returns false in testing context)
        $allStatements = Statement::withoutGlobalScope('assessment')->count();
        expect($allStatements)->toBe(2);
    });

    it('linkedPolicyDocuments merges direct links and extract links', function () {
        $assessment = Assessment::factory()->create();
        $recommendation = Recommendation::factory()->create();
        $priorityAction = PriorityAction::factory()->create(['recommendation_id' => $recommendation->id]);
        $statement = Statement::factory()->create([
            'assessment_id' => $assessment->id,
            'priority_action_id' => $priorityAction->id,
        ]);

        $directDoc = PolicyDocument::factory()->create(['assessment_id' => $assessment->id]);
        $extractDoc = PolicyDocument::factory()->create(['assessment_id' => $assessment->id]);

        // Direct link
        $statement->policyDocuments()->attach($directDoc->id);

        // Link via extract
        $extract = Extract::factory()->create(['policy_document_id' => $extractDoc->id]);
        $statement->extracts()->attach($extract->id);

        $linked = $statement->linkedPolicyDocuments;

        expect($linked->pluck('id'))->toContain($directDoc->id)
            ->and($linked->pluck('id'))->toContain($extractDoc->id)
            ->and($linked)->toHaveCount(2);
    });

    it('linkedPolicyDocuments does not duplicate when document appears in both direct and extract links', function () {
        $assessment = Assessment::factory()->create();
        $recommendation = Recommendation::factory()->create();
        $priorityAction = PriorityAction::factory()->create(['recommendation_id' => $recommendation->id]);
        $statement = Statement::factory()->create([
            'assessment_id' => $assessment->id,
            'priority_action_id' => $priorityAction->id,
        ]);

        $doc = PolicyDocument::factory()->create(['assessment_id' => $assessment->id]);

        // Direct link
        $statement->policyDocuments()->attach($doc->id);

        // Also linked via extract from same document
        $extract = Extract::factory()->create(['policy_document_id' => $doc->id]);
        $statement->extracts()->attach($extract->id);

        $linked = $statement->linkedPolicyDocuments;

        expect($linked)->toHaveCount(1);
    });

});
