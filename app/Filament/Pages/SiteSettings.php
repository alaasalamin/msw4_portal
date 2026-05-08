<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SiteSettings extends Page
{
    protected string $view = 'filament.pages.site-settings';

    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-cog-6-tooth'; }
    public static function getNavigationGroup(): string|\UnitEnum|null  { return 'Configuration'; }
    public static function getNavigationSort(): ?int                    { return 1; }
    public static function getNavigationLabel(): string                 { return 'Site Settings'; }
    public function getTitle(): string                                  { return 'Site Settings'; }

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'site_name'        => Setting::get('site_name', config('app.name')),
            'site_description' => Setting::get('site_description'),
            'logo'             => Setting::get('logo'),
            'social_facebook'  => Setting::get('social_facebook'),
            'social_instagram' => Setting::get('social_instagram'),
            'social_twitter'   => Setting::get('social_twitter'),
            'social_linkedin'  => Setting::get('social_linkedin'),
            'social_youtube'   => Setting::get('social_youtube'),
            'social_tiktok'    => Setting::get('social_tiktok'),
            'social_whatsapp'  => Setting::get('social_whatsapp'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('General')->schema([
                    TextInput::make('site_name')
                        ->label('Site Name')
                        ->required()
                        ->maxLength(100),
                    Textarea::make('site_description')
                        ->label('Site Description')
                        ->rows(2)
                        ->maxLength(300),
                ])->columns(1),

                Section::make('Branding')->schema([
                    FileUpload::make('logo')
                        ->label('Site Logo')
                        ->image()
                        ->disk('public')
                        ->directory('settings')
                        ->helperText('Used in the header and email templates'),
                ])->columns(1),

                Section::make('Social Media')->schema([
                    TextInput::make('social_facebook') ->label('Facebook URL') ->url()->maxLength(200)->placeholder('https://facebook.com/yourpage'),
                    TextInput::make('social_instagram')->label('Instagram URL')->url()->maxLength(200)->placeholder('https://instagram.com/yourhandle'),
                    TextInput::make('social_twitter')  ->label('X / Twitter URL')->url()->maxLength(200)->placeholder('https://x.com/yourhandle'),
                    TextInput::make('social_linkedin') ->label('LinkedIn URL') ->url()->maxLength(200)->placeholder('https://linkedin.com/company/yourcompany'),
                    TextInput::make('social_youtube')  ->label('YouTube URL')  ->url()->maxLength(200)->placeholder('https://youtube.com/@yourchannel'),
                    TextInput::make('social_tiktok')   ->label('TikTok URL')   ->url()->maxLength(200)->placeholder('https://tiktok.com/@yourhandle'),
                    TextInput::make('social_whatsapp') ->label('WhatsApp URL') ->url()->maxLength(200)->placeholder('https://wa.me/491234567890'),
                ])->columns(2),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $changed = [];
        foreach ($data as $key => $value) {
            if ($value !== null) {
                $normalized = is_array($value) ? $value[0] : $value;
                $old = Setting::get($key);
                Setting::set($key, $normalized);
                if ($old !== $normalized && !in_array($key, ['logo', 'favicon', 'og_image'])) {
                    $changed[$key] = $normalized;
                }
            }
        }

        if (!empty($changed)) {
            activity('site_settings')
                ->causedBy(auth('admin')->user())
                ->withProperties($changed)
                ->log('Updated site settings');
        }

        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->action('save')
                ->icon('heroicon-o-check')
                ->color('primary'),
        ];
    }
}
