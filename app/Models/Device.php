<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Device extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['workflow_step_id', 'priority', 'ticket_number', 'customer_name', 'brand', 'model'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn (string $eventName) => "Device {$eventName}");
    }

    protected $fillable = [
        'ticket_number',
        'technician_id',
        'coordinator_id',
        'workflow_step_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'brand',
        'model',
        'serial_number',
        'color',
        'issue_description',
        'internal_notes',
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

    public function workflowStep(): BelongsTo
    {
        return $this->belongsTo(WorkflowStep::class, 'workflow_step_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(DeviceNote::class)->latest();
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
