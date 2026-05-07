<?php

namespace App\Filament\Pages\ThemeBuilder;

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
     * Default settings for a freshly-added section. Keep them tasteful so
     * dropping a section produces something usable without further config.
     */
    public static function defaultSettings(string $type): array
    {
        return match ($type) {
            'header' => [
                'logo'       => 'Brand',
                'logoImage'  => null,    // relative path within the public disk (overrides text when set)
                'logoHeight' => 48,      // displayed pixel height of the logo image
                'linksMode'  => 'auto',  // 'auto' = pull from published Site Pages, 'custom' = use links[]
                'links'      => [],
                'bg'         => '#0f172a',
                'fg'         => '#ffffff',
                'linkColor'  => '#cbd5e1',
                'primary'    => '#ea580c', // site-wide brand accent (drives buttons, badges, links elsewhere)
                'sticky'     => true,
            ],
            'hero' => [
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
            ],
            'text' => [
                'heading'    => 'About us',
                'body'       => 'Replace this with your own copy. Use the editor on the left to change colors, alignment, and typography.',
                'align'      => 'left',
                'bg'         => '#ffffff',
                'fg'         => '#0f172a',
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
                'bg'           => '#0f172a',
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
                Section::make('Content')
                    ->schema([
                        TextInput::make('settings.eyebrow')->label('Eyebrow (small text above headline)')->maxLength(80),
                        TextInput::make('settings.headline')->label('Headline')->required()->maxLength(120),
                        Textarea::make('settings.subtitle')->label('Subtitle')->rows(3)->maxLength(300),
                        TextInput::make('settings.buttonText')->label('Button label')->maxLength(40),
                        TextInput::make('settings.buttonHref')->label('Button URL')->maxLength(200)->default('#'),
                        Select::make('settings.align')
                            ->label('Alignment')
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
                Section::make('Content')
                    ->schema([
                        TextInput::make('settings.heading')->label('Heading')->maxLength(120),
                        Textarea::make('settings.body')->label('Body')->rows(5)->required(),
                        Select::make('settings.align')
                            ->label('Alignment')
                            ->options(['left' => 'Left', 'center' => 'Center', 'right' => 'Right'])
                            ->default('left'),
                    ])->columns(1),
                Section::make('Style')
                    ->schema([
                        ColorPicker::make('settings.bg')->label('Background'),
                        ColorPicker::make('settings.fg')->label('Text color'),
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
                            ->dehydrateStateUsing(fn ($state) => (int) $state),
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
                        TextInput::make('settings.small')
                            ->label('Small print (bottom bar)')
                            ->maxLength(120)
                            ->helperText('Same {{company}} / {{year}} placeholders are supported here too.'),
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
