<?php

namespace App\Filament\Pages;

use App\Models\Employee;
use App\Models\WorkflowPhase;
use Filament\Pages\Page;

class WorkflowEmployees extends Page
{
    protected string $view = 'filament.pages.workflow-employees';

    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-users'; }
    public static function getNavigationGroup(): string|\UnitEnum|null  { return 'Workflow'; }
    public static function getNavigationSort(): ?int                    { return 2; }
    public static function getNavigationLabel(): string                 { return 'Mitarbeiter'; }
    public function getTitle(): string                                  { return 'Mitarbeiter & Zuständigkeiten'; }

    // ── Modal state ──────────────────────────────────────────────────────────
    public ?int    $editingEmployeeId   = null;
    public ?string $editingEmployeeName = null;
    public array   $assignedStepIds     = [];

    // ── Data loaders ─────────────────────────────────────────────────────────

    public function getEmployees(): \Illuminate\Database\Eloquent\Collection
    {
        return Employee::with(['workflowSteps.phase'])
            ->orderBy('name')
            ->get();
    }

    public function getPhases(): \Illuminate\Database\Eloquent\Collection
    {
        return WorkflowPhase::with(['steps' => fn ($q) => $q->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();
    }

    // ── Actions ──────────────────────────────────────────────────────────────

    public function openEdit(int $employeeId): void
    {
        $employee = Employee::with('workflowSteps')->findOrFail($employeeId);

        $this->editingEmployeeId   = $employeeId;
        $this->editingEmployeeName = $employee->name;
        $this->assignedStepIds     = $employee->workflowSteps->pluck('id')->map(fn ($id) => (string) $id)->toArray();
    }

    public function saveAssignments(): void
    {
        $employee = Employee::findOrFail($this->editingEmployeeId);
        $employee->workflowSteps()->sync($this->assignedStepIds);
        $this->closeEdit();
    }

    public function closeEdit(): void
    {
        $this->editingEmployeeId   = null;
        $this->editingEmployeeName = null;
        $this->assignedStepIds     = [];
    }
}
