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
        .cb-chip-step-btn {
            border:none; cursor:pointer;
            transition:background .15s, box-shadow .15s;
        }
        .cb-chip-step-btn:hover {
            background:rgba(99,102,241,.18);
            box-shadow:0 0 0 1.5px rgba(99,102,241,.35);
        }
        .cb-step-option:hover:not(.cb-step-option-active) {
            background:rgba(99,102,241,.05) !important;
        }
        .dark .cb-step-option:hover:not(.cb-step-option-active) {
            background:rgba(99,102,241,.1) !important;
        }
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

        /* row hover via CSS variable */
        :root { --cb-row-hover: rgba(99,102,241,0.04); }
        .dark { --cb-row-hover: rgba(99,102,241,0.08); }

        /* view modal */
        .cb-view-field { margin-bottom:14px; }
        .cb-view-label {
            font-size:10px; font-weight:700; letter-spacing:.06em;
            color:#9ca3af; text-transform:uppercase; margin-bottom:3px;
        }
        .dark .cb-view-label { color:#4b5563; }
        .cb-view-value {
            font-size:13px; color:#111827; line-height:1.55; word-break:break-word;
        }
        .dark .cb-view-value { color:#e2e8f0; }
        .cb-view-divider {
            height:1px; background:#f1f5f9; margin:18px 0;
        }
        .dark .cb-view-divider { background:rgba(255,255,255,0.06); }
        .cb-view-meta {
            font-size:11px; color:#9ca3af; display:flex; flex-wrap:wrap; gap:12px;
        }
        .dark .cb-view-meta { color:#4b5563; }

        /* reply thread inside view modal */
        .cb-reply-thread-item {
            border-left:2px solid #6366f1; padding:10px 14px;
            margin-bottom:10px; border-radius:0 8px 8px 0;
            background:rgba(99,102,241,0.04);
        }
        .dark .cb-reply-thread-item { background:rgba(99,102,241,0.08); }
        .cb-reply-thread-header {
            display:flex; justify-content:space-between; align-items:baseline;
            gap:8px; margin-bottom:6px;
        }
        .cb-reply-thread-subject {
            font-size:12px; font-weight:700; color:#111827;
        }
        .dark .cb-reply-thread-subject { color:#e2e8f0; }
        .cb-reply-thread-time {
            font-size:10px; color:#9ca3af; white-space:nowrap; flex-shrink:0;
        }
        .dark .cb-reply-thread-time { color:#4b5563; }
        .cb-reply-thread-body {
            font-size:12px; color:#374151; line-height:1.6; white-space:pre-wrap;
        }
        .dark .cb-reply-thread-body { color:#94a3b8; }

        /* replied badge */
        .cb-replied-badge {
            display:inline-flex; align-items:center; gap:4px;
            padding:2px 8px; border-radius:20px; font-size:10px; font-weight:700;
            background:rgba(16,185,129,0.1); color:#10b981;
            white-space:nowrap;
        }
        .dark .cb-replied-badge { background:rgba(16,185,129,0.12); color:#34d399; }

        /* preset reply buttons */
        .cb-preset-btn {
            display:inline-flex; align-items:center; gap:4px;
            padding:4px 10px; border-radius:20px; font-size:11px; font-weight:600;
            cursor:pointer; border:1.5px solid #e0e7ff;
            background:#eef2ff; color:#4f46e5; transition:all .15s;
        }
        .cb-preset-btn:hover { background:#e0e7ff; border-color:#6366f1; }
        .dark .cb-preset-btn {
            background:rgba(99,102,241,0.12); border-color:rgba(99,102,241,0.3);
            color:#a5b4fc;
        }
        .dark .cb-preset-btn:hover { background:rgba(99,102,241,0.2); border-color:#6366f1; }

        /* reply modal */
        .cb-reply-icon { background:#ede9fe; }
        .dark .cb-reply-icon { background:rgba(99,102,241,0.15); }

        .cb-reply-success-icon {
            width:48px; height:48px; border-radius:50%; background:#d1fae5;
            display:flex; align-items:center; justify-content:center; margin:0 auto 16px;
        }
        .dark .cb-reply-success-icon { background:rgba(16,185,129,0.15); }

        .cb-field-label {
            font-size:11px; font-weight:600; color:#6b7280;
            display:block; margin-bottom:4px; letter-spacing:.04em;
        }
        .dark .cb-field-label { color:#4b5563; }

        .cb-reply-input {
            width:100%; padding:8px 10px; border-radius:8px;
            border:1.5px solid #e5e7eb; font-size:13px;
            color:#111827; background:#f9fafb;
            outline:none; box-sizing:border-box; transition:border-color .15s;
        }
        .cb-reply-input:focus { border-color:#6366f1; }
        .dark .cb-reply-input {
            background:rgba(255,255,255,0.05);
            border-color:rgba(255,255,255,0.1);
            color:#f3f4f6;
        }
        .dark .cb-reply-input:focus { border-color:#6366f1; }
        .cb-reply-input::placeholder { color:#9ca3af; }
        .dark .cb-reply-input::placeholder { color:#4b5563; }

        textarea.cb-reply-input { resize:vertical; font-family:inherit; }

        .cb-reply-send {
            display:inline-flex; align-items:center; gap:6px;
            padding:8px 18px; border-radius:8px; border:none;
            cursor:pointer; font-size:13px; font-weight:600;
            background:#6366f1; color:#fff; transition:opacity .15s;
        }
        .cb-reply-send:hover { background:#4f46e5; }
        .cb-reply-send:disabled { opacity:.55; cursor:not-allowed; }

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
                        <div class="cb-card">
                            <a href="/admin/devices/{{ $device->id }}" class="cb-link" style="text-decoration:none; display:block;">
                                <div class="cb-card-ticket">{{ $device->ticket_number }}</div>
                                <div class="cb-card-device">{{ $device->brand }} {{ $device->model }}</div>
                                <div class="cb-card-customer">{{ $device->customer_name ?: '—' }}</div>
                            </a>
                            <div class="cb-card-meta" style="margin-top:8px;">
                                <button type="button"
                                    wire:click="openChangeStep({{ $device->id }})"
                                    class="cb-chip cb-chip-step cb-chip-step-btn"
                                    title="Click to change step">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:10px;height:10px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z"/>
                                    </svg>
                                    {{ $device->workflowStep?->label ?? '—' }}
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:8px;height:8px;opacity:.6;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                                    </svg>
                                </button>
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
                        </div>
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
                                <th>Status</th>
                                <th style="width:40px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($submissions as $sub)
                                @php $subEmail = $this->findEmailInData($sub->data ?? []); @endphp
                                <tr wire:click="openView({{ $sub->id }})" style="cursor:pointer;"
                                    class="cb-table-row"
                                    onmouseenter="this.style.background='var(--cb-row-hover)'"
                                    onmouseleave="this.style.background=''"
                                    >
                                    @foreach($fieldLabels as $label)
                                        <td>{{ $sub->data[$label] ?? '—' }}</td>
                                    @endforeach
                                    <td class="muted">{{ $sub->page_slug ?: '—' }}</td>
                                    <td class="muted" style="white-space:nowrap;">{{ $sub->created_at->format('d M Y H:i') }}</td>
                                    <td>
                                        @if($sub->replied_at)
                                            <span class="cb-replied-badge" title="Replied {{ $sub->replied_at->diffForHumans() }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:9px;height:9px;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                                                </svg>
                                                Replied
                                            </span>
                                        @elseif($subEmail)
                                            <span class="muted" style="font-size:10px;">Pending</span>
                                        @else
                                            <span class="muted" style="font-size:10px;">—</span>
                                        @endif
                                    </td>
                                    <td style="text-align:right; white-space:nowrap;" wire:click.stop>
                                        @if($subEmail && !$sub->replied_at)
                                            <button type="button"
                                                wire:click="openReply({{ $sub->id }})"
                                                style="background:none; border:none; cursor:pointer; color:#6366f1; padding:2px; margin-right:4px;"
                                                title="Reply to {{ $subEmail }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:13px;height:13px;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
                                                </svg>
                                            </button>
                                        @elseif($subEmail && $sub->replied_at)
                                            <button type="button"
                                                wire:click="openReply({{ $sub->id }})"
                                                style="background:none; border:none; cursor:pointer; color:#10b981; padding:2px; margin-right:4px; opacity:0.6;"
                                                title="Send another reply to {{ $subEmail }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:13px;height:13px;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
                                                </svg>
                                            </button>
                                        @endif
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

    // ── Badge tracking ─────────────────────────────────────────────────────────
    function updateBadges(counts) {
        Object.entries(counts).forEach(([slug, count]) => {

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

    }

    function fetchAndUpdate() {
        fetch('/admin/api/board-counts', { credentials: 'same-origin' })
            .then(r => r.ok ? r.json() : null)
            .then(counts => { if (counts) updateBadges(counts); })
            .catch(() => {});
    }

    // Align with Livewire's 5 s poll — clear any previous interval first
    if (window._boardPollInterval) clearInterval(window._boardPollInterval);
    fetchAndUpdate();
    window._boardPollInterval = setInterval(fetchAndUpdate, 5000);

    document.addEventListener('livewire:navigating', () => {
        clearInterval(window._boardPollInterval);
        window._boardPollInterval = null;
    }, { once: true });
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

{{-- ── Change step modal ────────────────────────────────────────────────── --}}
@if($changeStepDeviceId)
    @php
        $csDevice = \App\Models\Device::find($changeStepDeviceId);
        $allSteps = $this->getAllSteps();
        $stepsByPhase = $allSteps->groupBy(fn($s) => $s->phase?->name ?? 'General');
    @endphp
    @if($csDevice)
        <div class="cb-modal-backdrop" wire:click.self="cancelChangeStep">
            <div class="cb-modal" style="max-width:400px;">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:18px;">
                    <div>
                        <div class="cb-modal-title">Change step</div>
                        <div class="cb-modal-desc" style="margin-top:2px;">
                            {{ $csDevice->ticket_number }} — {{ $csDevice->brand }} {{ $csDevice->model }}
                        </div>
                    </div>
                    <button type="button" wire:click="cancelChangeStep"
                        style="background:none;border:none;cursor:pointer;color:#9ca3af;padding:2px;margin-left:12px;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;height:18px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div style="display:flex; flex-direction:column; gap:6px; max-height:360px; overflow-y:auto;">
                    @foreach($stepsByPhase as $phase => $steps)
                        <div style="font-size:10px; font-weight:700; letter-spacing:.06em; color:#9ca3af; text-transform:uppercase; padding:4px 0 2px; margin-top:4px;">
                            {{ $phase }}
                        </div>
                        @foreach($steps as $step)
                            <button type="button"
                                wire:click="$set('changeStepValue', {{ $step->id }})"
                                style="text-align:left; padding:9px 12px; border-radius:8px; border:1.5px solid {{ $changeStepValue == $step->id ? '#6366f1' : 'transparent' }}; background:{{ $changeStepValue == $step->id ? 'rgba(99,102,241,.08)' : 'transparent' }}; cursor:pointer; font-size:13px; font-weight:{{ $changeStepValue == $step->id ? '600' : '400' }}; color:{{ $changeStepValue == $step->id ? '#6366f1' : 'inherit' }}; width:100%; transition:all .12s;"
                                class="cb-step-option {{ $changeStepValue == $step->id ? 'cb-step-option-active' : '' }}">
                                @if($changeStepValue == $step->id)
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:12px;height:12px;display:inline;vertical-align:middle;margin-right:4px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                                    </svg>
                                @endif
                                {{ $step->label }}
                            </button>
                        @endforeach
                    @endforeach
                </div>

                <div class="cb-modal-actions" style="margin-top:18px;">
                    <button type="button" class="cb-modal-cancel" wire:click="cancelChangeStep">Cancel</button>
                    <button type="button" class="cb-reply-send"
                        wire:click="applyChangeStep"
                        wire:loading.attr="disabled"
                        wire:target="applyChangeStep"
                        @if(!$changeStepValue || $changeStepValue == $csDevice->workflow_step_id) disabled @endif
                        style="opacity: {{ (!$changeStepValue || $changeStepValue == $csDevice->workflow_step_id) ? '.45' : '1' }}; cursor: {{ (!$changeStepValue || $changeStepValue == $csDevice->workflow_step_id) ? 'not-allowed' : 'pointer' }};">
                        <span wire:loading.remove wire:target="applyChangeStep">Move</span>
                        <span wire:loading wire:target="applyChangeStep">Moving…</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
@endif

{{-- ── View submission modal ────────────────────────────────────────────── --}}
@if($viewSubmissionId)
    @php
        $viewSub   = $this->getSubmissions()->firstWhere('id', $viewSubmissionId);
        $viewEmail = $viewSub ? $this->findEmailInData($viewSub->data ?? []) : null;
        $viewForm  = $board?->form;
        $viewLabels = $viewForm?->fields->pluck('label')->all() ?? [];
    @endphp
    @if($viewSub)
        <div class="cb-modal-backdrop" wire:click.self="closeView">
            <div class="cb-modal" style="max-width:520px; max-height:85vh; overflow-y:auto;">

                {{-- Header --}}
                <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:20px;">
                    <div>
                        <div class="cb-modal-title" style="margin-bottom:4px;">Submission details</div>
                        <div class="cb-view-meta">
                            <span>{{ $viewSub->created_at->format('d M Y, H:i') }}</span>
                            @if($viewSub->page_slug)
                                <span>Page: {{ $viewSub->page_slug }}</span>
                            @endif
                            @if($viewSub->replied_at)
                                <span class="cb-replied-badge">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:9px;height:9px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                                    </svg>
                                    Replied {{ $viewSub->replied_at->diffForHumans() }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <button type="button" wire:click="closeView"
                        style="background:none;border:none;cursor:pointer;color:#9ca3af;padding:2px;margin-left:12px;flex-shrink:0;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;height:18px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="cb-view-divider"></div>

                {{-- Fields --}}
                @foreach($viewLabels as $label)
                    @if(isset($viewSub->data[$label]))
                        <div class="cb-view-field">
                            <div class="cb-view-label">{{ $label }}</div>
                            <div class="cb-view-value">{{ $viewSub->data[$label] ?: '—' }}</div>
                        </div>
                    @endif
                @endforeach

                {{-- Any extra data keys not in form labels --}}
                @foreach($viewSub->data ?? [] as $key => $value)
                    @if(!in_array($key, $viewLabels) && !str_starts_with($key, '_'))
                        <div class="cb-view-field">
                            <div class="cb-view-label">{{ $key }}</div>
                            <div class="cb-view-value">{{ $value ?: '—' }}</div>
                        </div>
                    @endif
                @endforeach

                {{-- Replies thread --}}
                @if(!empty($viewSub->replies))
                    <div class="cb-view-divider"></div>
                    <div style="margin-bottom:4px;">
                        <div class="cb-view-label" style="margin-bottom:12px;">
                            Replies ({{ count($viewSub->replies) }})
                        </div>
                        @foreach(array_reverse($viewSub->replies) as $reply)
                            <div class="cb-reply-thread-item">
                                <div class="cb-reply-thread-header">
                                    <span class="cb-reply-thread-subject">{{ $reply['subject'] }}</span>
                                    <span class="cb-reply-thread-time">{{ \Carbon\Carbon::parse($reply['sent_at'])->diffForHumans() }}</span>
                                </div>
                                <div class="cb-reply-thread-body">{{ $reply['body'] }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="cb-view-divider"></div>

                {{-- Actions --}}
                <div style="display:flex; gap:8px; justify-content:flex-end; flex-wrap:wrap;">
                    <button type="button" class="cb-modal-cancel" wire:click="closeView">Close</button>

                    @if($viewEmail)
                        <button type="button"
                            wire:click="openReply({{ $viewSub->id }})"
                            style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;border:none;cursor:pointer;font-size:13px;font-weight:600;background:#6366f1;color:#fff;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:13px;height:13px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
                            </svg>
                            {{ $viewSub->replied_at ? 'Reply again' : 'Reply' }}
                        </button>
                    @endif

                    <button type="button"
                        wire:click="confirmDeleteSubmission({{ $viewSub->id }})"
                        style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;border:none;cursor:pointer;font-size:13px;font-weight:600;background:#fee2e2;color:#ef4444;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:13px;height:13px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                        </svg>
                        Delete
                    </button>
                </div>

            </div>
        </div>
    @endif
@endif

{{-- ── Reply modal ───────────────────────────────────────────────────────── --}}
@if($replySubmissionId)
    <div class="cb-modal-backdrop" wire:click.self="cancelReply">
        <div class="cb-modal" style="max-width:480px;">
            @if($replySent)
                {{-- Success state --}}
                <div style="text-align:center; padding:12px 0;">
                    <div class="cb-reply-success-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="#10b981" style="width:24px;height:24px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                        </svg>
                    </div>
                    <div class="cb-modal-title">Email sent!</div>
                    <div class="cb-modal-desc" style="margin-bottom:20px;">Your reply was delivered to {{ $replyEmail }}</div>
                    <button type="button" class="cb-modal-cancel" style="width:100%;" wire:click="cancelReply">Close</button>
                </div>
            @else
                {{-- Compose state --}}
                <div class="cb-modal-icon cb-reply-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#6366f1" style="width:22px;height:22px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
                    </svg>
                </div>
                <div class="cb-modal-title">Reply to submission</div>

                {{-- Preset picker --}}
                @php $presets = $board?->form?->preset_replies ?? []; @endphp
                @if(!empty($presets))
                    <div style="margin-bottom:14px;">
                        <label class="cb-field-label">USE PRESET</label>
                        <div style="display:flex; flex-wrap:wrap; gap:6px;">
                            @foreach($presets as $i => $preset)
                                <button type="button"
                                    wire:click="loadPreset({{ $i }})"
                                    class="cb-preset-btn">
                                    {{ $preset['name'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div style="margin-bottom:14px;">
                    <label class="cb-field-label">TO</label>
                    <div style="font-size:13px;color:#6366f1;font-weight:500;">{{ $replyEmail }}</div>
                </div>

                <div style="margin-bottom:12px;">
                    <label class="cb-field-label">SUBJECT</label>
                    <input type="text" wire:model="replySubject" placeholder="Subject…" class="cb-reply-input">
                    @error('replySubject') <div style="color:#ef4444;font-size:11px;margin-top:3px;">{{ $message }}</div> @enderror
                </div>

                <div style="margin-bottom:18px;">
                    <label class="cb-field-label">MESSAGE</label>
                    <textarea wire:model="replyBody" rows="6" placeholder="Write your reply…" class="cb-reply-input"></textarea>
                    @error('replyBody') <div style="color:#ef4444;font-size:11px;margin-top:3px;">{{ $message }}</div> @enderror
                </div>

                <div class="cb-modal-actions">
                    <button type="button" class="cb-modal-cancel" wire:click="cancelReply">Cancel</button>
                    <button type="button" class="cb-reply-send"
                        wire:click="sendReply"
                        wire:loading.attr="disabled"
                        wire:target="sendReply">
                        <span wire:loading.remove wire:target="sendReply">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:13px;height:13px;display:inline;vertical-align:middle;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/>
                            </svg>
                            Send
                        </span>
                        <span wire:loading wire:target="sendReply">Sending…</span>
                    </button>
                </div>
            @endif
        </div>
    </div>
@endif

</x-filament-panels::page>
