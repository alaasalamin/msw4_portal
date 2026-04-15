<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowPhase extends Model
{
    protected $fillable = ['label', 'sort_order'];

    protected $casts = ['sort_order' => 'integer'];

    public function steps(): HasMany
    {
        return $this->hasMany(WorkflowStep::class, 'phase_id')->orderBy('sort_order');
    }
}
