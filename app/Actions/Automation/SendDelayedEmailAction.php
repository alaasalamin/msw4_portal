<?php

namespace App\Actions\Automation;

use App\Jobs\SendAutomationEmailJob;
use App\Models\AutomationRule;
use App\Models\Device;

class SendDelayedEmailAction
{
    public function execute(Device $device, array $config, array $context = []): array
    {
        $value = max(1, (int) ($config['delay_value'] ?? 1));
        $unit  = $config['delay_unit'] ?? 'hours';

        $delay = match ($unit) {
            'minutes' => now()->addMinutes($value),
            'days'    => now()->addDays($value),
            default   => now()->addHours($value),
        };

        $ruleId   = $context['rule_id']   ?? 0;
        $ruleName = $context['rule_name'] ?? '';

        SendAutomationEmailJob::dispatch($device, $config, $ruleId, $ruleName)
            ->delay($delay);

        return [
            'scheduled_at' => $delay->toIso8601String(),
            'delay'        => "{$value} {$unit}",
        ];
    }
}
