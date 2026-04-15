<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AutomationRule extends Model
{
    protected $fillable = ['name', 'description', 'trigger_type', 'trigger_config', 'is_active', 'sort_order'];

    protected $casts = [
        'trigger_config' => 'array',
        'is_active'      => 'boolean',
        'sort_order'     => 'integer',
    ];

    public function actions(): HasMany
    {
        return $this->hasMany(AutomationAction::class, 'rule_id')->orderBy('sort_order');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AutomationLog::class, 'rule_id');
    }

    public static function triggerLabels(): array
    {
        return [
            'step_changed'       => 'Schritt geändert',
            'customer_approved'  => 'Kunde hat zugestimmt',
            'customer_declined'  => 'Kunde hat abgelehnt',
            'payment_received'   => 'Zahlung erhalten',
        ];
    }
}
