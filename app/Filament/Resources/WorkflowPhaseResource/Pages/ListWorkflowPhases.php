<?php

namespace App\Filament\Resources\WorkflowPhaseResource\Pages;

use App\Filament\Resources\WorkflowPhaseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWorkflowPhases extends ListRecords
{
    protected static string $resource = WorkflowPhaseResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
