<?php

namespace App\Models;

use App\Models\CustomForm;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CustomPage extends Model
{
    protected $fillable = ['name', 'slug', 'icon', 'color', 'description', 'sort_order', 'form_id', 'workflow_step_ids'];

    protected $casts = [
        'sort_order'        => 'integer',
        'workflow_step_ids' => 'array',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $page) {
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->name);
            }
        });
    }

    public function entries(): HasMany
    {
        return $this->hasMany(CustomPageEntry::class);
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(CustomForm::class, 'form_id');
    }

    public function activeEntries(): HasMany
    {
        return $this->hasMany(CustomPageEntry::class)->whereNull('resolved_at');
    }
}
