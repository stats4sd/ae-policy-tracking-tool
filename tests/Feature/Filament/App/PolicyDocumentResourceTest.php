<?php

use App\Filament\App\Resources\PolicyDocuments\Pages\CreatePolicyDocument;
use App\Filament\App\Resources\PolicyDocuments\Pages\EditPolicyDocument;
use App\Filament\App\Resources\PolicyDocuments\Pages\ListPolicyDocuments;
use App\Jobs\PolicyDocumentAutoSearch;
use App\Models\Assessment;
use App\Models\Language;
use App\Models\PolicyDocument;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->assessment = Assessment::factory()->create();
    $this->assessment->members()->attach($this->user->id);

    $this->actingAs($this->user);
    Filament::setCurrentPanel(Filament::getPanel('app'));
    Filament::setTenant($this->assessment);
});

describe('ListPolicyDocuments', function () {

    it('renders the list page for the current assessment', function () {
        PolicyDocument::factory()->count(2)->create(['assessment_id' => $this->assessment->id]);

        livewire(ListPolicyDocuments::class)
            ->assertSuccessful();
    });

    it('shows policy documents belonging to the current assessment', function () {
        $doc = PolicyDocument::factory()->create([
            'assessment_id' => $this->assessment->id,
            'name' => 'National Agriculture Policy',
        ]);

        $otherAssessment = Assessment::factory()->create();
        PolicyDocument::factory()->create([
            'assessment_id' => $otherAssessment->id,
            'name' => 'Other Assessment Doc',
        ]);

        livewire(ListPolicyDocuments::class)
            ->assertCanSeeTableRecords([$doc]);
    });

    it('re-run auto search action dispatches a job for each selected document', function () {
        Queue::fake();

        $doc1 = PolicyDocument::factory()->create(['assessment_id' => $this->assessment->id]);
        $doc2 = PolicyDocument::factory()->create(['assessment_id' => $this->assessment->id]);

        livewire(ListPolicyDocuments::class)
            ->callTableBulkAction('redo_search', [$doc1, $doc2]);

        Queue::assertPushed(PolicyDocumentAutoSearch::class, 2);
    });

    it('delete action removes the document', function () {
        $doc = PolicyDocument::factory()->create(['assessment_id' => $this->assessment->id]);

        livewire(ListPolicyDocuments::class)
            ->callTableAction('delete', $doc)
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseMissing('policy_documents', ['id' => $doc->id]);
    });

});

describe('CreatePolicyDocument', function () {

    it('can render the create page', function () {
        livewire(CreatePolicyDocument::class)
            ->assertSuccessful();
    });

    it('creates a policy document with valid data', function () {
        $language = Language::factory()->create();

        livewire(CreatePolicyDocument::class)
            ->fillForm([
                'name' => 'National Food Security Policy 2023',
                'short_title' => 'NFSP 2023',
                'language_id' => $language->id,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('policy_documents', [
            'name' => 'National Food Security Policy 2023',
            'assessment_id' => $this->assessment->id,
        ]);
    });

    it('fails validation when name is missing', function () {
        livewire(CreatePolicyDocument::class)
            ->fillForm(['name' => ''])
            ->call('create')
            ->assertHasFormErrors(['name']);
    });

});

describe('EditPolicyDocument', function () {

    it('can render the edit page', function () {
        $doc = PolicyDocument::factory()->create(['assessment_id' => $this->assessment->id]);

        livewire(EditPolicyDocument::class, ['record' => $doc->getRouteKey()])
            ->assertSuccessful();
    });

    it('saves changes to the document', function () {
        $language = Language::factory()->create();
        $doc = PolicyDocument::factory()->create([
            'assessment_id' => $this->assessment->id,
            'name' => 'Original Name',
            'short_title' => 'ON',
            'language_id' => $language->id,
        ]);

        livewire(EditPolicyDocument::class, ['record' => $doc->getRouteKey()])
            ->fillForm(['name' => 'Updated Name'])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($doc->fresh()->name)->toBe('Updated Name');
    });

});
