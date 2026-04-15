<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class DeviceAllowance extends Model
{
    protected $fillable = [
        'device_id', 'rule_id', 'token', 'status',
        'customer_email', 'customer_name', 'message',
        'expires_at', 'responded_at',
    ];

    protected $casts = [
        'expires_at'   => 'datetime',
        'responded_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            $model->token ??= (string) Str::uuid();
        });
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function isPending(): bool  { return $this->status === 'pending'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
    public function isDeclined(): bool { return $this->status === 'declined'; }
    public function isExpired(): bool  { return $this->expires_at && $this->expires_at->isPast(); }
}
