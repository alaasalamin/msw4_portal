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
            'seo_title'        => Setting::get('seo_title'),
            'seo_description'  => Setting::get('seo_description'),
            'seo_keywords'     => Setting::get('seo_keywords'),
            'og_title'         => Setting::get('og_title'),
            'og_description'   => Setting::get('og_description'),
            'og_image'         => Setting::get('og_image'),
            'logo'             => Setting::get('logo'),
            'favicon'          => Setting::get('favicon'),
            'google_analytics' => Setting::get('google_analytics'),
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

                Section::make('SEO')->schema([
                    TextInput::make('seo_title')
                        ->label('Meta Title')
                        ->helperText('Shown in browser tab and Google results. Keep under 60 chars.')
                        ->maxLength(60),
                    Textarea::make('seo_description')
                        ->label('Meta Description')
                        ->helperText('Shown under the link in Google results. Keep under 160 chars.')
                        ->rows(3)
                        ->maxLength(160),
                    TextInput::make('seo_keywords')
                        ->label('Keywords')
                        ->helperText('Comma-separated. e.g. repair, phone, laptop')
                        ->maxLength(255),
                ])->columns(1),

                Section::make('Open Graph (Social Sharing)')->schema([
                    TextInput::make('og_title')
                        ->label('OG Title')
                        ->helperText('Title shown when sharing on Facebook, WhatsApp, etc.')
                        ->maxLength(100),
                    Textarea::make('og_description')
                        ->label('OG Description')
                        ->rows(2)
                        ->maxLength(200),
                    FileUpload::make('og_image')
                        ->label('OG Image')
                        ->image()
                        ->disk('public')
                        ->directory('settings')
                        ->helperText('Recommended: 1200×630px'),
                ])->columns(1),

                Section::make('Branding')->schema([
                    FileUpload::make('logo')
                        ->label('Site Logo')
                        ->image()
                        ->disk('public')
                        ->directory('settings')
                        ->helperText('Used in the header and email templates'),
                    FileUpload::make('favicon')
                        ->label('Favicon')
                        ->image()
                        ->acceptedFileTypes(['image/png', 'image/x-icon', 'image/vnd.microsoft.icon', 'image/svg+xml'])
                        ->disk('public')
                        ->directory('settings')
                        ->helperText('PNG, ICO or SVG — shown in browser tab (32×32 recommended)'),
                ])->columns(2),

                Section::make('Analytics')->schema([
                    TextInput::make('google_analytics')
                        ->label('Google Analytics ID')
                        ->helperText('e.g. G-XXXXXXXXXX')
                        ->maxLength(50),
                ])->columns(1),
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
