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
        .dwp-node-pending {
            animation: dwp-pending-ring 1s ease-in-out infinite;
        }
        @keyframes dwp-pending-ring {
            0%,100% { box-shadow:0 0 0 3px rgba(99,102,241,.3); }
            50%     { box-shadow:0 0 0 6px rgba(99,102,241,.15); }
        }

        @keyframes dwp-pulse { 0%,100%{opacity:.7;transform:scale(1);} 50%{opacity:.3;transform:scale(1.5);} }

        /* confirmation bar */
        .dwp-confirm-bar {
            display:flex; align-items:center; gap:10px; flex-wrap:wrap;
            margin-bottom:16px; padding:10px 14px; border-radius:10px;
            background:rgba(99,102,241,.08); border:1px solid rgba(99,102,241,.25);
        }
        .dark .dwp-confirm-bar { background:rgba(99,102,241,.13); border-color:rgba(99,102,241,.3); }
        .dwp-confirm-text { flex:1; font-size:12px; font-weight:500; color:#4f46e5; }
        .dark .dwp-confirm-text { color:#818cf8; }
        .dwp-confirm-btn {
            padding:5px 14px; border-radius:7px; border:none; cursor:pointer;
            font-size:12px; font-weight:700;
        }
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
    </style>

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

        {{-- Confirmation bar --}}
        @if ($pendingStepId)
            <div class="dwp-confirm-bar">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:15px;height:15px;flex-shrink:0;color:#6366f1;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
                <span class="dwp-confirm-text">
                    Schritt wechseln zu <strong>„{{ $pendingStepLabel }}"</strong>?
                </span>
                <button type="button" class="dwp-confirm-btn dwp-confirm-btn-yes" wire:click="confirmStep">
                    Bestätigen
                </button>
                <button type="button" class="dwp-confirm-btn dwp-confirm-btn-no" wire:click="cancelStep">
                    Abbrechen
                </button>
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
                                $isDone    = $isBeforePhase || ($isCurrentPhase && $i < $currentStepIndex);
                                $isCurrent = $isCurrentPhase && $step->id === $currentStepId;
                                $isPending = $pendingStepId === $step->id;
                                $isNext    = ! $isDone && ! $isCurrent;
                            @endphp

                            <div style="display:flex; flex-direction:column; align-items:center; gap:8px;">
                                <div style="position:relative;">
                                    @if($isCurrent && !$isPending)
                                        <div style="position:absolute; inset:-5px; border-radius:50%;
                                                    background:rgba({{ $r }},{{ $g }},{{ $b }},0.25);
                                                    animation:dwp-pulse 2s ease-in-out infinite;"></div>
                                    @endif

                                    <button type="button"
                                            wire:click="selectStep({{ $step->id }})"
                                            title="{{ $isCurrent ? 'Aktueller Schritt' : 'Zu diesem Schritt wechseln' }}"
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
