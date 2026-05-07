<x-filament-panels::page>
    @php
        $sections = $this->getPlacedSections();
        $types    = $this->getSectionTypes();
        $count    = count($sections);
    @endphp

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
    </style>

    <div
        class="tb-panel"
        x-data="themeBuilder()"
        x-on:theme-builder:section-saved.window="reloadPreview()"
        style="display:grid; grid-template-columns: 380px 1fr; gap:16px; align-items:start;"
    >
        {{-- ── Section list (left) ─────────────────────────────────── --}}
        <aside style="background:var(--tb-bg); border:1px solid var(--tb-border); border-radius:12px; padding:16px;
                      box-shadow:var(--tb-shadow); position:sticky; top:16px;">

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
                    <p style="font-size:11px; color:var(--tb-muted); margin:2px 0 0;">Top to bottom — drag isn't enabled yet, use ↑↓.</p>
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
                        <li style="display:flex; align-items:center; gap:10px; padding:10px;
                                   background:var(--tb-card-bg); border:1px solid var(--tb-border); border-radius:10px;">
                            <div style="flex:0 0 36px; height:36px; display:flex; align-items:center; justify-content:center;
                                        background:var(--tb-icon-bg); border:1px solid var(--tb-border); border-radius:8px; color:#0284c7;">
                                <x-filament::icon :icon="$meta['icon']" style="width:16px; height:16px;" />
                            </div>
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
                                {{-- Up --}}
                                <button
                                    type="button"
                                    @if ($first) disabled @endif
                                    wire:click="moveSection('{{ $sid }}', 'up')"
                                    title="Move up"
                                    @style([
                                        'width:26px; height:26px; display:inline-flex; align-items:center; justify-content:center;
                                         border:1px solid var(--tb-border); border-radius:6px; background:var(--tb-btn-bg);',
                                        'cursor:pointer; color:var(--tb-fg-soft);' => ! $first,
                                        'cursor:not-allowed; color:var(--tb-btn-disabled);' => $first,
                                    ])
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" style="width:13px; height:13px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
                                    </svg>
                                </button>
                                {{-- Down --}}
                                <button
                                    type="button"
                                    @if ($last) disabled @endif
                                    wire:click="moveSection('{{ $sid }}', 'down')"
                                    title="Move down"
                                    @style([
                                        'width:26px; height:26px; display:inline-flex; align-items:center; justify-content:center;
                                         border:1px solid var(--tb-border); border-radius:6px; background:var(--tb-btn-bg);',
                                        'cursor:pointer; color:var(--tb-fg-soft);' => ! $last,
                                        'cursor:not-allowed; color:var(--tb-btn-disabled);' => $last,
                                    ])
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" style="width:13px; height:13px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
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
            <div style="display:flex; align-items:center; justify-content:space-between; padding:6px 8px 8px;">
                <div style="display:flex; align-items:center; gap:8px;">
                    <span style="display:inline-block; width:8px; height:8px; border-radius:9999px; background:var(--tb-status-ok);"></span>
                    <span style="font-size:11px; font-weight:500; color:var(--tb-fg-soft);">Live preview · /</span>
                </div>
                <a
                    href="/"
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
            <div style="height:calc(100vh - 220px); min-height:600px; overflow:hidden;
                        border:1px solid var(--tb-border); border-radius:8px; background:#ffffff;">
                <iframe
                    x-ref="preview"
                    src="/"
                    style="width:100%; height:100%; border:0; display:block; background:#ffffff;"
                    title="Homepage preview"
                ></iframe>
            </div>
        </section>
    </div>

    <x-filament-actions::modals />

    <script>
        function themeBuilder() {
            return {
                reloadPreview() {
                    const iframe = this.$refs.preview;
                    if (!iframe) return;
                    iframe.src = '/?t=' + Date.now();
                },
            };
        }
    </script>
</x-filament-panels::page>
