<?php

namespace App\Filament\Widgets;

use App\Models\Device;
use App\Models\WorkflowPhase;
use App\Models\WorkflowStep;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;

class DeviceWorkflowProgressWidget extends Widget
{
    protected string $view = 'filament.widgets.device-workflow-progress-widget';

    protected int|string|array $columnSpan = 'full';

    public ?Model  $record          = null;
    public ?int    $pendingStepId   = null;
    public ?string $pendingStepLabel = null;
    public ?string $flashMessage    = null;

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
            return ['phases' => $phases, 'currentStepId' => null, 'currentPhaseId' => null, 'currentStepIndex' => null, 'currentPhaseOrder' => 0];
        }

        $currentStep    = $device->workflowStep;
        $currentPhaseId = $currentStep?->phase_id;

        $currentStepIndex = null;
        if ($currentStep) {
            $phase = $phases->firstWhere('id', $currentPhaseId);
            if ($phase) {
                $currentStepIndex = $phase->steps->search(fn ($s) => $s->id === $currentStep->id);
            }
        }

        $currentPhaseOrder = $phases->firstWhere('id', $currentPhaseId)?->sort_order ?? 0;

        return [
            'phases'            => $phases,
            'currentStepId'     => $device->workflow_step_id,
            'currentPhaseId'    => $currentPhaseId,
            'currentStepIndex'  => $currentStepIndex,
            'currentPhaseOrder' => $currentPhaseOrder,
        ];
    }

    /** User clicks a node — stage it for confirmation */
    public function selectStep(int $stepId): void
    {
        $device = $this->getDevice();
        if (! $device) return;

        // Clicking the already-pending or current step cancels
        if ($this->pendingStepId === $stepId || $device->workflow_step_id === $stepId) {
            $this->pendingStepId    = null;
            $this->pendingStepLabel = null;
            return;
        }

        $step = WorkflowStep::find($stepId);
        if (! $step) return;

        $this->pendingStepId    = $stepId;
        $this->pendingStepLabel = $step->label;
    }

    /** User confirmed — save the new step */
    public function confirmStep(): void
    {
        $device = $this->getDevice();
        if (! $device || ! $this->pendingStepId) return;

        $device->update(['workflow_step_id' => $this->pendingStepId]);
        $this->record = $device->fresh('workflowStep');

        $this->flashMessage     = 'Schritt geändert zu „' . $this->pendingStepLabel . '"';
        $this->pendingStepId    = null;
        $this->pendingStepLabel = null;
    }

    public function cancelStep(): void
    {
        $this->pendingStepId    = null;
        $this->pendingStepLabel = null;
    }
}
