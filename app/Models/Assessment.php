<?php

namespace App\Models;

use App\Mail\InviteUserToAssessment;
use Filament\Models\Contracts\HasName;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Stats4sd\FilamentTeamManagement\Models\Team;

// # Assessments are used as the tenant: users can join specific assessments, and the entire front-end is scoped to a specific assessment. Admin users should be able to access all assessments; other users may have access to one or multiple based on specific assignments.
class Assessment extends Team implements HasName
{
    protected static function booted(): void
    {
        static::creating(function ($query) {
            // set default status to 'In Progress' on creation
            $query->status = 'In Progress';
        });

        static::created(function (self $assessment) {

            ray('Assessment created - adding default search terms');

            // automatically give the assessment all the DefaultSearchTerms as new SearchTerm entries
            $defaultTerms = DefaultSearchTerm::select(['phrase', 'priority_action_id'])->get()->toArray();
            $assessment->searchTerms()->createMany($defaultTerms);
        });
    }

    /**
     * Generate an invitation to join this team for each of the provided email addresses
     */
    public function sendInvites(array $emails): void
    {

        foreach ($emails as $email) {
            // if email is empty, skip to next email
            if ($email == null || $email == '') {
                continue;
            }

            $invite = $this->invites()->create([
                'email' => $email,
                'inviter_id' => auth()->id(),
                'token' => Str::random(24),
            ]);

            Mail::to($invite->email)->send(new InviteUserToAssessment($invite));

            // show notification after sending invitation email to user
            Notification::make()
                ->success()
                ->title('Invitation Sent')
                ->body('An email invitation has been successfully sent to '.$email)
                ->send();
        }
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function statements(): HasMany
    {
        return $this->hasMany(Statement::class);
    }

    public function policyDocuments(): HasMany
    {
        return $this->hasMany(PolicyDocument::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'assessment_user', 'assessment_id', 'user_id');
    }

    public function getFilamentName(): string
    {
        return $this->title ?? $this->country->name;
    }

    // Override Team model's invites relationship to use our Invite model, to enable email sending on invite creation

    /** @return HasMany<Invite, $this> */
    public function invites(): HasMany
    {
        return $this->hasMany(Invite::class, 'assessment_id', 'id');
    }

    /** @return HasMany<SearchTerm, $this> */
    public function searchTerms(): HasMany
    {
        return $this->hasMany(SearchTerm::class);
    }

    /** @return HasManyThrough<Highlight, PolicyDocument, $this> */
    public function highlights(): HasManyThrough
    {
        return $this->hasManyThrough(Highlight::class, PolicyDocument::class);
    }
}
