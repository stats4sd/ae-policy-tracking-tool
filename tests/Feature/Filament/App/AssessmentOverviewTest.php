<?php

use App\Enums\AssessmentStatus;
use App\Filament\App\Pages\AssessmentOverview;
use App\Models\Assessment;
use App\Models\Country;
use App\Models\Recommendation;
use App\Models\User;
use Filament\Facades\Filament;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->country = Country::factory()->create(['name' => 'Kenya']);
    $this->assessment = Assessment::factory()->create(['country_id' => $this->country->id]);
    $this->assessment->members()->attach($this->user->id);

    $this->actingAs($this->user);
    Filament::setCurrentPanel(Filament::getPanel('app'));
    Filament::setTenant($this->assessment);
});

describe('AssessmentOverview page', function () {

    it('renders the assessment overview page', function () {
        Recommendation::factory()->create();

        livewire(AssessmentOverview::class)
            ->assertSuccessful();
    });

    it('mark as ready for review action changes status to Review', function () {
        Recommendation::factory()->create();

        expect($this->assessment->status)->toBe(AssessmentStatus::InProgress);

        livewire(AssessmentOverview::class)
            ->callAction('ready-for-review');

        expect($this->assessment->fresh()->status)->toBe(AssessmentStatus::Review);
    });

    it('mark as not ready action changes status back to In Progress', function () {
        $this->assessment->update(['status' => AssessmentStatus::Review]);
        Recommendation::factory()->create();

        livewire(AssessmentOverview::class)
            ->callAction('not-ready');

        expect($this->assessment->fresh()->status)->toBe(AssessmentStatus::InProgress);
    });

});
