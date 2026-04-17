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

    public string $mailSubject;
    public string $mailBody;
    public string $ticketNumber;
    public string $deviceLabel;

    public function __construct(
        string $subject,
        string $body,
        string $ticketNumber = '',
        string $deviceLabel  = '',
    ) {
        $this->mailSubject  = $subject;
        $this->mailBody     = $body;
        $this->ticketNumber = $ticketNumber;
        $this->deviceLabel  = $deviceLabel;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->mailSubject,
            replyTo: [
                new \Illuminate\Mail\Mailables\Address(
                    config('mail.from.address'),
                    config('mail.from.name'),
                ),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view:     'mail.automation',
            text:     'mail.automation-text',
        );
    }
}
