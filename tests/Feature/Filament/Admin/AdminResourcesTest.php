<?php

use App\Filament\Admin\Resources\AePrinciples\Pages\ListAePrinciples;
use App\Filament\Admin\Resources\Countries\Pages\ListCountries;
use App\Filament\Admin\Resources\Languages\Pages\ListLanguages;
use App\Filament\Admin\Resources\PriorityActions\Pages\ListPriorityActions;
use App\Filament\Admin\Resources\PriorityActions\Pages\ViewPriorityAction;
use App\Filament\Admin\Resources\Recommendations\Pages\ListRecommendations;
use App\Filament\Admin\Resources\Recommendations\Pages\ViewRecommendation;
use App\Filament\Admin\Resources\Scores\Pages\ListScores;
use App\Filament\Admin\Resources\SearchTerms\Pages\ListSearchTerms;
use App\Filament\Admin\Resources\Users\Pages\ListUsers;
use App\Models\AePrinciple;
use App\Models\Assessment;
use App\Models\Country;
use App\Models\Language;
use App\Models\PriorityAction;
use App\Models\Recommendation;
use App\Models\Score;
use App\Models\SearchTerm;
use App\Models\User;
use Filament\Facades\Filament;

beforeEach(function () {
    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');

    $this->actingAs($this->admin);
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

describe('Admin / RecommendationResource', function () {

    it('lists recommendations', function () {
        $recommendations = Recommendation::factory()->count(2)->create();

        livewire(ListRecommendations::class)
            ->assertCanSeeTableRecords($recommendations);
    });

    it('renders the view recommendation page', function () {
        $recommendation = Recommendation::factory()->create();

        livewire(ViewRecommendation::class, ['record' => $recommendation->getRouteKey()])
            ->assertSuccessful();
    });

});

describe('Admin / PriorityActionResource', function () {

    it('lists priority actions', function () {
        $recommendation = Recommendation::factory()->create();
        $priorityActions = PriorityAction::factory()->count(2)->create(['recommendation_id' => $recommendation->id]);

        livewire(ListPriorityActions::class)
            ->assertCanSeeTableRecords($priorityActions);
    });

    it('renders the view priority action page', function () {
        $recommendation = Recommendation::factory()->create();
        $priorityAction = PriorityAction::factory()->create(['recommendation_id' => $recommendation->id]);

        livewire(ViewPriorityAction::class, ['record' => $priorityAction->getRouteKey()])
            ->assertSuccessful();
    });

});

describe('Admin / ScoreResource', function () {

    it('lists scores', function () {
        $scores = Score::factory()->count(2)->create();

        livewire(ListScores::class)
            ->assertCanSeeTableRecords($scores);
    });

});

describe('Admin / SearchTermResource', function () {

    it('lists search terms', function () {
        $assessment = Assessment::factory()->create();
        $recommendation = Recommendation::factory()->create();
        $priorityAction = PriorityAction::factory()->create(['recommendation_id' => $recommendation->id]);
        $searchTerms = SearchTerm::factory()->count(2)->create([
            'assessment_id' => $assessment->id,
            'priority_action_id' => $priorityAction->id,
        ]);

        livewire(ListSearchTerms::class)
            ->assertCanSeeTableRecords($searchTerms);
    });

});

describe('Admin / UserResource', function () {

    it('lists users', function () {
        $users = User::factory()->count(3)->create();

        livewire(ListUsers::class)
            ->assertSuccessful();
    });

});

describe('Admin / CountryResource', function () {

    it('lists countries', function () {
        $countries = Country::factory()->count(2)->create();

        livewire(ListCountries::class)
            ->assertCanSeeTableRecords($countries);
    });

});

describe('Admin / LanguageResource', function () {

    it('lists languages', function () {
        Language::factory()->create(['id' => 'en']);
        Language::factory()->create(['id' => 'fr']);

        livewire(ListLanguages::class)
            ->assertSuccessful();
    });

});

describe('Admin / AePrincipleResource', function () {

    it('lists ae principles', function () {
        $principles = AePrinciple::factory()->count(2)->create();

        livewire(ListAePrinciples::class)
            ->assertCanSeeTableRecords($principles);
    });

});
