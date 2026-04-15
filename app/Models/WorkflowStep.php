<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class WorkflowStep extends Model
{
    protected $fillable = ['phase_id', 'label', 'sort_order', 'custom_fields'];

    protected $casts = ['sort_order' => 'integer', 'custom_fields' => 'array'];

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_workflow_step', 'workflow_step_id', 'employee_id');
    }

    public function phase(): BelongsTo
    {
        return $this->belongsTo(WorkflowPhase::class, 'phase_id');
    }

    public static function ordered(): \Illuminate\Database\Eloquent\Collection
    {
        return static::with('phase')->orderBy('sort_order')->get();
    }
}
