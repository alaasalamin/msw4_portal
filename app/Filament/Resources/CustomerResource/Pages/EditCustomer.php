<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Models\EngineRecord;
use App\Models\ObjectType;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Schema;

class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;

    protected string $view = 'filament.resources.customer-resource.pages.show-customer';

    public function getTitle(): string
    {
        $record = $this->getRecord();
        return '#' . $record->id . ' ' . $record->name;
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

    /**
     * Single dynamic action for any object type tile. The blade passes
     * `type_id` so the modal can resolve which type and which records to show.
     */
    public function viewObjectsAction(): Action
    {
        return Action::make('viewObjects')
            ->modalHeading(function (array $arguments): string {
                return ObjectType::find($arguments['type_id'] ?? null)?->name ?? 'Records';
            })
            ->modalIcon(function (array $arguments): ?string {
                return ObjectType::find($arguments['type_id'] ?? null)?->icon ?: 'heroicon-o-cube';
            })
            ->modalWidth('2xl')
            ->modalContent(function (array $arguments) {
                $type = ObjectType::find($arguments['type_id'] ?? null);
                if (! $type || ! Schema::hasTable($type->engineTable())) return null;

                $records = EngineRecord::forType($type)
                    ->newQuery()
                    ->where('customer_id', $this->getRecord()->id)
                    ->orderByDesc('id')
                    ->get();

                return view('filament.resources.customer-resource.modals.object-records', [
                    'type'    => $type,
                    'records' => $records,
                ]);
            })
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close');
    }
}
