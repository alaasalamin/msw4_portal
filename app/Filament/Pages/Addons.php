<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class Addons extends Page
{
    protected string $view = 'filament.pages.addons';

    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-puzzle-piece'; }
    public static function getNavigationGroup(): string|\UnitEnum|null  { return 'Configuration'; }
    public static function getNavigationSort(): ?int                    { return 3; }
    public static function getNavigationLabel(): string                 { return 'Add-ons'; }
    public function getTitle(): string                                  { return 'Add-ons'; }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'rsw_url'          => Setting::get('rsw_url'),
            'rsw_secret'       => Setting::get('rsw_secret'),
            'crm_url'          => Setting::get('crm_url'),
            'crm_secret'       => Setting::get('crm_secret'),
            'dhl_billing_email' => Setting::get('dhl_billing_email'),
            'dhl_billing_password' => Setting::get('dhl_billing_password'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('RSW')->schema([
                    TextInput::make('rsw_url')
                        ->label('RSW URL')
                        ->url()
                        ->placeholder('https://rsw.example.com')
                        ->maxLength(500),
                    TextInput::make('rsw_secret')
                        ->label('RSW Secret')
                        ->password()
                        ->revealable()
                        ->maxLength(500),
                ])->columns(2),

                Section::make('CRM')->schema([
                    TextInput::make('crm_url')
                        ->label('CRM URL')
                        ->url()
                        ->placeholder('https://crm.example.com')
                        ->maxLength(500),
                    TextInput::make('crm_secret')
                        ->label('CRM Secret')
                        ->password()
                        ->revealable()
                        ->maxLength(500),
                ])->columns(2),

                Section::make('DHL Billing')->schema([
                    TextInput::make('dhl_billing_email')
                        ->label('Billing Email')
                        ->email()
                        ->maxLength(255),
                    TextInput::make('dhl_billing_password')
                        ->label('Billing Password')
                        ->password()
                        ->revealable()
                        ->maxLength(500),
                ])->columns(2),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $changed = [];
        foreach ($data as $key => $value) {
            $old = Setting::get($key);
            Setting::set($key, $value);
            if ($old !== $value && !in_array($key, ['rsw_secret', 'crm_secret', 'dhl_billing_password'])) {
                $changed[$key] = $value;
            }
        }

        if (!empty($changed)) {
            activity('addons')
                ->causedBy(auth('admin')->user())
                ->withProperties($changed)
                ->log('Updated add-on settings');
        }

        Notification::make()
            ->title('Add-ons saved')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save')
                ->action('save')
                ->icon('heroicon-o-check')
                ->color('primary'),
        ];
    }
}
