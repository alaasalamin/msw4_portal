<x-filament-widgets::widget>
    @php
        $data               = $this->getProgressData();
        $phases             = $data['phases'];
        $currentStepId      = $data['currentStepId'];
        $currentPhaseId     = $data['currentPhaseId'];
        $currentStepIndex   = $data['currentStepIndex'];
        $currentPhaseOrder  = $data['currentPhaseOrder'] ?? 0;

        $colors = ['#3b82f6','#10b981','#f59e0b','#8b5cf6','#ef4444','#06b6d4','#ec4899'];

        function hexToRgb(string $hex): array {
            $hex = ltrim($hex, '#');
            return [hexdec(substr($hex,0,2)), hexdec(substr($hex,2,2)), hexdec(substr($hex,4,2))];
        }
    @endphp

    <style>
        .dwp-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:20px 24px; }
        .dark .dwp-card { background:#111827; border-color:rgba(255,255,255,0.08); }
        .dwp-title { font-size:13px; font-weight:600; color:#374151; }
        .dark .dwp-title { color:#f3f4f6; }
        .dwp-subtitle { font-size:11px; color:#9ca3af; }
        .dark .dwp-subtitle { color:#6b7280; }
        .dwp-divider { height:1px; background:#f3f4f6; margin:18px 0; }
        .dark .dwp-divider { background:rgba(255,255,255,0.06); }
        .dwp-step-label { font-size:10px; line-height:1.35; text-align:center; max-width:72px; word-break:break-word; margin:0; }
        .dwp-step-label-done  { color:#6b7280; }
        .dwp-step-label-cur   { font-weight:700; }
        .dwp-step-label-next  { color:#d1d5db; }
        .dark .dwp-step-label-done { color:#9ca3af; }
        .dark .dwp-step-label-next { color:#4b5563; }

        .dwp-node {
            width:30px; height:30px; border-radius:50%; border-width:2px; border-style:solid;
            display:flex; align-items:center; justify-content:center;
            font-size:11px; font-weight:700; flex-shrink:0;
            box-shadow:0 1px 4px rgba(0,0,0,0.08);
            cursor:pointer; transition:transform .15s, box-shadow .15s;
        }
        .dwp-node:hover { transform:scale(1.18); box-shadow:0 4px 12px rgba(0,0,0,.18); }
        .dark .dwp-node { box-shadow:0 1px 4px rgba(0,0,0,0.4); }

        /* pending ring */
        .dwp-node-pending { animation: dwp-pending-ring 1s ease-in-out infinite; }
        @keyframes dwp-pending-ring {
            0%,100% { box-shadow:0 0 0 3px rgba(99,102,241,.3); }
            50%     { box-shadow:0 0 0 6px rgba(99,102,241,.15); }
        }

        @keyframes dwp-pulse { 0%,100%{opacity:.7;transform:scale(1);} 50%{opacity:.3;transform:scale(1.5);} }

        /* has-fields dot indicator */
        .dwp-fields-dot {
            position:absolute; top:-3px; right:-3px;
            width:8px; height:8px; border-radius:50%;
            background:#f59e0b; border:1.5px solid #fff;
        }
        .dark .dwp-fields-dot { border-color:#111827; }

        /* sub-node data chips */
        .dwp-subnodes {
            display:flex; flex-direction:column; align-items:center; gap:4px;
            margin-top:6px;
        }
        .dwp-subnode-line {
            width:1px; height:8px; background:rgba(99,102,241,0.35);
        }
        .dwp-subnode-chip {
            background:rgba(99,102,241,0.08); border:1px solid rgba(99,102,241,0.2);
            border-radius:6px; padding:3px 7px; font-size:9px; line-height:1.4;
            color:#6366f1; max-width:80px; word-break:break-word; text-align:center;
        }
        .dark .dwp-subnode-chip { background:rgba(99,102,241,0.15); border-color:rgba(99,102,241,0.3); color:#818cf8; }
        .dwp-subnode-chip-label { color:#9ca3af; font-size:8px; display:block; }
        .dark .dwp-subnode-chip-label { color:#6b7280; }

        /* confirmation bar */
        .dwp-confirm-bar {
            display:flex; align-items:center; gap:10px; flex-wrap:wrap;
            margin-bottom:16px; padding:10px 14px; border-radius:10px;
            background:rgba(99,102,241,.08); border:1px solid rgba(99,102,241,.25);
        }
        .dark .dwp-confirm-bar { background:rgba(99,102,241,.13); border-color:rgba(99,102,241,.3); }
        .dwp-confirm-text { flex:1; font-size:12px; font-weight:500; color:#4f46e5; }
        .dark .dwp-confirm-text { color:#818cf8; }
        .dwp-confirm-btn { padding:5px 14px; border-radius:7px; border:none; cursor:pointer; font-size:12px; font-weight:700; }
        .dwp-confirm-btn-yes { background:#6366f1; color:#fff; transition:opacity .15s; }
        .dwp-confirm-btn-yes:hover { opacity:.88; }
        .dwp-confirm-btn-no  { background:rgba(107,114,128,.12); color:#6b7280; margin-left:4px; transition:background .15s; }
        .dwp-confirm-btn-no:hover { background:rgba(107,114,128,.2); }

        /* flash */
        .dwp-flash {
            display:flex; align-items:center; gap:6px;
            margin-bottom:14px; padding:8px 14px; border-radius:8px;
            background:rgba(16,185,129,.1); border:1px solid rgba(16,185,129,.25);
            font-size:12px; font-weight:600; color:#10b981;
        }

        /* modal overlay */
        .dwp-modal-overlay {
            position:fixed; inset:0; z-index:9999;
            background:rgba(0,0,0,.45); backdrop-filter:blur(3px);
            display:flex; align-items:center; justify-content:center; padding:16px;
        }
        .dwp-modal {
            background:#fff; border-radius:14px; padding:24px;
            width:100%; max-width:420px; box-shadow:0 20px 60px rgba(0,0,0,.25);
            animation: dwp-modal-in .18s ease-out;
        }
        .dark .dwp-modal { background:#1f2937; border:1px solid rgba(255,255,255,0.1); }
        @keyframes dwp-modal-in { from{opacity:0;transform:scale(.94)} to{opacity:1;transform:scale(1)} }
        .dwp-modal-title {
            font-size:14px; font-weight:700; color:#111827; margin-bottom:4px;
        }
        .dark .dwp-modal-title { color:#f9fafb; }
        .dwp-modal-subtitle {
            font-size:11px; color:#6b7280; margin-bottom:18px;
        }
        .dwp-modal-field { margin-bottom:14px; }
        .dwp-modal-label { display:block; font-size:11px; font-weight:600; color:#374151; margin-bottom:5px; }
        .dark .dwp-modal-label { color:#d1d5db; }
        .dwp-modal-input {
            width:100%; padding:8px 11px; border-radius:8px; font-size:13px;
            border:1.5px solid #e5e7eb; background:#f9fafb; color:#111827;
            transition:border-color .15s;
        }
        .dwp-modal-input:focus { outline:none; border-color:#6366f1; background:#fff; }
        .dark .dwp-modal-input { background:#111827; border-color:rgba(255,255,255,0.12); color:#f3f4f6; }
        .dark .dwp-modal-input:focus { border-color:#6366f1; background:#1a1f35; }
        .dwp-modal-actions { display:flex; gap:8px; margin-top:20px; }
        .dwp-modal-btn-submit {
            flex:1; padding:9px 0; border-radius:8px; border:none; cursor:pointer;
            background:#6366f1; color:#fff; font-size:13px; font-weight:700;
            transition:opacity .15s;
        }
        .dwp-modal-btn-submit:hover { opacity:.88; }
        .dwp-modal-btn-cancel {
            padding:9px 16px; border-radius:8px; border:none; cursor:pointer;
            background:rgba(107,114,128,.12); color:#6b7280; font-size:13px; font-weight:600;
            transition:background .15s;
        }
        .dwp-modal-btn-cancel:hover { background:rgba(107,114,128,.2); }
    </style>

    {{-- Custom-fields modal --}}
    @if ($showFieldModal)
        <div class="dwp-modal-overlay" wire:click.self="cancelModal">
            <div class="dwp-modal">
                <p class="dwp-modal-title">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:14px;height:14px;display:inline;margin-right:4px;color:#6366f1;vertical-align:-2px;">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z" clip-rule="evenodd"/>
                    </svg>
                    Schritt: {{ $modalStepLabel }}
                </p>
                <p class="dwp-modal-subtitle">Bitte die erforderlichen Felder ausfüllen, um diesen Schritt zu aktivieren.</p>

                @foreach ($modalFields as $i => $field)
                    <div class="dwp-modal-field">
                        <label class="dwp-modal-label">
                            {{ $field['label'] }}
                            <span style="color:#ef4444;">*</span>
                        </label>
                        @if(($field['type'] ?? 'text') === 'textarea')
                            <textarea class="dwp-modal-input" rows="3"
                                wire:model="modalValues.{{ $i }}"
                                placeholder="{{ $field['label'] }}..."></textarea>
                        @else
                            <input type="{{ $field['type'] ?? 'text' }}"
                                class="dwp-modal-input"
                                wire:model="modalValues.{{ $i }}"
                                placeholder="{{ $field['label'] }}..."
                                wire:keydown.enter="submitModal">
                        @endif
                    </div>
                @endforeach

                <div class="dwp-modal-actions">
                    <button type="button" class="dwp-modal-btn-cancel" wire:click="cancelModal">Abbrechen</button>
                    <button type="button" class="dwp-modal-btn-submit" wire:click="submitModal">
                        Speichern &amp; Schritt wechseln
                    </button>
                </div>
            </div>
        </div>
    @endif

    <div class="dwp-card">

        {{-- Flash message --}}
        @if ($flashMessage)
            <div class="dwp-flash">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:14px;height:14px;flex-shrink:0;">
                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z"/>
                </svg>
                {{ $flashMessage }}
            </div>
        @endif

        {{-- Confirmation bar (steps without custom fields) --}}
        @if ($pendingStepId)
            <div class="dwp-confirm-bar">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:15px;height:15px;flex-shrink:0;color:#6366f1;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
                <span class="dwp-confirm-text">
                    Schritt wechseln zu <strong>„{{ $pendingStepLabel }}"</strong>?
                </span>
                <button type="button" class="dwp-confirm-btn dwp-confirm-btn-yes" wire:click="confirmStep">Bestätigen</button>
                <button type="button" class="dwp-confirm-btn dwp-confirm-btn-no" wire:click="cancelStep">Abbrechen</button>
            </div>
        @endif

        {{-- Header --}}
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
            <span class="dwp-title">Reparatur-Fortschritt</span>
            @if($currentStepId)
                @php $step = $this->getDevice()?->workflowStep; @endphp
                <span class="dwp-subtitle">
                    Aktuell: <strong style="color:#374151;" class="dark:text-gray-200">{{ $step?->label }}</strong>
                </span>
            @else
                <span class="dwp-subtitle">Noch kein Schritt zugewiesen — klicke einen Schritt an</span>
            @endif
        </div>

        @forelse ($phases as $phaseIndex => $phase)
            @php
                $color     = $colors[$phaseIndex % count($colors)];
                [$r,$g,$b] = hexToRgb($color);
                $steps     = $phase->steps;
                $count     = $steps->count();
                if ($count === 0) continue;

                $isCurrentPhase = $phase->id === $currentPhaseId;
                $isBeforePhase  = $currentStepId && $phase->sort_order < $currentPhaseOrder;
                $isAfterPhase   = ! $isCurrentPhase && ! $isBeforePhase;

                $pillOpacity    = $isAfterPhase ? '0.4' : '1';
                $completedCount = $isBeforePhase ? $count : ($isCurrentPhase ? ($currentStepIndex + 1) : 0);
                $pct            = $count > 0 ? round($completedCount / $count * 100) : 0;
            @endphp

            <div>
                {{-- Phase pill --}}
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; opacity:{{ $pillOpacity }};">
                    <div style="display:inline-flex; align-items:center; gap:6px;
                                background:rgba({{ $r }},{{ $g }},{{ $b }},0.12);
                                border:1px solid rgba({{ $r }},{{ $g }},{{ $b }},0.35);
                                border-radius:20px; padding:3px 10px 3px 5px;">
                        <span style="display:inline-flex; align-items:center; justify-content:center;
                                     width:18px; height:18px; border-radius:50%;
                                     background:{{ $isAfterPhase ? '#d1d5db' : $color }}; color:#fff; font-size:10px; font-weight:700;">
                            {{ $phaseIndex + 1 }}
                        </span>
                        <span style="font-size:11px; font-weight:600; color:{{ $isAfterPhase ? '#9ca3af' : $color }};">
                            {{ $phase->label }}
                        </span>
                    </div>
                    @if(! $isAfterPhase)
                        <span style="font-size:11px; color:{{ $color }}; font-weight:600;">{{ $pct }}%</span>
                    @endif
                </div>

                {{-- Track --}}
                <div style="position:relative; padding:0 16px; opacity:{{ $pillOpacity }};">
                    <div style="position:absolute; top:14px; height:3px; border-radius:2px;
                                background:rgba({{ $r }},{{ $g }},{{ $b }},0.15);
                                left:  calc(16px + (100% - 32px) / {{ $count }} / 2);
                                right: calc(16px + (100% - 32px) / {{ $count }} / 2);"></div>

                    @if($completedCount > 0 && $count > 1)
                        @php $fillPct = min(100, ($completedCount - 1) / ($count - 1) * 100); @endphp
                        <div style="position:absolute; top:14px; height:3px; border-radius:2px;
                                    background:{{ $color }};
                                    left: calc(16px + (100% - 32px) / {{ $count }} / 2);
                                    width: calc(({{ $fillPct }}% / 100) * (100% - 32px - (100% - 32px) / {{ $count }}));
                                    transition: width 0.5s ease;"></div>
                    @endif

                    {{-- Nodes --}}
                    <div style="display:grid; grid-template-columns:repeat({{ $count }}, 1fr); gap:4px; position:relative; z-index:1;">
                        @foreach ($steps as $i => $step)
                            @php
                                $isDone      = $isBeforePhase || ($isCurrentPhase && $i < $currentStepIndex);
                                $isCurrent   = $isCurrentPhase && $step->id === $currentStepId;
                                $isPending   = $pendingStepId === $step->id;
                                $isNext      = ! $isDone && ! $isCurrent;
                                $hasFields   = ! empty($step->custom_fields);
                                $savedValues = $hasFields ? $this->getStepValues($step->id) : [];
                            @endphp

                            <div style="display:flex; flex-direction:column; align-items:center; gap:8px;">
                                <div style="position:relative;">
                                    @if($isCurrent && !$isPending)
                                        <div style="position:absolute; inset:-5px; border-radius:50%;
                                                    background:rgba({{ $r }},{{ $g }},{{ $b }},0.25);
                                                    animation:dwp-pulse 2s ease-in-out infinite;"></div>
                                    @endif

                                    {{-- Yellow dot if step has custom fields --}}
                                    @if($hasFields)
                                        <span class="dwp-fields-dot" title="Hat Pflichtfelder"></span>
                                    @endif

                                    <button type="button"
                                            wire:click="selectStep({{ $step->id }})"
                                            title="{{ $isCurrent ? 'Aktueller Schritt' : ($hasFields ? 'Felder ausfüllen & Schritt wechseln' : 'Zu diesem Schritt wechseln') }}"
                                            class="dwp-node {{ $isPending ? 'dwp-node-pending' : '' }}"
                                            style="position:relative;
                                                @if($isPending)
                                                    background:#6366f1; border-color:#6366f1; color:#fff;
                                                @elseif($isDone)
                                                    background:{{ $color }}; border-color:{{ $color }}; color:#fff;
                                                @elseif($isCurrent)
                                                    background:{{ $color }}; border-color:{{ $color }}; color:#fff;
                                                    box-shadow: 0 0 0 3px rgba({{ $r }},{{ $g }},{{ $b }},0.3);
                                                @else
                                                    background:#f9fafb; border-color:#e5e7eb; color:#9ca3af;
                                                @endif
                                            ">
                                        @if($isPending)
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:13px;height:13px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.042 21.672 13.684 16.6m0 0-2.51 2.225.569-9.47 5.227 7.917-3.286-.672ZM12 2.25V4.5m5.834.166-1.591 1.591M20.25 10.5H18M7.757 14.743l-1.59 1.59M6 10.5H3.75m4.007-4.243-1.59-1.59"/>
                                            </svg>
                                        @elseif($isDone)
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:14px;height:14px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                                            </svg>
                                        @elseif($hasFields && !$isCurrent)
                                            {{-- Fields icon for steps with required inputs --}}
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:13px;height:13px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931ZM16.862 4.487 19.5 7.125"/>
                                            </svg>
                                        @else
                                            {{ $i + 1 }}
                                        @endif
                                    </button>
                                </div>

                                <p class="dwp-step-label {{ $isPending ? '' : ($isDone ? 'dwp-step-label-done' : ($isCurrent ? 'dwp-step-label-cur' : 'dwp-step-label-next')) }}"
                                   style="{{ $isPending ? 'color:#6366f1;font-weight:700;' : ($isCurrent ? 'color:'.$color.';' : '') }}"
                                   title="{{ $step->label }}">
                                    {{ $step->label }}
                                </p>

                                {{-- Sub-node data chips for saved values --}}
                                @if($hasFields && count($savedValues) > 0)
                                    <div class="dwp-subnodes">
                                        <div class="dwp-subnode-line"></div>
                                        @foreach($savedValues as $fieldLabel => $fieldValue)
                                            @if($fieldValue !== '' && $fieldValue !== null)
                                                <div class="dwp-subnode-chip" title="{{ $fieldLabel }}: {{ $fieldValue }}">
                                                    <span class="dwp-subnode-chip-label">{{ $fieldLabel }}</span>
                                                    {{ Str::limit($fieldValue, 18) }}
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            @if(! $loop->last)
                <div class="dwp-divider"></div>
            @endif

        @empty
            <p style="text-align:center; font-size:13px; color:#9ca3af; padding:20px 0;">Keine Phasen konfiguriert.</p>
        @endforelse

    </div>
</x-filament-widgets::widget>
