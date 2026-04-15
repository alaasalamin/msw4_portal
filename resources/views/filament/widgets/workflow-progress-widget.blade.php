<x-filament-widgets::widget>
    @php
        $phases    = $this->getPhases();
        $totalSteps = $phases->sum(fn ($p) => $p->steps->count());
        $colors = ['#3b82f6','#10b981','#f59e0b','#8b5cf6','#ef4444','#06b6d4','#ec4899'];
    @endphp

    <style>
        .wf-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:20px 24px; }
        .dark .wf-card { background:#111827; border-color:rgba(255,255,255,0.08); }
        .wf-header-title { font-size:13px; font-weight:600; color:#374151; }
        .dark .wf-header-title { color:#f3f4f6; }
        .wf-header-count { font-size:11px; color:#9ca3af; }
        .dark .wf-header-count { color:#6b7280; }
        .wf-divider { height:1px; background:#f3f4f6; margin:20px 0; }
        .dark .wf-divider { background:rgba(255,255,255,0.06); }
        .wf-step-label { font-size:10px; line-height:1.35; color:#6b7280; text-align:center; max-width:72px; word-break:break-word; margin:0; }
        .dark .wf-step-label { color:#9ca3af; }
        .wf-node { width:30px; height:30px; border-radius:50%; border-width:2px; border-style:solid; background:#fff; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; flex-shrink:0; box-shadow:0 1px 4px rgba(0,0,0,0.08); }
        .dark .wf-node { background:#111827; box-shadow:0 1px 4px rgba(0,0,0,0.4); }
    </style>

    <div class="wf-card">

        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
            <span class="wf-header-title">Reparatur-Fortschritt — Übersicht</span>
            <span class="wf-header-count">{{ $totalSteps }} Schritte · {{ $phases->count() }} Phasen</span>
        </div>

        @forelse ($phases as $phaseIndex => $phase)
            @php
                $color = $colors[$phaseIndex % count($colors)];
                $items = $phase->steps;
                $count = $items->count();
                // Extract RGB for rgba usage
                $hex   = ltrim($color, '#');
                $r     = hexdec(substr($hex,0,2));
                $g     = hexdec(substr($hex,2,2));
                $b     = hexdec(substr($hex,4,2));
            @endphp

            @if ($count > 0)
                <div>
                    {{-- Phase pill --}}
                    <div style="display:inline-flex; align-items:center; gap:6px;
                                background:rgba({{ $r }},{{ $g }},{{ $b }},0.12);
                                border:1px solid rgba({{ $r }},{{ $g }},{{ $b }},0.35);
                                border-radius:20px; padding:3px 10px 3px 5px; margin-bottom:16px;">
                        <span style="display:inline-flex; align-items:center; justify-content:center;
                                     width:18px; height:18px; border-radius:50%;
                                     background:{{ $color }}; color:#fff; font-size:10px; font-weight:700;">
                            {{ $phaseIndex + 1 }}
                        </span>
                        <span style="font-size:11px; font-weight:600; color:{{ $color }};">{{ $phase->label }}</span>
                    </div>

                    {{-- Track --}}
                    <div style="position:relative; padding:0 16px;">
                        <div style="position:absolute; top:14px; height:2px;
                                    background:rgba({{ $r }},{{ $g }},{{ $b }},0.25); border-radius:2px;
                                    left:  calc(16px + (100% - 32px) / {{ $count }} / 2);
                                    right: calc(16px + (100% - 32px) / {{ $count }} / 2);"></div>

                        <div style="display:grid; grid-template-columns:repeat({{ $count }}, 1fr); gap:4px; position:relative; z-index:1;">
                            @foreach ($items as $i => $step)
                                <div style="display:flex; flex-direction:column; align-items:center; gap:8px;">
                                    <div class="wf-node" style="border-color:{{ $color }}; color:{{ $color }};">
                                        {{ $i + 1 }}
                                    </div>
                                    <p class="wf-step-label" title="{{ $step->label }}">{{ $step->label }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                @if (! $loop->last)
                    <div class="wf-divider"></div>
                @endif
            @endif

        @empty
            <p style="text-align:center; font-size:13px; color:#9ca3af; padding:20px 0;">
                Keine Phasen vorhanden.
            </p>
        @endforelse

    </div>
</x-filament-widgets::widget>
