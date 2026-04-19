<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'street',
        'house_number',
        'postal_code',
        'city',
    ];

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }
}
