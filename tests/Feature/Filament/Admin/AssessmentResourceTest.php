<?php

use App\Filament\Admin\Resources\Assessments\Pages\ListAssessments;
use App\Models\Assessment;
use App\Models\Country;
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
        $country = Country::factory()->create();
        $language = Language::factory()->create();

        livewire(ListAssessments::class)
            ->callAction('create', ['country_id' => $country->id, 'language_id' => $language->id]);

        $this->assertDatabaseHas('assessments', ['country_id' => $country->id, 'language_id' => $language->id]);
    });

    it('fails validation when country is missing', function () {
        $countBefore = Assessment::query()->count();
        $language = Language::factory()->create();

        livewire(ListAssessments::class)
            ->callAction('create', ['country_id' => null, 'language_id' => $language->id]);

        expect(Assessment::query()->count())->toBe($countBefore);
    });

    it('fails validation when language is missing', function () {
        $countBefore = Assessment::query()->count();
        $country = Country::factory()->create();

        livewire(ListAssessments::class)
            ->callAction('create', ['language_id' => null, 'country_id' => $country->id]);

        expect(Assessment::query()->count())->toBe($countBefore);
    });

});