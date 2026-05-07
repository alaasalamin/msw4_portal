<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ThemeBuilder extends Page
{
    protected string $view = 'filament.pages.theme-builder';

    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-squares-2x2'; }
    public static function getNavigationGroup(): string|\UnitEnum|null  { return 'Configuration'; }
    public static function getNavigationSort(): ?int                    { return 3; }
    public static function getNavigationLabel(): string                 { return 'Theme Builder'; }
    public function getTitle(): string                                  { return 'Theme Builder'; }

    /**
     * Returns the list of sections for the left-hand sidebar.
     * Each section maps to a Filament action method on this page.
     */
    public function getSections(): array
    {
        return [
            ['key' => 'header',       'label' => 'Header / Navbar',  'icon' => 'heroicon-o-bars-3',           'desc' => 'Brand text and navigation.'],
            ['key' => 'hero',         'label' => 'Hero',             'icon' => 'heroicon-o-rocket-launch',    'desc' => 'Headline, subtitle, badge & bullets.'],
            ['key' => 'stats',        'label' => 'Stats Bar',        'icon' => 'heroicon-o-chart-bar',        'desc' => 'The four-column stats below the hero.'],
            ['key' => 'services',     'label' => 'Services',         'icon' => 'heroicon-o-wrench-screwdriver','desc' => '"What we repair" cards.'],
            ['key' => 'process',      'label' => 'How It Works',     'icon' => 'heroicon-o-list-bullet',      'desc' => 'The numbered process steps.'],
            ['key' => 'partners',     'label' => 'B2B Partners',     'icon' => 'heroicon-o-building-office', 'desc' => 'Partner programme block.'],
            ['key' => 'testimonials', 'label' => 'Testimonials',     'icon' => 'heroicon-o-chat-bubble-left-right', 'desc' => 'Customer quotes.'],
            ['key' => 'footer',       'label' => 'Footer',           'icon' => 'heroicon-o-bars-3-bottom-left','desc' => 'Tagline, brand, contact emails.'],
        ];
    }

    // ─────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────

    /** Decode a JSON setting back to a PHP array. */
    protected function jsonSetting(string $key, array $default): array
    {
        return json_decode(Setting::get($key) ?? '', true) ?? $default;
    }

    /** Set many string settings at once. */
    protected function setStrings(array $data, array $keys): void
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $data)) {
                Setting::set($key, $data[$key] ?? '');
            }
        }
    }

    /** Persist a repeater whose rows are objects → JSON. */
    protected function setJson(string $key, ?array $rows): void
    {
        Setting::set($key, json_encode(array_values($rows ?? [])));
    }

    /** Persist a repeater whose rows are { text } → flat JSON array of strings. */
    protected function setTextList(string $key, ?array $rows): void
    {
        $values = array_values(array_map(fn ($r) => $r['text'] ?? '', $rows ?? []));
        Setting::set($key, json_encode($values));
    }

    protected function logChange(string $section): void
    {
        activity('theme_builder')
            ->causedBy(auth('admin')->user())
            ->log("Updated theme section: {$section}");
    }

    protected function saved(string $section): void
    {
        $this->logChange($section);
        $this->dispatch('theme-builder:section-saved');
        Notification::make()
            ->title(ucfirst($section) . ' section saved')
            ->success()
            ->send();
    }

    // ─────────────────────────────────────────────────────────────────
    // Per-section actions (each opens its own modal form)
    // ─────────────────────────────────────────────────────────────────

    public function editHeaderAction(): Action
    {
        return Action::make('editHeader')
            ->label('Edit')
            ->icon('heroicon-m-pencil-square')
            ->modalHeading('Header / Navbar')
            ->modalDescription('Brand text shown in the top bar. Navigation links auto-populate from your published Site Pages.')
            ->modalSubmitActionLabel('Save')
            ->fillForm(fn () => [
                'home_brand_prefix' => Setting::get('home_brand_prefix', 'Moon'),
                'home_brand_suffix' => Setting::get('home_brand_suffix', '.Repair'),
            ])
            ->schema([
                TextInput::make('home_brand_prefix')
                    ->label('Brand prefix')
                    ->helperText('White part of the wordmark, e.g. "Moon".')
                    ->required()
                    ->maxLength(40),
                TextInput::make('home_brand_suffix')
                    ->label('Brand suffix (orange)')
                    ->helperText('Highlighted part, e.g. ".Repair".')
                    ->required()
                    ->maxLength(40),
            ])
            ->action(function (array $data) {
                $this->setStrings($data, ['home_brand_prefix', 'home_brand_suffix']);
                $this->saved('header');
            });
    }

    public function editHeroAction(): Action
    {
        return Action::make('editHero')
            ->label('Edit')
            ->icon('heroicon-m-pencil-square')
            ->modalHeading('Hero Section')
            ->modalSubmitActionLabel('Save')
            ->fillForm(fn () => [
                'home_hero_badge'         => Setting::get('home_hero_badge'),
                'home_hero_title'         => Setting::get('home_hero_title'),
                'home_hero_subtitle'      => Setting::get('home_hero_subtitle'),
                'home_hero_rating'        => Setting::get('home_hero_rating'),
                'home_hero_repairs_count' => Setting::get('home_hero_repairs_count'),
                'home_hero_bullets'       => array_map(fn ($t) => ['text' => $t], $this->jsonSetting('home_hero_bullets', [])),
            ])
            ->schema([
                TextInput::make('home_hero_badge')->label('Badge text')->maxLength(120),
                Textarea::make('home_hero_title')
                    ->label('Headline (HTML allowed)')
                    ->helperText('Wrap the highlighted word in <span class="text-orange-400">…</span>')
                    ->rows(2),
                Textarea::make('home_hero_subtitle')->label('Subtitle paragraph')->rows(3),
                TextInput::make('home_hero_rating')->label('Rating text')->maxLength(20),
                TextInput::make('home_hero_repairs_count')->label('Repairs count label')->maxLength(60),
                Repeater::make('home_hero_bullets')
                    ->label('Bullet points')
                    ->schema([TextInput::make('text')->label('Bullet')->required()->maxLength(120)])
                    ->addActionLabel('Add bullet')
                    ->collapsible(),
            ])
            ->action(function (array $data) {
                $this->setStrings($data, [
                    'home_hero_badge', 'home_hero_title', 'home_hero_subtitle',
                    'home_hero_rating', 'home_hero_repairs_count',
                ]);
                $this->setTextList('home_hero_bullets', $data['home_hero_bullets'] ?? []);
                $this->saved('hero');
            });
    }

    public function editStatsAction(): Action
    {
        return Action::make('editStats')
            ->label('Edit')
            ->icon('heroicon-m-pencil-square')
            ->modalHeading('Stats Bar')
            ->modalSubmitActionLabel('Save')
            ->fillForm(fn () => [
                'home_stats' => $this->jsonSetting('home_stats', []),
            ])
            ->schema([
                Repeater::make('home_stats')
                    ->label('Stats (4 column bar below the hero)')
                    ->schema([
                        TextInput::make('value')->label('Value')->required()->maxLength(20),
                        TextInput::make('label')->label('Label')->required()->maxLength(40),
                    ])
                    ->columns(2)
                    ->addActionLabel('Add stat')
                    ->collapsible(),
            ])
            ->action(function (array $data) {
                $this->setJson('home_stats', $data['home_stats'] ?? []);
                $this->saved('stats');
            });
    }

    public function editServicesAction(): Action
    {
        return Action::make('editServices')
            ->label('Edit')
            ->icon('heroicon-m-pencil-square')
            ->modalHeading('Services Section')
            ->modalSubmitActionLabel('Save')
            ->fillForm(fn () => [
                'home_services_label'    => Setting::get('home_services_label'),
                'home_services_title'    => Setting::get('home_services_title'),
                'home_services_subtitle' => Setting::get('home_services_subtitle'),
                'home_services_items'    => $this->jsonSetting('home_services_items', []),
            ])
            ->schema([
                TextInput::make('home_services_label')->label('Section label')->maxLength(60),
                TextInput::make('home_services_title')->label('Heading')->maxLength(100),
                Textarea::make('home_services_subtitle')->label('Subheading')->rows(2),
                Repeater::make('home_services_items')
                    ->label('Service cards (icon order: Phone, Laptop, Tablet, Shield, Database, Clock)')
                    ->schema([
                        TextInput::make('title')->label('Title')->required()->maxLength(60),
                        Textarea::make('desc')->label('Description')->rows(2)->required()->maxLength(200),
                    ])
                    ->columns(2)
                    ->addActionLabel('Add service card')
                    ->collapsible(),
            ])
            ->action(function (array $data) {
                $this->setStrings($data, ['home_services_label', 'home_services_title', 'home_services_subtitle']);
                $this->setJson('home_services_items', $data['home_services_items'] ?? []);
                $this->saved('services');
            });
    }

    public function editProcessAction(): Action
    {
        return Action::make('editProcess')
            ->label('Edit')
            ->icon('heroicon-m-pencil-square')
            ->modalHeading('How It Works Section')
            ->modalSubmitActionLabel('Save')
            ->fillForm(fn () => [
                'home_process_label' => Setting::get('home_process_label'),
                'home_process_title' => Setting::get('home_process_title'),
                'home_steps'         => $this->jsonSetting('home_steps', []),
            ])
            ->schema([
                TextInput::make('home_process_label')->label('Section label')->maxLength(60),
                TextInput::make('home_process_title')->label('Heading')->maxLength(100),
                Repeater::make('home_steps')
                    ->label('Steps')
                    ->schema([
                        TextInput::make('num')->label('Number (e.g. 01)')->required()->maxLength(4),
                        TextInput::make('title')->label('Step title')->required()->maxLength(60),
                        Textarea::make('desc')->label('Step description')->rows(2)->required()->maxLength(200),
                    ])
                    ->columns(3)
                    ->addActionLabel('Add step')
                    ->collapsible(),
            ])
            ->action(function (array $data) {
                $this->setStrings($data, ['home_process_label', 'home_process_title']);
                $this->setJson('home_steps', $data['home_steps'] ?? []);
                $this->saved('process');
            });
    }

    public function editPartnersAction(): Action
    {
        return Action::make('editPartners')
            ->label('Edit')
            ->icon('heroicon-m-pencil-square')
            ->modalHeading('B2B Partners Section')
            ->modalSubmitActionLabel('Save')
            ->fillForm(fn () => [
                'home_partners_label'     => Setting::get('home_partners_label'),
                'home_partners_title'     => Setting::get('home_partners_title'),
                'home_partners_subtitle'  => Setting::get('home_partners_subtitle'),
                'home_partners_cta_email' => Setting::get('home_partners_cta_email'),
                'home_partners_benefits'  => array_map(fn ($t) => ['text' => $t], $this->jsonSetting('home_partners_benefits', [])),
                'home_partners_features'  => $this->jsonSetting('home_partners_features', []),
            ])
            ->schema([
                TextInput::make('home_partners_label')->label('Badge label')->maxLength(60),
                TextInput::make('home_partners_title')->label('Heading')->maxLength(120),
                Textarea::make('home_partners_subtitle')->label('Body text')->rows(3),
                TextInput::make('home_partners_cta_email')->label('Contact Sales email')->email()->maxLength(100),
                Repeater::make('home_partners_benefits')
                    ->label('Benefit list items')
                    ->schema([TextInput::make('text')->label('Benefit')->required()->maxLength(100)])
                    ->addActionLabel('Add benefit')
                    ->collapsible(),
                Repeater::make('home_partners_features')
                    ->label('Feature tiles (2×2 grid)')
                    ->schema([
                        TextInput::make('title')->label('Title')->required()->maxLength(60),
                        Textarea::make('desc')->label('Description')->rows(2)->required()->maxLength(150),
                    ])
                    ->columns(2)
                    ->addActionLabel('Add feature tile')
                    ->collapsible(),
            ])
            ->action(function (array $data) {
                $this->setStrings($data, [
                    'home_partners_label', 'home_partners_title',
                    'home_partners_subtitle', 'home_partners_cta_email',
                ]);
                $this->setTextList('home_partners_benefits', $data['home_partners_benefits'] ?? []);
                $this->setJson('home_partners_features', $data['home_partners_features'] ?? []);
                $this->saved('partners');
            });
    }

    public function editTestimonialsAction(): Action
    {
        return Action::make('editTestimonials')
            ->label('Edit')
            ->icon('heroicon-m-pencil-square')
            ->modalHeading('Testimonials')
            ->modalSubmitActionLabel('Save')
            ->fillForm(fn () => [
                'home_testimonials' => $this->jsonSetting('home_testimonials', []),
            ])
            ->schema([
                Repeater::make('home_testimonials')
                    ->label('Testimonial cards')
                    ->schema([
                        Textarea::make('quote')->label('Quote')->rows(3)->required()->maxLength(300),
                        TextInput::make('name')->label('Name')->required()->maxLength(60),
                        TextInput::make('role')->label('Role / Company')->required()->maxLength(80),
                    ])
                    ->columns(3)
                    ->addActionLabel('Add testimonial')
                    ->collapsible(),
            ])
            ->action(function (array $data) {
                $this->setJson('home_testimonials', $data['home_testimonials'] ?? []);
                $this->saved('testimonials');
            });
    }

    public function editFooterAction(): Action
    {
        return Action::make('editFooter')
            ->label('Edit')
            ->icon('heroicon-m-pencil-square')
            ->modalHeading('Footer')
            ->modalDescription('Page links, blog categories, and socials are managed in their own admin areas.')
            ->modalSubmitActionLabel('Save')
            ->fillForm(fn () => [
                'home_footer_tagline'        => Setting::get('home_footer_tagline'),
                'home_footer_email_hello'    => Setting::get('home_footer_email_hello'),
                'home_footer_email_partners' => Setting::get('home_footer_email_partners'),
            ])
            ->schema([
                Textarea::make('home_footer_tagline')->label('Tagline paragraph')->rows(2)->maxLength(200),
                TextInput::make('home_footer_email_hello')->label('General email')->email()->maxLength(100),
                TextInput::make('home_footer_email_partners')->label('Partners email')->email()->maxLength(100),
            ])
            ->action(function (array $data) {
                $this->setStrings($data, [
                    'home_footer_tagline', 'home_footer_email_hello', 'home_footer_email_partners',
                ]);
                $this->saved('footer');
            });
    }

    /**
     * Inline-edit endpoint for plain string settings.
     * Called from the iframe via postMessage → Livewire.
     */
    public function saveField(string $key, string $value): void
    {
        if (! in_array($key, $this->editableStringKeys(), true)) {
            return;
        }
        Setting::set($key, $value);
        activity('theme_builder')
            ->causedBy(auth('admin')->user())
            ->withProperties(['key' => $key])
            ->log("Inline edit: {$key}");
        Notification::make()
            ->title('Saved')
            ->body($key)
            ->success()
            ->send();
    }

    /**
     * Inline-edit endpoint for one row (and optionally one field) of a JSON-list setting.
     * If $field is null the whole row is treated as a string (e.g. bullets, benefits).
     */
    public function saveListField(string $key, int $index, ?string $field, string $value): void
    {
        if (! array_key_exists($key, $this->editableListKeys())) {
            return;
        }
        $rows = $this->jsonSetting($key, []);
        if (! isset($rows[$index])) {
            return;
        }
        if ($field === null || $field === '') {
            $rows[$index] = $value;
        } else {
            if (! in_array($field, $this->editableListKeys()[$key], true)) {
                return;
            }
            if (! is_array($rows[$index])) {
                return;
            }
            $rows[$index][$field] = $value;
        }
        $this->setJson($key, $rows);
        activity('theme_builder')
            ->causedBy(auth('admin')->user())
            ->withProperties(['key' => $key, 'index' => $index, 'field' => $field])
            ->log("Inline edit: {$key}[{$index}]" . ($field ? ".{$field}" : ''));
        Notification::make()
            ->title('Saved')
            ->body($key . '[' . $index . ']' . ($field ? '.' . $field : ''))
            ->success()
            ->send();
    }

    /**
     * Apply a batch of pending inline edits in a single round trip.
     * Each change is { kind: 'str'|'list', key, index?, field?, value }.
     */
    public function saveBatch(array $changes): void
    {
        $stringKeys = $this->editableStringKeys();
        $listKeys   = $this->editableListKeys();

        // Group list edits by key so we mutate each JSON setting once.
        $listEdits = [];
        $strCount = 0;
        foreach ($changes as $c) {
            $kind  = $c['kind']  ?? null;
            $key   = $c['key']   ?? null;
            $value = $c['value'] ?? '';

            if ($kind === 'str') {
                if (! in_array($key, $stringKeys, true)) continue;
                Setting::set($key, (string) $value);
                $strCount++;
                continue;
            }

            if ($kind === 'list') {
                if (! array_key_exists($key, $listKeys)) continue;
                $idx   = (int) ($c['index'] ?? 0);
                $field = $c['field'] ?? null;
                if ($field === '') $field = null;
                if ($field !== null && ! in_array($field, $listKeys[$key], true)) continue;
                $listEdits[$key][] = ['index' => $idx, 'field' => $field, 'value' => (string) $value];
            }
        }

        $listCount = 0;
        foreach ($listEdits as $key => $edits) {
            $rows = $this->jsonSetting($key, []);
            foreach ($edits as $e) {
                if (! isset($rows[$e['index']])) continue;
                if ($e['field'] === null) {
                    $rows[$e['index']] = $e['value'];
                } else {
                    if (! is_array($rows[$e['index']])) continue;
                    $rows[$e['index']][$e['field']] = $e['value'];
                }
                $listCount++;
            }
            $this->setJson($key, $rows);
        }

        $total = $strCount + $listCount;
        if ($total > 0) {
            activity('theme_builder')
                ->causedBy(auth('admin')->user())
                ->withProperties(['changes' => $total])
                ->log("Inline edits saved ({$total})");

            Notification::make()
                ->title('Saved')
                ->body($total . ' change' . ($total === 1 ? '' : 's') . ' applied')
                ->success()
                ->send();
        }
    }

    /** Whitelist of single-string settings the inline editor may write to. */
    protected function editableStringKeys(): array
    {
        return [
            'home_brand_prefix', 'home_brand_suffix',
            'home_hero_badge', 'home_hero_subtitle', 'home_hero_rating', 'home_hero_repairs_count',
            'home_services_label', 'home_services_title', 'home_services_subtitle',
            'home_process_label', 'home_process_title',
            'home_partners_label', 'home_partners_title', 'home_partners_subtitle',
            'home_footer_tagline', 'home_footer_email_hello', 'home_footer_email_partners',
        ];
    }

    /**
     * Whitelist of list settings + which item fields may be inline-edited.
     * Empty array means rows are plain strings (no field).
     */
    protected function editableListKeys(): array
    {
        return [
            'home_hero_bullets'       => [],
            'home_partners_benefits'  => [],
            'home_stats'              => ['value', 'label'],
            'home_services_items'     => ['title', 'desc'],
            'home_steps'              => ['num', 'title', 'desc'],
            'home_partners_features'  => ['title', 'desc'],
            'home_testimonials'       => ['quote', 'name', 'role'],
        ];
    }

    /** Returns the action object for a given section key (used by the blade). */
    public function actionFor(string $key): ?Action
    {
        return match ($key) {
            'header'       => $this->editHeaderAction(),
            'hero'         => $this->editHeroAction(),
            'stats'        => $this->editStatsAction(),
            'services'     => $this->editServicesAction(),
            'process'      => $this->editProcessAction(),
            'partners'     => $this->editPartnersAction(),
            'testimonials' => $this->editTestimonialsAction(),
            'footer'       => $this->editFooterAction(),
            default        => null,
        };
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label('Open in new tab')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url('/')
                ->openUrlInNewTab()
                ->color('gray'),
        ];
    }
}
