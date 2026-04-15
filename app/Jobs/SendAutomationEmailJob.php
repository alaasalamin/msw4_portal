<?php

namespace App\Jobs;

use App\Mail\AutomationMail;
use App\Models\AutomationLog;
use App\Models\Device;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendAutomationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public readonly Device $device,
        public readonly array  $config,
        public readonly int    $ruleId,
        public readonly string $ruleName,
    ) {}

    public function handle(): void
    {
        $recipient = match ($this->config['recipient'] ?? 'customer') {
            'custom' => $this->config['custom_email'] ?? null,
            default  => $this->device->customer_email,
        };

        if (! $recipient) {
            throw new \RuntimeException("No recipient email for device {$this->device->ticket_number}.");
        }

        $subject = $this->interpolate($this->config['subject'] ?? 'Benachrichtigung');
        $body    = $this->interpolate($this->config['body']    ?? '');

        Mail::to($recipient)->send(new AutomationMail(
            subject:      $subject,
            body:         $body,
            ticketNumber: $this->device->ticket_number,
            deviceLabel:  trim("{$this->device->brand} {$this->device->model}"),
        ));

        AutomationLog::create([
            'rule_id'      => $this->ruleId,
            'rule_name'    => $this->ruleName,
            'trigger_type' => 'delayed_email',
            'device_id'    => $this->device->id,
            'action_type'  => 'send_delayed_email',
            'status'       => 'success',
            'payload'      => ['to' => $recipient, 'subject' => $subject],
        ]);
    }

    private function interpolate(string $text): string
    {
        return str_replace(
            ['{{ticket}}', '{{brand}}', '{{model}}', '{{customer}}'],
            [$this->device->ticket_number, $this->device->brand, $this->device->model, $this->device->customer_name ?? ''],
            $text,
        );
    }
}
