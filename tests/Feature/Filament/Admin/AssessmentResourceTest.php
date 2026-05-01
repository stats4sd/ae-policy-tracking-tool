<?php

use App\Filament\Admin\Resources\Assessments\Pages\ListAssessments;
use App\Models\Assessment;
use App\Models\Jurisdiction;
use App\Models\Language;
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

    it('creates an assessment via the modal action', function () {
        $jurisdiction = Jurisdiction::factory()->create();
        $language = Language::factory()->create();

        livewire(ListAssessments::class)
            ->callAction('create', ['jurisdiction_id' => $jurisdiction->id, 'language_id' => $language->id]);

        $this->assertDatabaseHas('assessments', ['jurisdiction_id' => $jurisdiction->id, 'language_id' => $language->id]);
    });

    it('fails validation when language is missing', function () {
        $countBefore = Assessment::query()->count();
        $jurisdiction = Jurisdiction::factory()->create();

        livewire(ListAssessments::class)
            ->callAction('create', ['language_id' => null, 'jurisdiction_id' => $jurisdiction->id]);

        expect(Assessment::query()->count())->toBe($countBefore);
    });

});
