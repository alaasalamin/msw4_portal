<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class DevicePhotoToken extends Model
{
    protected $fillable = ['device_id', 'token', 'used_at'];

    protected $casts = [
        'used_at' => 'datetime',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function isUsed(): bool
    {
        return $this->used_at !== null;
    }

    /** Get or create the active (unused) token for a device. */
    public static function activeForDevice(Device $device): static
    {
        $existing = static::where('device_id', $device->id)
            ->whereNull('used_at')
            ->latest()
            ->first();

        if ($existing) {
            return $existing;
        }

        return static::create([
            'device_id' => $device->id,
            'token'     => Str::random(48),
        ]);
    }

    /** Mark as used and immediately generate a fresh token for the device. */
    public function consume(): static
    {
        $this->update(['used_at' => now()]);

        return static::create([
            'device_id' => $this->device_id,
            'token'     => Str::random(48),
        ]);
    }
}
