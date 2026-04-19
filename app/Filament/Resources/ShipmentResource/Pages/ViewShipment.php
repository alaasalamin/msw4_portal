<?php

namespace App\Filament\Resources\ShipmentResource\Pages;

use App\Filament\Resources\ShipmentResource;
use App\Services\DhlService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewShipment extends ViewRecord
{
    protected static string $resource = ShipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_label')
                ->label('Download Label')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->url(fn () => $this->record->label_url)
                ->openUrlInNewTab()
                ->visible(fn () => filled($this->record->label_url)),

            Action::make('track')
                ->label('Refresh Tracking')
                ->icon('heroicon-o-map-pin')
                ->color('info')
                ->visible(fn () => filled($this->record->tracking_number))
                ->action(function () {
                    try {
                        $tracking = app(DhlService::class)->trackShipment($this->record->tracking_number);

                        if (! empty($tracking[0]['status']['description'])) {
                            $this->record->update(['status' => $tracking[0]['status']['description']]);
                            $this->refreshFormData(['status']);
                        }

                        Notification::make()->title('Tracking updated')->success()->send();
                    } catch (\RuntimeException $e) {
                        Notification::make()->title('Tracking Error')->body($e->getMessage())->danger()->send();
                    }
                }),

            EditAction::make(),
        ];
    }
}
