<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomPageEntry extends Model
{
    protected $fillable = ['custom_page_id', 'device_id', 'automation_rule_id', 'notes', 'resolved_at'];

    protected $casts = ['resolved_at' => 'datetime'];

    public function page(): BelongsTo
    {
        return $this->belongsTo(CustomPage::class, 'custom_page_id');
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(AutomationRule::class, 'automation_rule_id');
    }

    public function scopeActive($query)
    {
        return $query->whereNull('resolved_at');
    }
}
