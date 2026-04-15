<?php

namespace App\Notifications;

use App\Models\Device;
use App\Models\WorkflowStep;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class DeviceStepChanged extends Notification implements ShouldBroadcast
{
    use Queueable;

    public function __construct(
        public readonly Device       $device,
        public readonly WorkflowStep $step,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'device_id'     => $this->device->id,
            'ticket_number' => $this->device->ticket_number,
            'brand'         => $this->device->brand,
            'model'         => $this->device->model,
            'step_id'       => $this->step->id,
            'step_label'    => $this->step->label,
            'message'       => "Gerät {$this->device->ticket_number} ({$this->device->brand} {$this->device->model}) ist jetzt auf Schritt: {$this->step->label}",
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }
}
