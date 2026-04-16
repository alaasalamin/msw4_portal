<?php

namespace App\Actions\Automation;

use App\Models\Device;

class UpdateDeviceFieldAction
{
    private const ALLOWED = [
        'estimated_cost',
        'final_cost',
        'priority',
        'completed_at',
        'internal_notes',
    ];

    public function execute(Device $device, array $config, array $context = []): array
    {
        $field = $config['field'] ?? '';

        if (! in_array($field, self::ALLOWED, true)) {
            throw new \RuntimeException("Field '{$field}' is not allowed to be updated by automation.");
        }

        $value = match ($field) {
            'completed_at'   => now(),
            'estimated_cost',
            'final_cost'     => is_numeric($config['value'] ?? null)
                                    ? round((float) $config['value'], 2)
                                    : throw new \RuntimeException("Value must be numeric for {$field}."),
            'priority'       => in_array($config['value'] ?? '', ['low','normal','high','urgent'])
                                    ? $config['value']
                                    : throw new \RuntimeException("Invalid priority value."),
            'internal_notes' => $this->interpolate($config['value'] ?? '', $device),
            default          => $config['value'] ?? null,
        };

        $old = $device->$field;
        $device->update([$field => $value]);

        return ['field' => $field, 'old' => $old, 'new' => $value];
    }

    private function interpolate(string $text, Device $device): string
    {
        return str_replace(
            ['{{ticket}}', '{{brand}}', '{{model}}', '{{customer}}'],
            [$device->ticket_number, $device->brand, $device->model, $device->customer_name ?? ''],
            $text,
        );
    }
}
