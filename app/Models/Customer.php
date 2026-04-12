<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Builder;

class Customer extends Authenticatable
{
    protected $table = 'users';

    protected static function booted(): void
    {
        static::addGlobalScope('type', function (Builder $builder) {
            $builder->where('type', 'customer');
        });

        static::creating(function ($model) {
            $model->type = 'customer';
        });
    }
}
