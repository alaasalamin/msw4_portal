<?php

namespace App\Actions\Automation;

use App\Mail\AutomationMail;
use App\Models\Device;
use Illuminate\Support\Facades\Mail;

class SendEmailAction
{
    public function execute(Device $device, array $config, array $context = []): array
    {
        $recipient = match ($config['recipient'] ?? 'customer') {
            'custom' => $config['custom_email'] ?? null,
            default  => $device->customer_email,
        };

        if (! $recipient) {
            throw new \RuntimeException("No recipient email available.");
        }

        $subject = $this->interpolate($config['subject'] ?? 'Benachrichtigung', $device);
        $body    = $this->interpolate($config['body']    ?? '',                  $device);

        Mail::to($recipient)->send(new AutomationMail(
            subject:      $subject,
            body:         $body,
            ticketNumber: $device->ticket_number,
            deviceLabel:  trim("{$device->brand} {$device->model}"),
        ));

        return ['to' => $recipient, 'subject' => $subject];
    }

    private function interpolate(string $text, Device $device): string
    {
        return str_replace(
            ['{{ticket}}', '{{brand}}', '{{model}}', '{{customer}}'],
            [$device->ticket_number, $device->brand, $device->model, $device->customer_name],
            $text
        );
    }
}
