<?php

namespace App\Filament\Pages;

use App\Models\WorkflowPhase;
use App\Models\WorkflowStep;
use Filament\Pages\Page;

class WorkflowPhasesAndSteps extends Page
{
    protected string $view = 'filament.pages.workflow-phases-and-steps';

    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-rectangle-stack'; }
    public static function getNavigationGroup(): string|\UnitEnum|null  { return 'Workflow'; }
    public static function getNavigationSort(): ?int                    { return 1; }
    public static function getNavigationLabel(): string                 { return 'Phasen & Schritte'; }
    public function getTitle(): string                                   { return 'Phasen & Schritte'; }

    // ── Phase state ──────────────────────────────────────────────────────────
    public string $newPhaseLabel      = '';
    public int    $newPhaseSortOrder  = 0;

    public ?int   $editPhaseId        = null;
    public string $editPhaseLabel     = '';
    public int    $editPhaseSortOrder = 0;

    // ── Step state ───────────────────────────────────────────────────────────
    public ?int   $addingStepPhaseId  = null;
    public string $newStepLabel       = '';
    public int    $newStepSortOrder   = 0;

    public ?int   $editStepId         = null;
    public string $editStepLabel      = '';
    public int    $editStepSortOrder  = 0;
    public ?int   $editStepPhaseId    = null;

    // ── Data ─────────────────────────────────────────────────────────────────
    public function getPhases(): \Illuminate\Database\Eloquent\Collection
    {
        return WorkflowPhase::with(['steps' => fn ($q) => $q->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();
    }

    // ── Phase actions ────────────────────────────────────────────────────────
    public function addPhase(): void
    {
        $label = trim($this->newPhaseLabel);
        if ($label === '') return;

        WorkflowPhase::create([
            'label'      => $label,
            'sort_order' => $this->newPhaseSortOrder,
        ]);

        $this->newPhaseLabel     = '';
        $this->newPhaseSortOrder = 0;
    }

    public function startEditPhase(int $id): void
    {
        $phase = WorkflowPhase::findOrFail($id);
        $this->editPhaseId        = $id;
        $this->editPhaseLabel     = $phase->label;
        $this->editPhaseSortOrder = $phase->sort_order;
        $this->editStepId         = null; // close any open step edit
    }

    public function savePhase(): void
    {
        $label = trim($this->editPhaseLabel);
        if ($label === '' || ! $this->editPhaseId) return;

        WorkflowPhase::findOrFail($this->editPhaseId)->update([
            'label'      => $label,
            'sort_order' => $this->editPhaseSortOrder,
        ]);

        $this->editPhaseId = null;
    }

    public function cancelEditPhase(): void
    {
        $this->editPhaseId = null;
    }

    public function deletePhase(int $id): void
    {
        $phase = WorkflowPhase::findOrFail($id);
        if ($phase->steps()->exists()) return; // guard: can't delete with steps
        $phase->delete();
    }

    // ── Step actions ─────────────────────────────────────────────────────────
    public function openAddStep(int $phaseId): void
    {
        $this->addingStepPhaseId = $phaseId;
        $this->newStepLabel      = '';
        $this->newStepSortOrder  = 0;
        $this->editStepId        = null;
        $this->editPhaseId       = null;
    }

    public function addStep(): void
    {
        $label = trim($this->newStepLabel);
        if ($label === '' || ! $this->addingStepPhaseId) return;

        WorkflowStep::create([
            'phase_id'   => $this->addingStepPhaseId,
            'label'      => $label,
            'sort_order' => $this->newStepSortOrder,
        ]);

        $this->addingStepPhaseId = null;
        $this->newStepLabel      = '';
        $this->newStepSortOrder  = 0;
    }

    public function cancelAddStep(): void
    {
        $this->addingStepPhaseId = null;
    }

    public function startEditStep(int $id): void
    {
        $step = WorkflowStep::findOrFail($id);
        $this->editStepId        = $id;
        $this->editStepLabel     = $step->label;
        $this->editStepSortOrder = $step->sort_order;
        $this->editStepPhaseId   = $step->phase_id;
        $this->editPhaseId       = null;
        $this->addingStepPhaseId = null;
    }

    public function saveStep(): void
    {
        $label = trim($this->editStepLabel);
        if ($label === '' || ! $this->editStepId) return;

        WorkflowStep::findOrFail($this->editStepId)->update([
            'label'      => $label,
            'sort_order' => $this->editStepSortOrder,
            'phase_id'   => $this->editStepPhaseId,
        ]);

        $this->editStepId = null;
    }

    public function cancelEditStep(): void
    {
        $this->editStepId = null;
    }

    public function deleteStep(int $id): void
    {
        WorkflowStep::findOrFail($id)->delete();
    }
}
