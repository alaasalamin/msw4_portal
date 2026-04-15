<?php

namespace App\Actions\Automation;

use App\Models\Device;

class GenerateInvoiceAction
{
    public function execute(Device $device, array $config, array $context = []): array
    {
        // RSW integration — to be implemented when API credentials are configured
        // $rsw = new \App\Integrations\RswClient(config('services.rsw.api_key'));
        // $invoice = $rsw->createInvoice([...]);

        throw new \RuntimeException("RSW integration not yet configured. Set up API credentials in Settings first.");
    }
}
