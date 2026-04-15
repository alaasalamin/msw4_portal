<?php

namespace App\Actions\Automation;

use App\Models\Device;
use App\Models\WorkflowStep;

class ChangeStepAction
{
    public function execute(Device $device, array $config, array $context = []): array
    {
        $stepId = (int) ($config['step_id'] ?? 0);
        if (! $stepId) {
            throw new \RuntimeException("No target step configured.");
        }

        $step = WorkflowStep::findOrFail($stepId);
        $device->update(['workflow_step_id' => $step->id]);

        return ['new_step_id' => $step->id, 'new_step_label' => $step->label];
    }
}
