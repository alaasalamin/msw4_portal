<?php

namespace App\Filament\Pages;

use App\Models\CustomPage;
use App\Models\CustomPageEntry;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;

class CustomBoardPage extends Page
{
    protected string $view = 'filament.pages.custom-board-page';

    // Unique slug per board is handled via getNavigationItems + URL param
    protected static ?string $slug = 'board/{board}';

    // Must be true so Filament calls our getNavigationItems() override
    protected static bool $shouldRegisterNavigation = true;

    public string $boardSlug = '';
    public ?CustomPage $board = null;

    public function mount(string $board): void
    {
        $this->boardSlug = $board;
        $this->board     = CustomPage::where('slug', $board)->firstOrFail();
    }

    public function getTitle(): string
    {
        return $this->board?->name ?? 'Board';
    }

    public function getEntries()
    {
        if (! $this->board) return collect();

        return CustomPageEntry::with(['device.workflowStep', 'rule'])
            ->where('custom_page_id', $this->board->id)
            ->whereNull('resolved_at')
            ->latest()
            ->get();
    }

    /** Mark an entry as resolved (done / handled) */
    public function resolve(int $entryId): void
    {
        CustomPageEntry::where('id', $entryId)
            ->where('custom_page_id', $this->board?->id)
            ->update(['resolved_at' => now()]);
    }

    /** Remove entry from board entirely */
    public function remove(int $entryId): void
    {
        CustomPageEntry::where('id', $entryId)
            ->where('custom_page_id', $this->board?->id)
            ->delete();
    }

    /** Dynamically register one nav item per board */
    public static function getNavigationItems(): array
    {
        try {
            return CustomPage::orderBy('sort_order')
                ->get()
                ->map(fn (CustomPage $page) =>
                    NavigationItem::make($page->name)
                        ->url('/admin/board/' . $page->slug)
                        ->icon($page->icon ?: 'heroicon-o-clipboard-document-list')
                        ->sort(10 + $page->sort_order) // after Dashboard (sort -2/null)
                        ->badge(
                            CustomPageEntry::where('custom_page_id', $page->id)
                                ->whereNull('resolved_at')
                                ->count() ?: null
                        )
                        ->isActiveWhen(fn () => str_contains(request()->url(), '/board/' . $page->slug))
                )
                ->all();
        } catch (\Throwable) {
            return [];
        }
    }
}
