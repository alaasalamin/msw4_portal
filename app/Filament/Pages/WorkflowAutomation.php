<?php

namespace App\Filament\Pages;

use App\Models\WorkflowPhase;
use App\Models\WorkflowStep;
use Filament\Pages\Page;

class WorkflowAutomation extends Page
{
    protected string $view = 'filament.pages.workflow-automation';

    protected static bool $shouldRegisterNavigation = false;

    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-bolt'; }
    public static function getNavigationGroup(): string|\UnitEnum|null  { return 'Workflow'; }
    public static function getNavigationSort(): ?int                    { return 3; }
    public static function getNavigationLabel(): string                 { return 'Automation'; }
    public function getTitle(): string                                  { return 'Automation'; }

    public ?int $selectedStepId = null;
    public ?array $selectedStep = null;

    public function getPhases(): \Illuminate\Database\Eloquent\Collection
    {
        return WorkflowPhase::with([
            'steps' => fn ($q) => $q->orderBy('sort_order'),
            'steps.employees',
        ])
            ->orderBy('sort_order')
            ->get();
    }

    public function selectStep(int $stepId): void
    {
        $step = WorkflowStep::with('phase')->findOrFail($stepId);

        $this->selectedStepId = $stepId;
        $this->selectedStep   = [
            'id'    => $step->id,
            'label' => $step->label,
            'phase' => $step->phase?->label,
        ];
    }

    public function closeModal(): void
    {
        $this->selectedStepId = null;
        $this->selectedStep   = null;
    }
}
