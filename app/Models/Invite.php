<?php

namespace App\Models;

use App\Mail\InviteUserToAssessment;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class Invite extends \Stats4sd\FilamentTeamManagement\Models\Invite
{
    protected static function booted()
    {
        parent::booted();

        // automatically generate and send email when an invite is created
        self::creating(function (self $invite) {
            if (! $invite->token) {
                $invite->token = \Illuminate\Support\Str::random(24);
            }
        });

        self::created(function (self $invite) {
            Mail::to($invite->email)->send(new InviteUserToAssessment($invite));

            // show notification after sending invitation email to user
            // Note, this doesn't work for back-end or queued tasks, only for synchronous front-end operations
            \Filament\Notifications\Notification::make()
                ->success()
                ->title('Invitation Sent')
                ->body('An email invitation has been successfully sent to '.$invite->email)
                ->send();
        });

    }
}
