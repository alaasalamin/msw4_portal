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

        /* card customer color in dark */
        .dark .cb-card-customer { color:#9ca3af; }
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
        <div style="margin-left:auto; font-size:12px; color:#9ca3af;">
            {{ $entries->count() }} open {{ Str::plural('entry', $entries->count()) }}
        </div>
    </div>

    {{-- ── Device entries ─────────────────────────────────────────────────── --}}
    @if($entries->isEmpty())
        <div class="cb-empty">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" display="block" style="margin:0 auto 12px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"/>
            </svg>
            <p class="cb-empty-title">No open entries</p>
            <p class="cb-empty-sub">Entries are added here automatically by automation rules.</p>
        </div>
    @else
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
                <span style="font-size:11px; padding:2px 8px; border-radius:12px; background:rgba({{ implode(',', array_map('hexdec', str_split(ltrim($color,'#'),2))) }},.1); color:{{ $color }}; font-weight:600;">
                    {{ $stepDevices->count() }}
                </span>
            </div>

            @if($stepDevices->isEmpty())
                <div style="text-align:center; padding:28px; background:#f9fafb; border-radius:12px; border:1px dashed #e5e7eb; color:#9ca3af; font-size:13px;" class="dark:bg-white/5 dark:border-white/10">
                    No devices at these steps right now.
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
                    <span style="font-size:11px; padding:2px 8px; border-radius:12px; background:rgba(99,102,241,.1); color:#6366f1; font-weight:600;">
                        {{ $submissions->count() }}
                    </span>
                </div>
            </div>

            @if($submissions->isEmpty())
                <div style="text-align:center; padding:32px; background:#f9fafb; border-radius:12px; border:1px dashed #e5e7eb; color:#9ca3af; font-size:13px;">
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
                                            wire:click="deleteSubmission({{ $sub->id }})"
                                            wire:confirm="Delete this submission?"
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

</x-filament-panels::page>
