<?php

namespace App\Filament\Pages;

use App\Filament\Pages\ThemeBuilder\SectionRegistry;
use App\Models\Setting;
use App\Models\SitePage;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\View as SchemaView;
use Illuminate\Support\Str;

class ThemeBuilder extends Page
{
    protected string $view = 'filament.pages.theme-builder';

    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-squares-2x2'; }
    public static function getNavigationGroup(): string|\UnitEnum|null  { return 'Configuration'; }
    public static function getNavigationSort(): ?int                    { return 3; }
    public static function getNavigationLabel(): string                 { return 'Website Builder'; }
    public function getTitle(): string                                  { return 'Website Builder'; }

    /**
     * Currently-edited page. null = the homepage (stored in `home_sections`
     * Setting). A numeric value = SitePage id (stored in
     * `site_pages.theme_sections`).
     */
    public ?string $currentPageId = null;

    public function mount(): void
    {
        // Allow ?page=xxx to deep-link a specific page.
        $requested = request()->query('page');
        if ($requested === 'home' || $requested === null) {
            $this->currentPageId = null;
        } else {
            // Validate the page exists.
            if (SitePage::whereKey($requested)->exists()) {
                $this->currentPageId = (string) $requested;
            }
        }
    }

    public function selectPage(?string $id): void
    {
        $this->currentPageId = ($id === null || $id === '' || $id === 'home') ? null : $id;
        $this->dispatch('theme-builder:section-saved');
    }

    // ─────────────────────────────────────────────────────────────────
    // Page list / lookup
    // ─────────────────────────────────────────────────────────────────

    /** Pages available for editing in the picker (Homepage + every SitePage). */
    public function getPages(): array
    {
        $rows = [
            ['id' => null, 'title' => 'Homepage', 'slug' => '', 'previewUrl' => '/'],
        ];
        foreach (SitePage::orderBy('title')->get(['id', 'title', 'slug', 'status']) as $p) {
            $rows[] = [
                'id'         => (string) $p->id,
                'title'      => $p->title . ($p->status === 'published' ? '' : ' (draft)'),
                'slug'       => $p->slug,
                'previewUrl' => '/' . $p->slug,
            ];
        }
        return $rows;
    }

    public function getCurrentPage(): array
    {
        foreach ($this->getPages() as $p) {
            if ((string) ($p['id'] ?? '') === (string) ($this->currentPageId ?? '')) {
                return $p;
            }
        }
        return $this->getPages()[0];
    }

    public function getPreviewUrl(): string
    {
        return $this->getCurrentPage()['previewUrl'] ?? '/';
    }

    // ─────────────────────────────────────────────────────────────────
    // Active section storage (homepage Setting OR SitePage column)
    // ─────────────────────────────────────────────────────────────────

    /** Returns the current page's ordered list of placed sections. */
    public function getPlacedSections(): array
    {
        if ($this->currentPageId === null) {
            $rows = json_decode(Setting::get('home_sections') ?? '', true);
            return is_array($rows) ? array_values($rows) : [];
        }
        $page = SitePage::find($this->currentPageId);
        $rows = $page?->theme_sections;
        return is_array($rows) ? array_values($rows) : [];
    }

    protected function persistSections(array $sections): void
    {
        $sections = array_values($sections);
        if ($this->currentPageId === null) {
            Setting::set('home_sections', json_encode($sections));
            return;
        }
        $page = SitePage::find($this->currentPageId);
        if ($page) {
            $page->theme_sections = $sections;
            $page->save();
        }
    }

    protected function findSectionIndex(array $sections, ?string $id): ?int
    {
        if (! $id) return null;
        foreach ($sections as $i => $s) {
            if (($s['id'] ?? null) === $id) return $i;
        }
        return null;
    }

    /** Available section types, used by the picker. */
    public function getSectionTypes(): array
    {
        return SectionRegistry::types();
    }

    // ─────────────────────────────────────────────────────────────────
    // Create new page
    // ─────────────────────────────────────────────────────────────────

