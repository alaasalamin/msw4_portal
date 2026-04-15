<?php

namespace App\Filament\Resources\AutomationRuleResource\Pages;

use App\Filament\Resources\AutomationRuleResource;
use App\Models\AutomationRule;
use App\Models\WorkflowPhase;
use App\Models\WorkflowStep;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAutomationRules extends ListRecords
{
    protected static string $resource = AutomationRuleResource::class;

    // Step-click modal state
    public ?int   $selectedStepId = null;
    public ?array $selectedStep   = null;

    public function getView(): string
    {
        return 'filament.resources.automation-rules.list';
    }

    // ── Workflow diagram ─────────────────────────────────────────────────────

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

    // ── Automation rules ─────────────────────────────────────────────────────

    public function toggleRule(int $id): void
    {
        $rule = AutomationRule::findOrFail($id);
        $rule->update(['is_active' => ! $rule->is_active]);
    }

    public function deleteRule(int $id): void
    {
        AutomationRule::findOrFail($id)->delete();
    }

    public function getAutomationRules(): \Illuminate\Database\Eloquent\Collection
    {
        return AutomationRule::with(['actions', 'logs'])->orderBy('sort_order')->get();
    }

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Automation erstellen')];
    }
}
