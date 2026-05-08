<?php

namespace App\Filament\Pages\ThemeBuilder;

use App\Models\Setting;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;

class SectionRegistry
{
    /**
     * The set of section types a user can drop on the homepage.
     * Keep this list in sync with the React Sections/* components.
     */
    public static function types(): array
    {
        return [
            'header' => [
                'label' => 'Header',
                'icon'  => 'heroicon-o-bars-3',
                'desc'  => 'Top bar with logo and navigation links.',
            ],
            'hero' => [
                'label' => 'Hero',
                'icon'  => 'heroicon-o-rocket-launch',
                'desc'  => 'Big headline area with a call-to-action button.',
            ],
            'text' => [
                'label' => 'Text Block',
                'icon'  => 'heroicon-o-document-text',
                'desc'  => 'Heading and paragraph for free-form content.',
            ],
            'team' => [
                'label' => 'Team Members',
                'icon'  => 'heroicon-o-user-group',
                'desc'  => 'Grid of team members with photo, name, role, and bio.',
            ],
            'blog_posts' => [
                'label' => 'Blog Posts',
                'icon'  => 'heroicon-o-newspaper',
                'desc'  => 'Latest published posts from your blog with featured images.',
            ],
            'map' => [
                'label' => 'Google Maps',
                'icon'  => 'heroicon-o-map-pin',
                'desc'  => 'Embedded Google map of your company address with directions button.',
            ],
            'reviews' => [
                'label' => 'Customer Reviews',
                'icon'  => 'heroicon-o-star',
                'desc'  => 'Grid of customer reviews with stars, photo, name, and quote.',
            ],
            'form' => [
                'label' => 'Form',
                'icon'  => 'heroicon-o-envelope',
                'desc'  => 'Embed any form from /admin/forms with a heading and paragraph.',
            ],
            'pricing' => [
                'label' => 'Pricing',
                'icon'  => 'heroicon-o-currency-dollar',
                'desc'  => 'Pricing tier cards with features and a call-to-action button.',
            ],
            'faq' => [
                'label' => 'FAQ',
                'icon'  => 'heroicon-o-question-mark-circle',
                'desc'  => 'Frequently asked questions, optionally collapsible.',
            ],
            'cta' => [
                'label' => 'CTA Banner',
                'icon'  => 'heroicon-o-megaphone',
                'desc'  => 'Bold call-to-action strip with one or two buttons.',
            ],
            'stats' => [
                'label' => 'Stats Counter',
                'icon'  => 'heroicon-o-chart-bar',
                'desc'  => 'Row of big numbers that count up when scrolled into view.',
            ],
            'steps' => [
                'label' => 'Steps / Process',
                'icon'  => 'heroicon-o-list-bullet',
                'desc'  => 'Numbered process — "How it works" with steps and connector lines.',
            ],
            'gallery' => [
                'label' => 'Gallery',
                'icon'  => 'heroicon-o-photo',
                'desc'  => 'Image grid with optional captions and a click-to-open lightbox.',
            ],
            'footer' => [
                'label' => 'Footer',
                'icon'  => 'heroicon-o-bars-3-bottom-left',
                'desc'  => 'Bottom bar with tagline and small print.',
            ],
        ];
    }

    public static function exists(string $type): bool
    {
        return array_key_exists($type, static::types());
    }

    /**
     * Visual layout variants per section type. Each variant entry is
     * { key, label, description }. Types not listed here have a single
     * implicit variant and the icon-design picker stays hidden for them.
     */
    public static function variants(string $type): array
    {
        return match ($type) {
            'hero' => [
                ['key' => 'centered',    'label' => 'Centered',         'description' => 'Eyebrow, headline, subtitle and button stacked in the middle.'],
                ['key' => 'split',       'label' => 'Split image',      'description' => 'Heading and button on the left, image on the right.'],
                ['key' => 'bg_image',    'label' => 'Background image', 'description' => 'Full-bleed photo with a dark overlay; content sits on top.'],
                ['key' => 'minimal',     'label' => 'Minimal',          'description' => 'Compact left-aligned — just the headline and one button.'],
                ['key' => 'with_stats',  'label' => 'With stats',       'description' => 'Centered hero plus a small row of three quick stats.'],
            ],
            'text' => [
                ['key' => 'default',     'label' => 'Heading + body',   'description' => 'Plain heading with paragraph below, alignment configurable.'],
                ['key' => 'two_column',  'label' => 'Two columns',      'description' => 'Heading on the left, body on the right — editorial layout.'],
                ['key' => 'callout',     'label' => 'Callout box',      'description' => 'Highlighted box with a colored left border. Good for tips and notes.'],
                ['key' => 'quote',       'label' => 'Pull quote',       'description' => 'Big italic quote with optional attribution.'],
            ],
            'reviews' => [
                ['key' => 'cards',          'label' => 'Cards',           'description' => 'Uniform grid of cards with stars, quote, and reviewer.'],
                ['key' => 'single_featured','label' => 'Single featured', 'description' => 'One spotlighted quote at a time with prev/next controls.'],
                ['key' => 'bubbles',        'label' => 'Bubbles',         'description' => 'Chat-bubble style cards with a small tail under each one.'],
                ['key' => 'list',           'label' => 'List',            'description' => 'Vertical list, hairline dividers, no card backgrounds.'],
            ],
            'pricing' => [
                ['key' => 'cards',   'label' => 'Cards',          'description' => 'Side-by-side plan cards with feature lists.'],
                ['key' => 'table',   'label' => 'Comparison table','description' => 'Plans as columns of a table with name, price, features and CTA rows.'],
                ['key' => 'tabs',    'label' => 'Tabs',           'description' => 'One plan at a time, switch with tabs at the top.'],
                ['key' => 'minimal', 'label' => 'Minimal list',   'description' => 'Compact rows: name + price + button, no feature lists.'],
            ],
            'cta' => [
                ['key' => 'centered', 'label' => 'Centered',     'description' => 'Big centered heading with the buttons stacked underneath.'],
                ['key' => 'split',    'label' => 'Split',        'description' => 'Heading on the left, buttons aligned right.'],
                ['key' => 'gradient', 'label' => 'Gradient',     'description' => 'Vivid gradient background (uses your primary color).'],
                ['key' => 'boxed',    'label' => 'Boxed card',   'description' => 'Tinted section bg with the CTA inside an elevated card.'],
                ['key' => 'inset',    'label' => 'Inset',        'description' => 'Small contained card with rounded corners on a calm bg.'],
            ],
            'header' => [
                ['key' => 'classic',  'label' => 'Classic',  'description' => 'Logo on the left, nav links on the right.'],
                ['key' => 'centered', 'label' => 'Centered', 'description' => 'Logo centered above a thin nav row.'],
                ['key' => 'split',    'label' => 'Split',    'description' => 'Logo left, nav centered, primary CTA button on the right.'],
                ['key' => 'minimal',  'label' => 'Minimal',  'description' => 'Logo only — every link collapses into a hamburger.'],
            ],
            'steps' => [
                ['key' => 'horizontal','label' => 'Horizontal','description' => 'Steps in a row, dashed connector lines between them.'],
                ['key' => 'vertical',  'label' => 'Vertical',  'description' => 'Timeline list — circles on the left, content on the right.'],
                ['key' => 'cards',     'label' => 'Cards',     'description' => 'Elevated cards in a grid, no connector lines.'],
                ['key' => 'arrows',    'label' => 'Arrows',    'description' => 'Horizontal flow with chevron arrows pointing between steps.'],
                ['key' => 'timeline',  'label' => 'Timeline',  'description' => 'Rich vertical timeline with filled circles and a continuous bar.'],
            ],
            default => [],
        };
    }

