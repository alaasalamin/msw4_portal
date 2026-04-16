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

    public ?Model  $record           = null;
    public ?int    $pendingStepId    = null;
    public ?string $pendingStepLabel = null;
    public ?string $flashMessage     = null;

    // Modal state
    public bool    $showFieldModal   = false;
    public ?int    $modalStepId      = null;
    public ?string $modalStepLabel   = null;
    public array   $modalFields      = [];   // [{label, type}, ...]
    public array   $modalValues      = [];   // indexed array matching modalFields

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

    /** User clicks a node */
    public function selectStep(int $stepId): void
    {
        $device = $this->getDevice();
        if (! $device) return;

        // Clicking the current step does nothing
        if ($device->workflow_step_id === $stepId) return;

        // Clicking the already-pending step cancels staging
        if ($this->pendingStepId === $stepId) {
            $this->pendingStepId    = null;
            $this->pendingStepLabel = null;
            return;
        }

        $step = WorkflowStep::find($stepId);
        if (! $step) return;

        // If step has custom fields → open the modal
        if (! empty($step->custom_fields)) {
            $this->pendingStepId    = null;
            $this->pendingStepLabel = null;
            $this->modalStepId      = $stepId;
            $this->modalStepLabel   = $step->label;
            $this->modalFields      = $step->custom_fields;
            $this->modalValues      = array_fill(0, count($step->custom_fields), '');
            $this->showFieldModal   = true;
            return;
        }

        // Otherwise stage for plain confirmation
        $this->pendingStepId    = $stepId;
        $this->pendingStepLabel = $step->label;
    }

    /** Plain confirmation (no custom fields) */
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

    /** Modal submit — save step + filled custom field values */
    public function submitModal(): void
    {
        $device = $this->getDevice();
        if (! $device || ! $this->modalStepId) return;

        $stepData = $device->step_data ?? [];
        $values   = [];
        foreach ($this->modalFields as $i => $field) {
            $values[$field['label']] = $this->modalValues[$i] ?? '';
        }
        $stepData[(string) $this->modalStepId] = $values;

        $device->update([
            'workflow_step_id' => $this->modalStepId,
            'step_data'        => $stepData,
        ]);
        $this->record = $device->fresh('workflowStep');

        $this->flashMessage   = 'Schritt geändert zu „' . $this->modalStepLabel . '" — Felder gespeichert';
        $this->closeModal();
    }

    public function cancelModal(): void
    {
        $this->closeModal();
    }

    private function closeModal(): void
    {
        $this->showFieldModal = false;
        $this->modalStepId    = null;
        $this->modalStepLabel = null;
        $this->modalFields    = [];
        $this->modalValues    = [];
    }

    /** Returns saved values for a given step id (used in blade) */
    public function getStepValues(int $stepId): array
    {
        $device = $this->getDevice();
        return $device?->step_data[(string) $stepId] ?? [];
    }
}
