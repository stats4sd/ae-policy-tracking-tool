<?php

use App\Models\Assessment;
use App\Models\Extract;
use App\Models\PolicyDocument;
use App\Models\PriorityAction;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->assessment = Assessment::factory()->create();
    $this->assessment->members()->attach($this->user->id);
    $this->document = PolicyDocument::factory()->create(['assessment_id' => $this->assessment->id]);
});

describe('POST /extracts', function () {

    it('creates an extract with valid data', function () {
        $this->actingAs($this->user)
            ->postJson('/extracts', [
                'policy_document_id' => $this->document->id,
                'page_number' => 1,
                'extract' => 'Some important text extracted from the policy.',
                'start_offset' => 0,
                'end_offset' => 50,
                'color' => 'yellow',
            ])
            ->assertCreated()
            ->assertJsonFragment([
                'policy_document_id' => $this->document->id,
                'extract' => 'Some important text extracted from the policy.',
            ]);

        $this->assertDatabaseHas('extracts', [
            'policy_document_id' => $this->document->id,
            'extract' => 'Some important text extracted from the policy.',
        ]);
    });

    it('attaches priority actions to the new extract', function () {
        $priorityAction = PriorityAction::factory()->create();

        $response = $this->actingAs($this->user)
            ->postJson('/extracts', [
                'policy_document_id' => $this->document->id,
                'page_number' => 1,
                'extract' => 'Policy extract text',
                'start_offset' => 0,
                'end_offset' => 20,
                'color' => 'blue',
                'priority_actions' => [$priorityAction->id],
            ])
            ->assertCreated();

        $extractId = $response->json('id');
        $this->assertDatabaseHas('extract_priority_action', [
            'extract_id' => $extractId,
            'priority_action_id' => $priorityAction->id,
        ]);
    });

    it('returns 422 when policy_document_id is missing', function () {
        $this->actingAs($this->user)
            ->postJson('/extracts', [
                'page_number' => 1,
                'extract' => 'Some text',
                'start_offset' => 0,
                'end_offset' => 10,
                'color' => 'blue',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['policy_document_id']);
    });

    it('returns 422 when policy_document_id does not exist', function () {
        $this->actingAs($this->user)
            ->postJson('/extracts', [
                'policy_document_id' => 99999,
                'page_number' => 1,
                'extract' => 'Some text',
                'start_offset' => 0,
                'end_offset' => 10,
                'color' => 'blue',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['policy_document_id']);
    });

    it('returns 422 when page_number is missing', function () {
        $this->actingAs($this->user)
            ->postJson('/extracts', [
                'policy_document_id' => $this->document->id,
                'extract' => 'Some text',
                'start_offset' => 0,
                'end_offset' => 10,
                'color' => 'blue',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['page_number']);
    });

    it('returns 422 when page_number is less than 1', function () {
        $this->actingAs($this->user)
            ->postJson('/extracts', [
                'policy_document_id' => $this->document->id,
                'page_number' => 0,
                'extract' => 'Some text',
                'start_offset' => 0,
                'end_offset' => 10,
                'color' => 'blue',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['page_number']);
    });

    it('returns 422 when extract text is missing', function () {
        $this->actingAs($this->user)
            ->postJson('/extracts', [
                'policy_document_id' => $this->document->id,
                'page_number' => 1,
                'start_offset' => 0,
                'end_offset' => 10,
                'color' => 'blue',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['extract']);
    });

    it('returns 422 when start_offset is missing', function () {
        $this->actingAs($this->user)
            ->postJson('/extracts', [
                'policy_document_id' => $this->document->id,
                'page_number' => 1,
                'extract' => 'Some text',
                'end_offset' => 10,
                'color' => 'blue',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['start_offset']);
    });

    it('returns 422 when end_offset is missing', function () {
        $this->actingAs($this->user)
            ->postJson('/extracts', [
                'policy_document_id' => $this->document->id,
                'page_number' => 1,
                'extract' => 'Some text',
                'start_offset' => 0,
                'color' => 'blue',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['end_offset']);
    });

    it('returns 422 when color is missing', function () {
        $this->actingAs($this->user)
            ->postJson('/extracts', [
                'policy_document_id' => $this->document->id,
                'page_number' => 1,
                'extract' => 'Some text',
                'start_offset' => 0,
                'end_offset' => 10,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['color']);
    });

    it('accepts a null score_id', function () {
        $this->actingAs($this->user)
            ->postJson('/extracts', [
                'policy_document_id' => $this->document->id,
                'page_number' => 1,
                'extract' => 'Some text',
                'start_offset' => 0,
                'end_offset' => 10,
                'color' => 'blue',
                'score_id' => null,
            ])
            ->assertCreated();
    });

    it('returns 422 when score_id is provided but does not exist', function () {
        $this->actingAs($this->user)
            ->postJson('/extracts', [
                'policy_document_id' => $this->document->id,
                'page_number' => 1,
                'extract' => 'Some text',
                'start_offset' => 0,
                'end_offset' => 10,
                'color' => 'blue',
                'score_id' => 99999,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['score_id']);
    });

});

describe('PUT /extracts/{extract}', function () {

    it('updates the verified status', function () {
        $extract = Extract::factory()->create([
            'policy_document_id' => $this->document->id,
            'verified' => false,
        ]);

        $this->actingAs($this->user)
            ->putJson("/extracts/{$extract->id}", [
                'verified' => true,
                'priority_actions' => [],
            ])
            ->assertOk();

        expect($extract->fresh()->verified)->toBeTrue();
    });

    it('syncs priority actions on update', function () {
        $pa1 = PriorityAction::factory()->create();
        $pa2 = PriorityAction::factory()->create();
        $extract = Extract::factory()->create(['policy_document_id' => $this->document->id]);
        $extract->priorityActions()->attach($pa1->id);

        $this->actingAs($this->user)
            ->putJson("/extracts/{$extract->id}", [
                'priority_actions' => [$pa2->id],
            ])
            ->assertOk();

        expect($extract->fresh()->priorityActions->pluck('id')->toArray())->toBe([$pa2->id]);
    });

    it('detaches all priority actions when empty array is sent', function () {
        $pa = PriorityAction::factory()->create();
        $extract = Extract::factory()->create(['policy_document_id' => $this->document->id]);
        $extract->priorityActions()->attach($pa->id);

        $this->actingAs($this->user)
            ->putJson("/extracts/{$extract->id}", [
                'priority_actions' => [],
            ])
            ->assertOk();

        expect($extract->fresh()->priorityActions)->toHaveCount(0);
    });

    it('returns 404 for a non-existent extract', function () {
        $this->actingAs($this->user)
            ->putJson('/extracts/99999', [
                'priority_actions' => [],
            ])
            ->assertNotFound();
    });

});

describe('DELETE /extracts/{extract}', function () {

    it('soft deletes an extract', function () {
        $extract = Extract::factory()->create(['policy_document_id' => $this->document->id]);

        $this->actingAs($this->user)
            ->deleteJson("/extracts/{$extract->id}")
            ->assertNoContent();

        $this->assertSoftDeleted('extracts', ['id' => $extract->id]);
    });

    it('returns 404 for a non-existent extract', function () {
        $this->actingAs($this->user)
            ->deleteJson('/extracts/99999')
            ->assertNotFound();
    });

    it('returns 404 for an already soft-deleted extract', function () {
        $extract = Extract::factory()->create(['policy_document_id' => $this->document->id]);
        $extract->delete();

        $this->actingAs($this->user)
            ->deleteJson("/extracts/{$extract->id}")
            ->assertNotFound();
    });

});
