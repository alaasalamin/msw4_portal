<x-filament-panels::page>
    @php
        $audit   = $this->getAudit();
        $summary = $this->getAuditSummary($audit);
        $snippet = $this->getSnippetState();
    @endphp

    {{-- Light + dark theme variables — same approach as the Theme Builder. --}}
    <style>
        .seo-panel {
            --tb-bg:          #ffffff;
            --tb-card-bg:     #f9fafb;
            --tb-fg:          #111827;
            --tb-fg-soft:     #374151;
            --tb-muted:       #6b7280;
            --tb-very-muted:  #9ca3af;
            --tb-border:      #e5e7eb;
            --tb-border-soft: #f3f4f6;
        }
        .dark .seo-panel,
        html.dark .seo-panel {
            --tb-bg:          #18181b;
            --tb-card-bg:     #27272a;
            --tb-fg:          #f4f4f5;
            --tb-fg-soft:     #e4e4e7;
            --tb-muted:       #a1a1aa;
            --tb-very-muted:  #71717a;
            --tb-border:      #3f3f46;
            --tb-border-soft: #2a2a2e;
        }
    </style>

    <div class="seo-panel" style="display:flex; flex-direction:column; gap:18px;">

        {{-- Summary cards --}}
        <div style="display:grid; grid-template-columns:repeat(4, minmax(0, 1fr)); gap:12px;">
            @foreach ([
                ['label' => 'Total items',   'value' => $summary['total'],    'tone' => 'neutral'],
                ['label' => 'Healthy',       'value' => $summary['good'],     'tone' => 'good'],
                ['label' => 'Warnings',      'value' => $summary['warnings'], 'tone' => 'warn'],
                ['label' => 'Issues',        'value' => $summary['issues'],   'tone' => 'bad'],
            ] as $card)
                @php
                    $tone = match ($card['tone']) {
                        'good'    => ['fg' => '#16a34a', 'bg' => 'rgba(22,163,74,.10)'],
                        'warn'    => ['fg' => '#d97706', 'bg' => 'rgba(217,119,6,.10)'],
                        'bad'     => ['fg' => '#dc2626', 'bg' => 'rgba(220,38,38,.10)'],
                        default   => ['fg' => 'var(--tb-fg-soft)', 'bg' => 'var(--tb-card-bg)'],
                    };
                @endphp
                <div style="background:var(--tb-bg); border:1px solid var(--tb-border); border-radius:12px; padding:14px 16px;
                            display:flex; flex-direction:column; gap:6px;">
                    <span style="font-size:11px; font-weight:600; color:var(--tb-muted); text-transform:uppercase; letter-spacing:.06em;">
                        {{ $card['label'] }}
                    </span>
                    <span style="font-size:28px; font-weight:800; color:{{ $tone['fg'] }};
                                 background:{{ $tone['bg'] }}; align-self:flex-start;
                                 padding:1px 12px; border-radius:9999px; line-height:1.4;">
                        {{ $card['value'] }}
                    </span>
                </div>
            @endforeach
        </div>

        {{-- Avg score card (full width) --}}
        <div style="background:var(--tb-bg); border:1px solid var(--tb-border); border-radius:12px; padding:16px;
                    display:flex; align-items:center; gap:16px;">
            <div style="flex:0 0 auto; width:62px; height:62px; border-radius:9999px;
                        background:conic-gradient(
                            {{ $summary['avg'] >= 80 ? '#16a34a' : ($summary['avg'] >= 50 ? '#d97706' : '#dc2626') }} {{ $summary['avg'] * 3.6 }}deg,
                            color-mix(in srgb, var(--tb-fg) 8%, transparent) 0
                        );
                        display:flex; align-items:center; justify-content:center;">
                <div style="width:48px; height:48px; border-radius:9999px; background:var(--tb-bg);
                            display:flex; align-items:center; justify-content:center;
                            font-weight:800; font-size:14px; color:var(--tb-fg);">
                    {{ $summary['avg'] }}
                </div>
            </div>
            <div>
                <div style="font-size:13px; font-weight:700; color:var(--tb-fg); margin-bottom:2px;">Average SEO score</div>
                <div style="font-size:12px; color:var(--tb-muted);">
                    @if ($summary['avg'] >= 80)
                        Site is in great shape. Keep an eye on warnings.
                    @elseif ($summary['avg'] >= 50)
                        A few items need attention — see the audit below.
                    @else
                        Several items need work. Start with the issues at the top.
                    @endif
                </div>
            </div>
        </div>

        {{-- Live Google search snippet preview --}}
        <div
            x-data="{
                title:       @js($snippet['title']),
                description: @js($snippet['description']),
                url:         @js($snippet['url']),
                siteName:    @js($snippet['siteName']),
                saving:      false,
                get titleLen() { return (this.title || '').length; },
                get descLen()  { return (this.description || '').length; },
                get prettyUrl() {
                    try { const u = new URL(this.url); return u.host + (u.pathname === '/' ? '' : u.pathname); }
                    catch (e) { return this.url; }
                },
                get breadcrumbHost() {
                    try { return new URL(this.url).host; } catch (e) { return this.url; }
                },
                get titleClass() {
                    if (this.titleLen === 0) return 'bad';
                    if (this.titleLen < 30 || this.titleLen > 60) return 'warn';
                    return 'good';
                },
                get descClass() {
                    if (this.descLen === 0) return 'bad';
                    if (this.descLen < 70 || this.descLen > 160) return 'warn';
                    return 'good';
                },
                async saveSnippet() {
                    this.saving = true;
                    try { await this.$wire.call('saveSnippet', this.title, this.description); }
                    finally { this.saving = false; }
                },
            }"
            style="background:var(--tb-bg); border:1px solid var(--tb-border); border-radius:12px; padding:18px;"
        >
            <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-bottom:14px;">
                <div>
                    <h2 style="font-size:14px; font-weight:600; color:var(--tb-fg); margin:0 0 2px;">Search snippet preview</h2>
                    <p style="font-size:11px; color:var(--tb-muted); margin:0;">Edit on the left, watch the Google result render in real time on the right.</p>
                </div>
                <button
                    type="button"
                    x-bind:disabled="saving"
                    x-on:click="saveSnippet"
                    style="display:inline-flex; align-items:center; gap:6px;
                           background:#0284c7; color:#fff;
                           border:1px solid #0369a1; border-radius:8px;
                           padding:8px 14px; font-size:13px; font-weight:600; cursor:pointer;
                           box-shadow:0 1px 2px rgba(2,132,199,.3), inset 0 1px 0 rgba(255,255,255,.18);"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" x-show="!saving"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" x-show="saving" x-cloak style="animation:spin 1s linear infinite;">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/>
                        <path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z" opacity=".95"/>
                    </svg>
                    <span x-text="saving ? 'Saving…' : 'Save snippet'"></span>
                </button>
            </div>

            <div class="seo-snippet-grid" style="display:grid; grid-template-columns:1fr 1.1fr; gap:18px;">
                {{-- Left: editor --}}
                <div style="display:flex; flex-direction:column; gap:14px;">
                    <label style="display:flex; flex-direction:column; gap:6px;">
                        <span style="display:flex; align-items:center; justify-content:space-between; font-size:11px; font-weight:600; color:var(--tb-muted); text-transform:uppercase; letter-spacing:.06em;">
                            <span>Page URL</span>
                        </span>
                        <input
                            type="text"
                            x-model="url"
                            placeholder="https://example.com/page"
                            style="padding:9px 12px; border:1px solid var(--tb-border); border-radius:8px;
                                   font-size:13px; background:var(--tb-card-bg); color:var(--tb-fg); outline:none;"
                            x-on:focus="$event.target.style.borderColor='#0284c7'"
                            x-on:blur="$event.target.style.borderColor=''"
                        />
                    </label>

                    <label style="display:flex; flex-direction:column; gap:6px;">
                        <span style="display:flex; align-items:center; justify-content:space-between; font-size:11px; font-weight:600; color:var(--tb-muted); text-transform:uppercase; letter-spacing:.06em;">
                            <span>Meta title</span>
                            <span x-bind:style="
                                titleClass === 'good' ? 'color:#16a34a' :
                                titleClass === 'warn' ? 'color:#d97706' : 'color:#dc2626'
                            "><span x-text="titleLen"></span> / 60</span>
                        </span>
                        <input
                            type="text"
                            x-model="title"
                            maxlength="120"
                            placeholder="Your headline as it appears in Google"
                            style="padding:9px 12px; border:1px solid var(--tb-border); border-radius:8px;
                                   font-size:13px; background:var(--tb-card-bg); color:var(--tb-fg); outline:none;"
                            x-on:focus="$event.target.style.borderColor='#0284c7'"
                            x-on:blur="$event.target.style.borderColor=''"
                        />
                    </label>

                    <label style="display:flex; flex-direction:column; gap:6px;">
                        <span style="display:flex; align-items:center; justify-content:space-between; font-size:11px; font-weight:600; color:var(--tb-muted); text-transform:uppercase; letter-spacing:.06em;">
                            <span>Meta description</span>
                            <span x-bind:style="
                                descClass === 'good' ? 'color:#16a34a' :
                                descClass === 'warn' ? 'color:#d97706' : 'color:#dc2626'
                            "><span x-text="descLen"></span> / 160</span>
                        </span>
                        <textarea
                            x-model="description"
                            rows="4"
                            maxlength="280"
                            placeholder="A 70–160 character summary of the page"
                            style="padding:9px 12px; border:1px solid var(--tb-border); border-radius:8px;
                                   font-size:13px; background:var(--tb-card-bg); color:var(--tb-fg); outline:none; resize:vertical; font-family:inherit;"
                            x-on:focus="$event.target.style.borderColor='#0284c7'"
                            x-on:blur="$event.target.style.borderColor=''"
                        ></textarea>
                    </label>

                    <div style="font-size:11px; color:var(--tb-muted); line-height:1.5;">
                        Google typically truncates titles past <strong>~60 chars</strong> and descriptions past <strong>~160 chars</strong> in desktop search results.
                    </div>
                </div>

                {{-- Right: Google search result mockup --}}
                <div style="background:#ffffff; border:1px solid var(--tb-border); border-radius:14px;
                            padding:18px 20px; display:flex; flex-direction:column; gap:6px;
                            box-shadow:0 1px 2px rgba(15,23,42,.04);">
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:6px;">
                        <div style="width:18px; height:18px; border-radius:9999px; background:#e8eaed;
                                    display:flex; align-items:center; justify-content:center;
                                    font-size:9px; font-weight:700; color:#5f6368;
                                    text-transform:uppercase;"
                             x-text="(siteName || 'S').charAt(0)">
                        </div>
                        <div style="display:flex; flex-direction:column; line-height:1.2;">
                            <span style="font-size:12px; color:#202124; font-weight:500;" x-text="siteName"></span>
                            <span style="font-size:11px; color:#5f6368;" x-text="prettyUrl"></span>
                        </div>
                    </div>
                    <a href="#" onclick="return false;" style="
                        font-size:18px;
                        line-height:1.3;
                        color:#1a0dab;
                        font-weight:400;
                        text-decoration:none;
                        cursor:pointer;
                        font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;
                    ">
                        <span x-text="title || 'Untitled — your meta title goes here'"></span>
                    </a>
                    <p style="
                        margin:0;
                        font-size:13.5px;
                        line-height:1.55;
                        color:#4d5156;
                        font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;
                        word-wrap:break-word;
                    ">
                        <span x-text="description || 'Your meta description shows here. Aim for 70–160 characters that summarize the page and invite the click.'"></span>
                    </p>
                    <div style="margin-top:6px; font-size:11px; color:#5f6368; font-family:Arial, sans-serif;">
                        <span style="opacity:.7;">⓵</span> A live preview — actual rendering varies by query, device and Google.
                    </div>
                </div>
            </div>

            <style>
                @keyframes spin { to { transform: rotate(360deg); } }
                @media (max-width: 880px) {
                    .seo-snippet-grid { grid-template-columns: 1fr !important; }
                }
            </style>
        </div>

        {{-- Settings form --}}
        <div style="background:var(--tb-bg); border:1px solid var(--tb-border); border-radius:12px; padding:18px;">
            <form wire:submit="save">
                {{ $this->form }}
            </form>
        </div>

        {{-- Audit table --}}
        <div style="background:var(--tb-bg); border:1px solid var(--tb-border); border-radius:12px; overflow:hidden;">
            <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 18px;
                        border-bottom:1px solid var(--tb-border-soft);">
                <div>
                    <div style="font-size:14px; font-weight:600; color:var(--tb-fg);">Page & post audit</div>
                    <div style="font-size:11px; color:var(--tb-muted); margin-top:2px;">
                        Sorted worst first. Recommended lengths: title 30–60 chars, description 70–160 chars.
                    </div>
                </div>
            </div>
            @if (count($audit) === 0)
                <div style="padding:36px 18px; text-align:center; color:var(--tb-muted); font-size:13px;">
                    Nothing to audit yet — publish a page or a post first.
                </div>
            @else
                <div style="overflow-x:auto;">
                    <table style="width:100%; border-collapse:collapse; font-size:13px; min-width:800px;">
                        <thead>
                            <tr style="background:var(--tb-card-bg); color:var(--tb-muted);
                                       text-transform:uppercase; letter-spacing:.06em; font-size:10.5px;">
                                <th style="text-align:left; padding:10px 14px; font-weight:700;">Type</th>
                                <th style="text-align:left; padding:10px 14px; font-weight:700;">Title / URL</th>
                                <th style="text-align:left; padding:10px 14px; font-weight:700;">Meta title</th>
                                <th style="text-align:left; padding:10px 14px; font-weight:700;">Meta description</th>
                                <th style="text-align:right; padding:10px 14px; font-weight:700;">Score</th>
                                <th style="text-align:right; padding:10px 14px; font-weight:700;">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($audit as $item)
                                @php
                                    $sc = $item['score'];
                                    $scTone = $sc >= 80 ? ['#16a34a', 'rgba(22,163,74,.12)']
                                            : ($sc >= 50 ? ['#d97706', 'rgba(217,119,6,.12)']
                                            : ['#dc2626', 'rgba(220,38,38,.12)']);
                                @endphp
                                <tr style="border-top:1px solid var(--tb-border-soft); vertical-align:top;">
                                    <td style="padding:12px 14px; color:var(--tb-muted); font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.06em;">
                                        {{ $item['type'] }}
                                    </td>
                                    <td style="padding:12px 14px;">
                                        <div style="font-weight:600; color:var(--tb-fg); margin-bottom:2px;">{{ $item['title'] }}</div>
                                        <a href="{{ $item['url'] }}" target="_blank" rel="noopener"
                                           style="font-size:11px; color:var(--tb-very-muted); text-decoration:none; word-break:break-all;">
                                            {{ $item['url'] }}
                                        </a>
                                        @if (count($item['issues']) || count($item['warnings']))
                                            <ul style="list-style:none; padding:0; margin:8px 0 0; display:flex; flex-direction:column; gap:3px;">
                                                @foreach ($item['issues'] as $issue)
                                                    <li style="color:#dc2626; font-size:11px;">⛔ {{ $issue }}</li>
                                                @endforeach
                                                @foreach ($item['warnings'] as $warning)
                                                    <li style="color:#d97706; font-size:11px;">⚠️ {{ $warning }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </td>
                                    <td style="padding:12px 14px; color:var(--tb-fg-soft); max-width:240px;">
                                        @if ($item['metaTitle'])
                                            <div style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $item['metaTitle'] }}</div>
                                            <div style="font-size:10px; color:var(--tb-muted); margin-top:2px;">{{ $item['titleLen'] }} chars</div>
                                        @else
                                            <span style="color:var(--tb-very-muted); font-style:italic;">missing</span>
                                        @endif
                                    </td>
                                    <td style="padding:12px 14px; color:var(--tb-fg-soft); max-width:280px;">
                                        @if ($item['metaDescription'])
                                            <div style="display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; line-height:1.4;">{{ $item['metaDescription'] }}</div>
                                            <div style="font-size:10px; color:var(--tb-muted); margin-top:2px;">{{ $item['descLen'] }} chars</div>
                                        @else
                                            <span style="color:var(--tb-very-muted); font-style:italic;">missing</span>
                                        @endif
                                    </td>
                                    <td style="padding:12px 14px; text-align:right; white-space:nowrap;">
                                        <span style="display:inline-flex; align-items:center; justify-content:center;
                                                     min-width:42px; padding:3px 10px; border-radius:9999px;
                                                     font-weight:700; font-size:12px;
                                                     color:{{ $scTone[0] }}; background:{{ $scTone[1] }};">
                                            {{ $sc }}
                                        </span>
                                    </td>
                                    <td style="padding:12px 14px; text-align:right; white-space:nowrap;">
                                        <button
                                            type="button"
                                            wire:click="mountAction('editMeta', { type: '{{ $item['kind'] }}', id: {{ (int) $item['id'] }} })"
                                            title="Edit meta for this {{ strtolower($item['type']) }}"
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
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <x-filament-actions::modals />
</x-filament-panels::page>
