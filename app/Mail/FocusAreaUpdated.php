<?php

namespace App\Mail;

use App\Models\User;
use App\Models\AnalystAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FocusAreaUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $trader, public AnalystAssignment $assignment)
    {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Coaching Focus Assigned',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.analyst.focus-area-updated',
        );
    }
}