    public function editPageAction(): Action
    {
        return Action::make('editPage')
            ->label('Edit page')
            ->iconButton()
            ->icon('heroicon-m-pencil-square')
            ->color('gray')
            ->tooltip('Edit page')
            ->modalHeading(function (): string {
                $page = $this->currentPageId ? SitePage::find($this->currentPageId) : null;
                return $page ? "Edit page — {$page->title}" : 'Edit page';
            })
            ->modalSubmitActionLabel('Save')
            ->modalWidth('md')
            ->fillForm(function () {
                $page = $this->currentPageId ? SitePage::find($this->currentPageId) : null;
                if (! $page) return [];
                return [
                    'title'  => $page->title,
                    'slug'   => $page->slug,
                    'status' => $page->status,
                ];
            })
            ->schema([
                TextInput::make('title')
                    ->label('Page title')
                    ->required()
                    ->maxLength(120),
                TextInput::make('slug')
                    ->label('URL slug')
                    ->required()
                    ->maxLength(120)
                    ->rule('alpha_dash')
                    ->helperText('Reachable at /{slug}.'),
                Select::make('status')
                    ->label('Status')
                    ->options(['draft' => 'Draft', 'published' => 'Published'])
                    ->default('published')
                    ->required(),
            ])
            ->action(function (array $data) {
                $page = $this->currentPageId ? SitePage::find($this->currentPageId) : null;
                if (! $page) {
                    Notification::make()->title('Page not found')->danger()->send();
                    return;
                }
                $newSlug = $data['slug'] ?? $page->slug;
                if ($newSlug !== $page->slug && SitePage::where('slug', $newSlug)->where('id', '!=', $page->id)->exists()) {
                    $newSlug = SitePage::uniqueSlug($data['title'] ?? $page->title);
                }
                $page->title  = $data['title']  ?? $page->title;
                $page->slug   = $newSlug;
                $page->status = $data['status'] ?? $page->status;
                $page->save();

                Notification::make()
                    ->title('Page updated')
                    ->body($page->title)
                    ->success()
                    ->send();

                $this->dispatch('theme-builder:section-saved');
            });
    }

    public function deletePageAction(): Action
    {
        return Action::make('deletePage')
            ->label('Delete page')
            ->iconButton()
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->tooltip('Delete page')
            ->requiresConfirmation()
            ->modalHeading('Delete this page?')
            ->modalDescription('This permanently removes the page and all its sections. The change is immediate and cannot be undone.')
            ->modalSubmitActionLabel('Delete')
            ->action(function () {
                $page = $this->currentPageId ? SitePage::find($this->currentPageId) : null;
                if (! $page) return;
                $title = $page->title;
                $page->delete();
                $this->currentPageId = null;
                Notification::make()->title("Deleted: {$title}")->success()->send();
                $this->dispatch('theme-builder:section-saved');
            });
    }

