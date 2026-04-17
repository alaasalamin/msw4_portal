<?php

namespace App\Livewire\Admin;

use App\Models\Employee;
use Livewire\Component;

class NavPreferences extends Component
{
    public bool $open = false;
    public array $visible = [];

    public const GROUPS = [
        'Operations',
        'Workflow',
        'Content',
        'Blog',
        'User Management',
        'Configuration',
    ];

    public function mount(): void
    {
        $hidden = $this->employee()?->nav_preferences['hidden_groups'] ?? [];
        $this->visible = array_values(array_diff(self::GROUPS, $hidden));
    }

    public function toggle(): void
    {
        $this->open = ! $this->open;
    }

    public function save(): void
    {
        $hidden = array_values(array_diff(self::GROUPS, $this->visible));

        $this->employee()?->update([
            'nav_preferences' => ['hidden_groups' => $hidden],
        ]);

        $this->open = false;
        $this->js('window.location.reload()');
    }

    private function employee(): ?Employee
    {
        return auth('employee')->user();
    }

    public function render()
    {
        return view('livewire.admin.nav-preferences', [
            'allGroups' => self::GROUPS,
        ]);
    }
}
