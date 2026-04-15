<?php

namespace App\Filament\Widgets;

use App\Models\WorkflowStep;
use Filament\Widgets\Widget;

class WorkflowProgressWidget extends Widget
{
    protected string $view = 'filament.widgets.workflow-progress-widget';

    protected int|string|array $columnSpan = 'full';

    public function getPhases(): \Illuminate\Database\Eloquent\Collection
    {
        return \App\Models\WorkflowPhase::with(['steps' => fn ($q) => $q->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();
    }
}
