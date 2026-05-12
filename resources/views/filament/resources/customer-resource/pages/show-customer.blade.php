<x-filament-panels::page>
    @php
        $customer = $this->getRecord();
        $types = \App\Models\ObjectType::orderBy('sort_order')->orderBy('name')->get();
        $counts = [];
        foreach ($types as $t) {
            $table = $t->engineTable();
            $counts[$t->id] = \Illuminate\Support\Facades\Schema::hasTable($table)
                ? (int) \Illuminate\Support\Facades\DB::table($table)->where('customer_id', $customer->id)->count()
                : 0;
        }
    @endphp

    {{-- Light + dark CSS variables (same pattern as Theme Builder / SEO Optimizer). --}}
    <style>
        .cust-panel {
            --cp-bg:           #ffffff;
            --cp-card-bg:      #f9fafb;
            --cp-fg:           #111827;
            --cp-muted:        #6b7280;
            --cp-border:       #e5e7eb;
            --cp-line:         #d4d4d8;
            --cp-accent:       #4f46e5;
            --cp-accent-soft:  rgba(79, 70, 229, .10);
            --cp-accent-ring:  rgba(79, 70, 229, .25);
            --cp-accent-hover: #4338ca;
        }
        .dark .cust-panel,
        html.dark .cust-panel {
            --cp-bg:           #18181b;
            --cp-card-bg:      #27272a;
            --cp-fg:           #f4f4f5;
            --cp-muted:        #a1a1aa;
            --cp-border:       #3f3f46;
            --cp-line:         #52525b;
            --cp-accent:       #818cf8;
            --cp-accent-soft:  rgba(129, 140, 248, .12);
            --cp-accent-ring:  rgba(129, 140, 248, .30);
            --cp-accent-hover: #a5b4fc;
        }

        /* ── Graph layout ─────────────────────────────────────────────── */
        .cust-graph {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 60vh;
            padding: 32px 16px;
            gap: 0;
        }

        .cust-primary-col {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .cust-side {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .cust-tiles {
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
            max-width: 480px;
        }

        .cust-line-h {
            width: 56px;
            height: 2px;
            background: var(--cp-line);
            border-radius: 1px;
            flex-shrink: 0;
        }

        /* ── Tile (square) ────────────────────────────────────────────── */
        .cust-device {
            position: relative;
            width: 112px;
            height: 112px;
            border-radius: 16px;
            background: var(--cp-card-bg);
            border: 1px solid var(--cp-border);
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 6px;
            color: var(--cp-fg);
            cursor: pointer;
            font: inherit;
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
            text-align: center;
        }
        .cust-device:focus { outline: none; }
        .cust-device:focus-visible {
            box-shadow: 0 0 0 3px var(--cp-accent-ring);
        }
        .cust-device:hover {
            transform: translateY(-2px);
            border-color: var(--cp-accent-ring);
            box-shadow: 0 6px 18px rgba(0,0,0,.06);
        }

        .cust-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            min-width: 24px;
            height: 24px;
            padding: 0 7px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            line-height: 1;
            box-shadow: 0 0 0 2px var(--cp-bg);
        }
        .cust-badge--danger {
            background: #dc2626;
            color: #ffffff;
        }
        .cust-badge--zero {
            background: #e5e7eb;
            color: #6b7280;
        }
        .dark .cust-badge--zero,
        html.dark .cust-badge--zero {
            background: #3f3f46;
            color: #a1a1aa;
        }
        .cust-device svg { width: 36px; height: 36px; color: var(--cp-accent); }
        .cust-device-label {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .04em;
            color: var(--cp-muted);
            text-transform: uppercase;
            padding: 0 6px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 100%;
        }

        /* ── Empty-state hint ────────────────────────────────────────── */
        .cust-empty {
            display: inline-flex;
            flex-direction: column;
            gap: 4px;
            padding: 12px 16px;
            border-radius: 12px;
            border: 1px dashed var(--cp-border);
            background: var(--cp-card-bg);
            color: var(--cp-muted);
            font-size: 12px;
            max-width: 240px;
        }
        .cust-empty a { color: var(--cp-accent); text-decoration: none; font-weight: 600; }
        .cust-empty a:hover { text-decoration: underline; }

        /* ── Customer trigger ─────────────────────────────────────────── */
        .cust-trigger {
            background: transparent;
            border: 0;
            padding: 24px 32px;
            border-radius: 24px;
            cursor: pointer;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            gap: 18px;
            transition: transform .2s ease, background-color .2s ease;
            outline: none;
        }
        .cust-trigger:hover .cust-icon-wrap {
            transform: scale(1.05);
            box-shadow: 0 0 0 8px var(--cp-accent-ring);
        }
        .cust-trigger:hover .cust-edit-badge {
            background: var(--cp-accent-hover);
        }
        .cust-trigger:focus-visible {
            box-shadow: 0 0 0 3px var(--cp-accent-ring);
        }

        .cust-icon-wrap {
            position: relative;
            width: 160px;
            height: 160px;
            border-radius: 9999px;
            background: var(--cp-accent-soft);
            color: var(--cp-accent);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 0 1px var(--cp-accent-ring);
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .cust-icon-wrap svg { width: 80px; height: 80px; }

        .cust-edit-badge {
            position: absolute;
            bottom: -4px;
            right: -4px;
            width: 44px;
            height: 44px;
            border-radius: 9999px;
            background: var(--cp-accent);
            color: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 0 4px var(--cp-bg), 0 6px 14px rgba(0,0,0,.12);
            transition: background-color .2s ease;
        }
        .cust-edit-badge svg { width: 20px; height: 20px; }

        .cust-name {
            font-size: 20px;
            font-weight: 700;
            color: var(--cp-fg);
            line-height: 1.2;
        }
        .cust-email {
            font-size: 14px;
            color: var(--cp-muted);
        }
        .cust-hint {
            margin-top: 6px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--cp-accent);
        }
    </style>

    {{-- Hidden triggers — render modal wiring; visible buttons call mountAction(...) --}}
    <div style="display:none;">
        {{ $this->editAction }}
        {{ $this->viewObjectsAction }}
    </div>

    @php
        // Spider-web geometry: customer in the center, types radiating out.
        $size       = 620;            // square container, px
        $center     = $size / 2;
        $radius     = 230;            // tile center distance from container center
        $count      = $types->count();
        $n          = max($count, 1);

        $positions = [];
        foreach ($types as $i => $type) {
            // Start at the top (-90°) and walk clockwise.
            $angle  = (($i / $n) * 360) - 90;
            $rad    = deg2rad($angle);
            $positions[$type->id] = [
                'x' => $center + cos($rad) * $radius,
                'y' => $center + sin($rad) * $radius,
            ];
        }
    @endphp

    <div class="cust-panel" style="display:flex; align-items:center; justify-content:center; min-height:70vh; padding:24px 16px;">
        @if ($count === 0)
            <div class="cust-empty">
                No object types yet. Define one in
                <a href="{{ route('filament.admin.resources.object-types.index') }}">Object Engine → Object Types</a>
                and each will appear here, radiating out from this customer.
            </div>
        @else
            <div
                class="cust-spider"
                style="position:relative; width:{{ $size }}px; height:{{ $size }}px; max-width:100%;"
            >
                {{-- Connector lines (behind the buttons) --}}
                <svg
                    width="{{ $size }}"
                    height="{{ $size }}"
                    viewBox="0 0 {{ $size }} {{ $size }}"
                    style="position:absolute; inset:0; pointer-events:none;"
                    aria-hidden="true"
                >
                    @foreach ($types as $type)
                        <line
                            x1="{{ $center }}"
                            y1="{{ $center }}"
                            x2="{{ $positions[$type->id]['x'] }}"
                            y2="{{ $positions[$type->id]['y'] }}"
                            stroke="var(--cp-line)"
                            stroke-width="2"
                            stroke-linecap="round"
                        />
                    @endforeach
                </svg>

                {{-- Customer in the dead center --}}
                <button
                    type="button"
                    class="cust-trigger"
                    wire:click="mountAction('edit')"
                    style="position:absolute; left:{{ $center }}px; top:{{ $center }}px; transform:translate(-50%, -50%); padding:0;"
                >
                    <span class="cust-icon-wrap">
                        {{-- Heroicon user (outline) --}}
                        <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <span class="cust-edit-badge">
                            {{-- Heroicon pencil-square (mini) --}}
                            <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487 18.549 2.799a2.121 2.121 0 1 1 3 3L19.862 7.487M16.862 4.487 6.65 14.7a4.5 4.5 0 0 0-1.13 1.897l-1.04 3.46 3.46-1.04a4.5 4.5 0 0 0 1.897-1.13L19.862 7.487M16.862 4.487l3 3" />
                            </svg>
                        </span>
                    </span>
                </button>

                {{-- Object tiles radiating around the customer --}}
                @foreach ($types as $type)
                    @php
                        $tileCount = (int) ($counts[$type->id] ?? 0);
                        $iconName  = $type->icon ?: 'heroicon-o-cube';
                        $pos       = $positions[$type->id];
                    @endphp
                    <button
                        type="button"
                        class="cust-device"
                        title="{{ $type->name }}"
                        wire:click="mountAction('viewObjects', { type_id: {{ $type->id }} })"
                        style="position:absolute; left:{{ $pos['x'] }}px; top:{{ $pos['y'] }}px; transform:translate(-50%, -50%);"
                    >
                        <span class="cust-badge {{ $tileCount > 0 ? 'cust-badge--danger' : 'cust-badge--zero' }}">{{ $tileCount }}</span>
                        @svg($iconName, ['style' => 'width:36px;height:36px;'])
                        <span class="cust-device-label">{{ $type->name }}</span>
                    </button>
                @endforeach
            </div>
        @endif
    </div>
</x-filament-panels::page>
