<?php

namespace App\Notifications;

use App\Models\Device;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class AutomationNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public function __construct(
        public readonly Device $device,
        public readonly string $message,
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
            'message'       => $this->message,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }
}
