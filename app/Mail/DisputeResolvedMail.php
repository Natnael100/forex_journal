<?php

namespace App\Mail;

use App\Models\Dispute;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DisputeResolvedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $dispute;

    /**
     * Create a new message instance.
     */
    public function __construct(Dispute $dispute)
    {
        $this->dispute = $dispute;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Update on Your Dispute Case #' . $this->dispute->id)
                    ->markdown('emails.disputes.resolved');
    }
}
