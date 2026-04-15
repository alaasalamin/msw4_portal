<?php

namespace App\Actions\Automation;

use App\Models\Device;
use App\Models\DeviceAllowance;

class SendAllowanceAction
{
    public function execute(Device $device, array $config, array $context = []): array
    {
        $email = $device->customer_email;
        if (! $email) {
            throw new \RuntimeException("Device {$device->ticket_number} has no customer email.");
        }

        // Create allowance record with token
        $allowance = DeviceAllowance::create([
            'device_id'      => $device->id,
            'customer_email' => $email,
            'customer_name'  => $device->customer_name,
            'message'        => $config['message'] ?? null,
            'expires_at'     => now()->addDays((int) ($config['expires_days'] ?? 7)),
        ]);

        // TODO: dispatch a Mailable to $email with approve/decline links
        // Mail::to($email)->send(new AllowanceRequestMail($allowance));

        return ['allowance_id' => $allowance->id, 'token' => $allowance->token, 'email' => $email];
    }
}
