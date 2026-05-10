<x-filament-panels::page>
    @php
        $sections    = $this->getPlacedSections();
        $types       = $this->getSectionTypes();
        $count       = count($sections);
        $pages       = $this->getPages();
        $currentPage = $this->getCurrentPage();
        $previewUrl  = $this->getPreviewUrl();
    @endphp

    {{-- Make the same Google Fonts the welcome page loads available
         inside the admin so the font picker (and any rendered preview)
         shows each option in its real typeface. --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Calistoga&family=Inter:wght@400;600&family=JetBrains+Mono:wght@500&display=swap">

    {{--
        Theme variables: light defaults on .tb-panel, dark overrides under .dark.
        Filament toggles a `dark` class on <html>, so these flip whenever the
        admin user switches the panel theme from the user menu.
    --}}
    <style>
        .tb-panel {
            --tb-bg:           #ffffff;
            --tb-card-bg:      #f9fafb;
            --tb-icon-bg:      #ffffff;
            --tb-empty-bg:     #fafafa;
            --tb-fg:           #111827;
            --tb-fg-soft:      #374151;
            --tb-muted:        #6b7280;
            --tb-very-muted:   #9ca3af;
            --tb-border:       #e5e7eb;
            --tb-border-soft:  #f3f4f6;
            --tb-counter-bg:   #f3f4f6;
            --tb-tip-bg:       #eff6ff;
            --tb-tip-border:   #bfdbfe;
            --tb-tip-fg:       #1e40af;
            --tb-empty-icon-bg:#eff6ff;
            --tb-empty-icon-fg:#1d4ed8;
            --tb-btn-bg:       #ffffff;
            --tb-btn-disabled: #d1d5db;
            --tb-shadow:       0 1px 2px rgba(0,0,0,.04);
            --tb-danger-fg:    #b91c1c;
            --tb-danger-border:#fecaca;
            --tb-status-ok:    #10b981;
            --tb-link-fg:      #0284c7;
        }
        .tb-design-btn:hover {
            border-color: #0284c7 !important;
            transform: translateY(-1px);
        }
        .tb-design-btn:active { transform: translateY(0); }
        .tb-design-btn:focus-visible {
            outline: 2px solid #0284c7;
            outline-offset: 2px;
        }

        /* Drag handle + row drag affordances */
        .tb-grip {
            flex: 0 0 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--tb-very-muted);
            cursor: grab;
            user-select: none;
            touch-action: none;
            transition: color .12s ease;
        }
        .tb-grip:hover { color: var(--tb-fg-soft); }
        .tb-grip:active,
        .tb-row--dragging .tb-grip { cursor: grabbing; }

        /* Smooth lift on the row that's currently being dragged. The
           surrounding rows stay where they are — only this one grows
           and casts a primary-tinted shadow so the user has a clear
           "picked up" anchor. The transition runs on enter and exit. */
        .tb-row {
            position: relative;
            transition:
                transform   200ms cubic-bezier(0.22, 0.61, 0.36, 1),
                box-shadow  180ms ease,
                border-color 160ms ease;
        }
        .tb-row--dragging {
            transform: scale(1.04);
            box-shadow:
                0 14px 32px -8px rgba(2, 132, 199, 0.35),
                0 6px 14px rgba(15, 23, 42, 0.12);
            border-color: #0284c7 !important;
            z-index: 5;
        }
        /* Drop indicator — a thin primary line on the side the dragged row
           will land on. Implemented via box-shadow so it doesn't shift
           layout the way an actual border would. */
        .tb-row--over-before { box-shadow: inset 0 3px 0 0 #0284c7; }
        .tb-row--over-after  { box-shadow: inset 0 -3px 0 0 #0284c7; }
        .dark .tb-panel,
        html.dark .tb-panel {
            --tb-bg:           #18181b;  /* zinc-900 */
            --tb-card-bg:      #27272a;  /* zinc-800 */
            --tb-icon-bg:      #1f1f23;
            --tb-empty-bg:     #1c1c1f;
            --tb-fg:           #f4f4f5;  /* zinc-100 */
            --tb-fg-soft:      #e4e4e7;
            --tb-muted:        #a1a1aa;  /* zinc-400 */
            --tb-very-muted:   #71717a;  /* zinc-500 */
            --tb-border:       #3f3f46;  /* zinc-700 */
            --tb-border-soft:  #2a2a2e;
            --tb-counter-bg:   #3f3f46;
            --tb-tip-bg:       rgba(59,130,246,.10);
            --tb-tip-border:   rgba(59,130,246,.30);
            --tb-tip-fg:       #93c5fd;
            --tb-empty-icon-bg:rgba(59,130,246,.12);
            --tb-empty-icon-fg:#60a5fa;
            --tb-btn-bg:       #27272a;
            --tb-btn-disabled: #52525b;
            --tb-shadow:       0 1px 2px rgba(0,0,0,.4);
            --tb-danger-fg:    #fca5a5;
            --tb-danger-border:rgba(220,38,38,.35);
            --tb-status-ok:    #34d399;
            --tb-link-fg:      #38bdf8;
        }

        /* Toggle-buttons in our edit-section modals: tint each unselected
           option with a light shade of its own color so users can read the
           color at a glance, even without knowing the keyword (info,
           danger, …). The selected option keeps Filament's default solid
           color treatment.

           Each .fi-color-{name} element has --color-50 … --color-950 set
           locally by Filament's utilities.css, so a single rule per state
           reads the right shade no matter which color is on the option. */
        .fi-fo-toggle-buttons:not(.tb-gradient-presets) input:not(:checked) + label.fi-color {
            background: var(--color-50) !important;
            color: var(--color-700) !important;
            box-shadow: inset 0 0 0 1px var(--color-200) !important;
            transition: background-color 120ms ease, color 120ms ease, box-shadow 120ms ease;
        }
        .fi-fo-toggle-buttons:not(.tb-gradient-presets) input:not(:checked) + label.fi-color:hover {
            background: var(--color-100) !important;
        }

        /* Dark mode: deeper translucent tint instead of the light-50 shade. */
        .dark .fi-fo-toggle-buttons:not(.tb-gradient-presets) input:not(:checked) + label.fi-color {
            background: color-mix(in srgb, var(--color-500) 18%, transparent) !important;
            color: var(--color-200) !important;
            box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--color-400) 30%, transparent) !important;
        }
        .dark .fi-fo-toggle-buttons:not(.tb-gradient-presets) input:not(:checked) + label.fi-color:hover {
            background: color-mix(in srgb, var(--color-500) 28%, transparent) !important;
        }

        /* ── Gradient preset swatches ──
           Each option button is painted with the gradient it represents
           via background-image (kept independent of background-color, so
           Filament's hover bg-gray-50 doesn't smother the gradient). The
           hover effect is a darker ring only — colors stay put. */
        .tb-gradient-presets .fi-fo-toggle-buttons-btn-ctn { width: 3.75rem; }
        .tb-gradient-presets label.fi-btn {
            width: 3.75rem; height: 2.25rem;
            padding: 0 !important;
            border-radius: 0.5rem !important;
            background-color: transparent !important;
            box-shadow: inset 0 0 0 1px rgba(15, 23, 42, 0.15) !important;
            transition: box-shadow 120ms ease !important;
        }
        /* Block the generic tinted-toggle rules from repainting these. */
        .tb-gradient-presets input + label.fi-btn,
        .tb-gradient-presets input + label.fi-btn:hover {
            background-color: transparent !important;
        }
        .tb-gradient-presets label.fi-btn:hover {
            box-shadow: inset 0 0 0 2px rgba(15, 23, 42, 0.55) !important;
        }
        .tb-gradient-presets input:checked + label.fi-btn,
        .tb-gradient-presets input:checked + label.fi-btn:hover {
            box-shadow: 0 0 0 2px var(--color-primary-500), inset 0 0 0 2px #ffffff !important;
        }
        .tb-gradient-presets label.fi-btn .fi-btn-label,
        .tb-gradient-presets label.fi-btn .fi-icon { display: none !important; }

        .tb-gradient-presets input[value="sunrise"]  + label.fi-btn { background-image: linear-gradient(to bottom right, #fb923c, #fb7185) !important; }
        .tb-gradient-presets input[value="ocean"]    + label.fi-btn { background-image: linear-gradient(to right,        #0ea5e9, #14b8a6) !important; }
        .tb-gradient-presets input[value="mint"]     + label.fi-btn { background-image: linear-gradient(to bottom,       #a7f3d0, #2dd4bf) !important; }
        .tb-gradient-presets input[value="lavender"] + label.fi-btn { background-image: linear-gradient(to right,        #a78bfa, #f472b6) !important; }
        .tb-gradient-presets input[value="sunset"]   + label.fi-btn { background-image: linear-gradient(to bottom,       #f97316, #dc2626) !important; }
        .tb-gradient-presets input[value="forest"]   + label.fi-btn { background-image: linear-gradient(to bottom,       #22c55e, #14532d) !important; }
        .tb-gradient-presets input[value="sky"]      + label.fi-btn { background-image: linear-gradient(to bottom,       #bae6fd, #6366f1) !important; }
        .tb-gradient-presets input[value="charcoal"] + label.fi-btn { background-image: linear-gradient(to bottom right, #475569, #0f172a) !important; }

        /* ── Font picker ──
           Render each option's label in the actual font it represents,
           so the editor can preview the typeface without applying it. */
        .tb-font-picker label.fi-btn { font-size: 0.95rem !important; letter-spacing: -0.005em; }
        .tb-font-picker input[value="system"]  + label.fi-btn { font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif !important; }
        .tb-font-picker input[value="inter"]   + label.fi-btn { font-family: 'Inter', system-ui, sans-serif !important; font-weight: 600 !important; }
        .tb-font-picker input[value="display"] + label.fi-btn { font-family: 'Calistoga', Georgia, serif !important; }
        .tb-font-picker input[value="serif"]   + label.fi-btn { font-family: Georgia, "Times New Roman", serif !important; font-style: italic; }
        .tb-font-picker input[value="mono"]    + label.fi-btn { font-family: 'JetBrains Mono', ui-monospace, Menlo, monospace !important; font-size: 0.85rem !important; }

        /* ── Edit Text Block modal layout ──
           The text-block edit form is heavy. The modal opens at 5xl wide,
           but each card is capped narrower and aligned to the left so the
           right side stays as empty breathing room — easier to scan than
           a full-width wall of fields. */
        .tb-text-card {
            max-width: 380px !important;
            margin-right: auto !important;
        }
        @media (max-width: 768px) {
            .tb-text-card { max-width: 100% !important; }
        }
    </style>

    <div
        class="tb-panel"
        x-data="themeBuilder(@js(['pages' => $this->getPages()]))"
        x-on:theme-builder:section-saved.window="reloadPreview()"
        style="display:grid; grid-template-columns: 380px 1fr; gap:16px; align-items:start;"
    >
        {{-- ── Section list (left) ─────────────────────────────────── --}}
        <aside style="background:var(--tb-bg); border:1px solid var(--tb-border); border-radius:12px; padding:16px;
                      box-shadow:var(--tb-shadow); position:sticky; top:16px;">

            {{-- Page picker + page-level actions, all in one row --}}
            <div style="display:flex; gap:8px; margin-bottom:12px; align-items:center;">
                <select
                    wire:change="selectPage($event.target.value)"
                    style="flex:1; min-width:0; height:36px; padding:0 10px; border-radius:8px;
                           border:1px solid var(--tb-border); background:var(--tb-btn-bg);
                           color:var(--tb-fg); font-size:13px; font-weight:500; cursor:pointer;"
                >
                    @foreach ($pages as $p)
                        <option
                            value="{{ $p['id'] ?? 'home' }}"
                            @selected((string) ($p['id'] ?? '') === (string) ($currentPage['id'] ?? ''))
                        >{{ $p['title'] }}</option>
                    @endforeach
                </select>
                @if (! empty($currentPage['id']))
                    {{-- Inline icon actions, only when a Site Page is selected --}}
                    <div class="tb-page-actions" style="display:inline-flex; align-items:center; gap:4px;">
                        {{ $this->editPageAction }}
                        {{ $this->deletePageAction }}
                    </div>
                @endif
                <div style="display:inline-flex; align-items:center;">{{ $this->createPageAction }}</div>
            </div>
            <style>
                /* Tighten Filament's default icon-button sizing so the inline row
                   feels balanced against the 36px page picker. */
                .tb-page-actions .fi-icon-btn {
                    width: 36px !important;
                    height: 36px !important;
                }
            </style>
            <div style="font-size:11px; color:var(--tb-muted); margin-bottom:14px; padding-bottom:14px;
                        border-bottom:1px solid var(--tb-border-soft);">
                Editing
                <strong style="color:var(--tb-fg);">{{ $currentPage['title'] }}</strong>
                @if (! empty($currentPage['slug']))
                    <span style="color:var(--tb-very-muted);">· /{{ $currentPage['slug'] }}</span>
                @endif
                @if (! empty($currentPage['id']))
                    <div style="margin-top:6px; padding:6px 10px; background:var(--tb-tip-bg);
                                border:1px solid var(--tb-tip-border); border-radius:6px;
                                color:var(--tb-tip-fg); font-size:10.5px; line-height:1.45;">
                        Header & footer are inherited from the Homepage — switch to <em>Homepage</em> above to edit them.
                    </div>
                @endif
            </div>

            {{-- Add Section CTA --}}
            <div style="margin-bottom:14px;">
                {{ $this->addSectionAction }}
            </div>

            {{-- Header --}}
            <div style="display:flex; align-items:center; justify-content:space-between; gap:8px;
                        padding-bottom:10px; margin-bottom:10px; border-bottom:1px solid var(--tb-border-soft);">
                <div>
                    <h2 style="font-size:13px; font-weight:600; color:var(--tb-fg); margin:0;">
                        Page sections
                        <span style="display:inline-block; margin-left:6px; padding:1px 7px; border-radius:9999px;
                                     background:var(--tb-counter-bg); color:var(--tb-muted); font-size:11px; font-weight:600;">
                            {{ $count }}
                        </span>
                    </h2>
                    <p style="font-size:11px; color:var(--tb-muted); margin:2px 0 0;">Top to bottom — drag a section by its grip to reorder.</p>
                </div>
                <button
                    type="button"
                    x-on:click="reloadPreview()"
                    style="display:inline-flex; align-items:center; gap:4px; font-size:11px; font-weight:500; color:var(--tb-fg-soft);
                           background:var(--tb-btn-bg); border:1px solid var(--tb-border); border-radius:6px; padding:4px 8px; cursor:pointer;"
                    title="Refresh preview"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:13px; height:13px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    Refresh
                </button>
            </div>

            {{-- Empty state --}}
            @if ($count === 0)
                <div style="padding:28px 16px; text-align:center; border:1px dashed var(--tb-border); border-radius:10px; background:var(--tb-empty-bg);">
                    <div style="margin:0 auto 10px; width:40px; height:40px; border-radius:10px;
                                background:var(--tb-empty-icon-bg); color:var(--tb-empty-icon-fg); display:flex; align-items:center; justify-content:center;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" style="width:22px; height:22px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </div>
                    <div style="font-size:13px; font-weight:600; color:var(--tb-fg);">No sections yet</div>
                    <p style="margin:4px 0 0; font-size:11px; color:var(--tb-muted);">
                        Click <strong>Add section</strong> above to drop your first block. The homepage will show the sections you place here, in order.
                    </p>
                </div>
            @else
                <ul style="list-style:none; margin:0; padding:0; display:flex; flex-direction:column; gap:8px;">
                    @foreach ($sections as $i => $section)
                        @php
                            $type = $section['type'] ?? null;
                            $meta = $types[$type] ?? ['label' => ucfirst((string) $type), 'icon' => 'heroicon-o-square-2-stack', 'desc' => ''];
                            $sid  = $section['id'] ?? '';
                            $first = $i === 0;
                            $last  = $i === $count - 1;
                        @endphp
                        @php $hasVariants = $this->typeHasVariants($type ?? ''); @endphp
                        <li
                            class="tb-row"
                            data-section-id="{{ $sid }}"
                            draggable="true"
                            x-on:dragstart="onRowDragStart($event, '{{ $sid }}')"
                            x-on:dragover.prevent="onRowDragOver($event, '{{ $sid }}')"
                            x-on:dragleave="onRowDragLeave($event, '{{ $sid }}')"
                            x-on:drop.prevent="onRowDrop($event, '{{ $sid }}')"
                            x-on:dragend="onRowDragEnd()"
                            x-bind:class="{
                                'tb-row--dragging':    dragId === '{{ $sid }}',
                                'tb-row--over-before': overId === '{{ $sid }}' && overPos === 'before' && dragId !== '{{ $sid }}',
                                'tb-row--over-after':  overId === '{{ $sid }}' && overPos === 'after'  && dragId !== '{{ $sid }}',
                            }"
                            style="display:flex; align-items:center; gap:10px; padding:10px;
                                   background:var(--tb-card-bg); border:1px solid var(--tb-border); border-radius:10px;
                                   transition: transform .12s ease, opacity .12s ease;"
                        >
                            <span class="tb-grip" title="Drag to reorder" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:14px; height:14px;">
                                    <circle cx="9"  cy="6"  r="1.6"/>
                                    <circle cx="15" cy="6"  r="1.6"/>
                                    <circle cx="9"  cy="12" r="1.6"/>
                                    <circle cx="15" cy="12" r="1.6"/>
                                    <circle cx="9"  cy="18" r="1.6"/>
                                    <circle cx="15" cy="18" r="1.6"/>
                                </svg>
                            </span>
                            @if ($hasVariants)
                                <button
                                    type="button"
                                    wire:click="mountAction('chooseDesign', { id: '{{ $sid }}' })"
                                    title="Choose design — pick a visual variant for this section"
                                    aria-label="Choose design"
                                    class="tb-design-btn"
                                    style="flex:0 0 36px; height:36px; display:flex; align-items:center; justify-content:center;
                                           background:var(--tb-icon-bg); border:1px solid var(--tb-border); border-radius:8px; color:#0284c7;
                                           cursor:pointer; padding:0; position:relative; transition:border-color .12s ease, transform .08s ease;"
                                >
                                    <x-filament::icon :icon="$meta['icon']" style="width:16px; height:16px;" />
                                    {{-- Tiny "swatch" indicator badge in the corner --}}
                                    <span aria-hidden="true" style="position:absolute; bottom:-4px; right:-4px;
                                                                    width:14px; height:14px; border-radius:9999px;
                                                                    background:#0284c7; color:#fff;
                                                                    display:inline-flex; align-items:center; justify-content:center;
                                                                    border:2px solid var(--tb-card-bg);">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:8px; height:8px;">
                                            <path d="M12 2.5l2.6 5.27 5.82.85-4.21 4.1.99 5.78L12 15.77l-5.2 2.73.99-5.78-4.21-4.1 5.82-.85L12 2.5z" />
                                        </svg>
                                    </span>
                                </button>
                            @else
                                <div style="flex:0 0 36px; height:36px; display:flex; align-items:center; justify-content:center;
                                            background:var(--tb-icon-bg); border:1px solid var(--tb-border); border-radius:8px; color:#0284c7;">
                                    <x-filament::icon :icon="$meta['icon']" style="width:16px; height:16px;" />
                                </div>
                            @endif
                            <div style="min-width:0; flex:1;">
                                <div style="display:flex; align-items:center; gap:6px;">
                                    <span style="font-size:13px; font-weight:600; color:var(--tb-fg); line-height:1.2;">{{ $meta['label'] }}</span>
                                    <span style="font-size:10px; color:var(--tb-very-muted);">#{{ $i + 1 }}</span>
                                </div>
                                <div style="font-size:11px; color:var(--tb-muted); line-height:1.35; margin-top:2px;
                                            overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                    {{ $meta['desc'] }}
                                </div>
                            </div>
                            <div style="display:flex; align-items:center; gap:4px; flex:0 0 auto;">
                                {{-- Edit (gradient — same in both themes for emphasis) --}}
                                <button
                                    type="button"
                                    wire:click="mountAction('editSection', { id: '{{ $sid }}' })"
                                    style="display:inline-flex; align-items:center; gap:4px; padding:5px 10px;
                                           border:1px solid #0369a1; background:linear-gradient(180deg,#38bdf8,#0284c7);
                                           color:#fff; border-radius:6px; font-size:11px; font-weight:600; cursor:pointer;
                                           box-shadow:0 1px 2px rgba(2,132,199,.3), inset 0 1px 0 rgba(255,255,255,.2);"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" style="width:11px; height:11px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Z" />
                                    </svg>
                                    Edit
                                </button>
                                {{-- Delete --}}
                                <button
                                    type="button"
                                    wire:click="deleteSection('{{ $sid }}')"
                                    wire:confirm="Delete this {{ strtolower($meta['label']) }} section?"
                                    title="Delete section"
                                    style="width:26px; height:26px; display:inline-flex; align-items:center; justify-content:center;
                                           border:1px solid var(--tb-danger-border); background:var(--tb-btn-bg); color:var(--tb-danger-fg); border-radius:6px; cursor:pointer;"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" style="width:13px; height:13px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif

            <div style="margin-top:14px; padding:10px 12px; background:var(--tb-tip-bg);
                        border:1px solid var(--tb-tip-border); border-radius:8px;
                        font-size:11px; line-height:1.45; color:var(--tb-tip-fg);">
                <strong>Tip:</strong> click <em>Edit</em> on a section to change its content and colors. Changes save immediately and the preview updates.
            </div>
        </aside>

        {{-- ── Live preview (right) ────────────────────────────────── --}}
        <section style="background:var(--tb-bg); border:1px solid var(--tb-border); border-radius:12px; padding:8px; box-shadow:var(--tb-shadow);">
            <style>
                /* Resting buttons sit on the toolbar's own bg — the
                   .tb-device-btn--active state paints them in primary
                   so the user can spot which device is selected. The
                   pre-existing inline `style="background: transparent"`
                   was outranking the active class via specificity, so
                   the active state never showed. Everything is class-
                   driven now. */
                .tb-device-btn {
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    width: 28px;
                    height: 28px;
                    border-radius: 6px;
                    border: 1px solid transparent;
                    background: transparent;
                    color: var(--tb-fg-soft);
                    cursor: pointer;
                    transition: background-color 140ms ease, color 140ms ease, border-color 140ms ease;
                }
                .tb-device-btn:hover {
                    color: #0284c7;
                    background: rgba(2, 132, 199, 0.08);
                }
                .tb-device-btn--active,
                .tb-device-btn--active:hover {
                    background: #0284c7 !important;
                    border-color: #0284c7 !important;
                    color: #ffffff !important;
                }
                .tb-preview-frame {
                    height: calc(100vh - 220px);
                    min-height: 600px;
                    border: 1px solid var(--tb-border);
                    border-radius: 8px;
                    background: var(--tb-empty-bg);
                    overflow: hidden;
                    display: flex;
                    align-items: flex-start;
                    justify-content: center;
                }
                .tb-preview-stage {
                    height: 100%;
                    width: 100%;
                    background: #ffffff;
                    overflow: hidden;
                    transition: max-width 280ms cubic-bezier(0.22, 0.61, 0.36, 1);
                }
                /* Just the viewport size — no device chrome. The user wanted
                   the iframe to resize to iPad / iPhone widths without the
                   surrounding bezel-style frame. */
                .tb-preview-frame--tablet .tb-preview-stage { max-width: 820px; }
                .tb-preview-frame--mobile .tb-preview-stage { max-width: 412px; }
            </style>
            <div style="display:flex; align-items:center; justify-content:space-between; padding:6px 8px 8px; gap: 12px; flex-wrap: wrap;">
                <div style="display:flex; align-items:center; gap:8px; min-width: 0;">
                    <span style="display:inline-block; width:8px; height:8px; border-radius:9999px; background:var(--tb-status-ok); flex:none;"></span>
                    <span style="font-size:11px; font-weight:500; color:var(--tb-fg-soft); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Live preview · {{ $previewUrl }}</span>
                </div>

                {{-- Device toggle: iPad / mobile. Click an active button
                     again to clear it and return to the full-width view. --}}
                <div style="display:inline-flex; align-items:center; gap:4px; padding:3px; border-radius:8px; background:var(--tb-card-bg); border:1px solid var(--tb-border);">
                    <button type="button"
                        class="tb-device-btn"
                        x-bind:class="device === 'tablet' ? 'tb-device-btn--active' : ''"
                        x-on:click="device = device === 'tablet' ? 'desktop' : 'tablet'"
                        title="iPad / tablet view"
                        aria-label="Tablet view"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width:14px; height:14px;">
                            <rect x="5" y="2.5" width="14" height="19" rx="2.2" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 18.5h2" />
                        </svg>
                    </button>
                    <button type="button"
                        class="tb-device-btn"
                        x-bind:class="device === 'mobile' ? 'tb-device-btn--active' : ''"
                        x-on:click="device = device === 'mobile' ? 'desktop' : 'mobile'"
                        title="Mobile view"
                        aria-label="Mobile view"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width:14px; height:14px;">
                            <rect x="7" y="2.5" width="10" height="19" rx="2" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 18.5h2" />
                        </svg>
                    </button>
                </div>

                <a
                    href="{{ $previewUrl }}"
                    target="_blank"
                    rel="noopener"
                    style="display:inline-flex; align-items:center; gap:4px; font-size:11px; font-weight:500;
                           color:var(--tb-link-fg); text-decoration:none;"
                >
                    Open in new tab
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:12px; height:12px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                    </svg>
                </a>
            </div>
            <div
                class="tb-preview-frame"
                x-bind:class="`tb-preview-frame--${device}`"
            >
                <div class="tb-preview-stage">
                    <iframe
                        x-ref="preview"
                        src="{{ $previewUrl }}"
                        x-on:load="onPreviewNavigated"
                        style="width:100%; height:100%; border:0; display:block; background:#ffffff;"
                        title="Page preview"
                    ></iframe>
                </div>
            </div>
        </section>
    </div>

    <x-filament-actions::modals />

    <script>
        function themeBuilder(config = {}) {
            return {
                // ── Page list (for iframe ↔ page-picker sync) ──
                // The full set of pages the user can edit. Used by
                // onPreviewNavigated() to map the iframe's pathname back
                // to a page id when the visitor clicks an internal link.
                pages: config.pages || [],

                // ── Preview device toggle ──
                // 'desktop' | 'tablet' | 'mobile' — drives a class on the
                // preview frame that constrains the iframe's max-width to the
                // matching viewport (full / 820px / 412px) with smooth easing.
                device: 'desktop',

                // ── Drag-and-drop reorder state ──
                // dragId  = id of the row currently being dragged (set on dragstart)
                // overId  = id of the row the cursor is currently over
                // overPos = 'before' | 'after' relative to overId — which side of
                //           the hovered row the dragged item will land on, decided
                //           by whether the cursor is in the top or bottom half.
                dragId: null,
                overId: null,
                overPos: null,

                onRowDragStart(event, id) {
                    this.dragId = id;
                    // Required by Firefox to actually start the drag.
                    event.dataTransfer.effectAllowed = 'move';
                    event.dataTransfer.setData('text/plain', id);
                },
                onRowDragOver(event, id) {
                    if (!this.dragId || this.dragId === id) return;
                    event.dataTransfer.dropEffect = 'move';
                    const rect = event.currentTarget.getBoundingClientRect();
                    const midY = rect.top + rect.height / 2;
                    this.overId  = id;
                    this.overPos = event.clientY < midY ? 'before' : 'after';
                },
                onRowDragLeave(event, id) {
                    // Only clear the indicator when the cursor truly leaves
                    // this row — dragover fires repeatedly during a slow drag
                    // and we don't want to flicker.
                    if (this.overId === id && !event.currentTarget.contains(event.relatedTarget)) {
                        this.overId  = null;
                        this.overPos = null;
                    }
                },
                onRowDrop(event, targetId) {
                    if (!this.dragId || this.dragId === targetId) {
                        this.resetDragState();
                        return;
                    }
                    const rows = [...this.$root.querySelectorAll('[data-section-id]')];
                    const ids  = rows.map(r => r.dataset.sectionId);
                    const fromIdx = ids.indexOf(this.dragId);
                    if (fromIdx === -1) { this.resetDragState(); return; }

                    const [moved] = ids.splice(fromIdx, 1);
                    let toIdx = ids.indexOf(targetId);
                    if (this.overPos === 'after') toIdx += 1;
                    ids.splice(toIdx, 0, moved);

                    this.$wire.reorderSections(ids);
                    this.resetDragState();
                },
                onRowDragEnd() {
                    this.resetDragState();
                },
                resetDragState() {
                    this.dragId = null;
                    this.overId = null;
                    this.overPos = null;
                },

                reloadPreview() {
                    const iframe = this.$refs.preview;
                    if (!iframe) return;
                    // Use the server-rendered preview URL (this attribute reflects
                    // the currently-selected page) and add a cache-buster.
                    const base = iframe.getAttribute('src') || '/';
                    const url  = new URL(base, window.location.origin);
                    url.searchParams.set('t', Date.now());
                    iframe.src = url.pathname + url.search;
                },

                // Fires whenever the iframe finishes loading — both on
                // initial load and when the visitor clicks an internal
                // link inside the preview. If the new pathname matches a
                // page in the picker, sync the sidebar selection to it.
                // External / off-page links (no slug match) are ignored
                // so the picker stays where it was.
                onPreviewNavigated(event) {
                    const iframe = event.target;
                    let pathname;
                    try {
                        pathname = iframe.contentWindow.location.pathname;
                    } catch (e) {
                        // Cross-origin — can't read the location. Should
                        // never happen for our same-origin preview, but
                        // bail safely if it does.
                        return;
                    }
                    if (! pathname) return;

                    // Normalize: drop trailing slashes except for the
                    // homepage itself, which stays as '/'.
                    const normalize = (p) => {
                        if (! p || p === '/') return '/';
                        return p.replace(/\/+$/, '');
                    };
                    const target = normalize(pathname);

                    const matched = this.pages.find((p) => normalize(p.previewUrl || '') === target);
                    if (! matched) return;

                    const newId  = matched.id ?? null;
                    const currId = this.$wire.currentPageId ?? null;
                    if (String(currId) === String(newId)) return;

                    this.$wire.selectPage(newId);
                },
            };
        }
    </script>
</x-filament-panels::page>
