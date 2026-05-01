<?php

use App\Enums\AssessmentStatus;
use App\Models\Assessment;
use App\Models\Country;
use App\Models\DefaultSearchTerm;
use App\Models\Extract;
use App\Models\Invite;
use App\Models\Language;
use App\Models\PolicyDocument;
use App\Models\PriorityAction;
use App\Models\Recommendation;
use App\Models\Statement;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

describe('Assessment model', function () {

    it('sets status to In Progress on creation', function () {
        $assessment = Assessment::factory()->create();

        expect($assessment->status)->toBe(AssessmentStatus::InProgress);
    });

    it('creates search terms from default search terms on creation', function () {
        $recommendation = Recommendation::factory()->create();
        $priorityAction = PriorityAction::factory()->create(['recommendation_id' => $recommendation->id]);
        DefaultSearchTerm::factory()->create([
            'priority_action_id' => $priorityAction->id,
            'phrase' => ['en' => 'agroecology'],
        ]);

        $assessment = Assessment::factory()->create();

        expect($assessment->searchTerms()->count())->toBe(1);
        $this->assertDatabaseHas('search_terms', [
            'assessment_id' => $assessment->id,
            'priority_action_id' => $priorityAction->id,
        ]);
    });

    it('does not create search terms when no defaults exist', function () {
        $assessment = Assessment::factory()->create();

        expect($assessment->searchTerms()->count())->toBe(0);
    });

    it('sendInvites creates invite records for each email', function () {
        Mail::fake();

        $user = User::factory()->create();
        $assessment = Assessment::factory()->create();

        $this->actingAs($user);

        $assessment->sendInvites(['test@example.com', 'second@example.com']);

        $this->assertDatabaseHas('invites', [
            'assessment_id' => $assessment->id,
            'email' => 'test@example.com',
        ]);
        $this->assertDatabaseHas('invites', [
            'assessment_id' => $assessment->id,
            'email' => 'second@example.com',
        ]);
    });

    it('sendInvites skips empty email addresses', function () {
        Mail::fake();

        $user = User::factory()->create();
        $assessment = Assessment::factory()->create();
        $this->actingAs($user);

        $assessment->sendInvites(['valid@example.com', '', null]);

        expect(Invite::where('assessment_id', $assessment->id)->count())->toBe(1);
    });

    it('getFilamentName returns title when set', function () {
        $assessment = Assessment::factory()->create(['title' => 'Kenya Assessment 2024']);

        expect($assessment->getFilamentName())->toBe('Kenya Assessment 2024');
    });

    it('getFilamentName falls back to country name when no title', function () {
        $country = Country::factory()->create(['name' => 'Ghana']);
        $assessment = Assessment::factory()->create(['country_id' => $country->id, 'title' => null]);

        expect($assessment->getFilamentName())->toBe('Ghana');
    });

    it('has many policy documents', function () {
        $assessment = Assessment::factory()->create();
        PolicyDocument::factory()->count(3)->create(['assessment_id' => $assessment->id]);

        expect($assessment->policyDocuments)->toHaveCount(3);
    });

    it('has many statements', function () {
        $assessment = Assessment::factory()->create();
        $recommendation = Recommendation::factory()->create();
        $priorityAction = PriorityAction::factory()->create(['recommendation_id' => $recommendation->id]);
        Statement::factory()->count(2)->create([
            'assessment_id' => $assessment->id,
            'priority_action_id' => $priorityAction->id,
        ]);

        expect($assessment->statements()->withoutGlobalScopes()->count())->toBe(2);
    });

    it('has many extracts through policy documents', function () {
        $assessment = Assessment::factory()->create();
        $document = PolicyDocument::factory()->create(['assessment_id' => $assessment->id]);
        Extract::factory()->count(3)->create(['policy_document_id' => $document->id]);

        expect($assessment->extracts()->count())->toBe(3);
    });

    it('belongs to a country', function () {
        $country = Country::factory()->create();
        $assessment = Assessment::factory()->create(['country_id' => $country->id]);

        expect($assessment->country->id)->toBe($country->id);
    });

    it('belongs to a language', function () {
        $language = Language::factory()->create(['id' => 'en']);
        $assessment = Assessment::factory()->create(['language_id' => 'en']);

        expect($assessment->language->id)->toBe('en');
    });

});
