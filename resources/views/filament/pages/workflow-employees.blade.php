<x-filament-panels::page>
    @php
        $employees = $this->getEmployees();
        $phases    = $this->getPhases();
        $colors    = ['#3b82f6','#10b981','#f59e0b','#8b5cf6','#ef4444','#06b6d4','#ec4899'];
        $phaseColors = [];
        foreach ($phases as $i => $phase) {
            $phaseColors[$phase->id] = $colors[$i % count($colors)];
        }
    @endphp

    <style>
        /* ── Card ── */
        .wfe-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; overflow:hidden; }
        .dark .wfe-card { background:#111827; border-color:rgba(255,255,255,0.08); }

        /* ── Employee row ── */
        .wfe-row { display:flex; align-items:center; gap:16px; padding:14px 18px; border-bottom:1px solid #f3f4f6; transition:background 0.1s; }
        .wfe-row:last-child { border-bottom:none; }
        .wfe-row:hover { background:#f9fafb; }
        .dark .wfe-row { border-bottom-color:rgba(255,255,255,0.05); }
        .dark .wfe-row:hover { background:rgba(255,255,255,0.03); }

        /* ── Avatar ── */
        .wfe-avatar { width:36px; height:36px; border-radius:50%; background:#e5e7eb; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700; color:#6b7280; flex-shrink:0; }
        .dark .wfe-avatar { background:rgba(255,255,255,0.08); color:#9ca3af; }

        /* ── Name ── */
        .wfe-name { font-size:13px; font-weight:600; color:#111827; min-width:160px; }
        .dark .wfe-name { color:#f9fafb; }
        .wfe-email { font-size:11px; color:#9ca3af; }

        /* ── Tags ── */
        .wfe-tags { display:flex; flex-wrap:wrap; gap:5px; flex:1; }
        .wfe-tag { display:inline-flex; align-items:center; gap:4px; border-radius:20px; padding:2px 8px; font-size:10px; font-weight:500; border-width:1px; border-style:solid; }
        .wfe-tag-phase { font-size:9px; opacity:0.7; }

        /* ── Empty ── */
        .wfe-empty { font-size:11px; color:#d1d5db; font-style:italic; }
        .dark .wfe-empty { color:#4b5563; }

        /* ── Edit button ── */
        .wfe-edit-btn { margin-left:auto; flex-shrink:0; padding:5px 12px; border-radius:7px; font-size:11px; font-weight:600; border:none; cursor:pointer; background:#f3f4f6; color:#374151; transition:background 0.15s; }
        .wfe-edit-btn:hover { background:#e5e7eb; }
        .dark .wfe-edit-btn { background:rgba(255,255,255,0.07); color:#d1d5db; }
        .dark .wfe-edit-btn:hover { background:rgba(255,255,255,0.12); }

        /* ── Modal ── */
        .wfe-overlay { position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.45); backdrop-filter:blur(3px); display:flex; align-items:center; justify-content:center; padding:16px; }
        .wfe-modal { background:#fff; border-radius:14px; width:100%; max-width:520px; max-height:85vh; display:flex; flex-direction:column; box-shadow:0 20px 60px rgba(0,0,0,0.2); overflow:hidden; }
        .dark .wfe-modal { background:#1f2937; }

        .wfe-modal-header { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid #f3f4f6; flex-shrink:0; }
        .dark .wfe-modal-header { border-bottom-color:rgba(255,255,255,0.07); }
        .wfe-modal-title { font-size:14px; font-weight:600; color:#111827; }
        .dark .wfe-modal-title { color:#f9fafb; }
        .wfe-modal-close { width:28px; height:28px; border-radius:6px; border:none; cursor:pointer; background:#f3f4f6; color:#6b7280; font-size:16px; display:flex; align-items:center; justify-content:center; }
        .wfe-modal-close:hover { background:#e5e7eb; }
        .dark .wfe-modal-close { background:rgba(255,255,255,0.08); color:#9ca3af; }

        .wfe-modal-body { padding:20px; overflow-y:auto; flex:1; }

        /* ── Step checkboxes ── */
        .wfe-phase-label { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:#9ca3af; margin:0 0 8px; }
        .wfe-step-check { display:flex; align-items:center; gap:10px; padding:8px 10px; border-radius:8px; cursor:pointer; transition:background 0.1s; margin-bottom:2px; }
        .wfe-step-check:hover { background:#f9fafb; }
        .dark .wfe-step-check:hover { background:rgba(255,255,255,0.04); }
        .wfe-step-check input[type=checkbox] { width:15px; height:15px; accent-color:#3b82f6; cursor:pointer; flex-shrink:0; }
        .wfe-step-check-label { font-size:12px; color:#374151; cursor:pointer; }
        .dark .wfe-step-check-label { color:#d1d5db; }

        .wfe-modal-footer { padding:14px 20px; border-top:1px solid #f3f4f6; display:flex; justify-content:flex-end; gap:8px; flex-shrink:0; }
        .dark .wfe-modal-footer { border-top-color:rgba(255,255,255,0.07); }
        .wfe-btn-cancel { padding:7px 16px; border-radius:8px; font-size:12px; font-weight:500; border:1px solid #e5e7eb; background:#fff; color:#374151; cursor:pointer; }
        .wfe-btn-cancel:hover { background:#f9fafb; }
        .dark .wfe-btn-cancel { background:transparent; border-color:rgba(255,255,255,0.1); color:#d1d5db; }
        .wfe-btn-save { padding:7px 16px; border-radius:8px; font-size:12px; font-weight:600; border:none; background:#3b82f6; color:#fff; cursor:pointer; }
        .wfe-btn-save:hover { background:#2563eb; }
    </style>

    {{-- ── No employees state ── --}}
    @if ($employees->isEmpty())
        <div style="text-align:center; padding:48px; color:#9ca3af; font-size:13px;">
            Keine Mitarbeiter vorhanden. Lege zuerst Mitarbeiter an.
        </div>
    @else

        {{-- ── Header --}}
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
            <span style="font-size:13px; color:#6b7280;">{{ $employees->count() }} {{ $employees->count() === 1 ? 'Mitarbeiter' : 'Mitarbeiter' }}</span>
        </div>

        {{-- ── Employee list ── --}}
        <div class="wfe-card">
            @foreach ($employees as $employee)
                @php
                    $initials = collect(explode(' ', $employee->name))->map(fn($w) => strtoupper($w[0]))->take(2)->implode('');
                    $steps    = $employee->workflowSteps;
                @endphp
                <div class="wfe-row">

                    {{-- Avatar --}}
                    <div class="wfe-avatar">{{ $initials }}</div>

                    {{-- Name + email --}}
                    <div style="min-width:160px;">
                        <div class="wfe-name">{{ $employee->name }}</div>
                        <div class="wfe-email">{{ $employee->email }}</div>
                    </div>

                    {{-- Assigned step tags --}}
                    <div class="wfe-tags">
                        @forelse ($steps as $step)
                            @php $c = $phaseColors[$step->phase_id] ?? '#6b7280'; $hex = ltrim($c,'#'); $r=hexdec(substr($hex,0,2)); $g=hexdec(substr($hex,2,2)); $b=hexdec(substr($hex,4,2)); @endphp
                            <span class="wfe-tag"
                                  style="background:rgba({{ $r }},{{ $g }},{{ $b }},0.1); border-color:rgba({{ $r }},{{ $g }},{{ $b }},0.3); color:{{ $c }};">
                                <span class="wfe-tag-phase">{{ $step->phase?->label }}</span>
                                · {{ $step->label }}
                            </span>
                        @empty
                            <span class="wfe-empty">Keine Schritte zugewiesen</span>
                        @endforelse
                    </div>

                    {{-- Edit button --}}
                    <button type="button" class="wfe-edit-btn" wire:click="openEdit({{ $employee->id }})">
                        Bearbeiten
                    </button>

                </div>
            @endforeach
        </div>
    @endif

    {{-- ── Assignment modal ── --}}
    @if ($editingEmployeeId)
        <div class="wfe-overlay" wire:click.self="closeEdit">
            <div class="wfe-modal">

                <div class="wfe-modal-header">
                    <span class="wfe-modal-title">{{ $editingEmployeeName }} — Schritte zuweisen</span>
                    <button type="button" class="wfe-modal-close" wire:click="closeEdit">✕</button>
                </div>

                <div class="wfe-modal-body">
                    @foreach ($phases as $phaseIndex => $phase)
                        @if ($phase->steps->isNotEmpty())
                            <p class="wfe-phase-label" style="color:{{ $colors[$phaseIndex % count($colors)] }}; {{ $phaseIndex > 0 ? 'margin-top:20px;' : '' }}">
                                {{ $phase->label }}
                            </p>

                            @foreach ($phase->steps as $step)
                                <label class="wfe-step-check">
                                    <input
                                        type="checkbox"
                                        value="{{ $step->id }}"
                                        wire:model="assignedStepIds"
                                    />
                                    <span class="wfe-step-check-label">{{ $step->label }}</span>
                                </label>
                            @endforeach
                        @endif
                    @endforeach
                </div>

                <div class="wfe-modal-footer">
                    <button type="button" class="wfe-btn-cancel" wire:click="closeEdit">Abbrechen</button>
                    <button type="button" class="wfe-btn-save" wire:click="saveAssignments">Speichern</button>
                </div>

            </div>
        </div>
    @endif

</x-filament-panels::page>
