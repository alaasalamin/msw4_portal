<?php

namespace App\Actions\Automation;

use App\Models\Device;
use App\Models\Setting;
use App\Services\DhlService;

class GenerateDhlLabelAction
{
    public function execute(Device $device, array $config, array $context = []): array
    {
        $contact = $device->contact;

        if (! $contact) {
            throw new \RuntimeException("Device {$device->ticket_number} has no customer linked.");
        }

        if (blank($contact->street) || blank($contact->postal_code) || blank($contact->city)) {
            throw new \RuntimeException("Customer address is incomplete for {$contact->name}. Add street, postal code and city.");
        }

        $result = app(DhlService::class)->createShipment([
            'type'                   => 'domestic',
            'sender_name'            => Setting::get('company_owner_name'),
            'sender_company'         => Setting::get('company_name'),
            'sender_email'           => Setting::get('company_email'),
            'sender_phone'           => Setting::get('company_phone'),
            'sender_street'          => Setting::get('company_street'),
            'sender_house_number'    => Setting::get('company_house_number'),
            'sender_postal_code'     => Setting::get('company_postal_code'),
            'sender_city'            => Setting::get('company_city'),
            'sender_country'         => 'DEU',
            'recipient_name'         => $contact->name,
            'recipient_street'       => $contact->street,
            'recipient_house_number' => $contact->house_number,
            'recipient_postal_code'  => $contact->postal_code,
            'recipient_city'         => $contact->city,
            'recipient_country'      => 'DEU',
            'recipient_email'        => $contact->email,
            'recipient_phone'        => $contact->phone,
            'weight_kg'              => 0.2,
            'reference'              => $device->ticket_number,
        ]);

        $device->update([
            'dhl_tracking_number' => $result['tracking_number'],
            'dhl_label_url'       => $result['label_url'],
        ]);


        return [
            'tracking_number' => $result['tracking_number'],
            'label_url'       => $result['label_url'],
            'recipient'       => $contact->name,
        ];
    }
}
