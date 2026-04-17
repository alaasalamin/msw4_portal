<?php

namespace App\Services;

use App\Actions\Automation\SendEmailTemplateAction;
use App\Jobs\RunAutomationAction;
use App\Models\AutomationLog;
use App\Models\AutomationRule;
use App\Models\Device;
use App\Models\WorkflowStep;

class AutomationEngine
{
    public static function run(string $triggerType, Device $device, array $context = []): void
    {
        // Fire step-level email templates first (attached directly to the step node)
        if ($triggerType === 'step_changed') {
            static::sendStepEmailTemplates($device);
        }

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

    /** Send any email templates attached directly to the device's new workflow step */
    private static function sendStepEmailTemplates(Device $device): void
    {
        // No email on file → nothing to send, skip silently
        if (empty($device->customer_email)) return;

        $step = WorkflowStep::find($device->workflow_step_id);
        if (! $step || empty($step->email_template_ids)) return;

        $sender = new SendEmailTemplateAction();

        foreach ($step->email_template_ids as $templateId) {
            try {
                $payload = $sender->execute($device, [
                    'template_id' => $templateId,
                    'recipient'   => 'customer',
                ]);

                AutomationLog::create([
                    'rule_id'      => null,
                    'rule_name'    => 'Step: ' . $step->label,
                    'trigger_type' => 'step_changed',
                    'device_id'    => $device->id,
                    'action_type'  => 'send_email_template',
                    'status'       => 'success',
                    'payload'      => $payload,
                ]);
            } catch (\Throwable $e) {
                AutomationLog::create([
                    'rule_id'      => null,
                    'rule_name'    => 'Step: ' . $step->label,
                    'trigger_type' => 'step_changed',
                    'device_id'    => $device->id,
                    'action_type'  => 'send_email_template',
                    'status'       => 'failed',
                    'error'        => $e->getMessage(),
                ]);
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
