<x-filament-panels::page>
    @php
        $audit   = $this->getAudit();
        $summary = $this->getAuditSummary($audit);
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
