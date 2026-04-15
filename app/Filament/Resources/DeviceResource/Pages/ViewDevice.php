<?php

namespace App\Filament\Resources\DeviceResource\Pages;

use App\Filament\Resources\DeviceResource;
use App\Filament\Widgets\DeviceNotesWidget;
use App\Filament\Widgets\DeviceWorkflowProgressWidget;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDevice extends ViewRecord
{
    protected static string $resource = DeviceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            DeviceWorkflowProgressWidget::make(['record' => $this->record]),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            DeviceNotesWidget::make(['record' => $this->record]),
        ];
    }
}
