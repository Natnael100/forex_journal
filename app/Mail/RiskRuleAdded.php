<?php

namespace App\Mail;

use App\Models\User;
use App\Models\RiskRule;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RiskRuleAdded extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $trader, public RiskRule $rule)
    {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Risk Rule Assigned',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.analyst.risk-rule-added',
        );
    }
}
