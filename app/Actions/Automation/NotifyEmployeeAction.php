<?php

namespace App\Actions\Automation;

use App\Models\Device;
use App\Models\User;
use App\Notifications\AutomationNotification;

class NotifyEmployeeAction
{
    public function execute(Device $device, array $config, array $context = []): array
    {
        $message = $config['message'] ?? "Gerät {$device->ticket_number} erfordert deine Aufmerksamkeit.";

        $employeeIds = $config['employee_ids'] ?? [];

        $query = User::where('type', 'employee');
        if (! empty($employeeIds)) {
            $query->whereIn('id', $employeeIds);
        }

        // Interpolate placeholders
        $message = str_replace(
            ['{{ticket}}', '{{brand}}', '{{model}}', '{{customer}}'],
            [$device->ticket_number, $device->brand, $device->model, $device->customer?->name ?? ''],
            $message
        );

        $notified = [];
        $query->each(function (User $user) use ($device, $message, &$notified) {
            $user->notify(new AutomationNotification($device, $message));
            $notified[] = $user->id;
        });

        return ['notified_user_ids' => $notified, 'message' => $message];
    }
}
