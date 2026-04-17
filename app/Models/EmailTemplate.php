<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = ['name', 'subject', 'body'];

    /**
     * Replace {{variable}} placeholders with values from a Device.
     */
    public function render(Device $device): array
    {
        $map = [
            'ticket_number'        => $device->ticket_number ?? '',
            'customer_name'        => $device->customer_name ?? '',
            'customer_email'       => $device->customer_email ?? '',
            'customer_phone'       => $device->customer_phone ?? '',
            'brand'                => $device->brand ?? '',
            'model'                => $device->model ?? '',
            'serial_number'        => $device->serial_number ?? '',
            'color'                => $device->color ?? '',
            'storage_box'          => $device->storage_box ?? '',
            'issue_description'    => $device->issue_description ?? '',
            'internal_notes'       => $device->internal_notes ?? '',
            'priority'             => $device->priority ?? '',
            'estimated_cost'       => $device->estimated_cost ?? '',
            'final_cost'           => $device->final_cost ?? '',
            'received_at'          => $device->received_at?->format('d.m.Y') ?? '',
            'estimated_completion' => $device->estimated_completion?->format('d.m.Y') ?? '',
            'completed_at'         => $device->completed_at?->format('d.m.Y') ?? '',
            'workflow_step'        => $device->workflowStep?->label ?? '',
        ];

        $subject = $this->subject;
        $body    = $this->body;

        foreach ($map as $key => $value) {
            $placeholder = '{{' . $key . '}}';
            $subject     = str_replace($placeholder, $value, $subject);
            $body        = str_replace($placeholder, $value, $body);
        }

        return compact('subject', 'body');
    }
}
