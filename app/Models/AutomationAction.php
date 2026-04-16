<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutomationAction extends Model
{
    protected $fillable = ['rule_id', 'action_type', 'action_config', 'sort_order'];

    protected $casts = [
        'action_config' => 'array',
        'sort_order'    => 'integer',
    ];

    public function rule(): BelongsTo
    {
        return $this->belongsTo(AutomationRule::class, 'rule_id');
    }

    public static function actionLabels(): array
    {
        return [
            'send_allowance'      => 'Kundenfreigabe anfordern',
            'notify_employee'     => 'Mitarbeiter benachrichtigen',
            'send_email'          => 'E-Mail senden',
            'send_delayed_email'  => '⏱ Verzögerte E-Mail senden',
            'change_step'         => 'Schritt wechseln',
            'update_device_field' => 'Gerätefeld aktualisieren',
            'generate_invoice'    => 'Rechnung generieren (RSW)',
        ];
    }
}
