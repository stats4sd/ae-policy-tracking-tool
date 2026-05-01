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
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Stats4sd\FilamentTeamManagement\Mail\InviteUser;

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

    it('can edit a recommendation', function () {
        $recommendation = Recommendation::factory()->create();
        $newName = fake()->sentence(6);

        livewire(ListRecommendations::class)
            ->callAction(TestAction::make('edit')->table($recommendation), data: [
                'name' => $newName,
            ])
            ->assertHasNoFormErrors();

        expect($recommendation->refresh()->name)->toBe($newName);
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

    it('can edit a priority action', function () {
        $recommendation = Recommendation::factory()->create();
        $priorityAction = PriorityAction::factory()->create(['recommendation_id' => $recommendation->id]);
        $newShortName = fake()->words(3, true);

        livewire(ListPriorityActions::class)
            ->callAction(TestAction::make('edit')->table($priorityAction), data: [
                'short_name' => $newShortName,
                'name' => fake()->sentence(5),
            ])
            ->assertHasNoFormErrors();

        expect($priorityAction->refresh()->short_name)->toBe($newShortName);
    });

});

describe('Admin / ScoreResource', function () {

    it('lists scores', function () {
        $scores = Score::factory()->count(2)->create();

        livewire(ListScores::class)
            ->assertCanSeeTableRecords($scores);
    });

    it('can create a new score', function () {
        livewire(ListScores::class)
            ->callAction('create', data: [
                'name' => 'Strong alignment',
                'score' => 2,
            ])
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('scores', [
            'name' => 'Strong alignment',
            'score' => 2,
        ]);
    });

    it('fails validation when score name is missing', function () {
        livewire(ListScores::class)
            ->callAction('create', data: [
                'name' => null,
                'score' => 1,
            ])
            ->assertHasFormErrors(['name']);
    });

    it('can edit a score', function () {
        $score = Score::factory()->create();

        livewire(ListScores::class)
            ->callAction(TestAction::make('edit')->table($score), data: [
                'name' => 'Updated Score Name',
                'score' => 3,
            ])
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('scores', [
            'id' => $score->id,
            'name' => 'Updated Score Name',
            'score' => 3,
        ]);
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
        User::factory()->count(3)->create();

        livewire(ListUsers::class)
            ->assertSuccessful();
    });

    it('can invite a user by email', function () {
        Mail::fake();
        $role = Role::where('name', 'admin')->first();

        // Repeater items require UUID keys; mount the action first then set state directly
        livewire(ListUsers::class)
            ->mountAction('invite users')
            ->set('mountedActions.0.data.users', [
                (string) Str::uuid() => ['email' => 'invited@example.com', 'role' => $role->id],
            ])
            ->callMountedAction()
            ->assertHasNoFormErrors();

        Mail::assertSent(InviteUser::class);
    });

    it('can create a new user', function () {
        livewire(ListUsers::class)
            ->callAction('create', data: [
                'name' => 'New User',
                'email' => 'newuser@example.com',
            ])
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
        ]);
    });

    it('fails validation when user email is missing', function () {
        livewire(ListUsers::class)
            ->callAction('create', data: [
                'name' => 'No Email User',
                'email' => null,
            ])
            ->assertHasFormErrors(['email']);
    });

    it('can edit a user', function () {
        $user = User::factory()->create();

        livewire(ListUsers::class)
            ->callAction(TestAction::make('edit')->table($user), data: [
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
            ])
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    });

});

describe('Admin / CountryResource', function () {

    it('lists countries', function () {
        $countries = Country::factory()->count(2)->create();

        livewire(ListCountries::class)
            ->assertCanSeeTableRecords($countries);
    });

    it('can create a new country', function () {
        livewire(ListCountries::class)
            ->callAction('create', data: [
                'id' => 'TST',
                'name' => 'Test Country',
            ])
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('countries', ['id' => 'TST', 'name' => 'Test Country']);
    });

    it('fails validation when country name is missing', function () {
        livewire(ListCountries::class)
            ->callAction('create', data: [
                'id' => 'TST',
                'name' => null,
            ])
            ->assertHasFormErrors(['name']);
    });

    it('fails validation when country iso3 id is missing', function () {
        livewire(ListCountries::class)
            ->callAction('create', data: [
                'id' => null,
                'name' => 'Test Country',
            ])
            ->assertHasFormErrors(['id']);
    });

    it('can edit a country', function () {
        $country = Country::factory()->create();

        livewire(ListCountries::class)
            ->callAction(TestAction::make('edit')->table($country), data: [
                'name' => 'Updated Country',
            ])
            ->assertHasNoFormErrors();

        expect($country->refresh()->name)->toBe('Updated Country');
    });

});

describe('Admin / LanguageResource', function () {

    it('lists languages', function () {
        Language::factory()->create(['id' => 'en']);
        Language::factory()->create(['id' => 'fr']);

        livewire(ListLanguages::class)
            ->assertSuccessful();
    });

    it('can create a new language', function () {
        Language::factory()->create(['id' => 'en', 'name' => ['en' => 'English']]);

        livewire(ListLanguages::class)
            ->callAction('create', data: [
                'id' => 'fr',
                'name' => ['en' => 'French'],
            ])
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('languages', ['id' => 'fr']);
    });

    it('can edit a language name', function () {
        Language::factory()->create(['id' => 'en', 'name' => ['en' => 'English']]);
        $language = Language::factory()->create(['id' => 'fr', 'name' => ['en' => 'French']]);

        livewire(ListLanguages::class)
            ->callAction(TestAction::make('edit')->table($language), data: [
                'name' => ['en' => 'Français'],
            ])
            ->assertHasNoFormErrors();

        expect($language->refresh()->getTranslation('name', 'en'))->toBe('Français');
    });

    it('can delete a language that is not in use', function () {
        Language::factory()->create(['id' => 'en', 'name' => ['en' => 'English']]);
        $language = Language::factory()->create(['id' => 'zz', 'name' => ['en' => 'Unused Language']]);

        livewire(ListLanguages::class)
            ->callAction(TestAction::make('delete')->table($language))
            ->assertHasNoFormErrors();

        $this->assertDatabaseMissing('languages', ['id' => 'zz']);
    });

    it('delete action is disabled for a language in use', function () {
        $language = Language::factory()->create(['id' => 'en', 'name' => ['en' => 'English']]);
        Assessment::factory()->create(['language_id' => 'en']);

        livewire(ListLanguages::class)
            ->assertActionDisabled(TestAction::make('delete')->table($language));
    });

});

describe('Admin / AePrincipleResource', function () {

    it('lists ae principles', function () {
        $principles = AePrinciple::factory()->count(2)->create();

        livewire(ListAePrinciples::class)
            ->assertCanSeeTableRecords($principles);
    });

    it('can edit an agroecology principle', function () {
        $principle = AePrinciple::factory()->create(['name' => 'Original Principle']);

        livewire(ListAePrinciples::class)
            ->callAction(TestAction::make('edit')->table($principle), data: [
                'name' => 'Updated Principle',
            ])
            ->assertHasNoFormErrors();

        expect($principle->refresh()->name)->toBe('Updated Principle');
    });

});
