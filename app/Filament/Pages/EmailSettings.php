<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EmailSettings extends Page
{
    protected string $view = 'filament.pages.email-settings';

    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-envelope'; }
    public static function getNavigationGroup(): string|\UnitEnum|null  { return 'Configuration'; }
    public static function getNavigationSort(): ?int                    { return 3; }
    public static function getNavigationLabel(): string                 { return 'Email Settings'; }
    public function getTitle(): string                                  { return 'Email Settings'; }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'mail_mailer'            => Setting::get('mail_mailer', 'smtp'),
            'mail_host'              => Setting::get('mail_host'),
            'mail_port'              => Setting::get('mail_port', '587'),
            'mail_encryption'        => Setting::get('mail_encryption', 'tls'),
            'mail_username'          => Setting::get('mail_username'),
            'mail_password'          => Setting::get('mail_password'),
            'mail_from_address'      => Setting::get('mail_from_address'),
            'mail_from_name'         => Setting::get('mail_from_name', Setting::get('site_name', config('app.name'))),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Mailer')->schema([
                    Select::make('mail_mailer')
                        ->label('Mail Driver')
                        ->options([
                            'smtp'     => 'SMTP',
                            'mailgun'  => 'Mailgun',
                            'ses'      => 'Amazon SES',
                            'sendmail' => 'Sendmail',
                            'log'      => 'Log (testing)',
                        ])
                        ->required(),
                ])->columns(1),

                Section::make('SMTP Server')->schema([
                    TextInput::make('mail_host')
                        ->label('Host')
                        ->placeholder('smtp.gmail.com')
                        ->maxLength(255),
                    TextInput::make('mail_port')
                        ->label('Port')
                        ->numeric()
                        ->placeholder('587'),
                    Select::make('mail_encryption')
                        ->label('Encryption')
                        ->options([
                            'tls'  => 'TLS',
                            'ssl'  => 'SSL',
                            ''     => 'None',
                        ]),
                    TextInput::make('mail_username')
                        ->label('Username')
                        ->maxLength(255),
                    TextInput::make('mail_password')
                        ->label('Password')
                        ->password()
                        ->revealable()
                        ->maxLength(500),
                ])->columns(2),

                Section::make('Sender')->schema([
                    TextInput::make('mail_from_address')
                        ->label('From Address')
                        ->email()
                        ->placeholder('no-reply@yourdomain.com')
                        ->maxLength(255),
                    TextInput::make('mail_from_name')
                        ->label('From Name')
                        ->placeholder('MSW4 Portal')
                        ->maxLength(255),
                ])->columns(2),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $changed = [];
        foreach ($data as $key => $value) {
            $old = Setting::get($key);
            Setting::set($key, $value ?? '');
            if ($old !== $value && $key !== 'mail_password') {
                $changed[$key] = $value;
            }
        }

        if (!empty($changed)) {
            activity('email_settings')
                ->causedBy(auth('admin')->user())
                ->withProperties($changed)
                ->log('Updated email settings');
        }

        // Apply to running config immediately
        $this->applyMailConfig($data);

        Notification::make()
            ->title('Email settings saved')
            ->success()
            ->send();
    }

    private function applyMailConfig(array $data): void
    {
        config([
            'mail.default'                          => $data['mail_mailer'] ?? 'smtp',
            'mail.mailers.smtp.host'                => $data['mail_host'] ?? '',
            'mail.mailers.smtp.port'                => $data['mail_port'] ?? 587,
            'mail.mailers.smtp.encryption'          => $data['mail_encryption'] ?? 'tls',
            'mail.mailers.smtp.username'            => $data['mail_username'] ?? '',
            'mail.mailers.smtp.password'            => $data['mail_password'] ?? '',
            'mail.from.address'                     => $data['mail_from_address'] ?? '',
            'mail.from.name'                        => $data['mail_from_name'] ?? '',
        ]);
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
