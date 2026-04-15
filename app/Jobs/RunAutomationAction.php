<?php

namespace App\Jobs;

use App\Actions\Automation\ChangeStepAction;
use App\Actions\Automation\GenerateInvoiceAction;
use App\Actions\Automation\NotifyEmployeeAction;
use App\Actions\Automation\SendAllowanceAction;
use App\Actions\Automation\SendDelayedEmailAction;
use App\Actions\Automation\SendEmailAction;
use App\Models\AutomationAction;
use App\Models\AutomationLog;
use App\Models\AutomationRule;
use App\Models\Device;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class RunAutomationAction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly AutomationRule   $rule,
        public readonly AutomationAction $action,
        public readonly Device           $device,
        public readonly array            $context = [],
    ) {}

    public function handle(): void
    {
        try {
            $handler = $this->resolveHandler();
            $ruleContext = array_merge($this->context, [
                'rule_id'   => $this->rule->id,
                'rule_name' => $this->rule->name,
            ]);
            $payload = $handler->execute($this->device, $this->action->action_config ?? [], $ruleContext);

            AutomationLog::create([
                'rule_id'      => $this->rule->id,
                'rule_name'    => $this->rule->name,
                'trigger_type' => $this->rule->trigger_type,
                'device_id'    => $this->device->id,
                'action_type'  => $this->action->action_type,
                'status'       => 'success',
                'payload'      => $payload,
            ]);
        } catch (Throwable $e) {
            AutomationLog::create([
                'rule_id'      => $this->rule->id,
                'rule_name'    => $this->rule->name,
                'trigger_type' => $this->rule->trigger_type,
                'device_id'    => $this->device->id,
                'action_type'  => $this->action->action_type,
                'status'       => 'failed',
                'error'        => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    private function resolveHandler()
    {
        return match ($this->action->action_type) {
            'send_allowance'    => new SendAllowanceAction(),
            'notify_employee'   => new NotifyEmployeeAction(),
            'send_email'        => new SendEmailAction(),
            'send_delayed_email'=> new SendDelayedEmailAction(),
            'change_step'       => new ChangeStepAction(),
            'generate_invoice'  => new GenerateInvoiceAction(),
            default             => throw new \RuntimeException("Unknown action type: {$this->action->action_type}"),
        };
    }
}
