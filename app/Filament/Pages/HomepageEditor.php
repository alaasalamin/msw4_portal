<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HomepageEditor extends Page
{
    protected string $view = 'filament.pages.homepage-editor';

    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-paint-brush'; }
    public static function getNavigationGroup(): string|\UnitEnum|null  { return 'Configuration'; }
    public static function getNavigationSort(): ?int                    { return 2; }
    public static function getNavigationLabel(): string                 { return 'Homepage Editor'; }
    public function getTitle(): string                                  { return 'Homepage Editor'; }

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $j = fn(string $key, array $default) =>
            json_decode(Setting::get($key) ?? '', true) ?? $default;

        $this->form->fill([
            // Hero
            'home_hero_badge'        => Setting::get('home_hero_badge',         'Forchheim, Bayern · Zertifizierter Reparaturbetrieb'),
            'home_hero_title'        => Setting::get('home_hero_title',         'Handy Reparatur &amp; <span class="text-orange-400">Datenrettung</span>'),
            'home_hero_subtitle'     => Setting::get('home_hero_subtitle',      'Moon.Repair ist dein professionelles Reparatur- und Datenrettungszentrum in Forchheim. Ob Displayschaden, Akkutausch oder verlorene Daten — wir reparieren schnell, zuverlässig und mit Garantie.'),
            'home_hero_rating'       => Setting::get('home_hero_rating',        '4.7/5'),
            'home_hero_repairs_count'=> Setting::get('home_hero_repairs_count', 'über 13.810 Reparaturen'),
            'home_hero_bullets'      => array_map(
                fn($b) => ['text' => $b],
                $j('home_hero_bullets', [
                    'Diagnose meist noch am selben Tag',
                    '90 Tage Garantie auf alle Reparaturen',
                    'Professionelle Datenrettung bei Wasserschäden & Defekten',
                    'Transparente Preise — keine versteckten Kosten',
                ])
            ),

            // Stats
            'home_stats' => $j('home_stats', [
                ['value' => '13,810', 'label' => 'Devices Repaired'],
                ['value' => '320+',   'label' => 'B2B Partners'],
                ['value' => '98.6%',  'label' => 'Satisfaction Rate'],
                ['value' => '< 24h',  'label' => 'Avg. Turnaround'],
            ]),

            // Services section
            'home_services_label'    => Setting::get('home_services_label',    'What we repair'),
            'home_services_title'    => Setting::get('home_services_title',    'Every device, every problem'),
            'home_services_subtitle' => Setting::get('home_services_subtitle', 'Our certified technicians handle everything from cracked screens to complex motherboard issues.'),
            'home_services_items'    => $j('home_services_items', [
                ['title' => 'Smartphone Repair',  'desc' => 'Screen replacements, battery swaps, charging port fixes, and water damage recovery for all major brands.'],
                ['title' => 'Laptop & PC Repair', 'desc' => 'Keyboard, display, motherboard diagnostics, SSD upgrades, and OS recovery for Windows and macOS.'],
                ['title' => 'Tablet Repair',      'desc' => 'iPad and Android tablet glass, digitizer, and battery replacement with original components.'],
                ['title' => 'Warranty Service',   'desc' => 'Authorised warranty repairs and post-warranty service with certified parts and documented diagnostics.'],
                ['title' => 'Data Recovery',      'desc' => 'Professional data extraction from damaged drives, broken phones, and failed storage media.'],
                ['title' => 'Express Turnaround', 'desc' => 'Same-day diagnosis and priority repair lanes for business customers and urgent individual requests.'],
            ]),

            // How it works
            'home_process_label' => Setting::get('home_process_label', 'The process'),
            'home_process_title' => Setting::get('home_process_title', 'Simple from start to finish'),
            'home_steps'         => $j('home_steps', [
                ['num' => '01', 'title' => 'Submit Your Device', 'desc' => 'Drop off in-store, or your B2B partner ships the device through our portal.'],
                ['num' => '02', 'title' => 'Diagnosis & Quote',  'desc' => 'Our certified technicians diagnose the issue and send you a transparent repair quote.'],
                ['num' => '03', 'title' => 'Repair & Return',    'desc' => 'Approved repairs are completed with genuine parts and shipped back or ready for collection.'],
            ]),

            // Partners
            'home_partners_label'     => Setting::get('home_partners_label',    'B2B Partner Programme'),
            'home_partners_title'     => Setting::get('home_partners_title',    'Scale your repair operations with a trusted partner'),
            'home_partners_subtitle'  => Setting::get('home_partners_subtitle', 'Retail chains, insurers, and telecom operators rely on Moon.Repair to handle high-volume device repairs with predictable SLAs and complete visibility.'),
            'home_partners_cta_email' => Setting::get('home_partners_cta_email', 'partners@moon.repair'),
            'home_partners_benefits'  => array_map(
                fn($b) => ['text' => $b],
                $j('home_partners_benefits', [
                    'Bulk repair submissions via dedicated portal',
                    'Priority SLA with guaranteed turnaround times',
                    'Real-time device tracking per shipment',
                    'Consolidated invoicing and reporting',
                    'Dedicated account manager',
                    'API integration available',
                ])
            ),
            'home_partners_features'  => $j('home_partners_features', [
                ['title' => 'Bulk Submissions', 'desc' => 'Upload device lists via CSV or API integration.'],
                ['title' => 'Live Tracking',    'desc' => 'Real-time status updates per device and shipment.'],
                ['title' => 'SLA Dashboard',    'desc' => 'Monitor repair SLAs and escalate instantly.'],
                ['title' => 'Auto Invoicing',   'desc' => 'Consolidated monthly invoices with full line items.'],
            ]),

            // Testimonials
            'home_testimonials' => $j('home_testimonials', [
                ['quote' => 'Moon.Repair turned our device repair backlog from weeks into days. The partner portal is genuinely excellent.', 'name' => 'Sarah M.',  'role' => 'Operations Lead, TelecomPlus'],
                ['quote' => 'Dropped my phone in the morning, had a diagnosis by noon and it was ready by closing time. Incredible.',        'name' => 'Ahmed K.', 'role' => 'Individual Customer'],
                ['quote' => 'The real-time tracking and consolidated invoicing save us hours every month. Highly recommended.',              'name' => 'Lisa T.',  'role' => 'Procurement Manager, RetailChain AG'],
            ]),

            // Footer
            'home_footer_tagline'        => Setting::get('home_footer_tagline',        'Professional device repair at moon.repair — trusted by individuals and businesses. Certified technicians, genuine parts.'),
            'home_footer_email_hello'    => Setting::get('home_footer_email_hello',    'hello@moon.repair'),
            'home_footer_email_partners' => Setting::get('home_footer_email_partners', 'partners@moon.repair'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([

                // ── Hero ─────────────────────────────────────────────────────────
                Section::make('Hero Section')->schema([
                    TextInput::make('home_hero_badge')
                        ->label('Badge text')
                        ->helperText('Small pill above the headline')
                        ->maxLength(120),
                    Textarea::make('home_hero_title')
                        ->label('Headline (HTML allowed)')
                        ->helperText('Wrap the highlighted word in <span class="text-orange-400">…</span>')
                        ->rows(2),
                    Textarea::make('home_hero_subtitle')
                        ->label('Subtitle paragraph')
                        ->rows(3),
                    TextInput::make('home_hero_rating')
                        ->label('Rating text')
                        ->helperText('e.g. 4.7/5')
                        ->maxLength(20),
                    TextInput::make('home_hero_repairs_count')
                        ->label('Repairs count label')
                        ->helperText('e.g. über 13.810 Reparaturen')
                        ->maxLength(60),
                    Repeater::make('home_hero_bullets')
                        ->label('Bullet points')
                        ->schema([
                            TextInput::make('text')->label('Bullet')->required()->maxLength(120),
                        ])
                        ->addActionLabel('Add bullet')
                        ->collapsible()
                        ->defaultItems(0),
                ])->columns(2)->collapsible(),

                // ── Stats ─────────────────────────────────────────────────────────
                Section::make('Stats Bar')->schema([
                    Repeater::make('home_stats')
                        ->label('Stats (shown in 4-column bar below hero)')
                        ->schema([
                            TextInput::make('value')->label('Value')->required()->maxLength(20),
                            TextInput::make('label')->label('Label')->required()->maxLength(40),
                        ])
                        ->columns(2)
                        ->addActionLabel('Add stat')
                        ->collapsible()
                        ->defaultItems(0),
                ])->collapsible(),

                // ── Services ─────────────────────────────────────────────────────
                Section::make('Services Section')->schema([
                    TextInput::make('home_services_label')
                        ->label('Section label')
                        ->helperText('Small caps text above heading')
                        ->maxLength(60),
                    TextInput::make('home_services_title')
                        ->label('Heading')
                        ->maxLength(100),
                    Textarea::make('home_services_subtitle')
                        ->label('Subheading')
                        ->rows(2),
                    Repeater::make('home_services_items')
                        ->label('Service cards (icon order: Phone, Laptop, Tablet, Shield, Database, Clock)')
                        ->schema([
                            TextInput::make('title')->label('Title')->required()->maxLength(60),
                            Textarea::make('desc')->label('Description')->rows(2)->required()->maxLength(200),
                        ])
                        ->columns(2)
                        ->addActionLabel('Add service card')
                        ->collapsible()
                        ->defaultItems(0),
                ])->columns(2)->collapsible(),

                // ── How it works ──────────────────────────────────────────────────
                Section::make('How It Works Section')->schema([
                    TextInput::make('home_process_label')->label('Section label')->maxLength(60),
                    TextInput::make('home_process_title')->label('Heading')->maxLength(100),
                    Repeater::make('home_steps')
                        ->label('Steps')
                        ->schema([
                            TextInput::make('num')  ->label('Number (e.g. 01)')->required()->maxLength(4),
                            TextInput::make('title')->label('Step title')->required()->maxLength(60),
                            Textarea::make('desc') ->label('Step description')->rows(2)->required()->maxLength(200),
                        ])
                        ->columns(3)
                        ->addActionLabel('Add step')
                        ->collapsible()
                        ->defaultItems(0),
                ])->columns(2)->collapsible(),

                // ── Partners ──────────────────────────────────────────────────────
                Section::make('B2B Partners Section')->schema([
                    TextInput::make('home_partners_label')   ->label('Badge label')->maxLength(60),
                    TextInput::make('home_partners_title')   ->label('Heading')->maxLength(120),
                    Textarea::make('home_partners_subtitle') ->label('Body text')->rows(3),
                    TextInput::make('home_partners_cta_email')->label('Contact Sales email')->email()->maxLength(100),
                    Repeater::make('home_partners_benefits')
                        ->label('Benefit list items')
                        ->schema([
                            TextInput::make('text')->label('Benefit')->required()->maxLength(100),
                        ])
                        ->addActionLabel('Add benefit')
                        ->collapsible()
                        ->defaultItems(0),
                    Repeater::make('home_partners_features')
                        ->label('Feature tiles (2×2 grid)')
                        ->schema([
                            TextInput::make('title')->label('Title')->required()->maxLength(60),
                            Textarea::make('desc') ->label('Description')->rows(2)->required()->maxLength(150),
                        ])
                        ->columns(2)
                        ->addActionLabel('Add feature tile')
                        ->collapsible()
                        ->defaultItems(0),
                ])->columns(2)->collapsible(),

                // ── Testimonials ──────────────────────────────────────────────────
                Section::make('Testimonials')->schema([
                    Repeater::make('home_testimonials')
                        ->label('Testimonial cards')
                        ->schema([
                            Textarea::make('quote')->label('Quote')->rows(3)->required()->maxLength(300),
                            TextInput::make('name')->label('Name')->required()->maxLength(60),
                            TextInput::make('role')->label('Role / Company')->required()->maxLength(80),
                        ])
                        ->columns(3)
                        ->addActionLabel('Add testimonial')
                        ->collapsible()
                        ->defaultItems(0),
                ])->collapsible(),

                // ── Footer ────────────────────────────────────────────────────────
                Section::make('Footer')->schema([
                    Textarea::make('home_footer_tagline')
                        ->label('Tagline paragraph')
                        ->rows(2)
                        ->maxLength(200),
                    TextInput::make('home_footer_email_hello')
                        ->label('General email')
                        ->email()
                        ->maxLength(100),
                    TextInput::make('home_footer_email_partners')
                        ->label('Partners email')
                        ->email()
                        ->maxLength(100),
                ])->columns(3)->collapsible(),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Simple string settings
        $strings = [
            'home_hero_badge', 'home_hero_title', 'home_hero_subtitle',
            'home_hero_rating', 'home_hero_repairs_count',
            'home_services_label', 'home_services_title', 'home_services_subtitle',
            'home_process_label', 'home_process_title',
            'home_partners_label', 'home_partners_title', 'home_partners_subtitle', 'home_partners_cta_email',
            'home_footer_tagline', 'home_footer_email_hello', 'home_footer_email_partners',
        ];

        foreach ($strings as $key) {
            if (isset($data[$key])) {
                Setting::set($key, $data[$key]);
            }
        }

        // Repeater fields stored as flat JSON arrays
        $repeaterFlat = ['home_stats', 'home_services_items', 'home_steps', 'home_partners_features', 'home_testimonials'];
        foreach ($repeaterFlat as $key) {
            if (isset($data[$key])) {
                Setting::set($key, json_encode(array_values($data[$key])));
            }
        }

        // Repeaters that are stored as plain arrays of strings (unwrap 'text' key)
        $repeaterText = ['home_hero_bullets', 'home_partners_benefits'];
        foreach ($repeaterText as $key) {
            if (isset($data[$key])) {
                $values = array_values(array_map(fn($row) => $row['text'] ?? '', $data[$key]));
                Setting::set($key, json_encode($values));
            }
        }

        activity('homepage_editor')
            ->causedBy(auth('admin')->user())
            ->log('Updated homepage content');

        Notification::make()
            ->title('Homepage content saved')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Changes')
                ->action('save')
                ->icon('heroicon-o-check')
                ->color('primary'),
            Action::make('preview')
                ->label('Preview Homepage')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url('/')
                ->openUrlInNewTab()
                ->color('gray'),
        ];
    }
}
