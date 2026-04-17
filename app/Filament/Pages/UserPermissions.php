<?php

namespace App\Filament\Pages;

use App\Models\UserTypePermission;
use Filament\Actions\Action;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserPermissions extends Page
{
    protected string $view = 'filament.pages.user-permissions';

    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-lock-closed'; }
    public static function getNavigationGroup(): string|\UnitEnum|null  { return 'Configuration'; }
    public static function getNavigationSort(): ?int                    { return 5; }
    public static function getNavigationLabel(): string                 { return 'User Permissions'; }
    public function getTitle(): string                                  { return 'User Permissions'; }

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $stored = UserTypePermission::all()
            ->mapWithKeys(fn ($row) => ["{$row->user_type}__{$row->permission}" => $row->enabled])
            ->all();

        $defaults = [];
        foreach (UserTypePermission::definitions() as $type => $permissions) {
            foreach (array_keys($permissions) as $permission) {
                $key = "{$type}__{$permission}";
                $defaults[$key] = $stored[$key] ?? true;
            }
        }

        $this->form->fill($defaults);
    }

    public function form(Schema $schema): Schema
    {
        $definitions = UserTypePermission::definitions();
        $sections    = [];

        $typeLabels = [
            'customer' => 'Customer',
            'employee' => 'Employee',
            'partner'  => 'Partner',
        ];

        $typeDescriptions = [
            'customer' => 'Controls what registered customers can access in the customer portal.',
            'employee' => 'Controls what employees can do on the repair management board.',
            'partner'  => 'Controls what partner accounts can access in the partner portal.',
        ];

        foreach ($definitions as $type => $permissions) {
            $toggles = [];
            foreach ($permissions as $permission => $label) {
                $toggles[] = Toggle::make("{$type}__{$permission}")
                    ->label($label)
                    ->onColor('success')
                    ->offColor('danger')
                    ->inline(false)
                    ->columnSpan(1);
            }

            $sections[] = Section::make($typeLabels[$type] ?? ucfirst($type))
                ->description($typeDescriptions[$type] ?? '')
                ->schema($toggles)
                ->columns(2);
        }

        return $schema
            ->statePath('data')
            ->components($sections);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $changed = [];

        foreach ($data as $key => $enabled) {
            [$type, $permission] = explode('__', $key, 2);

            $row = UserTypePermission::firstOrNew(
                ['user_type' => $type, 'permission' => $permission]
            );

            $old = $row->exists ? $row->enabled : true;

            $row->enabled = (bool) $enabled;
            $row->save();

            if ((bool) $old !== (bool) $enabled) {
                $label = UserTypePermission::definitions()[$type][$permission] ?? $permission;
                $changed[] = ($enabled ? '✅ Enabled' : '🚫 Disabled')
                    . " — [{$type}] {$label}";
            }
        }

        UserTypePermission::clearCache();

        if (!empty($changed)) {
            activity('user_permissions')
                ->causedBy(auth('admin')->user())
                ->withProperties(['changes' => $changed])
                ->log('Updated user type permissions');
        }

        Notification::make()
            ->title('Permissions saved')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Permissions')
                ->action('save')
                ->icon('heroicon-o-check')
                ->color('primary'),
        ];
    }
}