    public static function hasVariants(string $type): bool
    {
        return count(static::variants($type)) > 1;
    }

    /**
     * Default settings for a freshly-added section. Keep them tasteful so
     * dropping a section produces something usable without further config.
     */
    public static function defaultSettings(string $type): array
    {
        return match ($type) {
            'header' => [
                'variant'    => 'classic',
                'logo'       => Setting::get('company_name') ?: 'Brand',
                'logoImage'  => null,    // relative path within the public disk (overrides text when set)
                'logoHeight' => 48,      // displayed pixel height of the logo image
                'linksMode'  => 'auto',  // 'auto' = pull from published Site Pages, 'custom' = use links[]
                'links'      => [],
                'bg'         => '#111111',
                'fg'         => '#ffffff',
                'linkColor'  => '#cbd5e1',
                'primary'    => '#ea580c', // site-wide brand accent (drives buttons, badges, links elsewhere)
                'sticky'     => true,
                // Used by the 'split' variant
                'ctaText'    => '',
                'ctaHref'    => '#',
            ],
            'hero' => [
                'variant'    => 'centered',
                'image'      => null,    // used by the 'split' and 'bg_image' variants
                'overlay'    => 0.55,    // dark overlay opacity for 'bg_image'
                'eyebrow'    => 'Welcome',
                'headline'   => 'A great big headline goes here',
                'subtitle'   => 'A short paragraph that explains what your product does and why people should care.',
                'buttonText' => 'Get started',
                'buttonHref' => '#',
                'align'      => 'center',
                'bg'         => '#ffffff',
                'fg'         => '#000000',
                'buttonBg'   => '#000000',
                'buttonFg'   => '#ffffff',
                'stats'      => [
                    ['value' => '10k+',  'label' => 'happy users'],
                    ['value' => '4.9★',  'label' => 'avg. rating'],
                    ['value' => '24/7',  'label' => 'support'],
                ],
            ],
            'text' => [
                'variant'      => 'default',
                'heading'      => 'About us',
                'body'         => 'Replace this with your own copy. Use the editor on the left to change colors, alignment, and typography.',
                'attribution'  => '',     // used by the 'quote' variant
                'calloutColor' => 'info', // used by the 'callout' variant
                'align'        => 'left',
                'bg'           => '#ffffff',
                'fg'           => '#0f172a',
            ],
            'gallery' => [
                'heading'        => 'Gallery',
                'subtitle'       => '',
                'images'         => [],
                'columns'        => 3,
                'aspect'         => 'square',
                'gap'            => 12,
                'showCaptions'   => true,
                'enableLightbox' => true,
                'bg'             => '#ffffff',
                'fg'             => '#0f172a',
                'mutedFg'        => '#64748b',
            ],
            'steps' => [
                'variant'        => 'horizontal',
                'heading'        => 'How it works',
                'subtitle'       => 'A simple, transparent process from start to finish.',
                'layout'         => 'horizontal',  // legacy — kept for back-compat with existing rows
                'showConnectors' => true,
                'autoNumber'     => true,
                'steps'          => [
                    ['number' => '01', 'title' => 'Submit your device',  'description' => 'Drop off in-store, or your B2B partner ships the device through our portal.'],
                    ['number' => '02', 'title' => 'Diagnosis & quote',   'description' => 'Our certified technicians diagnose the issue and send you a transparent repair quote.'],
                    ['number' => '03', 'title' => 'Repair & return',     'description' => 'Approved repairs are completed with genuine parts and shipped back or ready for collection.'],
                ],
                'bg'             => '#ffffff',
                'fg'             => '#0f172a',
                'mutedFg'        => '#64748b',
                'numberBg'       => '#0f172a',
                'numberFg'       => '#ffffff',
                'connectorColor' => '#cbd5e1',
            ],
            'stats' => [
                'heading'    => 'Numbers worth bragging about',
                'subtitle'   => '',
                'columns'    => 4,
                'align'      => 'center',
                'animate'    => true,
                'stats'      => [
                    ['value' => '13810', 'prefix' => '', 'suffix' => '',  'label' => 'Devices repaired',   'description' => 'Across the last 24 months.'],
                    ['value' => '320',   'prefix' => '', 'suffix' => '+', 'label' => 'B2B partners',        'description' => 'Retailers, telcos and insurers.'],
                    ['value' => '98.6',  'prefix' => '', 'suffix' => '%', 'label' => 'Satisfaction rate',   'description' => 'Based on post-repair surveys.'],
                    ['value' => '24',    'prefix' => '<', 'suffix' => 'h','label' => 'Avg. turnaround',     'description' => 'For most standard repairs.'],
                ],
                'bg'         => '#ffffff',
                'fg'         => '#0f172a',
                'mutedFg'    => '#64748b',
                'accent'     => '#0284c7',
            ],
            'cta' => [
                'variant'         => 'centered',
                'heading'         => 'Ready to get started?',
                'subtitle'        => 'Get in touch and we will reply within one business day.',
                'primaryText'     => 'Get started',
                'primaryHref'     => '#',
                'secondaryText'   => 'Talk to sales',
                'secondaryHref'   => 'mailto:hello@example.com',
                'align'           => 'center',  // legacy — kept for backwards compat with old saved rows
                'bg'              => '#0f172a',
                'fg'              => '#ffffff',
                'mutedFg'         => '#cbd5e1',
                'primaryBtnBg'    => '#ffffff',
                'primaryBtnFg'    => '#0f172a',
                'secondaryBtnBg'  => 'transparent',
                'secondaryBtnFg'  => '#ffffff',
                // Gradient variant
                'gradientFrom'    => '#0ea5e9',
                'gradientTo'      => '#1e3a8a',
                'gradientAngle'   => 135,
            ],
            'faq' => [
                'heading'      => 'Frequently asked questions',
                'subtitle'     => 'Quick answers to questions you may have.',
                'items'        => [
                    ['question' => 'How long do repairs usually take?', 'answer' => 'Most repairs are completed within 24 hours. We will send you an update as soon as your device is ready.'],
                    ['question' => 'Do you offer a warranty?',          'answer' => 'Yes — every repair comes with a 90-day warranty on both parts and labour.'],
                    ['question' => 'Can I track my repair status?',     'answer' => 'Absolutely. Once your device is checked in, you can follow progress in real time from your portal account.'],
                    ['question' => 'What payment methods do you accept?', 'answer' => 'Card, bank transfer, and (for B2B accounts) consolidated monthly invoicing.'],
                ],
                'layout'       => 'accordion',
                'expandFirst'  => true,
                'columns'      => 1,
                'bg'           => '#ffffff',
                'fg'           => '#0f172a',
                'mutedFg'      => '#475569',
                'cardBg'       => '#f8fafc',
                'borderColor'  => '#e5e7eb',
            ],
            'pricing' => [
                'variant'         => 'cards',
                'heading'         => 'Simple, transparent pricing',
                'subtitle'        => 'Pick a plan that fits where you are today. Upgrade any time.',
                'columns'         => 3,
                'plans'           => [
                    [
                        'name'        => 'Starter',
                        'price'       => '0',
                        'currency'    => '$',
                        'period'      => '/month',
                        'description' => 'For trying things out.',
                        'features'    => "1 project\nCommunity support\nBasic analytics",
                        'buttonText'  => 'Get started',
                        'buttonHref'  => '#',
                        'highlighted' => false,
                        'badge'       => '',
                    ],
                    [
                        'name'        => 'Pro',
                        'price'       => '29',
                        'currency'    => '$',
                        'period'      => '/month',
                        'description' => 'For growing teams.',
                        'features'    => "Unlimited projects\nPriority support\nAdvanced analytics\nIntegrations",
                        'buttonText'  => 'Start free trial',
                        'buttonHref'  => '#',
                        'highlighted' => true,
                        'badge'       => 'Most popular',
                    ],
                    [
                        'name'        => 'Enterprise',
                        'price'       => 'Custom',
                        'currency'    => '',
                        'period'      => '',
                        'description' => 'For large organisations.',
                        'features'    => "Everything in Pro\nDedicated account manager\nSLA & uptime guarantee\nCustom contracts",
                        'buttonText'  => 'Talk to sales',
                        'buttonHref'  => 'mailto:sales@example.com',
                        'highlighted' => false,
                        'badge'       => '',
                    ],
                ],
                'bg'              => '#ffffff',
                'fg'              => '#0f172a',
                'mutedFg'         => '#64748b',
                'cardBg'          => '#ffffff',
                'cardBorder'      => '#e5e7eb',
                'highlightedBg'   => '#0f172a',
                'highlightedFg'   => '#ffffff',
            ],
            'form' => [
                'heading'    => 'Contact us',
                'paragraph'  => 'Have a question or want to work together? Fill out the form below and we will get back to you shortly.',
                'form_id'    => null,
                'theme'      => 'light',
                'align'      => 'center',
                'bg'         => '#ffffff',
                'fg'         => '#0f172a',
                'mutedFg'    => '#64748b',
            ],
            'reviews' => [
                'variant'      => 'cards',
                'heading'      => 'What customers say',
                'subtitle'     => 'Real reviews from real people.',
                'columns'      => 3,
                'showPhotos'   => true,
                'showStars'    => true,
                'showDate'     => true,
                'showRole'     => true,
                'reviews'      => [
                    ['name' => 'Sarah M.', 'role' => 'Operations Lead, TelecomPlus',  'rating' => 5, 'quote' => 'Turnaround was fast and the experience felt premium from start to finish.', 'date' => '', 'photo' => null],
                    ['name' => 'Ahmed K.', 'role' => 'Individual Customer',           'rating' => 5, 'quote' => 'Dropped my phone in the morning, fixed by closing time. Honestly great.',       'date' => '', 'photo' => null],
                    ['name' => 'Lisa T.',  'role' => 'Procurement Manager, RetailAG', 'rating' => 4, 'quote' => 'The reporting and consolidated invoicing save us hours every month.',         'date' => '', 'photo' => null],
                ],
                'bg'           => '#f8fafc',
                'fg'           => '#0f172a',
                'cardBg'       => '#ffffff',
                'mutedFg'      => '#64748b',
                'starColor'    => '#facc15',
            ],
            'map' => [
                'heading'           => 'Find us',
                'subtitle'          => 'Drop by — we are easy to reach.',
                'addressMode'       => 'auto',   // auto = company address, custom = override below
                'customAddress'     => '',
                'height'            => 420,
                'zoom'              => 15,
                'layout'            => 'split',  // 'split' = map + info side-by-side, 'full' = full-width map
                'showAddressText'   => true,
                'showDirectionsBtn' => true,
                'directionsLabel'   => 'Get directions',
                'bg'                => '#ffffff',
                'fg'                => '#0f172a',
                'mutedFg'           => '#64748b',
            ],
            'blog_posts' => [
                'heading'      => 'From the blog',
                'subtitle'     => 'Tips, news and announcements.',
                'limit'        => 6,
                'categorySlug' => null, // null = all categories
                'columns'      => 3,
                'showExcerpt'  => true,
                'showCategory' => true,
                'showDate'     => true,
                'bg'           => '#ffffff',
                'fg'           => '#0f172a',
                'cardBg'       => '#ffffff',
                'mutedFg'      => '#64748b',
                'accent'       => '#0284c7',
            ],
            'team' => [
                'heading'    => 'Meet the team',
                'subtitle'   => 'The people behind the work.',
                'columns'    => 3,
                'members'    => [
                    ['name' => 'Alex Carter',  'jobTitle' => 'Founder & CEO',     'bio' => 'Leads vision and strategy.', 'photo' => null],
                    ['name' => 'Jamie Rivera', 'jobTitle' => 'Head of Engineering', 'bio' => 'Keeps the lights on.',     'photo' => null],
                    ['name' => 'Sam Patel',    'jobTitle' => 'Product Designer',  'bio' => 'Designs the details.',     'photo' => null],
                ],
                'bg'         => '#ffffff',
                'fg'         => '#0f172a',
                'cardBg'     => '#f8fafc',
                'mutedFg'    => '#64748b',
            ],
            'footer' => [
                'tagline'      => '© {{year}} {{company}}. All rights reserved.',
                'small'        => 'Built with care.',
                'showAddress'  => true,
                'showPages'    => true,
                'showSitemap'  => true,
                'showSocials'  => true,
                'bg'           => '#111111',
                'fg'           => '#e2e8f0',
                'mutedFg'      => '#94a3b8',
                'headingFg'    => '#ffffff',
            ],
            default => [],
        };
    }

