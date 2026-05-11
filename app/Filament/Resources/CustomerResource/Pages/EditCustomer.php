<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\HtmlString;

class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;

    protected string $view = 'filament.resources.customer-resource.pages.show-customer';

    public function getTitle(): string
    {
        $record = $this->getRecord();
        return '#' . $record->id . ' ' . trim($record->first_name . ' ' . $record->last_name);
    }

    public function getHeading(): string
    {
        return $this->getTitle();
    }

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    public function editAction(): Action
    {
        return Action::make('edit')
            ->label('Edit Customer')
            ->icon('heroicon-o-pencil-square')
            ->color('primary')
            ->modalHeading('Edit Customer')
            ->modalWidth('3xl')
            ->modalAutofocus(false)
            ->fillForm(function (): array {
                $data = $this->getRecord()->attributesToArray();
                unset($data['password']);
                return $data;
            })
            ->form(CustomerResource::formComponents())
            ->action(function (array $data): void {
                $this->getRecord()->update($data);

                Notification::make()
                    ->title('Customer updated')
                    ->success()
                    ->send();
            });
    }

    public function notesAction(): Action
    {
        return $this->placeholderTileAction('notes', 'Notes', 'heroicon-o-pencil-square');
    }

    public function devicesAction(): Action
    {
        return $this->placeholderTileAction('devices', 'Devices', 'heroicon-o-device-phone-mobile');
    }

    public function insuranceAction(): Action
    {
        return $this->placeholderTileAction('insurance', 'Insurance', 'heroicon-o-shield-check');
    }

    public function invoicesAction(): Action
    {
        return $this->placeholderTileAction('invoices', 'Invoices', 'heroicon-o-document-text');
    }

    protected function placeholderTileAction(string $name, string $heading, string $icon): Action
    {
        return Action::make($name)
            ->modalHeading($heading)
            ->modalIcon($icon)
            ->modalWidth('2xl')
            ->modalContent(new HtmlString(
                '<div style="padding:8px 4px 4px;">' .
                    '<p style="font-size:14px; line-height:1.6; color:rgb(107 114 128); margin:0;">' .
                        e($heading) . ' for this customer will appear here once wired to the data model.' .
                    '</p>' .
                '</div>'
            ))
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close');
    }
}
