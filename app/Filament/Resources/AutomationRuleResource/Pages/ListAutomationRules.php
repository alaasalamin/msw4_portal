<?php

namespace App\Filament\Resources\AutomationRuleResource\Pages;

use App\Filament\Resources\AutomationRuleResource;
use App\Models\AutomationRule;
use App\Models\EmailTemplate;
use App\Models\WorkflowPhase;
use App\Models\WorkflowStep;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAutomationRules extends ListRecords
{
    protected static string $resource = AutomationRuleResource::class;

    // Step-click modal state
    public ?int    $selectedStepId       = null;
    public ?array  $selectedStep         = null;
    public array   $stepFields           = [];    // custom_fields being edited
    public string  $newFieldLabel        = '';    // input for adding a new field
    public array   $stepEmailTemplateIds = [];    // email_template_ids being edited
    public ?int    $addTemplateId        = null;  // selected in the add-template dropdown

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

        $this->selectedStepId       = $stepId;
        $this->selectedStep         = [
            'id'    => $step->id,
            'label' => $step->label,
            'phase' => $step->phase?->label,
        ];
        $this->stepFields           = $step->custom_fields ?? [];
        $this->stepEmailTemplateIds = $step->email_template_ids ?? [];
        $this->newFieldLabel        = '';
        $this->addTemplateId        = null;
    }

    public function addField(): void
    {
        $label = trim($this->newFieldLabel);
        if ($label === '') return;
        $this->stepFields[]  = ['label' => $label, 'type' => 'text'];
        $this->newFieldLabel = '';
    }

    public function removeField(int $index): void
    {
        array_splice($this->stepFields, $index, 1);
    }

    public function addEmailTemplate(): void
    {
        if (! $this->addTemplateId) return;
        if (in_array($this->addTemplateId, $this->stepEmailTemplateIds)) return;
        $this->stepEmailTemplateIds[] = $this->addTemplateId;
        $this->addTemplateId = null;
    }

    public function removeEmailTemplate(int $templateId): void
    {
        $this->stepEmailTemplateIds = array_values(
            array_filter($this->stepEmailTemplateIds, fn ($id) => $id !== $templateId)
        );
    }

    public function saveStepFields(): void
    {
        WorkflowStep::findOrFail($this->selectedStepId)
            ->update([
                'custom_fields'      => $this->stepFields ?: null,
                'email_template_ids' => $this->stepEmailTemplateIds ?: null,
            ]);
        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->selectedStepId       = null;
        $this->selectedStep         = null;
        $this->stepFields           = [];
        $this->newFieldLabel        = '';
        $this->stepEmailTemplateIds = [];
        $this->addTemplateId        = null;
    }

    public function getAllEmailTemplates(): \Illuminate\Support\Collection
    {
        return EmailTemplate::orderBy('name')->get();
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
