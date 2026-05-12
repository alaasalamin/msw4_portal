<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ObjectType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'attributes',
        'sort_order',
    ];

    protected $casts = [
        'attributes' => 'array',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $type) {
            if (blank($type->slug) && filled($type->name)) {
                $type->slug = Str::slug($type->name);
            }
        });
    }

    public function records(): HasMany
    {
        return $this->hasMany(ObjectRecord::class);
    }
}
