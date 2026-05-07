<?php

namespace App\Filament\Pages;

use App\Filament\Pages\ThemeBuilder\SectionRegistry;
use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Str;

class ThemeBuilder extends Page
{
    protected string $view = 'filament.pages.theme-builder';

    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-squares-2x2'; }
    public static function getNavigationGroup(): string|\UnitEnum|null  { return 'Configuration'; }
    public static function getNavigationSort(): ?int                    { return 3; }
    public static function getNavigationLabel(): string                 { return 'Theme Builder'; }
    public function getTitle(): string                                  { return 'Theme Builder'; }

    // ─────────────────────────────────────────────────────────────────
    // Storage helpers
    // ─────────────────────────────────────────────────────────────────

    /** Returns the current ordered list of placed sections. */
    public function getPlacedSections(): array
    {
        $rows = json_decode(Setting::get('home_sections') ?? '', true);
        return is_array($rows) ? array_values($rows) : [];
    }

    protected function persistSections(array $sections): void
    {
        Setting::set('home_sections', json_encode(array_values($sections)));
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
    // Add Section (modal with type picker)
    // ─────────────────────────────────────────────────────────────────

    public function addSectionAction(): Action
    {
        $options = [];
        foreach (SectionRegistry::types() as $key => $meta) {
            $options[$key] = $meta['label'] . ' — ' . $meta['desc'];
        }

        return Action::make('addSection')
            ->label('Add section')
            ->icon('heroicon-m-plus')
            ->color('primary')
            ->modalHeading('Add a section')
            ->modalDescription('Pick a block to drop on your homepage. You can edit its content and colors right after.')
            ->modalSubmitActionLabel('Add')
            ->modalWidth('lg')
            ->schema([
                Select::make('type')
                    ->label('Section type')
                    ->options($options)
                    ->required()
                    ->native(false)
                    ->searchable(),
            ])
            ->action(function (array $data) {
                $type = $data['type'] ?? null;
                if (! SectionRegistry::exists($type)) return;

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
            });
    }

    // ─────────────────────────────────────────────────────────────────
    // Edit Section (modal scoped to a single section, by id)
    // ─────────────────────────────────────────────────────────────────

    public function editSectionAction(): Action
    {
        return Action::make('editSection')
            ->label('Edit')
            ->icon('heroicon-m-pencil-square')
            ->modalWidth('2xl')
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

    // ─────────────────────────────────────────────────────────────────
    // Header actions
    // ─────────────────────────────────────────────────────────────────

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
