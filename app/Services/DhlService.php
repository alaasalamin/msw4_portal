<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class DhlService
{
    private string $baseUrl;
    private string $trackingUrl;
    private string $apiKey;
    private string $apiSecret;
    private string $username;
    private string $password;
    private string $billingDomestic;
    private string $billingInternational;

    public function __construct()
    {
        $this->baseUrl              = config('dhl.base_url');
        $this->trackingUrl          = config('dhl.tracking_url');
        $this->apiKey               = config('dhl.api_key');
        $this->apiSecret            = config('dhl.api_secret');
        $this->username             = config('dhl.username');
        $this->password             = config('dhl.password');
        $this->billingDomestic      = config('dhl.billing_number_domestic');
        $this->billingInternational = config('dhl.billing_number_international');
    }

    public function createShipment(array $data): array
    {
        $isDomestic     = ($data['type'] ?? 'domestic') === 'domestic';
        $billingNumber  = $isDomestic ? $this->billingDomestic : $this->billingInternational;
        $product        = $isDomestic ? 'V01PAK' : 'V53WPAK';

        $shipDate = now()->isWeekend()
            ? now()->next('Monday')->format('Y-m-d')
            : now()->format('Y-m-d');

        $payload = [
            'profile' => 'STANDARD_GRUPPENPROFIL',
            'shipments' => [
                [
                    'product'       => $product,
                    'billingNumber' => $billingNumber,
                    'refNo'         => $data['reference'] ?? null,
                    'shipDate'      => $shipDate,
                    'shipper'       => [
                        'name1'       => $data['sender_name'],
                        'name2'       => $data['sender_company'] ?? null,
                        'addressStreet'      => $data['sender_street'],
                        'addressHouse'       => $data['sender_house_number'],
                        'postalCode'  => $data['sender_postal_code'],
                        'city'        => $data['sender_city'],
                        'country'     => $data['sender_country'] ?? 'DEU',
                        'email'       => $data['sender_email'] ?? null,
                        'phone'       => $data['sender_phone'] ?? null,
                    ],
                    'consignee' => [
                        'name1'       => $data['recipient_name'],
                        'name2'       => $data['recipient_company'] ?? null,
                        'addressStreet'      => $data['recipient_street'],
                        'addressHouse'       => $data['recipient_house_number'],
                        'postalCode'  => $data['recipient_postal_code'],
                        'city'        => $data['recipient_city'],
                        'country'     => $data['recipient_country'] ?? 'DEU',
                        'email'       => $data['recipient_email'] ?? null,
                        'phone'       => $data['recipient_phone'] ?? null,
                    ],
                    'details' => [
                        'weight' => [
                            'uom'   => 'kg',
                            'value' => (float) $data['weight_kg'],
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->shippingClient()
            ->post($this->baseUrl . '/orders?includeDocs=URL', $payload);

        return $this->parseShipmentResponse($response);
    }

    public function getLabel(string $trackingNumber): ?string
    {
        $response = $this->shippingClient()
            ->get($this->baseUrl . '/orders', [
                'shipmentTrackingNumber' => $trackingNumber,
                'includeDocs'            => 'URL',
            ]);

        if ($response->successful()) {
            $items = $response->json('items', []);
            return $items[0]['label']['url'] ?? null;
        }

        return null;
    }

    public function trackShipment(string $trackingNumber): array
    {
        $response = Http::withHeaders([
            'DHL-API-Key' => $this->apiKey,
        ])->get($this->trackingUrl, [
            'trackingNumber' => $trackingNumber,
            'service'        => 'express',
        ]);

        if ($response->successful()) {
            return $response->json('shipments', []);
        }

        throw new \RuntimeException('DHL tracking failed: ' . $response->body());
    }

    private function shippingClient()
    {
        return Http::withBasicAuth($this->username, $this->password)
            ->withHeaders([
                'dhl-api-key'  => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ]);
    }

    private function parseShipmentResponse(Response $response): array
    {
        if (! $response->successful()) {
            throw new \RuntimeException('DHL API error: ' . $response->body());
        }

        $items = $response->json('items', []);

        if (empty($items)) {
            throw new \RuntimeException('DHL returned no shipment items.');
        }

        $item = $items[0];

        return [
            'tracking_number' => $item['shipmentTrackingNumber'] ?? null,
            'label_url'       => $item['label']['url'] ?? null,
            'dhl_response'    => $response->json(),
        ];
    }
}
