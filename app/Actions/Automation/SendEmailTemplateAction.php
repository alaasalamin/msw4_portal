<?php

namespace App\Actions\Automation;

use App\Mail\AutomationMail;
use App\Models\Device;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Mail;

class SendEmailTemplateAction
{
    public function execute(Device $device, array $config, array $context = []): array
    {
        $template = EmailTemplate::find($config['template_id'] ?? null);

        if (! $template) {
            throw new \RuntimeException("Email template not found (id={$config['template_id']}).");
        }

        $recipient = match ($config['recipient'] ?? 'customer') {
            'custom' => $config['custom_email'] ?? null,
            default  => $device->customer_email,
        };

        if (! $recipient) {
            throw new \RuntimeException("No recipient email available for device #{$device->id}.");
        }

        ['subject' => $subject, 'body' => $body] = $template->render($device);

        Mail::to($recipient)->send(new AutomationMail(
            subject:      $subject,
            body:         $body,
            ticketNumber: $device->ticket_number,
            deviceLabel:  trim("{$device->brand} {$device->model}"),
        ));

        return [
            'to'          => $recipient,
            'subject'     => $subject,
            'template_id' => $template->id,
            'template'    => $template->name,
        ];
    }
}
