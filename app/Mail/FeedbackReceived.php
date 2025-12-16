<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Feedback;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FeedbackReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $trader, public Feedback $feedback)
    {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Feedback Received',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.feedback.received',
        );
    }
}
