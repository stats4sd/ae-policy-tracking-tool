<?php

use App\Filament\Admin\Resources\Assessments\Pages\CreateAssessment;
use App\Filament\Admin\Resources\Assessments\Pages\ListAssessments;
use App\Filament\Admin\Resources\Assessments\Pages\ViewAssessment;
use App\Models\Assessment;
use App\Models\Country;
use App\Models\User;
use Filament\Facades\Filament;

beforeEach(function () {
    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');

    $this->actingAs($this->admin);
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

describe('Admin / ListAssessments', function () {

    it('renders the list page', function () {
        livewire(ListAssessments::class)
            ->assertSuccessful();
    });

    it('shows all assessments', function () {
        $assessments = Assessment::factory()->count(3)->create();

        livewire(ListAssessments::class)
            ->assertCanSeeTableRecords($assessments);
    });

});

describe('Admin / CreateAssessment', function () {

    it('renders the create page', function () {
        livewire(CreateAssessment::class)
            ->assertSuccessful();
    });

    it('creates an assessment', function () {
        $country = Country::factory()->create();

        livewire(CreateAssessment::class)
            ->fillForm(['country_id' => $country->id])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('assessments', ['country_id' => $country->id]);
    });

    it('fails validation when country is missing', function () {
        livewire(CreateAssessment::class)
            ->fillForm(['country_id' => null])
            ->call('create')
            ->assertHasFormErrors(['country_id']);
    });

});

describe('Admin / ViewAssessment', function () {

    it('renders the view page', function () {
        $assessment = Assessment::factory()->create();

        livewire(ViewAssessment::class, ['record' => $assessment->getRouteKey()])
            ->assertSuccessful();
    });

});
