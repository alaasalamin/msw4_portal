<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AutomationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $subject,
        public readonly string $body,
        public readonly string $ticketNumber = '',
        public readonly string $deviceLabel  = '',
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->subject);
    }

    public function content(): Content
    {
        return new Content(view: 'mail.automation');
    }
}
