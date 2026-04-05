<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    protected $fillable = [
        'user_id',
        'tracking_number',
        'type',
        'status',
        'sender_name',
        'sender_company',
        'sender_street',
        'sender_house_number',
        'sender_postal_code',
        'sender_city',
        'sender_country',
        'sender_email',
        'sender_phone',
        'recipient_name',
        'recipient_company',
        'recipient_street',
        'recipient_house_number',
        'recipient_postal_code',
        'recipient_city',
        'recipient_country',
        'recipient_email',
        'recipient_phone',
        'weight_kg',
        'reference',
        'label_url',
        'label_pdf',
        'dhl_response',
    ];

    protected $casts = [
        'dhl_response' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
