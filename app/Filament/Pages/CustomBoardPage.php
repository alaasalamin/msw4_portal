<?php

namespace App\Filament\Pages;

use App\Models\CustomPage;
use App\Models\CustomPageEntry;
use App\Models\Device;
use App\Models\FormSubmission;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;

class CustomBoardPage extends Page
{
    protected string $view = 'filament.pages.custom-board-page';

    protected static ?string $slug = 'board';

    // Must be true so Filament calls our getNavigationItems() override
    protected static bool $shouldRegisterNavigation = true;

    public string $boardSlug  = '';
    public ?CustomPage $board = null;
    public string $search     = '';

    public function mount(): void
    {
        $slug            = request()->query('p', '');
        $this->boardSlug = $slug;
        $this->board     = CustomPage::with('form.fields')->where('slug', $slug)->firstOrFail();
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

    public function getStepDevices()
    {
        $stepIds = $this->board?->workflow_step_ids ?? [];
        if (empty($stepIds)) return collect();

        $q = Device::with('workflowStep')
            ->whereIn('workflow_step_id', $stepIds);

        if ($term = trim($this->search)) {
            $q->where(fn ($w) => $w
                ->where('ticket_number', 'like', "%$term%")
                ->orWhere('customer_name', 'like', "%$term%")
                ->orWhere('brand',         'like', "%$term%")
                ->orWhere('model',         'like', "%$term%")
                ->orWhere('storage_box',   'like', "%$term%")
                ->orWhere('customer_phone','like', "%$term%")
            );
        }

        return $q->orderBy('received_at', 'desc')->get();
    }

    public function getSubmissions()
    {
        if (! $this->board?->form_id) return collect();

        return FormSubmission::where('form_id', $this->board->form_id)
            ->latest()
            ->get();
    }

    public function deleteSubmission(int $id): void
    {
        FormSubmission::where('id', $id)
            ->where('form_id', $this->board?->form_id)
            ->delete();
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
                ->map(function (CustomPage $page) {
                    $total = 0;

                    // Automation entries
                    $total += CustomPageEntry::where('custom_page_id', $page->id)
                        ->whereNull('resolved_at')
                        ->count();

                    // Form submissions
                    if ($page->form_id) {
                        $total += FormSubmission::where('form_id', $page->form_id)->count();
                    }

                    // Step-filtered devices
                    if (! empty($page->workflow_step_ids)) {
                        $total += Device::whereIn('workflow_step_id', $page->workflow_step_ids)->count();
                    }

                    return NavigationItem::make($page->name)
                        ->url('/admin/board?p=' . $page->slug)
                        ->icon($page->icon ?: 'heroicon-o-clipboard-document-list')
                        ->sort(10 + $page->sort_order)
                        ->badge($total ?: null)
                        ->isActiveWhen(fn () => request()->query('p') === $page->slug);
                })
                ->all();
        } catch (\Throwable) {
            return [];
        }
    }
}
