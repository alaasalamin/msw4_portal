<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Http;

class Addons extends Page
{
    protected string $view = 'filament.pages.addons';

    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-puzzle-piece'; }
    public static function getNavigationGroup(): string|\UnitEnum|null  { return 'Configuration'; }
    public static function getNavigationSort(): ?int                    { return 3; }
    public static function getNavigationLabel(): string                 { return 'Add-ons'; }
    public function getTitle(): string                                  { return 'Add-ons'; }

    public ?array $data = [];

    // null = untested, 'ok' = connected, 'error' = failed
    public ?string $crmStatus  = null;
    public ?string $crmMessage = null;
    public bool    $crmTesting = false;

    public function mount(): void
    {
        $this->form->fill([
            'rsw_url'              => Setting::get('rsw_url'),
            'rsw_secret'           => Setting::get('rsw_secret'),
            'crm_url'              => Setting::get('crm_url'),
            'crm_secret'           => Setting::get('crm_secret'),
            'dhl_billing_email'    => Setting::get('dhl_billing_email'),
            'dhl_billing_password' => Setting::get('dhl_billing_password'),
        ]);
    }

    public function testCrm(): void
    {
        $this->crmTesting = true;
        $this->crmStatus  = null;
        $this->crmMessage = null;

        $url    = rtrim($this->data['crm_url'] ?? Setting::get('crm_url') ?? '', '/');
        $secret = $this->data['crm_secret'] ?? Setting::get('crm_secret') ?? '';

        if (blank($url) || blank($secret)) {
            $this->crmStatus  = 'error';
            $this->crmMessage = 'URL or secret is missing.';
            $this->crmTesting = false;
            return;
        }

        try {
            $response = Http::timeout(5)
                ->withHeader('X-Secret-Key', $secret)
                ->get($url . '/api/ping');

            if ($response->successful() && ($response->json('status') === 'ok')) {
                $this->crmStatus  = 'ok';
                $this->crmMessage = 'Connected to ' . ($response->json('app') ?? 'CRM');
            } else {
                $this->crmStatus  = 'error';
                $this->crmMessage = 'HTTP ' . $response->status() . ': ' . ($response->json('error') ?? 'Unexpected response');
            }
        } catch (\Throwable $e) {
            $this->crmStatus  = 'error';
            $this->crmMessage = $e->getMessage();
        }

        $this->crmTesting = false;
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
                ])->columns(2)
                  ->footerActions([
                      \Filament\Actions\Action::make('test_crm')
                          ->label('Test Connection')
                          ->icon('heroicon-o-signal')
                          ->color('gray')
                          ->action('testCrm'),
                  ])
                  ->footerActionsAlignment(\Filament\Support\Enums\Alignment::Start),

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
                ->causedBy(auth('employee')->user())
                ->withProperties($changed)
                ->log('Updated add-on settings');
        }

        // Reset status when credentials change
        $this->crmStatus  = null;
        $this->crmMessage = null;

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
