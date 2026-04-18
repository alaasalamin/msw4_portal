<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CustomForm extends Model
{
    protected $fillable = ['name', 'slug', 'crm_key', 'crm_sync', 'description', 'success_message', 'redirect_url', 'preset_replies'];

    protected $casts = [
        'preset_replies' => 'array',
        'crm_sync'       => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $form) {
            if (empty($form->slug)) {
                $form->slug = Str::slug($form->name);
            }
        });
    }

    public function fields(): HasMany
    {
        return $this->hasMany(CustomFormField::class, 'form_id')->orderBy('sort_order');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class, 'form_id');
    }
}
