<?php

namespace App\Models;

use App\Mail\InviteUserToAssessment;
use Carbon\Carbon;
use Filament\Models\Contracts\HasName;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Stats4sd\FilamentTeamManagement\Models\Team;

// # Assessments are used as the tenant: users can join specific assessments, and the entire front-end is scoped to a specific assessment. Admin users should be able to access all assessments; other users may have access to one or multiple based on specific assignments.
class Assessment extends Team implements HasName
{
    protected static function booted()
    {
        static::creating(function ($query) {
            $query->status = 'In Progress';
            $query->title = $query->country->name.' '.Carbon::now()->year;
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

    public function policies(): HasMany
    {
        return $this->hasMany(Policy::class);
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
}
