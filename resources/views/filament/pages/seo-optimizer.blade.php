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
                favicon:     @js($snippet['favicon']),
                ogImage:     @js($snippet['ogImage']),
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
                // Combine the user title with the site-name suffix (same rule
                // the blade emits in the page title tag). Skip the suffix if
                // the user already typed the site name in.
                combinedTitle(fallback) {
                    const t = (this.title || '').trim();
                    if (!t) return fallback || '';
                    const sn = (this.siteName || '').trim();
                    if (!sn) return t;
                    return t.toLowerCase().includes(sn.toLowerCase()) ? t : `${t} - ${sn}`;
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
                <div style="display:flex; align-items:center; gap:8px;">
                    {{ $this->uploadFaviconAction }}
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
                        <button
                            type="button"
                            wire:click="mountAction('uploadFavicon')"
                            title="Click to change favicon"
                            style="position:relative; width:22px; height:22px; padding:0; border-radius:9999px;
                                   background:#e8eaed; border:none; cursor:pointer; overflow:hidden;
                                   display:flex; align-items:center; justify-content:center;"
                        >
                            <template x-if="favicon">
                                <img x-bind:src="favicon" alt="" style="width:100%; height:100%; object-fit:cover; display:block;" />
                            </template>
                            <template x-if="!favicon">
                                <span style="font-size:10px; font-weight:700; color:#5f6368; text-transform:uppercase;" x-text="(siteName || 'S').charAt(0)"></span>
                            </template>
                        </button>
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
                        <span x-text="combinedTitle('Untitled — your meta title goes here')"></span>
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

            {{-- ── Social card preview (Facebook / X / LinkedIn) ───────── --}}
            <div style="margin-top:20px; padding-top:18px; border-top:1px solid var(--tb-border-soft);">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-bottom:12px;">
                    <div>
                        <h3 style="font-size:13px; font-weight:600; color:var(--tb-fg); margin:0 0 2px;">Social card preview</h3>
                        <p style="font-size:11px; color:var(--tb-muted); margin:0;">How the page renders when shared on Facebook, X, LinkedIn, WhatsApp and Slack.</p>
                    </div>
                    <div>{{ $this->uploadOgImageAction }}</div>
                </div>

                <div class="seo-social-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:18px;">
                    {{-- Facebook / OpenGraph card --}}
                    <div style="background:#ffffff; border:1px solid #dddfe2; border-radius:8px; overflow:hidden;
                                font-family:'Helvetica Neue', Helvetica, Arial, sans-serif; box-shadow:0 1px 2px rgba(0,0,0,.04);">
                        <button
                            type="button"
                            wire:click="mountAction('uploadOgImage')"
                            title="Click to change OG image"
                            style="position:relative; width:100%; aspect-ratio:1.91/1; background:#e4e6eb;
                                   border:0; padding:0; cursor:pointer; display:block; overflow:hidden;"
                        >
                            <template x-if="ogImage">
                                <img x-bind:src="ogImage" alt="" style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover; display:block;" />
                            </template>
                            <template x-if="!ogImage">
                                <div style="position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:8px; color:#65676b;">
                                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5z" />
                                    </svg>
                                    <span style="font-size:12px; font-weight:500;">Click to upload OG image — 1200×630</span>
                                </div>
                            </template>
                        </button>
                        <div style="padding:10px 14px; background:#f0f2f5; border-top:1px solid #dddfe2;">
                            <div style="font-size:11px; color:#65676b; text-transform:uppercase; letter-spacing:.04em;" x-text="breadcrumbHost || 'example.com'"></div>
                            <div style="font-size:15px; color:#1c1e21; font-weight:600; line-height:1.3; margin-top:2px;
                                        display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;"
                                 x-text="combinedTitle('Your title here')">
                            </div>
                            <div style="font-size:13px; color:#65676b; line-height:1.4; margin-top:3px;
                                        display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;"
                                 x-text="description || 'Your description here.'">
                            </div>
                        </div>
                        <div style="padding:6px 14px 10px; font-size:10.5px; color:#8a8d91;">Facebook</div>
                    </div>

                    {{-- X / Twitter summary_large_image card --}}
                    <div style="background:#ffffff; border:1px solid #cfd9de; border-radius:16px; overflow:hidden;
                                font-family:-apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
                                box-shadow:0 1px 2px rgba(0,0,0,.04);">
                        <div style="position:relative; width:100%; aspect-ratio:1.91/1; background:#e4e6eb; overflow:hidden;">
                            <template x-if="ogImage">
                                <img x-bind:src="ogImage" alt="" style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover; display:block;" />
                            </template>
                            <template x-if="!ogImage">
                                <div style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center; color:#536471;">
                                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5z" />
                                    </svg>
                                </div>
                            </template>
                            <div style="position:absolute; left:10px; bottom:10px; background:rgba(0,0,0,.6); color:#fff;
                                        padding:2px 8px; border-radius:4px; font-size:11px; font-weight:500;
                                        backdrop-filter:blur(4px);"
                                 x-text="breadcrumbHost || 'example.com'">
                            </div>
                        </div>
                        <div style="padding:8px 14px 12px;">
                            <div style="font-size:11px; color:#536471; line-height:1.4;" x-text="breadcrumbHost || 'example.com'"></div>
                            <div style="font-size:14px; color:#0f1419; font-weight:600; line-height:1.3; margin-top:2px;
                                        display:-webkit-box; -webkit-line-clamp:1; -webkit-box-orient:vertical; overflow:hidden;"
                                 x-text="combinedTitle('Your title here')">
                            </div>
                            <div style="font-size:13px; color:#536471; line-height:1.4; margin-top:2px;
                                        display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;"
                                 x-text="description || 'Your description here.'">
                            </div>
                        </div>
                        <div style="padding:0 14px 10px; font-size:10.5px; color:#8a8d91;">X / Twitter</div>
                    </div>
                </div>
            </div>

            <style>
                @keyframes spin { to { transform: rotate(360deg); } }
                @media (max-width: 880px) {
                    .seo-snippet-grid { grid-template-columns: 1fr !important; }
                    .seo-social-grid  { grid-template-columns: 1fr !important; }
                }
            </style>
        </div>

        {{-- Knowledge panel preview (explains the Structured data section below) --}}
        <div style="background:var(--tb-bg); border:1px solid var(--tb-border); border-radius:12px; padding:18px;">
            <div style="margin-bottom:14px;">
                <h2 style="font-size:14px; font-weight:600; color:var(--tb-fg); margin:0 0 4px;">Structured data preview — Knowledge Panel</h2>
                <p style="font-size:12px; color:var(--tb-muted); line-height:1.55; margin:0; max-width:70ch;">
                    The <strong>Structured data — organization</strong> fields below tell Google who you are.
                    When someone searches for your brand by name, Google may show a <em>Knowledge Panel</em> on the right of the results
                    with your logo, name, and website. Filling these fields adds a JSON-LD snippet to every page so Google can build that panel.
                    Below is a mock of how it typically looks, using the values you've saved.
                </p>
            </div>

            <div class="seo-kp-grid" style="display:grid; grid-template-columns:1fr 1.2fr; gap:18px; align-items:start;">
                {{-- Mock Google Knowledge Panel --}}
                <div style="background:#ffffff; border:1px solid #dadce0; border-radius:12px;
                            padding:20px; font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;
                            box-shadow:0 1px 2px rgba(0,0,0,.04), 0 4px 12px -8px rgba(0,0,0,.08);
                            max-width:380px;">
                    {{-- Logo area --}}
                    <div style="width:100%; aspect-ratio:1.6/1; background:#f8f9fa; border-radius:8px;
                                display:flex; align-items:center; justify-content:center; overflow:hidden;
                                margin-bottom:14px;">
                        @if ($snippet['orgLogo'])
                            <img src="{{ $snippet['orgLogo'] }}" alt="{{ $snippet['orgName'] }}"
                                 style="max-width:75%; max-height:80%; object-fit:contain; display:block;" />
                        @else
                            <div style="display:flex; flex-direction:column; align-items:center; gap:6px; color:#80868b;">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5z" />
                                </svg>
                                <span style="font-size:11px;">No logo set</span>
                            </div>
                        @endif
                    </div>

                    <div style="font-size:22px; font-weight:400; color:#202124; line-height:1.2; letter-spacing:-0.005em;">
                        {{ $snippet['orgName'] ?: 'Your organization name' }}
                    </div>
                    <div style="font-size:13px; color:#5f6368; margin-top:2px;">Organization</div>

                    @if ($snippet['orgUrl'])
                        <div style="margin-top:14px; padding-top:12px; border-top:1px solid #e8eaed;">
                            <div style="font-size:12px; color:#5f6368; font-weight:500;">Website</div>
                            <a href="{{ $snippet['orgUrl'] }}" target="_blank" rel="noopener"
                               style="font-size:14px; color:#1a0dab; text-decoration:none; word-break:break-all;">
                                {{ $snippet['orgUrl'] }}
                            </a>
                        </div>
                    @endif

                    <div style="margin-top:14px; font-size:11px; color:#80868b;">
                        <span style="opacity:.7;">⓵</span> Approximate Knowledge Panel preview — Google chooses what to show.
                    </div>
                </div>

                {{-- Quick explainer / why-this-matters card --}}
                <div style="display:flex; flex-direction:column; gap:12px; font-size:13px; color:var(--tb-fg-soft); line-height:1.6;">
                    <div style="background:rgba(2,132,199,.06); border:1px solid rgba(2,132,199,.20);
                                border-radius:10px; padding:12px 14px; color:var(--tb-fg);">
                        <strong style="color:#0284c7;">What gets emitted</strong>
                        <pre style="margin:8px 0 0; padding:10px 12px; border-radius:6px;
                                    background:rgba(15,23,42,.05); color:var(--tb-fg);
                                    font-size:11px; font-family:ui-monospace, Menlo, monospace;
                                    overflow-x:auto; line-height:1.55;">&lt;script type="application/ld+json"&gt;
{
  "@context": "https://schema.org",
  "@type":    "Organization",
  "name":     "{{ $snippet['orgName'] ?: 'Your name' }}",
  "url":      "{{ $snippet['orgUrl'] ?: 'https://example.com' }}",
  "logo":     "{{ $snippet['orgLogo'] ? '...' : '(no logo)' }}"
}
&lt;/script&gt;</pre>
                    </div>
                    <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:6px;">
                        <li style="display:flex; gap:8px;"><span>✅</span><span>Helps Google show your brand correctly when people search you by name.</span></li>
                        <li style="display:flex; gap:8px;"><span>✅</span><span>Adds a JSON-LD snippet to every public page automatically.</span></li>
                        <li style="display:flex; gap:8px;"><span>✅</span><span>Recognized by Bing, Yandex, DuckDuckGo and AI crawlers (ChatGPT, Perplexity).</span></li>
                        <li style="display:flex; gap:8px;"><span>ℹ️</span><span>Edits below take effect after you click <strong>Save settings</strong>.</span></li>
                    </ul>
                </div>
            </div>

            <style>
                @media (max-width: 880px) {
                    .seo-kp-grid { grid-template-columns: 1fr !important; }
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
                                <tr
                                    class="seo-audit-row"
                                    role="button"
                                    tabindex="0"
                                    title="Edit meta for this {{ strtolower($item['type']) }}"
                                    wire:click="mountAction('editMeta', { type: '{{ $item['kind'] }}', id: {{ (int) $item['id'] }} })"
                                    x-on:keydown.enter.prevent="$el.click()"
                                    x-on:keydown.space.prevent="$el.click()"
                                    style="border-top:1px solid var(--tb-border-soft); vertical-align:top; cursor:pointer; transition:background-color .12s ease;"
                                >
                                    <td style="padding:12px 14px; color:var(--tb-muted); font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.06em;">
                                        {{ $item['type'] }}
                                    </td>
                                    <td style="padding:12px 14px;">
                                        <div style="font-weight:600; color:var(--tb-fg); margin-bottom:2px;">{{ $item['title'] }}</div>
                                        <a href="{{ $item['url'] }}" target="_blank" rel="noopener"
                                           x-on:click.stop
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
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <style>
                    .seo-audit-row:hover { background: color-mix(in srgb, var(--tb-fg) 4%, transparent); }
                    .seo-audit-row:focus-visible { outline: 2px solid #0284c7; outline-offset: -2px; }
                </style>
            @endif
        </div>
    </div>

    <x-filament-actions::modals />
</x-filament-panels::page>
