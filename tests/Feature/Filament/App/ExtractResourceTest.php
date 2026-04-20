<?php

use App\Filament\App\Resources\Extracts\Pages\ListExtracts;
use App\Models\Assessment;
use App\Models\Extract;
use App\Models\PolicyDocument;
use App\Models\PriorityAction;
use App\Models\Theme;
use App\Models\User;
use Filament\Facades\Filament;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->assessment = Assessment::factory()->create();
    $this->assessment->members()->attach($this->user->id);

    $this->actingAs($this->user);
    Filament::setCurrentPanel(Filament::getPanel('app'));
    Filament::setTenant($this->assessment);
});

describe('ListExtracts', function () {

    it('renders the list page', function () {
        livewire(ListExtracts::class)
            ->assertSuccessful();
    });

    it('shows extracts belonging to the current assessment', function () {
        $document = PolicyDocument::factory()->create(['assessment_id' => $this->assessment->id]);
        $extract = Extract::factory()->create([
            'policy_document_id' => $document->id,
            'extract' => 'This is a relevant policy extract.',
        ]);

        livewire(ListExtracts::class)
            ->assertCanSeeTableRecords([$extract]);
    });

    it('shows only extracts from the current assessment', function () {
        $document = PolicyDocument::factory()->create(['assessment_id' => $this->assessment->id]);
        $extract = Extract::factory()->create(['policy_document_id' => $document->id]);

        $otherAssessment = Assessment::factory()->create();
        $otherDocument = PolicyDocument::factory()->create(['assessment_id' => $otherAssessment->id]);
        Extract::factory()->create(['policy_document_id' => $otherDocument->id]);

        livewire(ListExtracts::class)
            ->assertCanSeeTableRecords([$extract])
            ->assertCountTableRecords(1);
    });

    it('shows the list of themes in a table when filtering by theme', function () {
        $document = PolicyDocument::factory()->create(['assessment_id' => $this->assessment->id]);
        $extract = Extract::factory()->create(['policy_document_id' => $document->id]);

        $priorityAction = PriorityAction::factory()->create();
        $theme = Theme::factory()->create([
            'assessment_id' => $this->assessment->id,
            'priority_action_id' => $priorityAction->id,
        ]);
        $extract->priorityActions()->attach($priorityAction->id);

        $this->get(route('filament.app.resources.extracts.index', [
            'tenant' => $this->assessment->id,
            'tab' => $priorityAction->id,
        ]))->assertSee($theme->name);
    });

    it('does not show themes from a different assessment', function () {
        $document = PolicyDocument::factory()->create(['assessment_id' => $this->assessment->id]);
        $extract = Extract::factory()->create(['policy_document_id' => $document->id]);

        $priorityAction = PriorityAction::factory()->create();
        $extract->priorityActions()->attach($priorityAction->id);

        $otherAssessment = Assessment::factory()->create();
        $otherTheme = Theme::factory()->create([
            'assessment_id' => $otherAssessment->id,
            'priority_action_id' => $priorityAction->id,
        ]);

        $this->get(route('filament.app.resources.extracts.index', [
            'tenant' => $this->assessment->id,
            'tab' => $priorityAction->id,
        ]))->assertDontSee($otherTheme->name);
    });

});
