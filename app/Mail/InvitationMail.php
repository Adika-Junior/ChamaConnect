<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\EmailVerificationToken;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $tokenModel;

    public function __construct(EmailVerificationToken $tokenModel)
    {
        $this->tokenModel = $tokenModel;
    }

    public function build()
    {
        $link = url('/auth/register/' . $this->tokenModel->token);

        return $this->subject('You are invited to join Team Task Management')
            ->view('emails.invitation')
            ->with(['link' => $link, 'expires_at' => $this->tokenModel->expires_at]);
    }
}
