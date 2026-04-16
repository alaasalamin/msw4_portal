<?php

namespace App\Actions\Automation;

use App\Models\CustomPage;
use App\Models\CustomPageEntry;
use App\Models\Device;

class AddToPageAction
{
    public function execute(Device $device, array $config, array $context = []): void
    {
        $pageId = $config['page_id'] ?? null;
        if (! $pageId) return;

        $page = CustomPage::find($pageId);
        if (! $page) return;

        // Interpolate notes template
        $notes = $config['notes'] ?? '';
        if ($notes) {
            $notes = str_replace(
                ['{{ticket}}', '{{brand}}', '{{model}}', '{{customer}}'],
                [$device->ticket_number, $device->brand, $device->model, $device->customer_name],
                $notes
            );
        }

        $ruleId = $context['rule_id'] ?? null;

        // Avoid duplicate active entries for the same device+page
        CustomPageEntry::firstOrCreate(
            [
                'custom_page_id' => $page->id,
                'device_id'      => $device->id,
                'resolved_at'    => null,
            ],
            [
                'automation_rule_id' => $ruleId,
                'notes'              => $notes ?: null,
            ]
        );
    }
}
