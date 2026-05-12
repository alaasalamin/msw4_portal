<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $table = 'customers';

    protected $fillable = [
        'first_name',
        'last_name',
        'company_name',
        'customer_group_id',
        'email',
        'phone',
        'address_street',
        'address_building_number',
        'address_city',
        'address_zip_code',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function getNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(CustomerGroup::class, 'customer_group_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(CustomerTag::class);
    }
}
