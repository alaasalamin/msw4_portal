<?php

namespace App\Filament\Pages;

use App\Mail\AutomationMail;
use App\Models\CustomPage;
use App\Models\CustomPageEntry;
use App\Models\Device;
use App\Models\FormSubmission;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Mail;

class CustomBoardPage extends Page
{
    protected string $view = 'filament.pages.custom-board-page';

    protected static ?string $slug = 'board';

    // Must be true so Filament calls our getNavigationItems() override
    protected static bool $shouldRegisterNavigation = true;

    public string $boardSlug  = '';
    public ?CustomPage $board = null;
    public string $search     = '';
    public string $lastUpdated = '';

    // Delete modal
    public ?int $deleteSubmissionId = null;

    // View modal
    public ?int $viewSubmissionId = null;

    // Reply modal
    public ?int $replySubmissionId = null;
    public string $replyEmail      = '';
    public string $replySubject    = '';
    public string $replyBody       = '';
    public bool   $replySent       = false;

    public function mount(): void
    {
        $slug              = request()->query('p', '');
        $this->boardSlug   = $slug;
        $this->board       = CustomPage::with('form.fields')->where('slug', $slug)->firstOrFail();
        $this->lastUpdated = now()->format('H:i:s');
    }

    public function refresh(): void
    {
        $this->lastUpdated = now()->format('H:i:s');
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

    public function confirmDeleteSubmission(int $id): void
    {
        $this->viewSubmissionId   = null;
        $this->deleteSubmissionId = $id;
    }

    public function deleteSubmission(): void
    {
        if (! $this->deleteSubmissionId) return;

        FormSubmission::where('id', $this->deleteSubmissionId)
            ->where('form_id', $this->board?->form_id)
            ->delete();

        $this->deleteSubmissionId = null;
    }

    public function cancelDeleteSubmission(): void
    {
        $this->deleteSubmissionId = null;
    }

    // ── View ──────────────────────────────────────────────────────────────────

    public function openView(int $id): void
    {
        $this->viewSubmissionId = $id;
    }

    public function closeView(): void
    {
        $this->viewSubmissionId = null;
    }

    // ── Reply ─────────────────────────────────────────────────────────────────

    /** Find the first email address in a submission's data (checks key names first) */
    public function findEmailInData(array $data): ?string
    {
        // Prefer a field whose label contains "email"
        foreach ($data as $key => $value) {
            if (is_string($value) && stripos($key, 'email') !== false && filter_var($value, FILTER_VALIDATE_EMAIL)) {
                return $value;
            }
        }
        // Fallback: any value that is a valid email
        foreach ($data as $value) {
            if (is_string($value) && filter_var($value, FILTER_VALIDATE_EMAIL)) {
                return $value;
            }
        }
        return null;
    }

    public function openReply(int $id): void
    {
        $sub = FormSubmission::find($id);
        if (! $sub) return;

        $email = $this->findEmailInData($sub->data ?? []);
        if (! $email) return;

        $this->viewSubmissionId  = null; // close view modal if open
        $this->replySubmissionId = $id;
        $this->replyEmail        = $email;
        $this->replySubject      = '';
        $this->replyBody         = '';
        $this->replySent         = false;
    }

    public function loadPreset(int $index): void
    {
        $presets = $this->board?->form?->preset_replies ?? [];
        $preset  = $presets[$index] ?? null;
        if (! $preset) return;

        $sub  = FormSubmission::find($this->replySubmissionId);
        $data = $sub?->data ?? [];

        $subject = $preset['subject'] ?? '';
        $body    = $preset['body']    ?? '';

        foreach ($data as $label => $value) {
            $placeholder = '{{' . $label . '}}';
            $subject     = str_replace($placeholder, (string) ($value ?? ''), $subject);
            $body        = str_replace($placeholder, (string) ($value ?? ''), $body);
        }

        $this->replySubject = $subject;
        $this->replyBody    = $body;
    }

    public function sendReply(): void
    {
        $this->validate([
            'replySubject' => 'required|string|max:255',
            'replyBody'    => 'required|string|max:5000',
        ]);

        Mail::to($this->replyEmail)->send(
            new AutomationMail($this->replySubject, $this->replyBody)
        );

        $sub     = FormSubmission::find($this->replySubmissionId);
        $replies = $sub->replies ?? [];
        $replies[] = [
            'subject'  => $this->replySubject,
            'body'     => $this->replyBody,
            'sent_at'  => now()->toIso8601String(),
        ];
        $sub->replies    = $replies;
        $sub->replied_at = now();
        $sub->save();

        $this->replySent = true;
    }

    public function cancelReply(): void
    {
        $this->replySubmissionId = null;
        $this->replySent         = false;
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

                    // Form submissions (only unreplied)
                    if ($page->form_id) {
                        $total += FormSubmission::where('form_id', $page->form_id)
                            ->whereNull('replied_at')
                            ->count();
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
