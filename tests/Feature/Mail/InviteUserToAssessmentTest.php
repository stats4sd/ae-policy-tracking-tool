<?php

use App\Mail\InviteUserToAssessment;
use App\Models\Assessment;
use App\Models\Invite;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

describe('InviteUserToAssessment mailable', function () {

    beforeEach(function () {
        Mail::fake();

        $this->user = User::factory()->create();
        $this->assessment = Assessment::factory()->create(['title' => 'Ghana Assessment 2024']);
    });

    it('is sent to the correct recipient', function () {
        $this->actingAs($this->user);

        $this->assessment->sendInvites(['recipient@example.com']);

        Mail::assertSent(InviteUserToAssessment::class, function ($mail) {
            return $mail->hasTo('recipient@example.com');
        });
    });

    it('is sent when an invite is created', function () {
        $this->actingAs($this->user);

        $this->assessment->sendInvites(['newuser@example.com']);

        Mail::assertSent(InviteUserToAssessment::class);
    });

    it('has the correct subject containing the assessment title', function () {
        $invite = new Invite([
            'email' => 'test@example.com',
            'token' => 'abc123def456ghi789jkl',
            'assessment_id' => $this->assessment->id,
            'inviter_id' => $this->user->id,
        ]);
        $invite->setRelation('team', $this->assessment);

        $mailable = new InviteUserToAssessment($invite);
        $envelope = $mailable->envelope();

        expect($envelope->subject)->toContain('Invitation To Join Team')
            ->and($envelope->subject)->toContain('Ghana Assessment 2024');
    });

    it('mailable contains a signed URL with the invite token', function () {
        $invite = new Invite([
            'email' => 'test@example.com',
            'token' => 'uniquetoken123456789012',
            'assessment_id' => $this->assessment->id,
            'inviter_id' => $this->user->id,
        ]);
        $invite->setRelation('team', $this->assessment);

        $mailable = new InviteUserToAssessment($invite);
        $content = $mailable->content();

        expect($content->with['acceptUrl'])->toContain('uniquetoken123456789012');
    });

});
