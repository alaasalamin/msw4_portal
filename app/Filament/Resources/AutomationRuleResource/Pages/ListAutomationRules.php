<?php

namespace App\Filament\Resources\AutomationRuleResource\Pages;

use App\Filament\Resources\AutomationRuleResource;
use App\Models\AutomationRule;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAutomationRules extends ListRecords
{
    protected static string $resource = AutomationRuleResource::class;

    public function getView(): string
    {
        return 'filament.resources.automation-rules.list';
    }

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
