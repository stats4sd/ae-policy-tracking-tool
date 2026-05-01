<?php

use App\Filament\App\Clusters\Setup\Resources\SearchTerms\Pages\ListSearchTerms;
use App\Models\Assessment;
use App\Models\PriorityAction;
use App\Models\Recommendation;
use App\Models\SearchTerm;
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

describe('Setup / ListSearchTerms', function () {

    it('renders the list page', function () {
        livewire(ListSearchTerms::class)
            ->assertSuccessful();
    });

    it('shows the current priority action(s) in the table', function () {
        $recommendation = Recommendation::factory()->create();
        $priorityAction = PriorityAction::factory()->create(['recommendation_id' => $recommendation->id]);
        $searchTerm = SearchTerm::factory()->create([
            'assessment_id' => $this->assessment->id,
            'priority_action_id' => $priorityAction->id,
        ]);

        livewire(ListSearchTerms::class)
            ->assertCanSeeTableRecords([$priorityAction]);

    });

    it('shows search terms from the current assessment in the list and not terms from another assessment', function () {
        $recommendation = Recommendation::factory()->create();
        $priorityAction = PriorityAction::factory()->create(['recommendation_id' => $recommendation->id]);

        $term = SearchTerm::factory()->create([
            'assessment_id' => $this->assessment->id,
            'priority_action_id' => $priorityAction->id,
        ]);

        $anotherTerm = SearchTerm::factory()->create([
            'assessment_id' => $this->assessment->id,
            'priority_action_id' => $priorityAction->id
        ]);

        $otherAssessment = Assessment::factory()->create();
        SearchTerm::factory()->create([
            'assessment_id' => $otherAssessment->id,
            'priority_action_id' => $priorityAction->id,
        ]);

        livewire(ListSearchTerms::class)
            ->assertTableColumnVisible('phrases')
            ->assertTableColumnStateSet(
                'phrases',
                $term->getTranslation('phrase', 'en') . '; ' . $anotherTerm->getTranslation('phrase', 'en'),
                record: $priorityAction
            );

    });

});
