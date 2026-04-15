<?php

namespace App\Filament\Widgets;

use App\Models\Admin;
use App\Models\Device;
use App\Models\DeviceNote;
use App\Models\DevicePhotoToken;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;

class DeviceNotesWidget extends Widget
{
    protected string $view = 'filament.widgets.device-notes-widget';

    protected int|string|array $columnSpan = 'full';

    public ?Model $record = null;

    // Note compose state
    public string $content   = '';
    public bool   $is_public = false;

    // Photo modal state
    public bool   $showPhotoModal = false;
    public string $photoUploadUrl = '';

    public string $flashMessage = '';

    protected function getDevice(): ?Device
    {
        return $this->record instanceof Device ? $this->record : null;
    }

    public function getNotes(): \Illuminate\Database\Eloquent\Collection
    {
        $device = $this->getDevice();
        if (! $device) {
            return DeviceNote::whereNull('id')->get(); // typed empty collection
        }

        return DeviceNote::where('device_id', $device->id)
            ->latest()
            ->get();
    }

    public function postNote(): void
    {
        $this->validate([
            'content' => ['required', 'string', 'min:1', 'max:5000'],
        ]);

        $device = $this->getDevice();
        if (! $device) {
            return;
        }

        $admin = auth('admin')->user();

        DeviceNote::create([
            'device_id'       => $device->id,
            'author_name'     => $admin->name,
            'author_role'     => 'admin',
            'authorable_type' => Admin::class,
            'authorable_id'   => $admin->id,
            'content'         => trim($this->content),
            'type'            => 'text',
            'is_public'       => $this->is_public,
        ]);

        $this->content      = '';
        $this->is_public    = false;
        $this->flashMessage = 'Notiz gespeichert.';
    }

    public function deleteNote(int $id): void
    {
        $note = DeviceNote::find($id);
        if (! $note) {
            return;
        }

        $admin = auth('admin')->user();
        if ($admin !== null) {
            $note->delete();
            $this->flashMessage = 'Notiz gelöscht.';
        }
    }

    // ── Photo upload ─────────────────────────────────────────────────────────

    public function openPhotoModal(): void
    {
        $device = $this->getDevice();
        if (! $device) {
            return;
        }

        $token = DevicePhotoToken::activeForDevice($device);

        $this->photoUploadUrl = url('/upload/' . $token->token);
        $this->showPhotoModal = true;
    }

    public function closePhotoModal(): void
    {
        $this->showPhotoModal = false;
    }

    public function refreshPhotoLink(): void
    {
        $device = $this->getDevice();
        if (! $device) {
            return;
        }

        // Expire any existing active token and create a fresh one
        DevicePhotoToken::where('device_id', $device->id)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);

        $token = DevicePhotoToken::activeForDevice($device);
        $this->photoUploadUrl = url('/upload/' . $token->token);
    }
}
