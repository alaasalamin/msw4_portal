<?php

namespace App\Services;

use App\Jobs\RunAutomationAction;
use App\Models\AutomationRule;
use App\Models\Device;

class AutomationEngine
{
    public static function run(string $triggerType, Device $device, array $context = []): void
    {
        $rules = AutomationRule::where('is_active', true)
            ->where('trigger_type', $triggerType)
            ->with('actions')
            ->orderBy('sort_order')
            ->get();

        foreach ($rules as $rule) {
            if (! static::matchesTrigger($rule, $device)) {
                continue;
            }

            foreach ($rule->actions as $action) {
                RunAutomationAction::dispatch($rule, $action, $device, $context);
            }
        }
    }

    private static function matchesTrigger(AutomationRule $rule, Device $device): bool
    {
        $config = $rule->trigger_config ?? [];

        return match ($rule->trigger_type) {
            'step_changed' => empty($config['step_id']) || (int) $config['step_id'] === (int) $device->workflow_step_id,
            default        => true,
        };
    }
}
