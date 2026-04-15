<?php

namespace App\Filament\Widgets;

use App\Models\Device;
use App\Models\WorkflowPhase;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;

class DeviceWorkflowProgressWidget extends Widget
{
    protected string $view = 'filament.widgets.device-workflow-progress-widget';

    protected int|string|array $columnSpan = 'full';

    public ?Model $record = null;

    public function getDevice(): ?Device
    {
        return $this->record instanceof Device ? $this->record : null;
    }

    public function getProgressData(): array
    {
        $device = $this->getDevice();

        $phases = WorkflowPhase::with(['steps' => fn ($q) => $q->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        if (! $device?->workflow_step_id) {
            return ['phases' => $phases, 'currentStepId' => null, 'currentPhaseId' => null, 'currentStepIndex' => null];
        }

        $currentStep  = $device->workflowStep;
        $currentPhaseId = $currentStep?->phase_id;

        // Find the current step's index within its phase
        $currentStepIndex = null;
        if ($currentStep) {
            $phase = $phases->firstWhere('id', $currentPhaseId);
            if ($phase) {
                $currentStepIndex = $phase->steps->search(fn ($s) => $s->id === $currentStep->id);
            }
        }

        // Determine phase order: before, current, after
        $currentPhaseOrder = $phases->firstWhere('id', $currentPhaseId)?->sort_order ?? 0;

        return [
            'phases'            => $phases,
            'currentStepId'     => $device->workflow_step_id,
            'currentPhaseId'    => $currentPhaseId,
            'currentStepIndex'  => $currentStepIndex,
            'currentPhaseOrder' => $currentPhaseOrder,
        ];
    }
}
