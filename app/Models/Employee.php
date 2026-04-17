<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Employee extends Authenticatable implements FilamentUser
{
    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
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

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function workflowSteps(): BelongsToMany
    {
        return $this->belongsToMany(WorkflowStep::class, 'employee_workflow_step', 'employee_id', 'workflow_step_id')
            ->with('phase')
            ->orderBy('workflow_steps.sort_order');
    }

    protected static function booted(): void
    {
        static::addGlobalScope('type', function (Builder $builder) {
            $builder->where('type', 'employee');
        });

        static::creating(function ($model) {
            $model->type = 'employee';
        });
    }
}
