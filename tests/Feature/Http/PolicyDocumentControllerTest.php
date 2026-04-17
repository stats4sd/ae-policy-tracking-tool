<?php

use App\Models\Assessment;
use App\Models\Extract;
use App\Models\PolicyDocument;
use App\Models\PolicyDocumentPage;
use App\Models\PriorityAction;
use App\Models\Recommendation;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->assessment = Assessment::factory()->create();
    $this->assessment->members()->attach($this->user->id);
    $this->document = PolicyDocument::factory()->create(['assessment_id' => $this->assessment->id]);
});

describe('GET /policy-documents/{document}/pages', function () {

    it('returns pages for the document', function () {
        PolicyDocumentPage::factory()->create([
            'policy_document_id' => $this->document->id,
            'page_number' => 1,
            'content' => 'Page one content',
        ]);
        PolicyDocumentPage::factory()->create([
            'policy_document_id' => $this->document->id,
            'page_number' => 2,
            'content' => 'Page two content',
        ]);

        $this->actingAs($this->user)
            ->getJson("/policy-documents/{$this->document->id}/pages")
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJsonFragment(['page_number' => 1, 'content' => 'Page one content'])
            ->assertJsonFragment(['page_number' => 2, 'content' => 'Page two content']);
    });

    it('returns pages ordered by page number', function () {
        PolicyDocumentPage::factory()->create([
            'policy_document_id' => $this->document->id,
            'page_number' => 3,
        ]);
        PolicyDocumentPage::factory()->create([
            'policy_document_id' => $this->document->id,
            'page_number' => 1,
        ]);
        PolicyDocumentPage::factory()->create([
            'policy_document_id' => $this->document->id,
            'page_number' => 2,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/policy-documents/{$this->document->id}/pages")
            ->assertOk();

        $pages = $response->json();
        expect($pages[0]['page_number'])->toBe(1)
            ->and($pages[1]['page_number'])->toBe(2)
            ->and($pages[2]['page_number'])->toBe(3);
    });

    it('returns empty array when document has no pages', function () {
        $this->actingAs($this->user)
            ->getJson("/policy-documents/{$this->document->id}/pages")
            ->assertOk()
            ->assertJsonCount(0);
    });

    it('returns 404 for a non-existent document', function () {
        $this->actingAs($this->user)
            ->getJson('/policy-documents/99999/pages')
            ->assertNotFound();
    });

    it('returns only page_number and content fields', function () {
        PolicyDocumentPage::factory()->create([
            'policy_document_id' => $this->document->id,
            'page_number' => 1,
            'content' => 'Content here',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/policy-documents/{$this->document->id}/pages")
            ->assertOk();

        $page = $response->json()[0];
        expect(array_keys($page))->toBe(['page_number', 'content']);
    });

});

describe('GET /policy-documents/{document}/extracts', function () {

    it('returns extracts for the document', function () {
        $extract = Extract::factory()->create([
            'policy_document_id' => $this->document->id,
        ]);

        $this->actingAs($this->user)
            ->getJson("/policy-documents/{$this->document->id}/extracts")
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['id' => $extract->id]);
    });

    it('returns empty array when document has no extracts', function () {
        $this->actingAs($this->user)
            ->getJson("/policy-documents/{$this->document->id}/extracts")
            ->assertOk()
            ->assertJsonCount(0);
    });

    it('returns extract with expected fields', function () {
        $extract = Extract::factory()->create([
            'policy_document_id' => $this->document->id,
            'page_number' => 1,
            'extract' => 'Some extract text',
            'start_offset' => 0,
            'end_offset' => 100,
            'color' => 'lightblue',
            'automatic' => true,
            'verified' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/policy-documents/{$this->document->id}/extracts")
            ->assertOk();

        $item = $response->json()[0];
        expect($item)->toHaveKeys(['id', 'policy_document_id', 'page_number', 'extract', 'start_offset', 'end_offset', 'color', 'automatic', 'verified', 'search_terms', 'search_terms_list', 'priority_actions', 'type_id']);
    });

    it('includes priority actions in extract response', function () {
        $recommendation = Recommendation::factory()->create();
        $priorityAction = PriorityAction::factory()->create(['recommendation_id' => $recommendation->id]);
        $extract = Extract::factory()->create(['policy_document_id' => $this->document->id]);
        $extract->priorityActions()->attach($priorityAction->id);

        $response = $this->actingAs($this->user)
            ->getJson("/policy-documents/{$this->document->id}/extracts")
            ->assertOk();

        expect($response->json()[0]['priority_actions'])->toContain($priorityAction->id);
    });

    it('returns 404 for a non-existent document', function () {
        $this->actingAs($this->user)
            ->getJson('/policy-documents/99999/extracts')
            ->assertNotFound();
    });

});