    /**
     * Filament form components used in the Edit modal for a section type.
     * Settings are kept in a flat array under a single 'settings' key so
     * the persistence layer doesn't care which type it's looking at.
     */
    public static function schemaFor(string $type): array
    {
        return match ($type) {
            'header' => [
                Section::make('Design')
                    ->schema([
                        Select::make('settings.variant')
                            ->label('Layout')
                            ->options([
                                'classic'  => 'Classic — logo left, nav right',
                                'centered' => 'Centered — logo above nav',
                                'split'    => 'Split — logo / nav / CTA',
                                'minimal'  => 'Minimal — logo only, hamburger nav',
                            ])
                            ->default('classic')
                            ->required()
                            ->live()
                            ->helperText('Use the small icon on the section card to switch designs visually.'),
                        TextInput::make('settings.ctaText')
                            ->label('CTA button label')
                            ->maxLength(40)
                            ->placeholder('Get started')
                            ->visible(fn (Get $get) => $get('settings.variant') === 'split')
                            ->helperText('Used by the Split variant. Leave empty to hide the button.'),
                        TextInput::make('settings.ctaHref')
                            ->label('CTA button URL')
                            ->maxLength(200)
                            ->default('#')
                            ->visible(fn (Get $get) => $get('settings.variant') === 'split'),
                    ])->columns(1),
                Section::make('Logo')
                    ->schema([
                        FileUpload::make('settings.logoImage')
                            ->label('Logo image (optional)')
                            ->helperText('PNG, JPG, or SVG. When set, this replaces the text below.')
                            ->image()
                            ->disk('public')
                            ->directory('theme-builder')
                            ->maxSize(2048)
                            ->imagePreviewHeight('60')
                            ->dehydrateStateUsing(fn ($state) => is_array($state) ? ($state[0] ?? null) : $state),
                        TextInput::make('settings.logoHeight')
                            ->label('Logo height (px)')
                            ->numeric()
                            ->minValue(16)
                            ->maxValue(160)
                            ->default(48)
                            ->helperText('Pixel height of the image. Width scales automatically.'),
                        TextInput::make('settings.logo')
                            ->label('Brand text (used when no image is uploaded)')
                            ->required()
                            ->maxLength(60),
                    ])->columns(2),
                Section::make('Content')
                    ->schema([
                        Select::make('settings.linksMode')
                            ->label('Navigation links source')
                            ->options([
                                'auto'   => 'Auto — published Site Pages (recommended)',
                                'custom' => 'Custom — define links manually below',
                            ])
                            ->default('auto')
                            ->required()
                            ->native(false)
                            ->live()
                            ->helperText('Auto pulls from your published pages at /admin/site-pages. Switch to Custom to override.'),
                        Repeater::make('settings.links')
                            ->label('Custom navigation links')
                            ->schema([
                                TextInput::make('label')->label('Label')->required()->maxLength(40),
                                TextInput::make('href')->label('URL')->required()->maxLength(200),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->addActionLabel('Add link')
                            ->reorderable()
                            ->visible(fn (Get $get) => $get('settings.linksMode') === 'custom'),
                    ])->columns(1),
                Section::make('Style')
                    ->schema([
                        ColorPicker::make('settings.bg')->label('Header background'),
                        ColorPicker::make('settings.fg')->label('Logo color'),
                        ColorPicker::make('settings.linkColor')->label('Link color'),
                        ColorPicker::make('settings.primary')
                            ->label('Primary brand color')
                            ->helperText('Used site-wide for accents, buttons and badges (blog, pages, etc.).'),
                        Select::make('settings.sticky')
                            ->label('Behavior')
                            ->options(['1' => 'Sticky (always visible on scroll)', '0' => 'Static (scrolls away)'])
                            ->default('1')
                            ->dehydrateStateUsing(fn ($state) => (bool) $state),
                    ])->columns(2),
            ],

            'hero' => [
                Section::make('Design')
                    ->schema([
                        Select::make('settings.variant')
                            ->label('Layout')
                            ->options([
                                'centered'   => 'Centered',
                                'split'      => 'Split image',
                                'bg_image'   => 'Background image',
                                'minimal'    => 'Minimal',
                                'with_stats' => 'With stats',
                            ])
                            ->default('centered')
                            ->required()
                            ->live()
                            ->helperText('Use the small icon on the section card to switch designs visually.'),
                        FileUpload::make('settings.image')
                            ->label('Image')
                            ->image()
                            ->disk('public')
                            ->directory('theme-builder/hero')
                            ->maxSize(4096)
                            ->imagePreviewHeight('80')
                            ->dehydrateStateUsing(fn ($state) => is_array($state) ? ($state[0] ?? null) : $state)
                            ->visible(fn (Get $get) => in_array($get('settings.variant'), ['split', 'bg_image'], true))
                            ->helperText('Used as the side image (Split) or full-bleed background (Background image).'),
                        TextInput::make('settings.overlay')
                            ->label('Background overlay opacity')
                            ->helperText('How dark the layer over the photo is, 0–1 (e.g. 0.55).')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(1)
                            ->step(0.05)
                            ->default(0.55)
                            ->visible(fn (Get $get) => $get('settings.variant') === 'bg_image'),
                        Repeater::make('settings.stats')
                            ->label('Stats row')
                            ->schema([
                                TextInput::make('value')->label('Value')->required()->maxLength(16),
                                TextInput::make('label')->label('Label')->required()->maxLength(40),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->reorderable()
                            ->minItems(1)
                            ->maxItems(4)
                            ->addActionLabel('Add stat')
                            ->visible(fn (Get $get) => $get('settings.variant') === 'with_stats')
                            ->helperText('Three works best. Used by the With stats variant.'),
                    ])->columns(1),
                Section::make('Content')
                    ->schema([
                        TextInput::make('settings.eyebrow')->label('Eyebrow (small text above headline)')->maxLength(80),
                        TextInput::make('settings.headline')->label('Headline')->required()->maxLength(120),
                        Textarea::make('settings.subtitle')->label('Subtitle')->rows(3)->maxLength(300),
                        TextInput::make('settings.buttonText')->label('Button label')->maxLength(40),
                        TextInput::make('settings.buttonHref')->label('Button URL')->maxLength(200)->default('#'),
                        Select::make('settings.align')
                            ->label('Alignment (centered variant only)')
                            ->options(['left' => 'Left', 'center' => 'Center', 'right' => 'Right'])
                            ->default('center'),
                    ])->columns(2),
                Section::make('Style')
                    ->schema([
                        ColorPicker::make('settings.bg')->label('Background'),
                        ColorPicker::make('settings.fg')->label('Text color'),
                        ColorPicker::make('settings.buttonBg')->label('Button background'),
                        ColorPicker::make('settings.buttonFg')->label('Button text'),
                    ])->columns(2),
            ],

            'text' => [
                Section::make('Design')
                    ->schema([
                        Select::make('settings.variant')
                            ->label('Layout')
                            ->options([
                                'default'    => 'Heading + body',
                                'two_column' => 'Two columns',
                                'callout'    => 'Callout box',
                                'quote'      => 'Pull quote',
                            ])
                            ->default('default')
                            ->required()
                            ->live()
                            ->helperText('Use the small icon on the section card to switch designs visually.'),
                    ])->columns(1),
                Section::make('Content')
                    ->schema([
                        TextInput::make('settings.heading')->label('Heading')->maxLength(120),
                        Textarea::make('settings.body')->label('Body')->rows(5)->required(),
                        TextInput::make('settings.attribution')
                            ->label('Attribution')
                            ->maxLength(120)
                            ->placeholder('— Author Name')
                            ->visible(fn (Get $get) => $get('settings.variant') === 'quote')
                            ->helperText('Used by the Pull quote variant.'),
                        Select::make('settings.calloutColor')
                            ->label('Callout color')
                            ->options([
                                'info'    => 'Info — light blue / blue accent',
                                'success' => 'Success — light green / green accent',
                                'warning' => 'Warning — light amber / amber accent',
                                'danger'  => 'Danger — light red / red accent',
                                'note'    => 'Note — light violet / purple accent',
                                'neutral' => 'Neutral — light gray / slate accent',
                                'brand'   => 'Brand — uses your site primary color',
                            ])
                            ->default('info')
                            ->required()
                            ->native(false)
                            ->visible(fn (Get $get) => $get('settings.variant') === 'callout')
                            ->helperText('Used by the Callout box variant.'),
                        Select::make('settings.align')
                            ->label('Alignment')
                            ->options(['left' => 'Left', 'center' => 'Center', 'right' => 'Right'])
                            ->default('left')
                            ->visible(fn (Get $get) => in_array($get('settings.variant') ?? 'default', ['default', 'callout', 'quote'], true))
                            ->helperText('Two-column variant ignores alignment.'),
                    ])->columns(1),
                Section::make('Style')
                    ->schema([
                        ColorPicker::make('settings.bg')->label('Background'),
                        ColorPicker::make('settings.fg')->label('Text color'),
                    ])->columns(2),
            ],

            'gallery' => [
                Section::make('Heading')
                    ->schema([
                        TextInput::make('settings.heading')->label('Section heading')->maxLength(120),
                        Textarea::make('settings.subtitle')->label('Subtitle')->rows(2)->maxLength(220),
                    ])->columns(1),

                Section::make('Images')
                    ->schema([
                        Repeater::make('settings.images')
                            ->label('Images')
                            ->schema([
                                FileUpload::make('image')
                                    ->label('Image')
                                    ->required()
                                    ->image()
                                    ->disk('public')
                                    ->directory('theme-builder/gallery')
                                    ->maxSize(5120)
                                    ->imagePreviewHeight('80')
                                    ->dehydrateStateUsing(fn ($state) => is_array($state) ? ($state[0] ?? null) : $state),
                                TextInput::make('caption')
                                    ->label('Caption (optional)')
                                    ->maxLength(140),
                                TextInput::make('alt')
                                    ->label('Alt text (for accessibility)')
                                    ->maxLength(140),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->cloneable()
                            ->reorderable()
                            ->addActionLabel('Add image')
                            ->itemLabel(fn (array $state): ?string => $state['caption'] ?? $state['alt'] ?? 'New image'),
                    ])->columns(1),

                Section::make('Layout')
                    ->schema([
                        Select::make('settings.columns')
                            ->label('Columns (desktop)')
                            ->options([2 => '2', 3 => '3', 4 => '4', 5 => '5'])
                            ->default(3)
                            ->required()
                            ->dehydrateStateUsing(fn ($state) => (int) $state),
                        Select::make('settings.aspect')
                            ->label('Aspect ratio')
                            ->options([
                                'square' => 'Square (1:1)',
                                '4:3'    => 'Landscape (4:3)',
                                '16:9'   => 'Wide (16:9)',
                                '3:4'    => 'Portrait (3:4)',
                                'auto'   => 'Original / mixed',
                            ])
                            ->default('square')
                            ->required(),
                        TextInput::make('settings.gap')
                            ->label('Gap (px)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(64)
                            ->default(12),
                        \Filament\Forms\Components\Toggle::make('settings.showCaptions')
                            ->label('Show captions under each image')
                            ->default(true),
                        \Filament\Forms\Components\Toggle::make('settings.enableLightbox')
                            ->label('Click to open larger (lightbox)')
                            ->default(true),
                    ])->columns(3),

                Section::make('Style')
                    ->schema([
                        ColorPicker::make('settings.bg')->label('Section background'),
                        ColorPicker::make('settings.fg')->label('Heading color'),
                        ColorPicker::make('settings.mutedFg')->label('Caption color'),
                    ])->columns(3),
            ],

            'steps' => [
                Section::make('Heading')
                    ->schema([
                        TextInput::make('settings.heading')->label('Section heading')->maxLength(120),
                        Textarea::make('settings.subtitle')->label('Subtitle')->rows(2)->maxLength(220),
                    ])->columns(1),

                Section::make('Steps')
                    ->schema([
                        Repeater::make('settings.steps')
                            ->label('Steps')
                            ->schema([
                                TextInput::make('number')
                                    ->label('Number / label')
                                    ->maxLength(8)
                                    ->placeholder('e.g. 01 or A')
                                    ->helperText('Leave empty to use the auto-number setting.'),
                                TextInput::make('title')
                                    ->label('Title')
                                    ->required()
                                    ->maxLength(80),
                                Textarea::make('description')
                                    ->label('Description')
                                    ->rows(2)
                                    ->maxLength(280),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->cloneable()
                            ->reorderable()
                            ->addActionLabel('Add step')
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'New step'),
                    ])->columns(1),

                Section::make('Design')
                    ->schema([
                        Select::make('settings.variant')
                            ->label('Layout')
                            ->options([
                                'horizontal' => 'Horizontal — steps in a row',
                                'vertical'   => 'Vertical — timeline list',
                                'cards'      => 'Cards — elevated cards, no connectors',
                                'arrows'     => 'Arrows — chevron arrows between steps',
                                'timeline'   => 'Timeline — vertical with filled circles + bar',
                            ])
                            ->default('horizontal')
                            ->required()
                            ->live()
                            ->helperText('Use the small icon on the section card to switch designs visually.'),
                        \Filament\Forms\Components\Toggle::make('settings.autoNumber')
                            ->label('Auto-number steps (01, 02, 03…)')
                            ->default(true)
                            ->helperText('When on, leave the per-step "Number / label" empty to use this.'),
                        \Filament\Forms\Components\Toggle::make('settings.showConnectors')
                            ->label('Show connector lines between steps')
                            ->default(true)
                            ->visible(fn (Get $get) => in_array($get('settings.variant') ?? 'horizontal', ['horizontal', 'vertical'], true))
                            ->helperText('Cards / Arrows / Timeline have their own built-in connectors.'),
                    ])->columns(3),

                Section::make('Style')
                    ->schema([
                        ColorPicker::make('settings.bg')->label('Section background'),
                        ColorPicker::make('settings.fg')->label('Title color'),
                        ColorPicker::make('settings.mutedFg')->label('Description color'),
                        ColorPicker::make('settings.numberBg')->label('Number circle background'),
                        ColorPicker::make('settings.numberFg')->label('Number text color'),
                        ColorPicker::make('settings.connectorColor')->label('Connector line color'),
                    ])->columns(2),
            ],

            'stats' => [
                Section::make('Heading')
                    ->schema([
                        TextInput::make('settings.heading')->label('Section heading')->maxLength(120),
                        Textarea::make('settings.subtitle')->label('Subtitle')->rows(2)->maxLength(220),
                    ])->columns(1),

                Section::make('Stats')
                    ->schema([
                        Repeater::make('settings.stats')
                            ->label('Stats')
                            ->schema([
                                TextInput::make('value')
                                    ->label('Value')
                                    ->required()
                                    ->maxLength(20)
                                    ->helperText('Numbers animate up when in view (e.g. 13810 → 13,810).'),
                                TextInput::make('prefix')
                                    ->label('Prefix')
                                    ->maxLength(8)
                                    ->placeholder('e.g. < or $'),
                                TextInput::make('suffix')
                                    ->label('Suffix')
                                    ->maxLength(8)
                                    ->placeholder('e.g. % or +'),
                                TextInput::make('label')
                                    ->label('Label')
                                    ->required()
                                    ->maxLength(60),
                                TextInput::make('description')
                                    ->label('Description (optional)')
                                    ->maxLength(120),
                            ])
                            ->columns(3)
                            ->collapsible()
                            ->cloneable()
                            ->reorderable()
                            ->addActionLabel('Add stat')
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? 'New stat'),
                    ])->columns(1),

                Section::make('Layout')
                    ->schema([
                        Select::make('settings.columns')
                            ->label('Columns (desktop)')
                            ->options([2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6'])
                            ->default(4)
                            ->required()
                            ->dehydrateStateUsing(fn ($state) => (int) $state),
                        Select::make('settings.align')
                            ->label('Alignment')
                            ->options(['center' => 'Centered', 'left' => 'Left'])
                            ->default('center')
                            ->required(),
                        \Filament\Forms\Components\Toggle::make('settings.animate')
                            ->label('Count up on scroll into view')
                            ->default(true),
                    ])->columns(3),

                Section::make('Style')
                    ->schema([
                        ColorPicker::make('settings.bg')->label('Section background'),
                        ColorPicker::make('settings.fg')->label('Number color'),
                        ColorPicker::make('settings.mutedFg')->label('Label / description color'),
                        ColorPicker::make('settings.accent')->label('Prefix / suffix accent'),
                    ])->columns(2),
            ],

            'cta' => [
                Section::make('Design')
                    ->schema([
                        Select::make('settings.variant')
                            ->label('Layout')
                            ->options([
                                'centered' => 'Centered',
                                'split'    => 'Split',
                                'gradient' => 'Gradient',
                                'boxed'    => 'Boxed card',
                                'inset'    => 'Inset',
                            ])
                            ->default('centered')
                            ->required()
                            ->live()
                            ->helperText('Use the small icon on the section card to switch designs visually.'),
                    ])->columns(1),
                Section::make('Content')
                    ->schema([
                        TextInput::make('settings.heading')
                            ->label('Heading')
                            ->required()
                            ->maxLength(160),
                        Textarea::make('settings.subtitle')
                            ->label('Subtitle')
                            ->rows(2)
                            ->maxLength(280),
                    ])->columns(1),

                Section::make('Buttons')
                    ->schema([
                        TextInput::make('settings.primaryText')
                            ->label('Primary button label')
                            ->required()
                            ->maxLength(40),
                        TextInput::make('settings.primaryHref')
                            ->label('Primary button URL')
                            ->required()
                            ->maxLength(200)
                            ->default('#'),
                        TextInput::make('settings.secondaryText')
                            ->label('Secondary button label (optional)')
                            ->maxLength(40),
                        TextInput::make('settings.secondaryHref')
                            ->label('Secondary button URL')
                            ->maxLength(200),
                    ])->columns(2),

                Section::make('Gradient colors')
                    ->description('Used by the Gradient layout. From → To linearly interpolated at the chosen angle.')
                    ->schema([
                        ColorPicker::make('settings.gradientFrom')
                            ->label('Gradient — from')
                            ->default('#0ea5e9'),
                        ColorPicker::make('settings.gradientTo')
                            ->label('Gradient — to')
                            ->default('#1e3a8a'),
                        TextInput::make('settings.gradientAngle')
                            ->label('Angle (degrees)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(360)
                            ->default(135)
                            ->helperText('0 = top → bottom, 90 = left → right, 135 = top-left → bottom-right.'),
                    ])
                    ->columns(3)
                    ->visible(fn (Get $get) => ($get('settings.variant') ?? 'centered') === 'gradient'),

                Section::make('Style')
                    ->schema([
                        ColorPicker::make('settings.bg')->label('Section background'),
                        ColorPicker::make('settings.fg')->label('Heading color'),
                        ColorPicker::make('settings.mutedFg')->label('Subtitle color'),
                        ColorPicker::make('settings.primaryBtnBg')->label('Primary button background'),
                        ColorPicker::make('settings.primaryBtnFg')->label('Primary button text'),
                        ColorPicker::make('settings.secondaryBtnBg')->label('Secondary button background'),
                        ColorPicker::make('settings.secondaryBtnFg')->label('Secondary button text'),
                    ])->columns(2),
            ],

            'faq' => [
                Section::make('Heading')
                    ->schema([
                        TextInput::make('settings.heading')->label('Section heading')->maxLength(120),
                        Textarea::make('settings.subtitle')->label('Subtitle')->rows(2)->maxLength(220),
                    ])->columns(1),

                Section::make('Questions')
                    ->schema([
                        Repeater::make('settings.items')
                            ->label('FAQ items')
                            ->schema([
                                TextInput::make('question')
                                    ->label('Question')
                                    ->required()
                                    ->maxLength(200),
                                Textarea::make('answer')
                                    ->label('Answer')
                                    ->required()
                                    ->rows(3)
                                    ->maxLength(1000),
                            ])
                            ->columns(1)
                            ->collapsible()
                            ->cloneable()
                            ->reorderable()
                            ->addActionLabel('Add question')
                            ->itemLabel(fn (array $state): ?string => $state['question'] ?? 'New question'),
                    ])->columns(1),

                Section::make('Layout')
                    ->schema([
                        Select::make('settings.layout')
                            ->label('Display style')
                            ->options([
                                'accordion' => 'Accordion (collapsible, click to expand)',
                                'open'      => 'Always open',
                                'two-col'   => 'Two-column grid (always open)',
                            ])
                            ->default('accordion')
                            ->required()
                            ->live(),
                        \Filament\Forms\Components\Toggle::make('settings.expandFirst')
                            ->label('Expand first item by default')
                            ->default(true)
                            ->visible(fn (Get $get) => $get('settings.layout') === 'accordion'),
                    ])->columns(2),

                Section::make('Style')
                    ->schema([
                        ColorPicker::make('settings.bg')->label('Section background'),
                        ColorPicker::make('settings.fg')->label('Question color'),
                        ColorPicker::make('settings.mutedFg')->label('Answer color'),
                        ColorPicker::make('settings.cardBg')->label('Card / row background'),
                        ColorPicker::make('settings.borderColor')->label('Border / divider color'),
                    ])->columns(2),
            ],

            'pricing' => [
                Section::make('Design')
                    ->schema([
                        Select::make('settings.variant')
                            ->label('Layout')
                            ->options([
                                'cards'   => 'Cards',
                                'table'   => 'Comparison table',
                                'tabs'    => 'Tabs',
                                'minimal' => 'Minimal list',
                            ])
                            ->default('cards')
                            ->required()
                            ->live()
                            ->helperText('Use the small icon on the section card to switch designs visually.'),
                    ])->columns(1),

                Section::make('Heading')
                    ->schema([
                        TextInput::make('settings.heading')->label('Section heading')->maxLength(120),
                        Textarea::make('settings.subtitle')->label('Subtitle')->rows(2)->maxLength(220),
                    ])->columns(1),

                Section::make('Plans')
                    ->schema([
                        Repeater::make('settings.plans')
                            ->label('Pricing plans')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Plan name')
                                    ->required()
                                    ->maxLength(40),
                                TextInput::make('description')
                                    ->label('Tagline')
                                    ->maxLength(120),
                                TextInput::make('currency')
                                    ->label('Currency symbol')
                                    ->maxLength(4)
                                    ->default('$')
                                    ->helperText('Leave empty for "Custom" prices.'),
                                TextInput::make('price')
                                    ->label('Price')
                                    ->required()
                                    ->maxLength(20)
                                    ->helperText('Number, or text like "Custom".'),
                                TextInput::make('period')
                                    ->label('Period')
                                    ->maxLength(20)
                                    ->placeholder('/month'),
                                Textarea::make('features')
                                    ->label('Features')
                                    ->required()
                                    ->rows(5)
                                    ->maxLength(800)
                                    ->helperText('One feature per line.'),
                                TextInput::make('buttonText')
                                    ->label('Button label')
                                    ->maxLength(40)
                                    ->default('Get started'),
                                TextInput::make('buttonHref')
                                    ->label('Button URL')
                                    ->maxLength(200)
                                    ->default('#'),
                                \Filament\Forms\Components\Toggle::make('highlighted')
                                    ->label('Highlight this plan')
                                    ->default(false),
                                TextInput::make('badge')
                                    ->label('Badge text (e.g. "Most popular")')
                                    ->maxLength(40),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->cloneable()
                            ->reorderable()
                            ->addActionLabel('Add plan')
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'New plan'),
                    ])->columns(1),

                Section::make('Layout')
                    ->schema([
                        Select::make('settings.columns')
                            ->label('Columns (desktop)')
                            ->options([1 => '1', 2 => '2', 3 => '3', 4 => '4'])
                            ->default(3)
                            ->required()
                            ->dehydrateStateUsing(fn ($state) => (int) $state),
                    ])->columns(1),

                Section::make('Style')
                    ->schema([
                        ColorPicker::make('settings.bg')->label('Section background'),
                        ColorPicker::make('settings.fg')->label('Heading / plan name color'),
                        ColorPicker::make('settings.mutedFg')->label('Muted text color'),
                        ColorPicker::make('settings.cardBg')->label('Card background'),
                        ColorPicker::make('settings.cardBorder')->label('Card border'),
                        ColorPicker::make('settings.highlightedBg')->label('Highlighted card background'),
                        ColorPicker::make('settings.highlightedFg')->label('Highlighted card text'),
                    ])->columns(2),
            ],

            'form' => [
                Section::make('Content')
                    ->schema([
                        TextInput::make('settings.heading')
                            ->label('Heading')
                            ->maxLength(120)
                            ->placeholder('Contact us'),
                        Textarea::make('settings.paragraph')
                            ->label('Paragraph')
                            ->rows(3)
                            ->maxLength(600)
                            ->helperText('Optional copy shown above the form.'),
                        Select::make('settings.align')
                            ->label('Alignment of heading + paragraph')
                            ->options(['left' => 'Left', 'center' => 'Center'])
                            ->default('center'),
                    ])->columns(1),

                Section::make('Form')
                    ->schema([
                        Select::make('settings.form_id')
                            ->label('Pick a form')
                            ->options(fn () => \App\Models\CustomForm::orderBy('name')->pluck('name', 'id')->toArray())
                            ->searchable()
                            ->required()
                            ->native(false)
                            ->dehydrateStateUsing(fn ($state) => $state ? (int) $state : null)
                            ->helperText('Manage forms in /admin/forms.'),
                        Select::make('settings.theme')
                            ->label('Form theme')
                            ->options([
                                'light' => 'Light',
                                'muted' => 'Muted',
                                'dark'  => 'Dark',
                            ])
                            ->default('light')
                            ->required(),
                    ])->columns(2),

                Section::make('Style')
                    ->schema([
                        ColorPicker::make('settings.bg')->label('Section background'),
                        ColorPicker::make('settings.fg')->label('Heading color'),
                        ColorPicker::make('settings.mutedFg')->label('Paragraph color'),
                    ])->columns(3),
            ],

            'reviews' => [
                Section::make('Design')
                    ->schema([
                        Select::make('settings.variant')
                            ->label('Layout')
                            ->options([
                                'cards'           => 'Cards (grid)',
                                'single_featured' => 'Single featured (rotates)',
                                'bubbles'         => 'Bubbles',
                                'list'            => 'List',
                            ])
                            ->default('cards')
                            ->required()
                            ->live()
                            ->helperText('Use the small icon on the section card to switch designs visually.'),
                    ])->columns(1),

                Section::make('Heading')
                    ->schema([
                        TextInput::make('settings.heading')->label('Section heading')->maxLength(120),
                        Textarea::make('settings.subtitle')->label('Subtitle')->rows(2)->maxLength(220),
                    ])->columns(1),

                Section::make('Reviews')
                    ->schema([
                        Repeater::make('settings.reviews')
                            ->label('Customer reviews')
                            ->schema([
                                FileUpload::make('photo')
                                    ->label('Photo (optional)')
                                    ->image()
                                    ->disk('public')
                                    ->directory('theme-builder/reviews')
                                    ->maxSize(2048)
                                    ->imagePreviewHeight('60')
                                    ->imageCropAspectRatio('1:1')
                                    ->dehydrateStateUsing(fn ($state) => is_array($state) ? ($state[0] ?? null) : $state),
                                TextInput::make('name')->label('Name')->required()->maxLength(80),
                                TextInput::make('role')->label('Role / Company')->maxLength(120),
                                Select::make('rating')
                                    ->label('Stars (1–5)')
                                    ->options([1=>'★ 1', 2=>'★★ 2', 3=>'★★★ 3', 4=>'★★★★ 4', 5=>'★★★★★ 5'])
                                    ->default(5)
                                    ->required()
                                    ->dehydrateStateUsing(fn ($state) => (int) $state),
                                Textarea::make('quote')->label('Review text')->rows(3)->required()->maxLength(400),
                                TextInput::make('date')->label('Date (free-form, e.g. "Jun 2026")')->maxLength(40),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->cloneable()
                            ->reorderable()
                            ->addActionLabel('Add review')
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'New review'),
                    ])->columns(1),

                Section::make('Layout')
                    ->schema([
                        Select::make('settings.columns')
                            ->label('Columns (desktop)')
                            ->options([1 => '1 column', 2 => '2 columns', 3 => '3 columns'])
                            ->default(3)
                            ->required()
                            ->dehydrateStateUsing(fn ($state) => (int) $state)
                            ->visible(fn (Get $get) => in_array($get('settings.variant') ?? 'cards', ['cards', 'bubbles'], true))
                            ->helperText('Single featured shows one at a time; List is always one column.'),
                        \Filament\Forms\Components\Toggle::make('settings.showPhotos')->label('Show photos')->default(true),
                        \Filament\Forms\Components\Toggle::make('settings.showStars') ->label('Show stars')->default(true),
                        \Filament\Forms\Components\Toggle::make('settings.showRole')  ->label('Show role / company')->default(true),
                        \Filament\Forms\Components\Toggle::make('settings.showDate')  ->label('Show date')->default(true),
                    ])->columns(3),

                Section::make('Style')
                    ->schema([
                        ColorPicker::make('settings.bg')->label('Section background'),
                        ColorPicker::make('settings.fg')->label('Heading / name color'),
                        ColorPicker::make('settings.cardBg')->label('Card background'),
                        ColorPicker::make('settings.mutedFg')->label('Quote / muted color'),
                        ColorPicker::make('settings.starColor')->label('Star color'),
                    ])->columns(2),
            ],

            'map' => [
                Section::make('Heading')
                    ->schema([
                        TextInput::make('settings.heading')->label('Section heading')->maxLength(120),
                        Textarea::make('settings.subtitle')->label('Subtitle')->rows(2)->maxLength(220),
                    ])->columns(1),

                Section::make('Address')
                    ->schema([
                        Select::make('settings.addressMode')
                            ->label('Address source')
                            ->options([
                                'auto'   => 'Auto — use Company Details (recommended)',
                                'custom' => 'Custom — type address below',
                            ])
                            ->default('auto')
                            ->required()
                            ->native(false)
                            ->live()
                            ->helperText('Auto pulls from /admin/company-details. Switch to Custom to point the map elsewhere.'),
                        Textarea::make('settings.customAddress')
                            ->label('Custom address')
                            ->rows(2)
                            ->placeholder('e.g. Berliner Str. 1, 91301 Forchheim, Germany')
                            ->visible(fn (Get $get) => $get('settings.addressMode') === 'custom'),
                    ])->columns(1),

                Section::make('Map')
                    ->schema([
                        TextInput::make('settings.height')
                            ->label('Map height (px)')
                            ->numeric()->minValue(200)->maxValue(900)->default(420),
                        TextInput::make('settings.zoom')
                            ->label('Zoom level (1–20)')
                            ->numeric()->minValue(1)->maxValue(20)->default(15),
                        Select::make('settings.layout')
                            ->label('Layout')
                            ->options(['split' => 'Split — map + address side by side', 'full' => 'Full-width map'])
                            ->default('split')
                            ->required(),
                    ])->columns(3),

                Section::make('Address panel')
                    ->schema([
                        \Filament\Forms\Components\Toggle::make('settings.showAddressText')
                            ->label('Show address details next to map')
                            ->default(true),
                        \Filament\Forms\Components\Toggle::make('settings.showDirectionsBtn')
                            ->label('Show "Get directions" button')
                            ->default(true),
                        TextInput::make('settings.directionsLabel')
                            ->label('Directions button label')
                            ->maxLength(40)
                            ->default('Get directions'),
                    ])->columns(3),

                Section::make('Style')
                    ->schema([
                        ColorPicker::make('settings.bg')->label('Section background'),
                        ColorPicker::make('settings.fg')->label('Heading color'),
                        ColorPicker::make('settings.mutedFg')->label('Muted text color'),
                    ])->columns(3),
            ],

            'blog_posts' => [
                Section::make('Heading')
                    ->schema([
                        TextInput::make('settings.heading')->label('Section heading')->maxLength(120),
                        Textarea::make('settings.subtitle')->label('Subtitle')->rows(2)->maxLength(220),
                    ])->columns(1),

                Section::make('Posts')
                    ->schema([
                        Select::make('settings.categorySlug')
                            ->label('Category filter')
                            ->options(function () {
                                return \App\Models\PostCategory::orderBy('name')->pluck('name', 'slug')->toArray();
                            })
                            ->placeholder('All categories')
                            ->searchable()
                            ->native(false),
                        TextInput::make('settings.limit')
                            ->label('How many posts to show')
                            ->numeric()->minValue(1)->maxValue(24)->default(6),
                        Select::make('settings.columns')
                            ->label('Columns (desktop)')
                            ->options([1 => '1 column', 2 => '2 columns', 3 => '3 columns', 4 => '4 columns'])
                            ->default(3)
                            ->required()
                            ->dehydrateStateUsing(fn ($state) => (int) $state),
                    ])->columns(3),

                Section::make('Card content')
                    ->schema([
                        \Filament\Forms\Components\Toggle::make('settings.showCategory')->label('Show category pill')->default(true),
                        \Filament\Forms\Components\Toggle::make('settings.showExcerpt')->label('Show excerpt')->default(true),
                        \Filament\Forms\Components\Toggle::make('settings.showDate')->label('Show published date')->default(true),
                    ])->columns(3),

                Section::make('Style')
                    ->schema([
                        ColorPicker::make('settings.bg')->label('Section background'),
                        ColorPicker::make('settings.fg')->label('Title color'),
                        ColorPicker::make('settings.cardBg')->label('Card background'),
                        ColorPicker::make('settings.mutedFg')->label('Excerpt / date color'),
                        ColorPicker::make('settings.accent')->label('Category pill / hover accent'),
                    ])->columns(2),
            ],

            'team' => [
                Section::make('Heading')
                    ->schema([
                        TextInput::make('settings.heading')->label('Section heading')->maxLength(120),
                        Textarea::make('settings.subtitle')->label('Subtitle')->rows(2)->maxLength(220),
                    ])->columns(1),

                Section::make('Members')
                    ->schema([
                        Repeater::make('settings.members')
                            ->label('Team members')
                            ->schema([
                                FileUpload::make('photo')
                                    ->label('Photo')
                                    ->image()
                                    ->disk('public')
                                    ->directory('theme-builder/team')
                                    ->maxSize(2048)
                                    ->imagePreviewHeight('80')
                                    ->imageCropAspectRatio('1:1')
                                    ->dehydrateStateUsing(fn ($state) => is_array($state) ? ($state[0] ?? null) : $state),
                                TextInput::make('name')->label('Name')->required()->maxLength(80),
                                TextInput::make('jobTitle')->label('Job title')->required()->maxLength(80),
                                Textarea::make('bio')->label('Short bio')->rows(2)->maxLength(220),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->cloneable()
                            ->reorderable()
                            ->addActionLabel('Add member')
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'New member'),
                    ])->columns(1),

                Section::make('Style')
                    ->schema([
                        Select::make('settings.columns')
                            ->label('Columns (desktop)')
                            ->options([2 => '2 columns', 3 => '3 columns', 4 => '4 columns'])
                            ->default(3)
                            ->required()
                            ->dehydrateStateUsing(fn ($state) => (int) $state),
                        ColorPicker::make('settings.bg')->label('Section background'),
                        ColorPicker::make('settings.fg')->label('Heading / name color'),
                        ColorPicker::make('settings.cardBg')->label('Card background'),
                        ColorPicker::make('settings.mutedFg')->label('Job title / bio color'),
                    ])->columns(2),
            ],

            'footer' => [
                Section::make('Content')
                    ->schema([
                        Textarea::make('settings.tagline')
                            ->label('Tagline')
                            ->rows(2)
                            ->required()
                            ->maxLength(200)
                            ->helperText('Use {{company}} and {{year}} placeholders — they resolve to the live company name and current year.'),
                    ])->columns(1),
                Section::make('Visible columns')
                    ->description('Toggle the columns you want in the footer. Their content is pulled from your other admin areas.')
                    ->schema([
                        \Filament\Forms\Components\Toggle::make('settings.showAddress')
                            ->label('Address & contact')
                            ->helperText('Pulled from /admin/company-details')
                            ->default(true),
                        \Filament\Forms\Components\Toggle::make('settings.showPages')
                            ->label('Site pages')
                            ->helperText('Published pages from /admin/site-pages')
                            ->default(true),
                        \Filament\Forms\Components\Toggle::make('settings.showSitemap')
                            ->label('Blog sitemap')
                            ->helperText('Categories with their published posts')
                            ->default(true),
                        \Filament\Forms\Components\Toggle::make('settings.showSocials')
                            ->label('Social icons')
                            ->helperText('Configured in /admin/site-settings')
                            ->default(true),
                    ])->columns(2),
                Section::make('Style')
                    ->schema([
                        ColorPicker::make('settings.bg')->label('Background'),
                        ColorPicker::make('settings.headingFg')->label('Heading color'),
                        ColorPicker::make('settings.fg')->label('Body text color'),
                        ColorPicker::make('settings.mutedFg')->label('Muted text color'),
                    ])->columns(2),
            ],

            default => [],
        };
    }
}
