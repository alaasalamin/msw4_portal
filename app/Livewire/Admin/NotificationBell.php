<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;
use Livewire\Attributes\On;
use Livewire\Component;

class NotificationBell extends Component
{
    public int $unreadCount = 0;
    public bool $open = false;
    public array $notifications = [];

    private function getUser(): ?User
    {
        $admin = auth('admin')->user();
        return $admin ? User::find($admin->id) : null;
    }

    private function loadNotifications(User $user): array
    {
        return $user->notifications()
            ->where(fn ($q) => $q
                ->whereNull('read_at')                          // unread: always show
                ->orWhere('created_at', '>=', now()->subDay())  // read but recent (< 1 day): show
            )
            ->latest()
            ->limit(20)
            ->get()
            ->map(fn (DatabaseNotification $n) => [
                'id'        => $n->id,
                'message'   => $n->data['message'] ?? '',
                'device_id' => $n->data['device_id'] ?? null,
                'read'      => ! is_null($n->read_at),
                'time'      => $n->created_at->diffForHumans(),
            ])
            ->toArray();
    }

    public function mount(): void
    {
        $user = $this->getUser();
        if ($user) {
            $this->unreadCount    = $user->unreadNotifications()->count();
            $this->notifications  = $this->loadNotifications($user);
        }
    }

    #[On('notificationReceived')]
    public function onNotificationReceived(array $notification = []): void
    {
        $this->unreadCount++;

        array_unshift($this->notifications, [
            'id'        => $notification['id'] ?? uniqid(),
            'message'   => $notification['message'] ?? '',
            'device_id' => $notification['device_id'] ?? null,
            'read'      => false,
            'time'      => 'Gerade eben',
        ]);

        $this->notifications = array_slice($this->notifications, 0, 20);
    }

    public function toggle(): void
    {
        $this->open = ! $this->open;

        if ($this->open) {
            $user = $this->getUser();
            if ($user) {
                $this->notifications = $this->loadNotifications($user);
            }
        }
    }

    public function openNotification(string $notificationId): void
    {
        $user = $this->getUser();
        if (! $user) return;

        $notification = $user->notifications()->find($notificationId);
        if (! $notification) return;

        $deviceId = $notification->data['device_id'] ?? null;

        // Mark as read
        $notification->markAsRead();

        // Update local state
        $this->notifications = array_map(function ($n) use ($notificationId) {
            if ($n['id'] === $notificationId) {
                return array_merge($n, ['read' => true]);
            }
            return $n;
        }, $this->notifications);

        $this->unreadCount = max(0, $this->unreadCount - 1);
        $this->open = false;

        if ($deviceId) {
            $this->redirect('/admin/devices/' . $deviceId);
        }
    }

    public function markAllRead(): void
    {
        $user = $this->getUser();
        if ($user) {
            $user->unreadNotifications()->update(['read_at' => now()]);
        }

        $this->unreadCount    = 0;
        $this->notifications  = array_map(fn ($n) => array_merge($n, ['read' => true]), $this->notifications);
    }

    public function render()
    {
        return view('livewire.admin.notification-bell');
    }
}
