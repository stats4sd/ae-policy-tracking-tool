<?php

namespace App\Mail;

use Filament\Facades\Filament;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;
use Stats4sd\FilamentTeamManagement\Models\Invite;

class InviteUserToAssessment extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Invite $invite)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.from.address'),
            subject: config('app.name').': Invitation To Join Team '.$this->invite->team->title,

        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $routeName = Filament::getDefaultPanel()->generateRouteName('auth.register');

        return new Content(
            markdown: 'filament-team-management::emails.invite',
            with: [
                'acceptUrl' => URL::signedRoute(
                    $routeName,
                    [
                        'token' => $this->invite->token,
                    ],
                ),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
