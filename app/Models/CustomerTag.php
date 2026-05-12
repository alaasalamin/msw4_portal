<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CustomerTag extends Model
{
    protected $fillable = [
        'name',
        'color',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function customers(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class);
    }
}
