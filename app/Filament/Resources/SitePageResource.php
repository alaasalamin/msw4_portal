<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SitePageResource\Pages;
use App\Models\CustomForm;
use App\Models\SitePage;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class SitePageResource extends Resource
{
    protected static ?string $model = SitePage::class;

    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-document-text'; }
    public static function getNavigationGroup(): string|\UnitEnum|null  { return 'Content'; }
    public static function getNavigationSort(): ?int                    { return 1; }
    public static function getNavigationLabel(): string                 { return 'Pages'; }
    public static function getModelLabel(): string                      { return 'Page'; }

    public static function form(Schema $form): Schema
    {
        return $form->components([

            Section::make('Page Details')->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(200)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $context, $state, callable $set) {
                        if ($context === 'create') {
                            $set('slug', SitePage::uniqueSlug($state));
                        }
                    }),

                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(200)
                    ->prefix('/')
                    ->helperText('URL: /{slug}'),

                Select::make('status')
                    ->options(['draft' => 'Draft', 'published' => 'Published'])
                    ->default('draft')
                    ->required(),
            ])->columns(3),

            Section::make('SEO')->schema([
                TextInput::make('meta_title')
                    ->label('Meta Title')
                    ->maxLength(60)
                    ->helperText('Shown in browser tab. Defaults to page title.'),
                Textarea::make('meta_description')
                    ->label('Meta Description')
                    ->rows(2)
                    ->maxLength(160),
            ])->columns(2)->collapsible()->collapsed(),

            Section::make('Page Sections')->schema([
                Builder::make('sections')
                    ->label('')
                    ->blocks([

                        // ── Hero ──────────────────────────────────────────────
                        Block::make('page_hero')
                            ->label('Hero')
                            ->icon('heroicon-o-sparkles')
                            ->schema([
                                TextInput::make('badge')->label('Badge (optional)')->maxLength(120),
                                TextInput::make('title')->label('Headline (HTML allowed)')->required()
                                    ->helperText('Wrap highlighted word in <span class="text-orange-400">…</span>'),
                                Textarea::make('subtitle')->label('Subtitle')->rows(3),
                                TextInput::make('cta_label')->label('CTA Button label')->maxLength(60),
                                TextInput::make('cta_url')->label('CTA Button URL')->maxLength(200),
                                TextInput::make('cta_secondary_label')->label('Secondary button label')->maxLength(60),
                                TextInput::make('cta_secondary_url')->label('Secondary button URL')->maxLength(200),
                                Select::make('theme')
                                    ->label('Background')
                                    ->options(['dark' => 'Dark (zinc-900)', 'light' => 'Light (white)'])
                                    ->default('dark'),
                            ])->columns(2),

                        // ── Stats Bar ──────────────────────────────────────────
                        Block::make('stats_bar')
                            ->label('Stats Bar')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                Repeater::make('items')
                                    ->label('Stats')
                                    ->schema([
                                        TextInput::make('value')->required()->maxLength(20),
                                        TextInput::make('label')->required()->maxLength(40),
                                    ])
                                    ->columns(2)
                                    ->addActionLabel('Add stat')
                                    ->defaultItems(0),
                            ]),

                        // ── Features Grid ──────────────────────────────────────
                        Block::make('features_grid')
                            ->label('Features / Services Grid')
                            ->icon('heroicon-o-squares-2x2')
                            ->schema([
                                TextInput::make('label')->label('Section label (small caps)')->maxLength(60),
                                TextInput::make('title')->label('Heading')->required()->maxLength(120),
                                Textarea::make('subtitle')->label('Subheading')->rows(2),
                                Select::make('theme')
                                    ->options(['light' => 'Light (zinc-50)', 'dark' => 'Dark (zinc-900)'])
                                    ->default('light'),
                                Repeater::make('items')
                                    ->label('Cards')
                                    ->schema([
                                        TextInput::make('title')->required()->maxLength(60),
                                        Textarea::make('desc')->label('Description')->rows(2)->required()->maxLength(200),
                                    ])
                                    ->columns(2)
                                    ->addActionLabel('Add card')
                                    ->defaultItems(0)
                                    ->columnSpanFull(),
                            ])->columns(2),

                        // ── Process Steps ──────────────────────────────────────
                        Block::make('process_steps')
                            ->label('Process Steps')
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                TextInput::make('label')->label('Section label')->maxLength(60),
                                TextInput::make('title')->label('Heading')->required()->maxLength(120),
                                Repeater::make('steps')
                                    ->schema([
                                        TextInput::make('num')->label('Number (e.g. 01)')->required()->maxLength(4),
                                        TextInput::make('title')->required()->maxLength(60),
                                        Textarea::make('desc')->label('Description')->rows(2)->required()->maxLength(200),
                                    ])
                                    ->columns(3)
                                    ->addActionLabel('Add step')
                                    ->defaultItems(0)
                                    ->columnSpanFull(),
                            ])->columns(2),

                        // ── Testimonials ───────────────────────────────────────
                        Block::make('testimonials')
                            ->label('Testimonials')
                            ->icon('heroicon-o-chat-bubble-left-right')
                            ->schema([
                                Repeater::make('items')
                                    ->label('Reviews')
                                    ->schema([
                                        Textarea::make('quote')->rows(3)->required()->maxLength(300),
                                        TextInput::make('name')->required()->maxLength(60),
                                        TextInput::make('role')->label('Role / Company')->required()->maxLength(80),
                                    ])
                                    ->columns(3)
                                    ->addActionLabel('Add testimonial')
                                    ->defaultItems(0),
                            ]),

                        // ── CTA Banner ────────────────────────────────────────
                        Block::make('cta_banner')
                            ->label('CTA Banner')
                            ->icon('heroicon-o-megaphone')
                            ->schema([
                                TextInput::make('title')->label('Headline')->required()->maxLength(120),
                                Textarea::make('subtitle')->label('Subtitle')->rows(2)->maxLength(300),
                                TextInput::make('button_label')->label('Button label')->maxLength(60),
                                TextInput::make('button_url')->label('Button URL')->maxLength(200),
                                TextInput::make('button_secondary_label')->label('Secondary button label')->maxLength(60),
                                TextInput::make('button_secondary_url')->label('Secondary button URL')->maxLength(200),
                                Select::make('theme')
                                    ->options(['dark' => 'Dark (zinc-900)', 'light' => 'Light (white)', 'orange' => 'Orange'])
                                    ->default('dark'),
                            ])->columns(2),

                        // ── Contact Form ─────────────────────────────────────
                        Block::make('form_block')
                            ->label('Contact Form')
                            ->icon('heroicon-o-envelope')
                            ->schema([
                                Select::make('form_id')
                                    ->label('Select Form')
                                    ->options(fn () => CustomForm::pluck('name', 'id'))
                                    ->required()
                                    ->searchable(),
                                TextInput::make('title')
                                    ->label('Block Title (optional)')
                                    ->maxLength(120),
                                Textarea::make('description')
                                    ->label('Block Description (optional)')
                                    ->rows(2)
                                    ->maxLength(300),
                                Select::make('theme')
                                    ->options(['light' => 'Light (white)', 'dark' => 'Dark (zinc-900)', 'muted' => 'Muted (zinc-50)'])
                                    ->default('light'),
                            ])->columns(2),

                        // ── Text Block ────────────────────────────────────────
                        Block::make('text_block')
                            ->label('Text Block')
                            ->icon('heroicon-o-bars-3-bottom-left')
                            ->schema([
                                TextInput::make('heading')->label('Heading (optional)')->maxLength(150),
                                Textarea::make('body')->label('Body text (HTML allowed)')->rows(6)->required(),
                                Select::make('align')
                                    ->options(['left' => 'Left', 'center' => 'Center'])
                                    ->default('left'),
                                Select::make('theme')
                                    ->options(['light' => 'Light (white)', 'dark' => 'Dark (zinc-900)', 'muted' => 'Muted (zinc-50)'])
                                    ->default('light'),
                            ])->columns(2),

                    ])
                    ->addActionLabel('Add Section')
                    ->collapsible()
                    ->blockNumbers(false)
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->description(fn (SitePage $r) => '/' . $r->slug),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft'     => 'gray',
                        default     => 'gray',
                    }),

                TextColumn::make('sections')
                    ->label('Sections')
                    ->state(fn (SitePage $r) => count($r->sections ?? []) . ' section(s)')
                    ->color('gray'),

                TextColumn::make('updated_at')
                    ->label('Last updated')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(['published' => 'Published', 'draft' => 'Draft']),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSitePages::route('/'),
            'create' => Pages\CreateSitePage::route('/create'),
            'edit'   => Pages\EditSitePage::route('/{record}/edit'),
        ];
    }
}
