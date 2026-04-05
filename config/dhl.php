<?php

return [
    'api_key'                => env('DHL_API_KEY'),
    'api_secret'             => env('DHL_API_SECRET'),
    'username'               => env('DHL_USERNAME'),
    'password'               => env('DHL_PASSWORD'),
    'ekp'                    => env('DHL_EKP'),
    'billing_number_domestic'      => env('DHL_BILLING_NUMBER_DOMESTIC'),
    'billing_number_international' => env('DHL_BILLING_NUMBER_INTERNATIONAL'),
    'sandbox'                => env('DHL_SANDBOX', false),
    'base_url'               => env('DHL_SANDBOX', false)
        ? 'https://api-sandbox.dhl.com/parcel/de/shipping/v2'
        : 'https://api-eu.dhl.com/parcel/de/shipping/v2',
    'tracking_url'           => 'https://api-eu.dhl.com/track/shipments',
];
