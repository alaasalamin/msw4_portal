<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Device extends Model
{
    protected $fillable = [
        'ticket_number',
        'technician_id',
        'coordinator_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'brand',
        'model',
        'serial_number',
        'color',
        'issue_description',
        'internal_notes',
        'status',
        'priority',
        'estimated_cost',
        'final_cost',
        'received_at',
        'estimated_completion',
        'completed_at',
    ];

    protected $casts = [
        'received_at'          => 'datetime',
        'estimated_completion' => 'datetime',
        'completed_at'         => 'datetime',
        'estimated_cost'       => 'decimal:2',
        'final_cost'           => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Device $device) {
            $device->ticket_number = static::generateTicketNumber();
        });
    }

    private static function generateTicketNumber(): string
    {
        $year  = now()->format('Y');
        $last  = static::whereYear('created_at', $year)->max('id') ?? 0;
        return 'REP-' . $year . '-' . str_pad($last + 1, 4, '0', STR_PAD_LEFT);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function coordinator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coordinator_id');
    }

    public function getDaysInShopAttribute(): int
    {
        return (int) $this->received_at->diffInDays(now());
    }

    public function getAgingLevelAttribute(): string
    {
        $days = $this->days_in_shop;
        if ($days >= 7) return 'critical';
        if ($days >= 3) return 'warning';
        return 'ok';
    }
}
