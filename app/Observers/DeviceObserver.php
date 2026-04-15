<?php

namespace App\Observers;

use App\Models\Device;
use App\Models\User;
use App\Models\WorkflowStep;
use App\Notifications\DeviceStepChanged;
use App\Services\AutomationEngine;

class DeviceObserver
{
    public function updating(Device $device): void
    {
        if (! $device->isDirty('workflow_step_id')) {
            return;
        }

        $newStepId = $device->workflow_step_id;
        if (! $newStepId) {
            return;
        }

        $step = WorkflowStep::with('employees')->find($newStepId);
        if (! $step) {
            return;
        }

        // Notify each responsible employee (as User model so notifications table works)
        if ($step->employees->isNotEmpty()) {
            $employeeIds = $step->employees->pluck('id');
            User::whereIn('id', $employeeIds)->each(function (User $user) use ($device, $step) {
                $user->notify(new DeviceStepChanged($device, $step));
            });
        }

        // Run automation rules for this step change (always, regardless of step employees)
        AutomationEngine::run('step_changed', $device);
    }
}
