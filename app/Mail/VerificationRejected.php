<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerificationRejected extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public string $reason)
    {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Account Verification Status',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.verification.rejected',
        );
    }
}
