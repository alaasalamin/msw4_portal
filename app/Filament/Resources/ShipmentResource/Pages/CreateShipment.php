<?php

namespace App\Filament\Resources\ShipmentResource\Pages;

use App\Filament\Resources\ShipmentResource;
use App\Models\Setting;
use App\Services\DhlService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateShipment extends CreateRecord
{
    protected static string $resource = ShipmentResource::class;

    public function mount(): void
    {
        parent::mount();

        $this->form->fill([
            'sender_name'         => Setting::get('company_owner_name'),
            'sender_company'      => Setting::get('company_name'),
            'sender_email'        => Setting::get('company_email'),
            'sender_phone'        => Setting::get('company_phone'),
            'sender_street'       => Setting::get('company_street'),
            'sender_house_number' => Setting::get('company_house_number'),
            'sender_postal_code'  => Setting::get('company_postal_code'),
            'sender_city'         => Setting::get('company_city'),
            'sender_country'      => Setting::get('company_country'),
        ]);
    }

    protected function handleRecordCreation(array $data): Model
    {
        try {
            $dhlResult = app(DhlService::class)->createShipment($data);

            return static::getModel()::create([
                ...$data,
                'tracking_number' => $dhlResult['tracking_number'],
                'label_url'       => $dhlResult['label_url'],
                'dhl_response'    => $dhlResult['dhl_response'],
                'status'          => 'label_created',
            ]);
        } catch (\RuntimeException $e) {
            Notification::make()
                ->title('DHL Error')
                ->body($e->getMessage())
                ->danger()
                ->persistent()
                ->send();

            $this->halt();
        }
    }
}