    public function createPageAction(): Action
    {
        return Action::make('createPage')
            ->label('New page')
            ->icon('heroicon-m-plus')
            ->color('gray')
            ->modalHeading('New page')
            ->modalDescription('Create a new public page. You can drop sections on it as soon as it exists.')
            ->modalSubmitActionLabel('Create')
            ->modalWidth('md')
            ->schema([
                TextInput::make('title')
                    ->label('Page title')
                    ->required()
                    ->maxLength(120)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        if (empty($get('slug'))) {
                            $set('slug', Str::slug((string) $state));
                        }
                    }),
                TextInput::make('slug')
                    ->label('URL slug')
                    ->required()
                    ->maxLength(120)
                    ->helperText('Will be reachable at /{slug}.')
                    ->rule('alpha_dash'),
                Select::make('status')
                    ->label('Status')
                    ->options(['draft' => 'Draft', 'published' => 'Published'])
                    ->default('published')
                    ->required(),
            ])
            ->action(function (array $data) {
                // Avoid slug collisions.
                $slug = SitePage::where('slug', $data['slug'])->exists()
                    ? SitePage::uniqueSlug($data['title'])
                    : $data['slug'];

                $page = SitePage::create([
                    'title'          => $data['title'],
                    'slug'           => $slug,
                    'status'         => $data['status'],
                    'sections'       => [],
                    'theme_sections' => [],
                ]);

                $this->currentPageId = (string) $page->id;

                Notification::make()
                    ->title('Page created')
                    ->body('Now editing: ' . $page->title)
                    ->success()
                    ->send();

                $this->dispatch('theme-builder:section-saved');
            });
    }

    // ─────────────────────────────────────────────────────────────────
    // Add section (modal with type picker)
    // ─────────────────────────────────────────────────────────────────

    public function addSectionAction(): Action
    {
        return Action::make('addSection')
            ->label('Add section')
            ->icon('heroicon-m-plus')
            ->color('primary')
            ->modalHeading('Add a section')
            ->modalDescription(fn () => $this->currentPageId === null
                ? 'Pick a block to drop on the homepage. Header and Footer set the site-wide chrome — every other page inherits them.'
                : 'Pick a content block to drop on this page. The header and footer are inherited from the homepage and edited there.')
            // No submit/cancel — clicking a card directly creates the
            // section and closes the modal via addSectionOfType().
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close')
            ->modalWidth('3xl')
            ->schema([
                SchemaView::make('filament.pages.theme-builder.section-type-picker'),
            ]);
    }

    /**
     * Direct add — invoked by the icon-grid cards in the picker modal.
     * Creates the section and unmounts the open action so the modal closes
     * without requiring a separate "Add" submit click.
     */
    public function addSectionOfType(string $type): void
    {
        if (! SectionRegistry::exists($type)) return;
        // Defensive: never let header/footer land on a non-homepage page.
        if ($this->currentPageId !== null && in_array($type, ['header', 'footer'], true)) return;

        $sections = $this->getPlacedSections();
        $sections[] = [
            'id'       => (string) Str::uuid(),
            'type'     => $type,
            'settings' => SectionRegistry::defaultSettings($type),
        ];
        $this->persistSections($sections);

        Notification::make()
            ->title(SectionRegistry::types()[$type]['label'] . ' section added')
            ->success()
            ->send();

        $this->dispatch('theme-builder:section-saved');

        // Close the picker modal that the click came from.
        $this->unmountAction();
    }

    // ─────────────────────────────────────────────────────────────────
    // Edit Section (modal scoped to a single section, by id)
    // ─────────────────────────────────────────────────────────────────

    public function editSectionAction(): Action
    {
        return Action::make('editSection')
            ->label('Edit')
            ->icon('heroicon-m-pencil-square')
            // Hero gets the wide modal because it ships a click-to-edit
            // visual preview at the top of the form. The other section
            // types stay at 2xl until they get the same treatment.
            ->modalWidth(function (array $arguments) {
                $section = $this->findSection($arguments['id'] ?? null);
                return ($section['type'] ?? null) === 'hero' ? '5xl' : '2xl';
            })
            ->modalSubmitActionLabel('Save')
            ->modalHeading(function (array $arguments): string {
                $section = $this->findSection($arguments['id'] ?? null);
                if (! $section) return 'Edit section';
                $label = SectionRegistry::types()[$section['type']]['label'] ?? ucfirst($section['type']);
                return "Edit {$label}";
            })
            ->fillForm(function (array $arguments) {
                $section = $this->findSection($arguments['id'] ?? null);
                return $section ? ['settings' => $section['settings']] : ['settings' => []];
            })
            ->schema(function (array $arguments) {
                $section = $this->findSection($arguments['id'] ?? null);
                if (! $section) return [];
                return SectionRegistry::schemaFor($section['type']);
            })
            ->action(function (array $arguments, array $data) {
                $id       = $arguments['id'] ?? null;
                $sections = $this->getPlacedSections();
                $i        = $this->findSectionIndex($sections, $id);
                if ($i === null) return;

                $sections[$i]['settings'] = array_merge(
                    $sections[$i]['settings'] ?? [],
                    $data['settings'] ?? [],
                );
                $this->persistSections($sections);

                Notification::make()->title('Section saved')->success()->send();
                $this->dispatch('theme-builder:section-saved');
            });
    }

    public function findSection(?string $id): ?array
    {
        if (! $id) return null;
        foreach ($this->getPlacedSections() as $s) {
            if (($s['id'] ?? null) === $id) return $s;
        }
        return null;
    }

    /** Does the given section type have multiple visual variants to choose from? */
    public function typeHasVariants(string $type): bool
    {
        return SectionRegistry::hasVariants($type);
    }

    public function chooseDesignAction(): Action
    {
        return Action::make('chooseDesign')
            ->label('Choose design')
            ->icon('heroicon-m-swatch')
            ->modalWidth('3xl')
            ->modalHeading(function (array $arguments): string {
                $section = $this->findSection($arguments['id'] ?? null);
                if (! $section) return 'Choose design';
                $label = SectionRegistry::types()[$section['type']]['label'] ?? ucfirst($section['type']);
                return "Choose {$label} design";
            })
            // Cards apply directly on click — no submit button.
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close')
            ->schema([
                SchemaView::make('filament.pages.theme-builder.variant-picker'),
            ]);
    }

    /** Direct apply — invoked by the variant cards in the design picker. */
    public function applyDesignVariant(string $id, string $variant): void
    {
        $sections = $this->getPlacedSections();
        $i        = $this->findSectionIndex($sections, $id);
        if ($i === null) return;

        // Defensive: only accept variant keys that actually exist for this type.
        $type  = $sections[$i]['type'] ?? null;
        $valid = collect(SectionRegistry::variants($type))->pluck('key')->all();
        if (! in_array($variant, $valid, true)) return;

        $sections[$i]['settings'] = array_merge(
            $sections[$i]['settings'] ?? [],
            ['variant' => $variant],
        );
        $this->persistSections($sections);

        Notification::make()->title('Design updated')->success()->send();
        $this->dispatch('theme-builder:section-saved');

        $this->unmountAction();
    }

    // ─────────────────────────────────────────────────────────────────
    // Delete + Reorder (Livewire methods, no modal)
    // ─────────────────────────────────────────────────────────────────

    public function deleteSection(string $id): void
    {
        $sections = $this->getPlacedSections();
        $i        = $this->findSectionIndex($sections, $id);
        if ($i === null) return;

        array_splice($sections, $i, 1);
        $this->persistSections($sections);

        Notification::make()->title('Section removed')->success()->send();
        $this->dispatch('theme-builder:section-saved');
    }

    public function moveSection(string $id, string $direction): void
    {
        $sections = $this->getPlacedSections();
        $i        = $this->findSectionIndex($sections, $id);
        if ($i === null) return;

        $j = $direction === 'up' ? $i - 1 : $i + 1;
        if ($j < 0 || $j >= count($sections)) return;

        [$sections[$i], $sections[$j]] = [$sections[$j], $sections[$i]];
        $this->persistSections($sections);

        $this->dispatch('theme-builder:section-saved');
    }

    /**
     * Persist a new section order from a drag-and-drop reorder. The frontend
     * sends section ids in their new top-to-bottom order; we look each one
     * up in the current section list, rebuild the array in that sequence,
     * and append any sections that weren't in the payload (defensive — should
     * never happen during a normal drag, but keeps state from being lost).
     *
     * @param  array<int, string>  $ids
     */
    public function reorderSections(array $ids): void
    {
        $sections = $this->getPlacedSections();

        $byId = [];
        foreach ($sections as $s) {
            if (! empty($s['id'])) $byId[$s['id']] = $s;
        }

        $reordered = [];
        foreach ($ids as $id) {
            if (isset($byId[$id])) {
                $reordered[] = $byId[$id];
                unset($byId[$id]);
            }
        }
        // Preserve any sections the payload didn't reference (paranoia guard).
        foreach ($byId as $section) $reordered[] = $section;

        $this->persistSections($reordered);
        $this->dispatch('theme-builder:section-saved');
    }

    // ─────────────────────────────────────────────────────────────────
    // Header actions
    // ─────────────────────────────────────────────────────────────────

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label('Open in new tab')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn () => $this->getPreviewUrl())
                ->openUrlInNewTab()
                ->color('gray'),
        ];
    }
}
