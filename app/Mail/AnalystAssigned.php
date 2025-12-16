<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AnalystAssigned extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $analyst, public User $trader)
    {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Trader Assignment',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.assignment.analyst-assigned',
        );
    }
}
