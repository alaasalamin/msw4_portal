<x-filament-panels::page>
    @php
        $jobs       = $this->getJobs();
        $failed     = $this->getFailedJobs();
        $delayed    = array_filter($jobs, fn($j) => $j['is_delayed']);
        $pending    = array_filter($jobs, fn($j) => !$j['is_delayed'] && !$j['reserved']);
        $running    = array_filter($jobs, fn($j) => $j['reserved']);

        $jobIcons = [
            'SendAutomationEmailJob' => ['icon' => '✉️',  'color' => '#6366f1'],
            'RunAutomationAction'    => ['icon' => '⚡',  'color' => '#f59e0b'],
        ];
    @endphp

    <style>
        .sj-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:24px; }
        @media(max-width:640px){ .sj-stats { grid-template-columns:repeat(2,1fr); } }

        .sj-stat {
            border-radius:12px; padding:16px 18px;
            border:1px solid rgba(0,0,0,.07);
            background:#fff; display:flex; flex-direction:column; gap:4px;
        }
        .dark .sj-stat { background:#111827; border-color:rgba(255,255,255,.08); }
        .sj-stat-val { font-size:28px; font-weight:800; line-height:1; }
        .sj-stat-lbl { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:#9ca3af; }

        .sj-section { margin-bottom:28px; }
        .sj-section-title {
            font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.06em;
            color:#9ca3af; margin-bottom:10px; display:flex; align-items:center; gap:8px;
        }
        .sj-section-title span { display:inline-flex; align-items:center; justify-content:center;
            min-width:20px; height:20px; border-radius:10px; padding:0 6px;
            font-size:10px; font-weight:800; color:#fff; }

        .sj-card {
            border-radius:10px; border:1px solid rgba(0,0,0,.06);
            background:#fff; margin-bottom:8px; overflow:hidden;
            transition:box-shadow .15s;
        }
        .sj-card:hover { box-shadow:0 4px 16px rgba(0,0,0,.08); }
        .dark .sj-card { background:#111827; border-color:rgba(255,255,255,.07); }
        .dark .sj-card:hover { box-shadow:0 4px 16px rgba(0,0,0,.4); }

        .sj-card-row {
            display:flex; align-items:center; gap:12px; padding:12px 16px;
        }

        .sj-icon {
            width:36px; height:36px; border-radius:9px; flex-shrink:0;
            display:flex; align-items:center; justify-content:center;
            font-size:16px;
        }

        .sj-job-name { font-size:13px; font-weight:600; color:#111827; }
        .dark .sj-job-name { color:#f3f4f6; }
        .sj-job-queue { font-size:10px; color:#9ca3af; font-weight:500;
            background:#f3f4f6; border-radius:4px; padding:1px 6px; }
        .dark .sj-job-queue { background:rgba(255,255,255,.07); }

        .sj-time {
            font-size:12px; font-weight:600; white-space:nowrap;
        }
        .sj-countdown { font-size:11px; color:#9ca3af; white-space:nowrap; }

        .sj-meta {
            padding:0 16px 12px 64px;
            display:flex; flex-wrap:wrap; gap:6px;
        }
        .sj-meta-pill {
            font-size:10px; padding:2px 8px; border-radius:20px;
            background:#f1f5f9; color:#475569; border:1px solid #e2e8f0;
        }
        .dark .sj-meta-pill { background:rgba(255,255,255,.05); color:#94a3b8; border-color:rgba(255,255,255,.08); }

        .sj-badge {
            font-size:10px; font-weight:700; padding:2px 8px; border-radius:20px; border:1px solid;
        }
        .sj-badge-delayed  { background:rgba(99,102,241,.1);  color:#6366f1; border-color:rgba(99,102,241,.25); }
        .sj-badge-pending  { background:rgba(16,185,129,.1);  color:#10b981; border-color:rgba(16,185,129,.25); }
        .sj-badge-running  { background:rgba(245,158,11,.1);  color:#f59e0b; border-color:rgba(245,158,11,.25); }
        .sj-badge-failed   { background:rgba(239,68,68,.1);   color:#ef4444; border-color:rgba(239,68,68,.25); }
        .sj-badge-attempts { background:rgba(107,114,128,.1); color:#6b7280; border-color:rgba(107,114,128,.2); }

        .sj-btn {
            display:inline-flex; align-items:center; gap:4px;
            font-size:11px; font-weight:600; padding:4px 10px;
            border-radius:6px; border:1px solid; cursor:pointer;
            background:transparent; transition:background .12s;
        }
        .sj-btn-retry { color:#6366f1; border-color:rgba(99,102,241,.3); }
        .sj-btn-retry:hover { background:rgba(99,102,241,.07); }
        .sj-btn-del   { color:#ef4444; border-color:rgba(239,68,68,.2); }
        .sj-btn-del:hover { background:rgba(239,68,68,.05); }

        .sj-exc {
            font-size:11px; color:#ef4444; font-family:monospace;
            padding:0 16px 10px 64px; word-break:break-all; opacity:.8;
        }
        .sj-empty { text-align:center; padding:28px; font-size:13px; color:#9ca3af; }

        .sj-progress-wrap { height:3px; background:rgba(99,102,241,.12); border-radius:2px; overflow:hidden; margin:0 16px 12px; }
        .sj-progress-fill { height:100%; border-radius:2px; background:linear-gradient(90deg,#6366f1,#8b5cf6); animation:sj-pulse 2s ease-in-out infinite; }
        @keyframes sj-pulse { 0%,100%{opacity:.6} 50%{opacity:1} }
    </style>

    {{-- ── Stats ── --}}
    <div class="sj-stats">
        <div class="sj-stat">
            <span class="sj-stat-val" style="color:#6366f1;">{{ count($delayed) }}</span>
            <span class="sj-stat-lbl">Geplant</span>
        </div>
        <div class="sj-stat">
            <span class="sj-stat-val" style="color:#10b981;">{{ count($pending) }}</span>
            <span class="sj-stat-lbl">Ausstehend</span>
        </div>
        <div class="sj-stat">
            <span class="sj-stat-val" style="color:#f59e0b;">{{ count($running) }}</span>
            <span class="sj-stat-lbl">Laufend</span>
        </div>
        <div class="sj-stat">
            <span class="sj-stat-val" style="color:#ef4444;">{{ count($failed) }}</span>
            <span class="sj-stat-lbl">Fehlgeschlagen</span>
        </div>
    </div>

    {{-- ── Delayed (scheduled) ── --}}
    <div class="sj-section">
        <div class="sj-section-title">
            ⏱ Geplante Jobs
            <span style="background:#6366f1;">{{ count($delayed) }}</span>
        </div>

        @forelse ($delayed as $job)
            @php
                $meta  = $jobIcons[$job['class']] ?? ['icon' => '⚙️', 'color' => '#6b7280'];
                $mins  = intdiv($job['delay_secs'], 60);
                $hours = intdiv($mins, 60);
                $fmt   = $hours > 0 ? "in {$hours} Std. " . ($mins % 60) . " Min." : "in {$mins} Min.";
            @endphp
            <div class="sj-card">
                <div class="sj-card-row">
                    <div class="sj-icon" style="background:rgba({{ implode(',', sscanf($meta['color'],'#%02x%02x%02x')) }},.12);">
                        {{ $meta['icon'] }}
                    </div>
                    <div style="flex:1; min-width:0;">
                        <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                            <span class="sj-job-name">{{ $job['class'] }}</span>
                            <span class="sj-job-queue">{{ $job['queue'] }}</span>
                            <span class="sj-badge sj-badge-delayed">Geplant</span>
                            @if($job['attempts'] > 0)
                                <span class="sj-badge sj-badge-attempts">{{ $job['attempts'] }}× versucht</span>
                            @endif
                        </div>
                    </div>
                    <div style="text-align:right; flex-shrink:0;">
                        <div class="sj-time" style="color:#6366f1;">{{ $fmt }}</div>
                        <div class="sj-countdown">{{ \Carbon\Carbon::createFromTimestamp($job['available_at'])->format('d.m.Y H:i') }}</div>
                    </div>
                    <button type="button" class="sj-btn sj-btn-del"
                            wire:click="deleteJob({{ $job['id'] }})"
                            wire:confirm="Job löschen?">✕</button>
                </div>
                @if (!empty($job['meta']))
                    <div class="sj-meta">
                        @foreach ($job['meta'] as $key => $val)
                            <span class="sj-meta-pill"><strong>{{ $key }}:</strong> {{ $val }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="sj-empty">Keine geplanten Jobs.</div>
        @endforelse
    </div>

    {{-- ── Pending / Running ── --}}
    @if (count($pending) || count($running))
        <div class="sj-section">
            <div class="sj-section-title">
                ▶ Ausstehend / Laufend
                <span style="background:#10b981;">{{ count($pending) + count($running) }}</span>
            </div>

            @foreach (array_merge($running, $pending) as $job)
                @php $meta = $jobIcons[$job['class']] ?? ['icon' => '⚙️', 'color' => '#6b7280']; @endphp
                <div class="sj-card">
                    <div class="sj-card-row">
                        <div class="sj-icon" style="background:rgba({{ implode(',', sscanf($meta['color'],'#%02x%02x%02x')) }},.12);">
                            {{ $meta['icon'] }}
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                                <span class="sj-job-name">{{ $job['class'] }}</span>
                                <span class="sj-job-queue">{{ $job['queue'] }}</span>
                                <span class="sj-badge {{ $job['reserved'] ? 'sj-badge-running' : 'sj-badge-pending' }}">
                                    {{ $job['reserved'] ? 'Laufend' : 'Ausstehend' }}
                                </span>
                            </div>
                        </div>
                        <div class="sj-countdown">
                            {{ \Carbon\Carbon::createFromTimestamp($job['created_at'])->diffForHumans() }}
                        </div>
                        <button type="button" class="sj-btn sj-btn-del"
                                wire:click="deleteJob({{ $job['id'] }})"
                                wire:confirm="Job löschen?">✕</button>
                    </div>
                    @if($job['reserved'])
                        <div class="sj-progress-wrap"><div class="sj-progress-fill" style="width:60%;"></div></div>
                    @endif
                    @if (!empty($job['meta']))
                        <div class="sj-meta">
                            @foreach ($job['meta'] as $key => $val)
                                <span class="sj-meta-pill"><strong>{{ $key }}:</strong> {{ $val }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    {{-- ── Failed ── --}}
    <div class="sj-section">
        <div class="sj-section-title">
            ✕ Fehlgeschlagene Jobs
            <span style="background:#ef4444;">{{ count($failed) }}</span>
        </div>

        @forelse ($failed as $job)
            @php
                $meta      = $jobIcons[$job['class']] ?? ['icon' => '⚙️', 'color' => '#6b7280'];
                $expanded  = $expandedJob === $job['uuid'];
            @endphp
            <div class="sj-card">
                <div class="sj-card-row">
                    <div class="sj-icon" style="background:rgba(239,68,68,.1);">
                        {{ $meta['icon'] }}
                    </div>
                    <div style="flex:1; min-width:0;">
                        <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                            <span class="sj-job-name">{{ $job['class'] }}</span>
                            <span class="sj-job-queue">{{ $job['queue'] }}</span>
                            <span class="sj-badge sj-badge-failed">Fehlgeschlagen</span>
                        </div>
                    </div>
                    <div class="sj-countdown">{{ $job['failed_at'] }}</div>
                    <button type="button" class="sj-btn sj-btn-retry"
                            wire:click="retryFailed('{{ $job['uuid'] }}')"
                            title="Erneut versuchen">↺ Retry</button>
                    <button type="button" class="sj-btn sj-btn-del"
                            wire:click="deleteFailed('{{ $job['uuid'] }}')"
                            wire:confirm="Fehlgeschlagenen Job löschen?">✕</button>
                </div>

                {{-- First line of exception + "Mehr anzeigen" toggle --}}
                @if ($job['exception'])
                    <div style="padding:0 16px 10px 64px;">
                        <div class="sj-exc" style="padding:0; margin-bottom:4px;">
                            {{ $job['exception'] }}
                        </div>
                        <button type="button"
                                wire:click="toggleJobLog('{{ $job['uuid'] }}')"
                                style="font-size:11px; font-weight:600; color:#6366f1; background:none; border:none;
                                       cursor:pointer; padding:0; display:inline-flex; align-items:center; gap:4px;">
                            {{ $expanded ? '▲ Weniger anzeigen' : '▼ Mehr anzeigen' }}
                        </button>
                    </div>

                    {{-- Full stack trace --}}
                    @if ($expanded)
                        <div style="margin:0 16px 14px; border-radius:8px; overflow:hidden;
                                    border:1px solid rgba(239,68,68,.2); background:rgba(239,68,68,.04);">
                            <div style="display:flex; align-items:center; justify-content:space-between;
                                        padding:6px 12px; border-bottom:1px solid rgba(239,68,68,.15);
                                        background:rgba(239,68,68,.08);">
                                <span style="font-size:10px; font-weight:700; color:#ef4444; letter-spacing:.05em; text-transform:uppercase;">
                                    Stack Trace
                                </span>
                                <button type="button"
                                        onclick="navigator.clipboard.writeText(this.closest('[data-trace]').querySelector('pre').innerText).then(()=>{ this.textContent='Kopiert ✓'; setTimeout(()=>this.textContent='Kopieren',1500); })"
                                        style="font-size:10px; font-weight:600; color:#9ca3af; background:none; border:none; cursor:pointer;">
                                    Kopieren
                                </button>
                            </div>
                            <div data-trace style="overflow-x:auto; max-height:360px; overflow-y:auto;">
                                <pre style="margin:0; padding:12px; font-size:11px; line-height:1.6;
                                            color:#fca5a5; font-family:'JetBrains Mono',Menlo,Monaco,monospace;
                                            white-space:pre; tab-size:4;">{{ $job['exception_full'] }}</pre>
                            </div>
                        </div>
                    @endif
                @endif

                @if (!empty($job['meta']))
                    <div class="sj-meta">
                        @foreach ($job['meta'] as $key => $val)
                            <span class="sj-meta-pill"><strong>{{ $key }}:</strong> {{ $val }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="sj-empty">Keine fehlgeschlagenen Jobs.</div>
        @endforelse
    </div>

    {{-- Auto-refresh every 30 seconds --}}
    @script
    <script>
        setInterval(() => { $wire.$refresh(); }, 30000);
    </script>
    @endscript

</x-filament-panels::page>
