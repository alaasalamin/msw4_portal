<x-filament-panels::page>
    @php
        $phases    = $this->getPhases();
        $totalSteps = $phases->sum(fn ($p) => $p->steps->count());
        $colors = ['#3b82f6','#10b981','#f59e0b','#8b5cf6','#ef4444','#06b6d4','#ec4899'];
    @endphp

    <style>
        .wfa-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:20px 24px; }
        .dark .wfa-card { background:#111827; border-color:rgba(255,255,255,0.08); }

        /* Employee avatars */
        .wfa-emp-row { display:inline-flex; align-items:center; gap:6px; margin-left:12px; }
        .wfa-emp-avatar { width:22px; height:22px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; font-size:9px; font-weight:700; color:#fff; flex-shrink:0; border:2px solid #fff; margin-left:-6px; }
        .dark .wfa-emp-avatar { border-color:#111827; }
        .wfa-emp-name { font-size:10px; color:#6b7280; white-space:nowrap; }
        .dark .wfa-emp-name { color:#9ca3af; }
        .wfa-emp-none { font-size:10px; color:#d1d5db; font-style:italic; margin-left:10px; }
        .dark .wfa-emp-none { color:#4b5563; }

        .wfa-header-title { font-size:13px; font-weight:600; color:#374151; }
        .dark .wfa-header-title { color:#f3f4f6; }
        .wfa-header-count { font-size:11px; color:#9ca3af; }
        .dark .wfa-header-count { color:#6b7280; }

        .wfa-divider { height:1px; background:#f3f4f6; margin:20px 0; }
        .dark .wfa-divider { background:rgba(255,255,255,0.06); }

        .wfa-step-label { font-size:10px; line-height:1.35; color:#6b7280; text-align:center; max-width:72px; word-break:break-word; margin:0; }
        .dark .wfa-step-label { color:#9ca3af; }

        .wfa-node {
            width:32px; height:32px; border-radius:50%; border-width:2px; border-style:solid;
            background:#fff; display:flex; align-items:center; justify-content:center;
            font-size:11px; font-weight:700; flex-shrink:0;
            box-shadow:0 1px 4px rgba(0,0,0,0.08);
            cursor:pointer; transition:transform 0.15s, box-shadow 0.15s;
        }
        .wfa-node:hover { transform:scale(1.18); box-shadow:0 4px 12px rgba(0,0,0,0.15); }
        .dark .wfa-node { background:#111827; }
        .dark .wfa-node:hover { box-shadow:0 4px 12px rgba(0,0,0,0.5); }

        /* Modal overlay */
        .wfa-overlay {
            position:fixed; inset:0; z-index:9999;
            background:rgba(0,0,0,0.45); backdrop-filter:blur(3px);
            display:flex; align-items:center; justify-content:center;
            padding:16px;
        }
        .wfa-modal {
            background:#fff; border-radius:14px; width:100%; max-width:480px;
            box-shadow:0 20px 60px rgba(0,0,0,0.2);
            overflow:hidden;
        }
        .dark .wfa-modal { background:#1f2937; }

        .wfa-modal-header {
            display:flex; align-items:center; justify-content:space-between;
            padding:16px 20px; border-bottom:1px solid #f3f4f6;
        }
        .dark .wfa-modal-header { border-bottom-color:rgba(255,255,255,0.07); }

        .wfa-modal-title { font-size:14px; font-weight:600; color:#111827; }
        .dark .wfa-modal-title { color:#f9fafb; }

        .wfa-modal-close {
            width:28px; height:28px; border-radius:6px; border:none; cursor:pointer;
            background:#f3f4f6; color:#6b7280; display:flex; align-items:center; justify-content:center;
            font-size:16px; transition:background 0.15s;
        }
        .wfa-modal-close:hover { background:#e5e7eb; }
        .dark .wfa-modal-close { background:rgba(255,255,255,0.08); color:#9ca3af; }
        .dark .wfa-modal-close:hover { background:rgba(255,255,255,0.12); }

        .wfa-modal-body { padding:20px; }
        .wfa-modal-step-name { font-size:16px; font-weight:600; color:#111827; margin:0 0 4px; }
        .dark .wfa-modal-step-name { color:#f9fafb; }
        .wfa-modal-phase { font-size:12px; color:#9ca3af; margin:0; }
        .dark .wfa-modal-phase { color:#6b7280; }
        .wfa-modal-placeholder { margin-top:16px; padding:14px; border-radius:8px; background:#f9fafb; border:1px dashed #d1d5db; text-align:center; font-size:12px; color:#9ca3af; }
        .dark .wfa-modal-placeholder { background:rgba(255,255,255,0.04); border-color:rgba(255,255,255,0.1); }
    </style>

    {{-- Progress bar --}}
    <div class="wfa-card">

        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
            <span class="wfa-header-title">Klicke auf einen Schritt, um Automationen zu konfigurieren</span>
            <span class="wfa-header-count">{{ $totalSteps }} Schritte</span>
        </div>

        @forelse ($phases as $phaseIndex => $phase)
            @php
                $color = $colors[$phaseIndex % count($colors)];
                $items = $phase->steps;
                $count = $items->count();
                $hex   = ltrim($color, '#');
                $r = hexdec(substr($hex,0,2));
                $g = hexdec(substr($hex,2,2));
                $b = hexdec(substr($hex,4,2));
            @endphp

            @if ($count > 0)
                <div>
                    {{-- Phase pill + responsible employees --}}
                    @php
                        $phaseEmployees = $phase->steps
                            ->flatMap(fn ($s) => $s->employees)
                            ->unique('id')
                            ->values();
                        $avatarColors = ['#3b82f6','#10b981','#f59e0b','#8b5cf6','#ef4444','#06b6d4','#ec4899','#f97316'];
                    @endphp
                    <div style="display:flex; align-items:center; flex-wrap:wrap; gap:8px; margin-bottom:16px;">

                        {{-- Phase pill --}}
                        <div style="display:inline-flex; align-items:center; gap:6px;
                                    background:rgba({{ $r }},{{ $g }},{{ $b }},0.12);
                                    border:1px solid rgba({{ $r }},{{ $g }},{{ $b }},0.35);
                                    border-radius:20px; padding:3px 10px 3px 5px;">
                            <span style="display:inline-flex; align-items:center; justify-content:center;
                                         width:18px; height:18px; border-radius:50%;
                                         background:{{ $color }}; color:#fff; font-size:10px; font-weight:700;">
                                {{ $phaseIndex + 1 }}
                            </span>
                            <span style="font-size:11px; font-weight:600; color:{{ $color }};">{{ $phase->label }}</span>
                        </div>

                        {{-- Divider dot --}}
                        <span style="width:3px; height:3px; border-radius:50%; background:#d1d5db; flex-shrink:0;"></span>

                        {{-- Responsible label --}}
                        <span style="font-size:10px; color:#9ca3af; white-space:nowrap;">Zuständig:</span>

                        @if ($phaseEmployees->isEmpty())
                            <span class="wfa-emp-none">Niemand zugewiesen</span>
                        @else
                            {{-- Stacked avatars --}}
                            <div style="display:inline-flex; align-items:center;">
                                @foreach ($phaseEmployees->take(5) as $ei => $emp)
                                    @php
                                        $initials = collect(explode(' ', $emp->name))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('');
                                        $ac = $avatarColors[$ei % count($avatarColors)];
                                    @endphp
                                    <span class="wfa-emp-avatar"
                                          style="background:{{ $ac }}; z-index:{{ 10 - $ei }};"
                                          title="{{ $emp->name }}">
                                        {{ $initials }}
                                    </span>
                                @endforeach
                                @if ($phaseEmployees->count() > 5)
                                    <span class="wfa-emp-avatar" style="background:#9ca3af; z-index:5;" title="{{ $phaseEmployees->count() - 5 }} weitere">
                                        +{{ $phaseEmployees->count() - 5 }}
                                    </span>
                                @endif
                            </div>

                            {{-- Name list --}}
                            <span class="wfa-emp-name">
                                {{ $phaseEmployees->pluck('name')->join(', ') }}
                            </span>
                        @endif
                    </div>

                    {{-- Track --}}
                    <div style="position:relative; padding:0 16px;">
                        <div style="position:absolute; top:15px; height:2px;
                                    background:rgba({{ $r }},{{ $g }},{{ $b }},0.25); border-radius:2px;
                                    left:  calc(16px + (100% - 32px) / {{ $count }} / 2);
                                    right: calc(16px + (100% - 32px) / {{ $count }} / 2);"></div>

                        <div style="display:grid; grid-template-columns:repeat({{ $count }}, 1fr); gap:4px; position:relative; z-index:1;">
                            @foreach ($items as $i => $step)
                                <div style="display:flex; flex-direction:column; align-items:center; gap:8px;">
                                    <button
                                        type="button"
                                        wire:click="selectStep({{ $step->id }})"
                                        class="wfa-node"
                                        style="border-color:{{ $color }}; color:{{ $color }};"
                                        title="{{ $step->label }}"
                                    >
                                        {{ $i + 1 }}
                                    </button>
                                    <p class="wfa-step-label">{{ $step->label }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                @if (! $loop->last)
                    <div class="wfa-divider"></div>
                @endif
            @endif

        @empty
            <p style="text-align:center; font-size:13px; color:#9ca3af; padding:20px 0;">
                Keine Phasen vorhanden.
            </p>
        @endforelse

    </div>

    {{-- Modal --}}
    @if ($selectedStep)
        <div class="wfa-overlay" wire:click.self="closeModal">
            <div class="wfa-modal">

                <div class="wfa-modal-header">
                    <span class="wfa-modal-title">Schritt konfigurieren</span>
                    <button type="button" class="wfa-modal-close" wire:click="closeModal">✕</button>
                </div>

                <div class="wfa-modal-body">
                    <p class="wfa-modal-step-name">{{ $selectedStep['label'] }}</p>
                    <p class="wfa-modal-phase">{{ $selectedStep['phase'] }}</p>

                    <div class="wfa-modal-placeholder">
                        Automation-Einstellungen erscheinen hier.
                    </div>
                </div>

            </div>
        </div>
    @endif

</x-filament-panels::page>
