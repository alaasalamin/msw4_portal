<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutomationLog extends Model
{
    protected $fillable = [
        'rule_id', 'rule_name', 'trigger_type',
        'device_id', 'action_type', 'status', 'payload', 'error',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
