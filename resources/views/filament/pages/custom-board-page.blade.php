<x-filament-panels::page>

    <style>
        .cb-header {
            display:flex; align-items:center; gap:12px; margin-bottom:20px;
        }
        .cb-icon-wrap {
            width:40px; height:40px; border-radius:10px;
            display:flex; align-items:center; justify-content:center;
            flex-shrink:0;
        }
        .cb-title { font-size:18px; font-weight:700; color:#111827; }
        .dark .cb-title { color:#f9fafb; }
        .cb-desc { font-size:12px; color:#6b7280; margin-top:2px; }

        .cb-grid {
            display:grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap:14px;
        }
        .cb-card {
            background:#fff; border:1px solid #e5e7eb; border-radius:12px;
            padding:16px; position:relative; transition:box-shadow .15s;
        }
        .cb-card:hover { box-shadow:0 4px 16px rgba(0,0,0,.08); }
        .dark .cb-card { background:#1f2937; border-color:rgba(255,255,255,0.08); }

        .cb-card-ticket {
            font-size:10px; font-weight:700; letter-spacing:.05em;
            color:#6366f1; margin-bottom:6px;
        }
        .cb-card-device {
            font-size:14px; font-weight:700; color:#111827; margin-bottom:2px;
        }
        .dark .cb-card-device { color:#f9fafb; }
        .cb-card-customer {
            font-size:12px; color:#6b7280; margin-bottom:10px;
        }

        .cb-card-meta {
            display:flex; flex-wrap:wrap; gap:6px; margin-bottom:10px;
        }
        .cb-chip {
            display:inline-flex; align-items:center; gap:4px;
            padding:3px 8px; border-radius:20px; font-size:10px; font-weight:600;
        }
        .cb-chip-step { background:rgba(99,102,241,.1); color:#6366f1; }
        .cb-chip-box  { background:rgba(16,185,129,.1); color:#10b981; }

        .cb-card-notes {
            font-size:11px; color:#374151; background:#f9fafb; border-radius:7px;
            padding:7px 10px; margin-bottom:10px; line-height:1.5;
        }
        .dark .cb-card-notes { background:rgba(255,255,255,0.05); color:#d1d5db; }

        .cb-card-footer {
            display:flex; align-items:center; justify-content:space-between;
            padding-top:10px; border-top:1px solid #f3f4f6;
        }
        .dark .cb-card-footer { border-color:rgba(255,255,255,0.06); }
        .cb-card-time { font-size:10px; color:#9ca3af; }
        .cb-card-rule { font-size:10px; color:#9ca3af; font-style:italic; }

        .cb-actions { display:flex; gap:6px; }
        .cb-btn {
            padding:4px 10px; border-radius:6px; border:none; cursor:pointer;
            font-size:11px; font-weight:600; transition:opacity .15s;
        }
        .cb-btn-resolve {
            background:rgba(16,185,129,.12); color:#10b981;
        }
        .cb-btn-resolve:hover { opacity:.8; }
        .cb-btn-remove {
            background:rgba(239,68,68,.08); color:#ef4444;
        }
        .cb-btn-remove:hover { opacity:.8; }

        .cb-empty {
            text-align:center; padding:60px 20px; color:#9ca3af;
        }
        .cb-empty svg { width:40px; height:40px; margin:0 auto 12px; opacity:.4; }
        .cb-empty-title { font-size:14px; font-weight:600; margin-bottom:4px; }
        .cb-empty-sub { font-size:12px; }

        .cb-link {
            color:inherit; text-decoration:none;
        }
        .cb-link:hover .cb-card-device { color:#6366f1; }

        /* section headings */
        .cb-section-title { font-size:14px; font-weight:700; color:#111827; }
        .dark .cb-section-title { color:#f9fafb; }

        /* submissions table */
        .cb-table { width:100%; border-collapse:collapse; font-size:12px; }
        .cb-table thead { background:#f9fafb; border-bottom:1px solid #e5e7eb; }
        .dark .cb-table thead { background:rgba(255,255,255,0.04); border-color:rgba(255,255,255,0.08); }
        .cb-table th { padding:10px 14px; text-align:left; font-weight:600; color:#374151; white-space:nowrap; }
        .dark .cb-table th { color:#d1d5db; }
        .cb-table td { padding:10px 14px; color:#374151; vertical-align:top; border-bottom:1px solid #f3f4f6; }
        .dark .cb-table td { color:#d1d5db; border-color:rgba(255,255,255,0.05); }
        .cb-table td.muted { color:#9ca3af; font-size:11px; }
        .dark .cb-table td.muted { color:#6b7280; }
        .cb-table-wrap { overflow-x:auto; border-radius:12px; border:1px solid #e5e7eb; }
        .dark .cb-table-wrap { border-color:rgba(255,255,255,0.08); }

        /* step-devices search input */
        .cb-search {
            width:100%; padding:8px 12px 8px 32px; border-radius:8px; font-size:12px;
            border:1.5px solid #e5e7eb; background:#f9fafb; color:#111827;
            outline:none; transition:border-color .15s, background .15s;
        }
        .cb-search::placeholder { color:#9ca3af; }
        .cb-search:focus { border-color:#6366f1; background:#fff; }
        .dark .cb-search { background:rgba(255,255,255,0.05); border-color:rgba(255,255,255,0.1); color:#f3f4f6; }
        .dark .cb-search::placeholder { color:#4b5563; }
        .dark .cb-search:focus { border-color:#6366f1; background:rgba(255,255,255,0.08); }

        /* card customer color in dark */
        .dark .cb-card-customer { color:#9ca3af; }

        /* sound enable button */
        .cb-sound-btn {
            display:inline-flex; align-items:center; gap:5px;
            padding:4px 10px; border-radius:20px; font-size:10px; font-weight:600;
            cursor:pointer; border:1px dashed #d1d5db; background:transparent;
            color:#9ca3af; transition:all .15s;
        }
        .cb-sound-btn:hover { border-color:#6366f1; color:#6366f1; }
        .dark .cb-sound-btn { border-color:rgba(255,255,255,0.15); color:#4b5563; }
        .dark .cb-sound-btn:hover { border-color:#6366f1; color:#6366f1; }

        /* empty state card */
        .cb-empty-card {
            text-align:center; padding:32px; border-radius:12px;
            border:1px dashed #e5e7eb; color:#9ca3af; font-size:13px;
            background:#f9fafb;
        }
        .dark .cb-empty-card { background:rgba(255,255,255,0.04); border-color:rgba(255,255,255,0.1); color:#6b7280; }

        /* delete confirm modal */
        .cb-modal-backdrop {
            position:fixed; inset:0; z-index:1000;
            background:rgba(0,0,0,0.5); backdrop-filter:blur(2px);
            display:flex; align-items:center; justify-content:center;
        }
        .cb-modal {
            background:#fff; border-radius:16px; padding:28px 28px 24px;
            width:100%; max-width:380px; box-shadow:0 20px 60px rgba(0,0,0,0.2);
        }
        .dark .cb-modal { background:#1e293b; border:1px solid rgba(255,255,255,0.08); }
        .cb-modal-icon {
            width:44px; height:44px; border-radius:50%; background:#fee2e2;
            display:flex; align-items:center; justify-content:center; margin-bottom:16px;
        }
        .dark .cb-modal-icon { background:rgba(239,68,68,0.15); }
        .cb-modal-title {
            font-size:16px; font-weight:700; color:#111827; margin-bottom:6px;
        }
        .dark .cb-modal-title { color:#f1f5f9; }
        .cb-modal-desc {
            font-size:13px; color:#6b7280; margin-bottom:24px; line-height:1.5;
        }
        .dark .cb-modal-desc { color:#94a3b8; }
        .cb-modal-actions { display:flex; gap:10px; justify-content:flex-end; }
        .cb-modal-cancel {
            padding:8px 18px; border-radius:8px; font-size:13px; font-weight:600;
            cursor:pointer; border:1px solid #e5e7eb; background:#f9fafb; color:#374151;
        }
        .dark .cb-modal-cancel { background:rgba(255,255,255,0.06); border-color:rgba(255,255,255,0.1); color:#cbd5e1; }
        .cb-modal-danger {
            padding:8px 18px; border-radius:8px; font-size:13px; font-weight:600;
            cursor:pointer; border:none; background:#ef4444; color:#fff;
        }
        .cb-modal-danger:hover { background:#dc2626; }

        /* live indicator */
        .cb-live {
            display:inline-flex; align-items:center; gap:5px;
            padding:3px 10px; border-radius:20px; font-size:10px; font-weight:700;
            background:rgba(16,185,129,0.1); color:#10b981;
            letter-spacing:.04em;
        }
        .dark .cb-live { background:rgba(16,185,129,0.12); }
        .cb-live-dot {
            width:6px; height:6px; border-radius:50%; background:#10b981;
            animation: cb-pulse 1.8s ease-in-out infinite;
        }
        @keyframes cb-pulse {
            0%, 100% { opacity:1; transform:scale(1); }
            50%       { opacity:.4; transform:scale(.7); }
        }
        .cb-last-updated {
            font-size:10px; color:#9ca3af; margin-left:4px;
        }
        .dark .cb-last-updated { color:#4b5563; }

        /* section counter pill */
        .cb-count {
            display:inline-flex; align-items:center; gap:4px;
            padding:2px 10px; border-radius:20px; font-size:11px; font-weight:700;
            background:#f3f4f6; color:#374151;
        }
        .dark .cb-count { background:rgba(255,255,255,0.08); color:#d1d5db; }
    </style>

    @php
        $entries = $this->getEntries();
        $board   = $this->board;
        $color   = $board?->color ?? '#6366f1';
    @endphp

    {{-- Page header --}}
    <div class="cb-header">
        <div class="cb-icon-wrap" style="background:{{ $color }}20; border:1px solid {{ $color }}40;">
            <x-filament::icon
                :icon="$board?->icon ?? 'heroicon-o-clipboard-document-list'"
                style="width:20px;height:20px;color:{{ $color }};"
            />
        </div>
        <div>
            <div class="cb-title cb-section-title" style="font-size:18px;">{{ $board?->name }}</div>
            @if($board?->description)
                <div class="cb-desc">{{ $board->description }}</div>
            @endif
        </div>
        <div style="margin-left:auto; display:flex; align-items:center; gap:10px; flex-shrink:0;">
            @if($entries->isNotEmpty())
                <span style="font-size:12px; color:#9ca3af;">
                    {{ $entries->count() }} open {{ Str::plural('entry', $entries->count()) }}
                </span>
            @endif
            <button id="cb-sound-btn" class="cb-sound-btn" title="Click to enable bell sound">
                🔔 Enable sound
            </button>
            <span class="cb-live">
                <span class="cb-live-dot"></span> LIVE
            </span>
            <span class="cb-last-updated">{{ $lastUpdated }}</span>
        </div>
    </div>

    {{-- ── Device entries (only shown when there are entries) ─────────────── --}}
    @if($entries->isNotEmpty())
        <div class="cb-grid">
            @foreach($entries as $entry)
                @php $device = $entry->device; @endphp
                <div class="cb-card">
                    <a href="/admin/devices/{{ $device?->id }}" class="cb-link">
                        <div class="cb-card-ticket">{{ $device?->ticket_number ?? '—' }}</div>
                        <div class="cb-card-device">{{ $device?->brand }} {{ $device?->model }}</div>
                        <div class="cb-card-customer">{{ $device?->customer_name ?: '—' }}</div>
                    </a>
                    <div class="cb-card-meta">
                        @if($device?->workflowStep)
                            <span class="cb-chip cb-chip-step">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:10px;height:10px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z"/>
                                </svg>
                                {{ $device->workflowStep->label }}
                            </span>
                        @endif
                        @if($device?->storage_box)
                            <span class="cb-chip cb-chip-box">📦 {{ $device->storage_box }}</span>
                        @endif
                    </div>
                    @if($entry->notes)
                        <div class="cb-card-notes">{{ $entry->notes }}</div>
                    @endif
                    <div class="cb-card-footer">
                        <div>
                            <div class="cb-card-time">{{ $entry->created_at->diffForHumans() }}</div>
                            @if($entry->rule)
                                <div class="cb-card-rule">via {{ $entry->rule->name }}</div>
                            @endif
                        </div>
                        <div class="cb-actions">
                            <button type="button" class="cb-btn cb-btn-resolve" wire:click="resolve({{ $entry->id }})" title="Mark as resolved">✓ Done</button>
                            <button type="button" class="cb-btn cb-btn-remove"  wire:click="remove({{ $entry->id }})"  title="Remove from board">✕</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ── Live-refreshing sections (devices + submissions) ──────────────── --}}
    <div wire:poll.5000ms="refresh">

    {{-- ── Step-filtered devices ───────────────────────────────────────── --}}
    @if(!empty($board?->workflow_step_ids))
        @php $stepDevices = $this->getStepDevices(); @endphp
        <div style="margin-top:{{ $entries->isEmpty() ? '0' : '36px' }};">
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:14px;">
                <span style="display:inline-flex; align-items:center; justify-content:center;
                             width:28px; height:28px; border-radius:8px;
                             background:rgba({{ implode(',', array_map('hexdec', str_split(ltrim($color,'#'),2))) }},.12); color:{{ $color }};">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px;height:14px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 8.25h3m-3 3.75h3M8.25 8.25h.008v.008H8.25V8.25Zm0 3.75h.008v.008H8.25V12Zm0 3.75h.008v.008H8.25v-.008Z"/>
                    </svg>
                </span>
                <span class="cb-section-title">Devices at selected steps</span>
                <span class="cb-count">{{ $stepDevices->count() }} device{{ $stepDevices->count() !== 1 ? 's' : '' }}</span>
            </div>

            {{-- Search bar --}}
            <div style="margin-bottom:14px; position:relative;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                     style="position:absolute; left:11px; top:50%; transform:translateY(-50%); width:14px; height:14px; color:#9ca3af; pointer-events:none;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                </svg>
                <input
                    type="text"
                    wire:model.live.debounce.200ms="search"
                    placeholder="Search ticket, customer, device, box…"
                    class="cb-search"
                >
                @if($search)
                    <button type="button" wire:click="$set('search','')"
                            style="position:absolute; right:10px; top:50%; transform:translateY(-50%);
                                   background:none; border:none; cursor:pointer; color:#9ca3af; line-height:1;">
                        ✕
                    </button>
                @endif
            </div>

            @if($stepDevices->isEmpty())
                <div style="text-align:center; padding:28px; background:#f9fafb; border-radius:12px; border:1px dashed #e5e7eb; color:#9ca3af; font-size:13px;" class="dark:bg-white/5 dark:border-white/10">
                    {{ $search ? 'No devices match "' . $search . '".' : 'No devices at these steps right now.' }}
                </div>
            @else
                <div class="cb-grid">
                    @foreach($stepDevices as $device)
                        <a href="/admin/devices/{{ $device->id }}" class="cb-card cb-link" style="display:block; text-decoration:none;">
                            <div class="cb-card-ticket">{{ $device->ticket_number }}</div>
                            <div class="cb-card-device">{{ $device->brand }} {{ $device->model }}</div>
                            <div class="cb-card-customer">{{ $device->customer_name ?: '—' }}</div>
                            <div class="cb-card-meta" style="margin-top:8px;">
                                <span class="cb-chip cb-chip-step">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:10px;height:10px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z"/>
                                    </svg>
                                    {{ $device->workflowStep?->label ?? '—' }}
                                </span>
                                @if($device->storage_box)
                                    <span class="cb-chip cb-chip-box">📦 {{ $device->storage_box }}</span>
                                @endif
                                @if($device->priority && $device->priority !== 'normal')
                                    <span class="cb-chip" style="background:rgba(239,68,68,.08); color:#ef4444;">
                                        {{ ucfirst($device->priority) }}
                                    </span>
                                @endif
                            </div>
                            @if($device->received_at)
                                <div class="cb-card-footer" style="margin-top:10px;">
                                    <div class="cb-card-time">{{ $device->received_at->diffForHumans() }}</div>
                                </div>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    {{-- ── Form submissions table (shown when a form is linked) ──────────── --}}
    @if($board?->form_id)
        @php $submissions = $this->getSubmissions(); $form = $board->form; @endphp

        <div style="margin-top:36px;">
            {{-- Section header --}}
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;">
                <div style="display:flex; align-items:center; gap:8px;">
                    <span style="display:inline-flex; align-items:center; justify-content:center;
                                 width:28px; height:28px; border-radius:8px;
                                 background:rgba(99,102,241,.1); color:#6366f1;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px;height:14px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z"/>
                        </svg>
                    </span>
                    <span class="cb-section-title">Form Submissions — {{ $form?->name }}</span>
                    <span class="cb-count">{{ $submissions->count() }} submission{{ $submissions->count() !== 1 ? 's' : '' }}</span>
                </div>
            </div>

            @if($submissions->isEmpty())
                <div class="cb-empty-card">
                    No submissions yet.
                </div>
            @else
                {{-- Build column list from form fields --}}
                @php $fieldLabels = $form?->fields->pluck('label')->all() ?? []; @endphp

                <div class="cb-table-wrap">
                    <table class="cb-table">
                        <thead>
                            <tr>
                                @foreach($fieldLabels as $label)
                                    <th>{{ $label }}</th>
                                @endforeach
                                <th>Page</th>
                                <th>Submitted</th>
                                <th style="width:40px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($submissions as $sub)
                                <tr>
                                    @foreach($fieldLabels as $label)
                                        <td>{{ $sub->data[$label] ?? '—' }}</td>
                                    @endforeach
                                    <td class="muted">{{ $sub->page_slug ?: '—' }}</td>
                                    <td class="muted" style="white-space:nowrap;">{{ $sub->created_at->format('d M Y H:i') }}</td>
                                    <td style="text-align:right;">
                                        <button type="button"
                                            wire:click="confirmDeleteSubmission({{ $sub->id }})"
                                            style="background:none; border:none; cursor:pointer; color:#d1d5db; padding:2px;"
                                            title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:13px;height:13px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

    </div>{{-- end wire:poll wrapper --}}

{{-- ── Real-time sidebar badge updater ──────────────────────────────────── --}}
@script
<script>
(function () {
    // Minimal badge HTML that matches Filament's fi-badge structure
    function badgeHtml(count) {
        return `<span class="fi-badge fi-size-sm" style="background-color:rgba(99,102,241,.1);color:rgb(99,102,241);border-color:rgba(99,102,241,.2);border-radius:9999px;padding:1px 8px;font-size:11px;font-weight:700;white-space:nowrap;">
                    <span class="fi-badge-label-ctn"><span class="fi-badge-label">${count}</span></span>
                </span>`;
    }

    // Bell delegates to admin-echo.js which owns the unlocked AudioContext
    function playBell() {
        if (typeof window._playAdminBell === 'function') {
            window._playAdminBell();
        }
    }

    // "Enable sound" button
    const soundBtn = document.getElementById('cb-sound-btn');
    if (soundBtn) {
        // Wait for the auto-unlock attempt, then hide if it succeeded
        (window._bellUnlockReady || Promise.resolve()).then(() => {
            if (window._bellDebug && window._bellDebug().ready) {
                soundBtn.style.display = 'none';
            }
        });

        soundBtn.addEventListener('click', async () => {
            if (typeof window._unlockAndPlayBell === 'function') {
                await window._unlockAndPlayBell();
            }
            soundBtn.style.transition = 'opacity .3s';
            soundBtn.style.opacity = '0';
            setTimeout(() => { soundBtn.style.display = 'none'; }, 350);
        });
    }

    // ── Badge tracking ─────────────────────────────────────────────────────────
    const _prevCounts = {};
    let   _initialized = false; // skip bell on very first poll (baseline load)

    function updateBadges(counts) {
        let anyIncrease = false;

        Object.entries(counts).forEach(([slug, count]) => {
            const prev = _prevCounts[slug] ?? null;
            // Only count as increase after baseline is set
            if (_initialized && count !== null && (prev === null || count > prev)) {
                anyIncrease = true;
            }
            _prevCounts[slug] = count;

            const btn = document.querySelector(
                `.fi-sidebar-item-btn[href*="board?p=${slug}"]`
            );
            if (!btn) return;

            const item     = btn.closest('.fi-sidebar-item');
            let badgeCtn   = item.querySelector('.fi-sidebar-item-badge-ctn');
            let label      = item.querySelector('.fi-badge-label');

            if (count) {
                if (label) {
                    label.textContent = count;
                    if (badgeCtn) badgeCtn.style.removeProperty('display');
                } else if (badgeCtn) {
                    badgeCtn.innerHTML = badgeHtml(count);
                    badgeCtn.style.removeProperty('display');
                } else {
                    badgeCtn = document.createElement('span');
                    badgeCtn.className = 'fi-sidebar-item-badge-ctn';
                    badgeCtn.setAttribute('data-board-badge', slug);
                    badgeCtn.innerHTML = badgeHtml(count);
                    btn.appendChild(badgeCtn);
                }
            } else {
                if (badgeCtn) {
                    if (badgeCtn.dataset.boardBadge) {
                        badgeCtn.remove();
                    } else {
                        badgeCtn.style.display = 'none';
                    }
                }
            }
        });

        _initialized = true;
        if (anyIncrease) playBell();
    }

    function fetchAndUpdate() {
        fetch('/admin/api/board-counts', { credentials: 'same-origin' })
            .then(r => r.ok ? r.json() : null)
            .then(counts => { if (counts) updateBadges(counts); })
            .catch(() => {});
    }

    // Align with Livewire's 5 s poll
    fetchAndUpdate();
    setInterval(fetchAndUpdate, 5000);
})();
</script>
@endscript

{{-- ── Delete submission confirm modal ──────────────────────────────────── --}}
@if($deleteSubmissionId)
    <div class="cb-modal-backdrop" wire:click.self="cancelDeleteSubmission">
        <div class="cb-modal">
            <div class="cb-modal-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#ef4444" style="width:22px;height:22px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                </svg>
            </div>
            <div class="cb-modal-title">Delete this submission?</div>
            <div class="cb-modal-desc">This action cannot be undone. The submission and all its data will be permanently removed.</div>
            <div class="cb-modal-actions">
                <button type="button" class="cb-modal-cancel" wire:click="cancelDeleteSubmission">Cancel</button>
                <button type="button" class="cb-modal-danger" wire:click="deleteSubmission">Delete</button>
            </div>
        </div>
    </div>
@endif

</x-filament-panels::page>
