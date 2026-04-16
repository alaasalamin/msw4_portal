<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomFormField extends Model
{
    protected $fillable = ['form_id', 'label', 'type', 'placeholder', 'is_required', 'options', 'sort_order'];

    protected $casts = [
        'is_required' => 'boolean',
        'options'     => 'array',
        'sort_order'  => 'integer',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(CustomForm::class, 'form_id');
    }
}
