<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObjectRecord extends Model
{
    protected $fillable = [
        'object_type_id',
        'customer_id',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(ObjectType::class, 'object_type_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
