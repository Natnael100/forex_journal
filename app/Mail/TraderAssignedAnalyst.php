<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TraderAssignedAnalyst extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $trader, public User $analyst)
    {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Analyst Assigned',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.assignment.trader-assigned',
        );
    }
}
