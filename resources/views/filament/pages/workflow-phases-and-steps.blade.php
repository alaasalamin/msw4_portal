<x-filament-panels::page>
    @php $phases = $this->getPhases(); @endphp

    <style>
        .ps-wrap { display:flex; flex-direction:column; gap:16px; }

        /* ── Phase card ── */
        .ps-phase {
            border-radius:12px; overflow:hidden;
            border:1px solid rgba(0,0,0,.07);
            background:#fff;
        }
        .dark .ps-phase { background:#111827; border-color:rgba(255,255,255,.08); }

        .ps-phase-header {
            display:flex; align-items:center; gap:10px;
            padding:12px 16px;
            background:#f9fafb;
            border-bottom:1px solid rgba(0,0,0,.05);
        }
        .dark .ps-phase-header { background:#0f172a; border-bottom-color:rgba(255,255,255,.05); }

        .ps-phase-badge {
            min-width:24px; height:24px; border-radius:6px;
            display:inline-flex; align-items:center; justify-content:center;
            font-size:11px; font-weight:800; color:#fff;
            padding:0 6px; flex-shrink:0;
        }
        .ps-phase-name {
            flex:1; font-size:13px; font-weight:700;
            color:#111827;
        }
        .dark .ps-phase-name { color:#f3f4f6; }
        .ps-phase-meta { font-size:11px; color:#9ca3af; }

        /* ── Steps list ── */
        .ps-steps { padding:8px 0; }

        .ps-step-row {
            display:flex; align-items:center; gap:10px;
            padding:8px 16px 8px 44px;
            border-bottom:1px solid rgba(0,0,0,.03);
            transition:background .12s;
        }
        .ps-step-row:last-child { border-bottom:none; }
        .ps-step-row:hover { background:rgba(99,102,241,.03); }
        .dark .ps-step-row { border-bottom-color:rgba(255,255,255,.03); }
        .dark .ps-step-row:hover { background:rgba(99,102,241,.06); }

        .ps-step-num {
            width:20px; height:20px; border-radius:50%;
            border:1.5px solid #d1d5db;
            display:inline-flex; align-items:center; justify-content:center;
            font-size:10px; font-weight:700; color:#9ca3af;
            flex-shrink:0;
        }
        .ps-step-name { flex:1; font-size:13px; color:#374151; }
        .dark .ps-step-name { color:#e5e7eb; }

        /* ── Inline form rows ── */
        .ps-form-row {
            display:flex; align-items:center; gap:8px;
            padding:10px 16px; background:rgba(99,102,241,.04);
            border-top:1px dashed rgba(99,102,241,.2);
        }
        .dark .ps-form-row { background:rgba(99,102,241,.08); }

        .ps-input {
            flex:1; padding:6px 10px; border-radius:7px; font-size:13px;
            border:1px solid #d1d5db; background:#fff; color:#111827;
            outline:none; transition:border-color .15s;
            min-width:0;
        }
        .ps-input:focus { border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,.1); }
        .dark .ps-input { background:#1f2937; border-color:rgba(255,255,255,.12); color:#f3f4f6; }
        .dark .ps-input:focus { border-color:#818cf8; }

        .ps-input-sm { width:64px; flex:none; text-align:center; }

        /* ── Buttons ── */
        .ps-btn {
            display:inline-flex; align-items:center; gap:4px;
            padding:5px 10px; border-radius:7px; border:1px solid;
            font-size:11px; font-weight:600; cursor:pointer;
            background:transparent; transition:background .12s, opacity .12s;
            white-space:nowrap;
        }
        .ps-btn-save   { color:#fff; background:#6366f1; border-color:#6366f1; }
        .ps-btn-save:hover { opacity:.88; }
        .ps-btn-cancel { color:#6b7280; border-color:#d1d5db; }
        .ps-btn-cancel:hover { background:#f3f4f6; }
        .dark .ps-btn-cancel:hover { background:rgba(255,255,255,.06); }
        .ps-btn-edit   { color:#6366f1; border-color:rgba(99,102,241,.3); }
        .ps-btn-edit:hover { background:rgba(99,102,241,.07); }
        .ps-btn-add    { color:#10b981; border-color:rgba(16,185,129,.3); }
        .ps-btn-add:hover { background:rgba(16,185,129,.06); }
        .ps-btn-del    { color:#ef4444; border-color:rgba(239,68,68,.2); }
        .ps-btn-del:hover { background:rgba(239,68,68,.05); }

        /* ── Add phase form ── */
        .ps-add-phase {
            border-radius:12px; border:2px dashed rgba(99,102,241,.25);
            padding:14px 16px;
        }
        .dark .ps-add-phase { border-color:rgba(99,102,241,.3); }
        .ps-add-phase-label { font-size:11px; font-weight:700; letter-spacing:.05em; text-transform:uppercase; color:#9ca3af; margin-bottom:8px; }

        /* ── Step indent connector ── */
        .ps-steps { position:relative; }
        .ps-step-row::before {
            content:''; display:block; width:16px; height:1px;
            background:#e5e7eb; flex-shrink:0; margin-left:-28px;
        }
        .dark .ps-step-row::before { background:rgba(255,255,255,.08); }
    </style>

    @php
        $phaseColors = ['#3b82f6','#10b981','#f59e0b','#8b5cf6','#ef4444','#06b6d4','#ec4899','#f97316'];
        $allPhaseOptions = $phases->pluck('label','id')->toArray();
    @endphp

    <div class="ps-wrap">

        {{-- ── Phase list ─────────────────────────────────────────────── --}}
        @forelse ($phases as $pi => $phase)
            @php $color = $phaseColors[$pi % count($phaseColors)]; @endphp

            <div class="ps-phase">

                {{-- Phase header --}}
                @if ($editPhaseId === $phase->id)
                    <div class="ps-form-row" style="border-top:none; border-bottom:1px dashed rgba(99,102,241,.2);">
                        <span style="font-size:11px; font-weight:700; color:#6366f1; white-space:nowrap;">Phase bearbeiten</span>
                        <input wire:model="editPhaseLabel" class="ps-input" placeholder="Bezeichnung" wire:keydown.enter="savePhase">
                        <input wire:model="editPhaseSortOrder" class="ps-input ps-input-sm" type="number" placeholder="Ord.">
                        <button type="button" class="ps-btn ps-btn-save" wire:click="savePhase">Speichern</button>
                        <button type="button" class="ps-btn ps-btn-cancel" wire:click="cancelEditPhase">Abbrechen</button>
                    </div>
                @else
                    <div class="ps-phase-header">
                        <span class="ps-phase-badge" style="background:{{ $color }};">{{ $pi + 1 }}</span>
                        <span class="ps-phase-name">{{ $phase->label }}</span>
                        <span class="ps-phase-meta">{{ $phase->steps->count() }} Schritte · #{{ $phase->sort_order }}</span>
                        <button type="button" class="ps-btn ps-btn-edit" wire:click="startEditPhase({{ $phase->id }})">✏ Bearbeiten</button>
                        <button type="button" class="ps-btn ps-btn-del"
                            @if($phase->steps->count() > 0) disabled title="Zuerst alle Schritte löschen" style="opacity:.4;cursor:not-allowed;"
                            @else wire:click="deletePhase({{ $phase->id }})" wire:confirm="Phase '{{ addslashes($phase->label) }}' wirklich löschen?"
                            @endif>
                            🗑
                        </button>
                    </div>
                @endif

                {{-- Steps --}}
                <div class="ps-steps">
                    @foreach ($phase->steps as $si => $step)
                        @if ($editStepId === $step->id)
                            <div class="ps-form-row">
                                <span style="font-size:11px; font-weight:700; color:#6366f1; white-space:nowrap;">Schritt bearbeiten</span>
                                <input wire:model="editStepLabel" class="ps-input" placeholder="Bezeichnung" wire:keydown.enter="saveStep">
                                <select wire:model="editStepPhaseId" class="ps-input" style="width:130px;flex:none;">
                                    @foreach($allPhaseOptions as $pid => $plabel)
                                        <option value="{{ $pid }}">{{ $plabel }}</option>
                                    @endforeach
                                </select>
                                <input wire:model="editStepSortOrder" class="ps-input ps-input-sm" type="number" placeholder="Ord.">
                                <button type="button" class="ps-btn ps-btn-save" wire:click="saveStep">Speichern</button>
                                <button type="button" class="ps-btn ps-btn-cancel" wire:click="cancelEditStep">Abbrechen</button>
                            </div>
                        @else
                            <div class="ps-step-row">
                                <span class="ps-step-num">{{ $si + 1 }}</span>
                                <span class="ps-step-name">{{ $step->label }}</span>
                                <span style="font-size:10px; color:#d1d5db;">#{{ $step->sort_order }}</span>
                                <button type="button" class="ps-btn ps-btn-edit" wire:click="startEditStep({{ $step->id }})">✏</button>
                                <button type="button" class="ps-btn ps-btn-del"
                                        wire:click="deleteStep({{ $step->id }})"
                                        wire:confirm="Schritt '{{ addslashes($step->label) }}' wirklich löschen?">🗑</button>
                            </div>
                        @endif
                    @endforeach

                    {{-- Inline add step --}}
                    @if ($addingStepPhaseId === $phase->id)
                        <div class="ps-form-row">
                            <span style="font-size:11px; font-weight:700; color:#10b981; white-space:nowrap;">+ Neuer Schritt</span>
                            <input wire:model="newStepLabel" class="ps-input" placeholder="Bezeichnung" wire:keydown.enter="addStep" autofocus>
                            <input wire:model="newStepSortOrder" class="ps-input ps-input-sm" type="number" placeholder="Ord.">
                            <button type="button" class="ps-btn ps-btn-save" wire:click="addStep">Hinzufügen</button>
                            <button type="button" class="ps-btn ps-btn-cancel" wire:click="cancelAddStep">Abbrechen</button>
                        </div>
                    @else
                        <div style="padding:8px 16px;">
                            <button type="button" class="ps-btn ps-btn-add" wire:click="openAddStep({{ $phase->id }})">
                                + Schritt hinzufügen
                            </button>
                        </div>
                    @endif
                </div>

            </div>
        @empty
            <div style="text-align:center; padding:40px; font-size:13px; color:#9ca3af;">
                Noch keine Phasen vorhanden. Erstelle unten deine erste Phase.
            </div>
        @endforelse

        {{-- ── Add new phase ───────────────────────────────────────────── --}}
        <div class="ps-add-phase">
            <div class="ps-add-phase-label">Neue Phase hinzufügen</div>
            <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                <input wire:model="newPhaseLabel" class="ps-input" placeholder="Bezeichnung, z.B. Diagnose & Vorbereitung"
                       wire:keydown.enter="addPhase">
                <input wire:model="newPhaseSortOrder" class="ps-input ps-input-sm" type="number" placeholder="Ord." style="width:70px;">
                <button type="button" class="ps-btn ps-btn-save" wire:click="addPhase">Phase erstellen</button>
            </div>
        </div>

    </div>
</x-filament-panels::page>
