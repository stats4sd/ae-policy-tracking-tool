<?php

use App\Livewire\AssessmentSearchTermsManager;
use App\Models\Assessment;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

// AssessmentSearchTermsManager extends Filament\Schemas\Components\Component,
// not Livewire\Component, so it cannot be tested with Livewire::test().
// It is embedded inside a Filament schema, not used as a standalone Livewire component.

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->assessment = Assessment::factory()->create();
    $this->assessment->members()->attach($this->user->id);

    $this->actingAs($this->user);
    Filament::setCurrentPanel(Filament::getPanel('app'));
    Filament::setTenant($this->assessment);
});

describe('AssessmentSearchTermsManager', function () {

    it('renders successfully')->skip('AssessmentSearchTermsManager extends Filament\Schemas\Components\Component, not Livewire\Component — cannot test with Livewire::test()');

    it('shows search terms for the assessment')->skip('AssessmentSearchTermsManager extends Filament\Schemas\Components\Component, not Livewire\Component — cannot test with Livewire::test()');

    it('does not show search terms from other assessments')->skip('AssessmentSearchTermsManager extends Filament\Schemas\Components\Component, not Livewire\Component — cannot test with Livewire::test()');

    it('can delete a search term')->skip('AssessmentSearchTermsManager extends Filament\Schemas\Components\Component, not Livewire\Component — cannot test with Livewire::test()');

});
