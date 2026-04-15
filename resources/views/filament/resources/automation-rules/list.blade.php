<x-filament-panels::page>
    @php
        $rules = $this->getAutomationRules();

        $triggerMeta = [
            'step_changed'      => ['icon' => '⚡', 'color' => '#8b5cf6', 'label' => 'Schritt geändert'],
            'customer_approved' => ['icon' => '✅', 'color' => '#10b981', 'label' => 'Kunde zugestimmt'],
            'customer_declined' => ['icon' => '❌', 'color' => '#ef4444', 'label' => 'Kunde abgelehnt'],
            'payment_received'  => ['icon' => '💶', 'color' => '#f59e0b', 'label' => 'Zahlung erhalten'],
        ];

        $actionMeta = [
            'send_allowance'   => ['icon' => '📋', 'color' => '#3b82f6', 'label' => 'Kundenfreigabe'],
            'notify_employee'  => ['icon' => '🔔', 'color' => '#f59e0b', 'label' => 'Mitarbeiter'],
            'send_email'       => ['icon' => '✉️',  'color' => '#10b981', 'label' => 'E-Mail'],
            'change_step'      => ['icon' => '➡️',  'color' => '#ec4899', 'label' => 'Schritt wechseln'],
            'generate_invoice' => ['icon' => '🧾', 'color' => '#eab308', 'label' => 'RSW Rechnung'],
        ];

        function nodeDetail(\App\Models\AutomationAction $action, array $meta): string {
            $cfg = $action->action_config ?? [];
            return match($action->action_type) {
                'send_allowance'   => 'Gültig ' . ($cfg['expires_days'] ?? 7) . ' Tage',
                'notify_employee'  => empty($cfg['employee_ids']) ? 'Alle Mitarbeiter' : count($cfg['employee_ids']) . ' Mitarbeiter',
                'send_email'       => $cfg['subject'] ?? 'Kein Betreff',
                'change_step'      => isset($cfg['step_id'])
                    ? (\App\Models\WorkflowStep::find($cfg['step_id'])?->label ?? 'Unbekannt')
                    : 'Kein Schritt',
                'generate_invoice' => 'Vorlage: ' . ($cfg['template'] ?? 'Standard'),
                default            => '',
            };
        }
    @endphp

    <style>
        /* ── Canvas wrapper ─────────────────────────── */
        .ac-empty {
            text-align: center; padding: 60px 24px; font-size: 14px;
        }
        .dark .ac-empty { color: #6b7280; }
        .ac-empty { color: #9ca3af; }

        /* ── Rule card ──────────────────────────────── */
        .ac-card {
            border-radius: 16px; padding: 0;
            border: 1px solid rgba(0,0,0,0.08);
            margin-bottom: 20px;
            overflow: hidden;
            transition: box-shadow .2s, border-color .2s;
        }
        .ac-card:hover { box-shadow: 0 8px 32px rgba(0,0,0,0.12); }
        .ac-card { background: #fff; }
        .dark .ac-card {
            background: #111827;
            border-color: rgba(255,255,255,0.07);
        }
        .dark .ac-card:hover { box-shadow: 0 8px 32px rgba(0,0,0,0.5); }

        /* ── Card header ────────────────────────────── */
        .ac-header {
            display: flex; align-items: center; gap: 12px;
            padding: 14px 20px;
            border-bottom: 1px solid rgba(0,0,0,0.06);
        }
        .dark .ac-header { border-bottom-color: rgba(255,255,255,0.06); }
        .ac-header { background: #f9fafb; }
        .dark .ac-header { background: #0f172a; }

        .ac-status-dot {
            width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
        }
        .ac-status-dot.active { background: #22c55e; box-shadow: 0 0 6px rgba(34,197,94,.6); }
        .ac-status-dot.inactive { background: #6b7280; }

        .ac-rule-name { font-size: 14px; font-weight: 600; flex: 1; }
        .ac-rule-name { color: #111827; }
        .dark .ac-rule-name { color: #f3f4f6; }

        .ac-rule-desc { font-size: 12px; }
        .ac-rule-desc { color: #9ca3af; }

        .ac-badge {
            font-size: 11px; font-weight: 600; padding: 2px 8px;
            border-radius: 20px; border: 1px solid;
        }
        .ac-badge-active { background: rgba(34,197,94,.1); color: #16a34a; border-color: rgba(34,197,94,.3); }
        .dark .ac-badge-active { color: #4ade80; }
        .ac-badge-inactive { background: rgba(107,114,128,.1); color: #6b7280; border-color: rgba(107,114,128,.2); }

        .ac-btn {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 12px; font-weight: 500;
            padding: 4px 10px; border-radius: 6px; border: 1px solid;
            cursor: pointer; background: transparent;
            transition: background .15s, color .15s;
            text-decoration: none;
        }
        .ac-btn-toggle { color: #6b7280; border-color: rgba(107,114,128,.25); }
        .ac-btn-toggle:hover { background: rgba(107,114,128,.08); }
        .ac-btn-edit { color: #6366f1; border-color: rgba(99,102,241,.3); }
        .ac-btn-edit:hover { background: rgba(99,102,241,.08); }
        .ac-btn-delete { color: #ef4444; border-color: rgba(239,68,68,.2); }
        .ac-btn-delete:hover { background: rgba(239,68,68,.06); }

        /* ── Node flow ──────────────────────────────── */
        .ac-flow {
            padding: 28px 24px 32px;
            display: flex; align-items: center;
            gap: 0; overflow-x: auto;
        }

        /* ── Connector ──────────────────────────────── */
        .ac-connector {
            flex-shrink: 0;
            display: flex; align-items: center;
            width: 72px; position: relative; height: 2px;
        }
        .ac-connector-line {
            position: absolute; top: 0; left: 0; right: 0; height: 2px;
            border-radius: 2px;
        }
        .ac-connector-arrow {
            position: absolute; right: -1px; top: -5px;
            width: 0; height: 0;
            border-top: 6px solid transparent;
            border-bottom: 6px solid transparent;
        }
        /* active connectors animate */
        .ac-card.is-active .ac-connector-line {
            background-size: 200% 100%;
            animation: ac-flow-anim 1.5s linear infinite;
        }
        @keyframes ac-flow-anim {
            0%   { background-position: 200% 0; }
            100% { background-position: 0% 0; }
        }
        .ac-card.is-inactive .ac-connector-line {
            background: rgba(107,114,128,.2) !important;
        }
        .ac-card.is-inactive .ac-connector-arrow {
            border-left-color: rgba(107,114,128,.2) !important;
        }

        /* ── Node ───────────────────────────────────── */
        .ac-node {
            flex-shrink: 0;
            display: flex; flex-direction: column; align-items: center; gap: 10px;
            width: 120px;
        }
        .ac-node-circle {
            width: 64px; height: 64px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px;
            border: 3px solid;
            position: relative;
            transition: transform .2s, box-shadow .2s;
        }
        .ac-node-circle:hover { transform: scale(1.08); }
        .ac-card.is-active .ac-node-circle {
            box-shadow: 0 0 0 6px var(--node-glow);
        }
        .ac-card.is-inactive .ac-node-circle {
            filter: grayscale(0.6) opacity(0.6);
            box-shadow: none !important;
        }

        /* pulse ring on trigger node when active */
        .ac-card.is-active .ac-node.is-trigger .ac-node-circle::after {
            content: '';
            position: absolute; inset: -8px; border-radius: 50%;
            border: 2px solid var(--node-color);
            animation: ac-pulse-ring 2s ease-out infinite;
            opacity: 0;
        }
        @keyframes ac-pulse-ring {
            0%   { transform: scale(.85); opacity: .8; }
            100% { transform: scale(1.4);  opacity: 0; }
        }

        .ac-node-label {
            font-size: 11px; font-weight: 600; text-align: center;
            text-transform: uppercase; letter-spacing: .04em;
        }
        .ac-node-label { color: #374151; }
        .dark .ac-node-label { color: #d1d5db; }

        .ac-node-detail {
            font-size: 11px; text-align: center; line-height: 1.35;
            max-width: 110px; word-break: break-word;
        }
        .ac-node-detail { color: #9ca3af; }
        .dark .ac-node-detail { color: #6b7280; }

        /* ── Empty state ────────────────────────────── */
        .ac-no-actions {
            font-size: 12px; font-style: italic; padding: 20px 24px;
        }
        .ac-no-actions { color: #9ca3af; }

        /* ── Log count badge ────────────────────────── */
        .ac-log-badge {
            font-size: 11px; color: #6b7280;
            display: flex; align-items: center; gap: 4px;
        }
    </style>

    <div style="max-width:100%;">

        @if($rules->isEmpty())
            <div class="ac-empty">
                <div style="font-size:40px; margin-bottom:12px;">⚡</div>
                <div style="font-weight:600; margin-bottom:6px;">Keine Automationen</div>
                <div>Erstelle deine erste Automation über den Button oben.</div>
            </div>
        @else
            @foreach($rules as $rule)
                @php
                    $tMeta  = $triggerMeta[$rule->trigger_type] ?? ['icon' => '⚡', 'color' => '#6366f1', 'label' => $rule->trigger_type];
                    $active = $rule->is_active;
                    $cfg    = $rule->trigger_config ?? [];

                    // Step label for trigger
                    $triggerDetail = '';
                    if ($rule->trigger_type === 'step_changed') {
                        $triggerDetail = ! empty($cfg['step_id'])
                            ? (\App\Models\WorkflowStep::find($cfg['step_id'])?->label ?? '?')
                            : 'Beliebiger Schritt';
                    }

                    $editUrl = \App\Filament\Resources\AutomationRuleResource::getUrl('edit', ['record' => $rule->id]);
                    $logCount = $rule->logs->count();
                @endphp

                <div class="ac-card {{ $active ? 'is-active' : 'is-inactive' }}">

                    {{-- Header --}}
                    <div class="ac-header">
                        <span class="ac-status-dot {{ $active ? 'active' : 'inactive' }}"></span>
                        <span class="ac-rule-name">{{ $rule->name }}</span>
                        @if($rule->description)
                            <span class="ac-rule-desc">{{ $rule->description }}</span>
                        @endif

                        <span class="ac-log-badge" title="Ausführungen">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                 stroke-width="1.5" stroke="currentColor" style="width:13px;height:13px;">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z"/>
                            </svg>
                            {{ $logCount }}
                        </span>

                        <span class="ac-badge {{ $active ? 'ac-badge-active' : 'ac-badge-inactive' }}">
                            {{ $active ? 'Aktiv' : 'Inaktiv' }}
                        </span>

                        <button wire:click="toggleRule({{ $rule->id }})" class="ac-btn ac-btn-toggle">
                            {{ $active ? '⏸ Pause' : '▶ Start' }}
                        </button>
                        <a href="{{ $editUrl }}" class="ac-btn ac-btn-edit">✏ Bearbeiten</a>
                        <button wire:click="deleteRule({{ $rule->id }})"
                                wire:confirm="Automation '{{ addslashes($rule->name) }}' wirklich löschen?"
                                class="ac-btn ac-btn-delete">
                            🗑
                        </button>
                    </div>

                    {{-- Node flow --}}
                    @if($rule->actions->isEmpty())
                        <div class="ac-no-actions">Keine Aktionen konfiguriert — bearbeite die Regel um Aktionen hinzuzufügen.</div>
                    @else
                        <div class="ac-flow">

                            {{-- Trigger node --}}
                            @php
                                $tc = $tMeta['color'];
                                [$tr, $tg, $tb] = sscanf($tc, '#%02x%02x%02x');
                            @endphp
                            <div class="ac-node is-trigger" style="--node-color:{{ $tc }}; --node-glow:rgba({{ $tr }},{{ $tg }},{{ $tb }},.15);">
                                <div class="ac-node-circle"
                                     style="border-color:{{ $tc }}; background:rgba({{ $tr }},{{ $tg }},{{ $tb }},.12);">
                                    {{ $tMeta['icon'] }}
                                </div>
                                <div class="ac-node-label" style="color:{{ $tc }};">{{ $tMeta['label'] }}</div>
                                @if($triggerDetail)
                                    <div class="ac-node-detail">{{ $triggerDetail }}</div>
                                @endif
                            </div>

                            {{-- Actions --}}
                            @foreach($rule->actions as $action)
                                @php
                                    $aMeta = $actionMeta[$action->action_type] ?? ['icon' => '⚙️', 'color' => '#6b7280', 'label' => $action->action_type];
                                    $ac = $aMeta['color'];
                                    [$ar, $ag, $ab] = sscanf($ac, '#%02x%02x%02x');
                                    $detail = nodeDetail($action, $aMeta);
                                @endphp

                                {{-- Connector --}}
                                <div class="ac-connector">
                                    <div class="ac-connector-line"
                                         style="background: linear-gradient(90deg, {{ $tc }}88 0%, {{ $ac }}88 50%, {{ $ac }} 100%);
                                                {{ $active ? 'background-size:200% 100%;' : '' }}">
                                    </div>
                                    <div class="ac-connector-arrow"
                                         style="border-left: 10px solid {{ $ac }};"></div>
                                </div>

                                {{-- Action node --}}
                                <div class="ac-node" style="--node-color:{{ $ac }}; --node-glow:rgba({{ $ar }},{{ $ag }},{{ $ab }},.15);">
                                    <div class="ac-node-circle"
                                         style="border-color:{{ $ac }}; background:rgba({{ $ar }},{{ $ag }},{{ $ab }},.12);">
                                        {{ $aMeta['icon'] }}
                                    </div>
                                    <div class="ac-node-label" style="color:{{ $ac }};">{{ $aMeta['label'] }}</div>
                                    @if($detail)
                                        <div class="ac-node-detail">{{ $detail }}</div>
                                    @endif
                                </div>

                                @php $tc = $ac; @endphp {{-- next connector starts from this node's color --}}
                            @endforeach

                        </div>
                    @endif

                </div>
            @endforeach
        @endif

    </div>
</x-filament-panels::page>
