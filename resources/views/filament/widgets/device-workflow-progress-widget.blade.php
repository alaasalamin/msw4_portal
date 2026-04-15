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
        .dwp-node { width:30px; height:30px; border-radius:50%; border-width:2px; border-style:solid;
                    display:flex; align-items:center; justify-content:center;
                    font-size:11px; font-weight:700; flex-shrink:0;
                    box-shadow:0 1px 4px rgba(0,0,0,0.08); }
        .dark .dwp-node { box-shadow:0 1px 4px rgba(0,0,0,0.4); }
        /* current step pulse ring */
        @keyframes dwp-pulse { 0%,100%{opacity:.7;transform:scale(1);} 50%{opacity:.3;transform:scale(1.5);} }
    </style>

    <div class="dwp-card">

        {{-- Header --}}
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
            <span class="dwp-title">Reparatur-Fortschritt</span>
            @if($currentStepId)
                @php $step = $this->getDevice()?->workflowStep; @endphp
                <span class="dwp-subtitle">
                    Aktuell: <strong style="color:#374151;">{{ $step?->label }}</strong>
                </span>
            @else
                <span class="dwp-subtitle">Noch kein Schritt zugewiesen</span>
            @endif
        </div>

        @forelse ($phases as $phaseIndex => $phase)
            @php
                $color     = $colors[$phaseIndex % count($colors)];
                [$r,$g,$b] = hexToRgb($color);
                $steps     = $phase->steps;
                $count     = $steps->count();
                if ($count === 0) continue;

                // Determine phase state
                $isCurrentPhase = $phase->id === $currentPhaseId;
                $isBeforePhase  = $currentStepId && $phase->sort_order < $currentPhaseOrder;
                $isAfterPhase   = ! $isCurrentPhase && ! $isBeforePhase;
            @endphp

            <div>
                {{-- Phase pill --}}
                @php
                    $pillOpacity = $isAfterPhase ? '0.4' : '1';
                    $completedCount = $isBeforePhase ? $count : ($isCurrentPhase ? ($currentStepIndex + 1) : 0);
                    $pct = $count > 0 ? round($completedCount / $count * 100) : 0;
                @endphp

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
                        <span style="font-size:11px; color:{{ $color }}; font-weight:600;">
                            {{ $pct }}%
                        </span>
                    @endif
                </div>

                {{-- Progress track --}}
                <div style="position:relative; padding:0 16px; opacity:{{ $pillOpacity }};">

                    {{-- Background track --}}
                    <div style="position:absolute; top:14px; height:3px; border-radius:2px;
                                background:rgba({{ $r }},{{ $g }},{{ $b }},0.15);
                                left:  calc(16px + (100% - 32px) / {{ $count }} / 2);
                                right: calc(16px + (100% - 32px) / {{ $count }} / 2);"></div>

                    {{-- Filled track --}}
                    @if($completedCount > 0 && $count > 1)
                        @php
                            $fillPct = min(100, ($completedCount - 1) / ($count - 1) * 100);
                        @endphp
                        <div style="position:absolute; top:14px; height:3px; border-radius:2px;
                                    background:{{ $color }};
                                    left: calc(16px + (100% - 32px) / {{ $count }} / 2);
                                    width: calc(({{ $fillPct }}% / 100) * (100% - 32px - (100% - 32px) / {{ $count }}));
                                    transition: width 0.5s ease;"></div>
                    @endif

                    {{-- Steps --}}
                    <div style="display:grid; grid-template-columns:repeat({{ $count }}, 1fr); gap:4px; position:relative; z-index:1;">
                        @foreach ($steps as $i => $step)
                            @php
                                $isDone    = $isBeforePhase || ($isCurrentPhase && $i < $currentStepIndex);
                                $isCurrent = $isCurrentPhase && $step->id === $currentStepId;
                                $isNext    = ! $isDone && ! $isCurrent;
                            @endphp

                            <div style="display:flex; flex-direction:column; align-items:center; gap:8px;">

                                {{-- Node --}}
                                <div style="position:relative;">
                                    @if($isCurrent)
                                        {{-- Pulse ring --}}
                                        <div style="position:absolute; inset:-5px; border-radius:50%;
                                                    background:rgba({{ $r }},{{ $g }},{{ $b }},0.25);
                                                    animation:dwp-pulse 2s ease-in-out infinite;"></div>
                                    @endif
                                    <div class="dwp-node" style="
                                        position:relative;
                                        @if($isDone)
                                            background:{{ $color }}; border-color:{{ $color }}; color:#fff;
                                        @elseif($isCurrent)
                                            background:{{ $color }}; border-color:{{ $color }}; color:#fff;
                                            box-shadow: 0 0 0 3px rgba({{ $r }},{{ $g }},{{ $b }},0.3);
                                        @else
                                            background:#f9fafb; border-color:#e5e7eb; color:#d1d5db;
                                        @endif
                                    ">
                                        @if($isDone)
                                            {{-- Checkmark --}}
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                 stroke-width="2.5" stroke="currentColor" style="width:14px;height:14px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                                            </svg>
                                        @else
                                            {{ $i + 1 }}
                                        @endif
                                    </div>
                                </div>

                                {{-- Label --}}
                                <p class="dwp-step-label {{ $isDone ? 'dwp-step-label-done' : ($isCurrent ? 'dwp-step-label-cur' : 'dwp-step-label-next') }}"
                                   style="{{ $isCurrent ? 'color:'.$color.';' : '' }}"
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
            <p style="text-align:center; font-size:13px; color:#9ca3af; padding:20px 0;">
                Keine Phasen konfiguriert.
            </p>
        @endforelse

    </div>
</x-filament-widgets::widget>
