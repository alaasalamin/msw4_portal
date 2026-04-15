<?php

namespace App\Filament\Resources\WorkflowPhaseResource\Pages;

use App\Filament\Resources\WorkflowPhaseResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWorkflowPhase extends EditRecord
{
    protected static string $resource = WorkflowPhaseResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()->requiresConfirmation()];
    }
}
