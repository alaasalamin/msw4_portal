<x-filament-panels::page>
    @php
        $rules  = $this->getAutomationRules();
        $phases = $this->getPhases();
        $totalSteps = $phases->sum(fn ($p) => $p->steps->count());
        $diagramColors = ['#3b82f6','#10b981','#f59e0b','#8b5cf6','#ef4444','#06b6d4','#ec4899'];

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

    {{-- ── Workflow diagram ──────────────────────────────────────────────── --}}
    <style>
        .wfa-card { border-radius:12px; padding:20px 24px; margin-bottom:24px; }
        .wfa-card { background:#fff; border:1px solid rgba(0,0,0,.07); }
        .dark .wfa-card { background:#111827; border-color:rgba(255,255,255,.08); }

        .wfa-emp-avatar { width:22px; height:22px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; font-size:9px; font-weight:700; color:#fff; flex-shrink:0; border:2px solid #fff; margin-left:-6px; }
        .dark .wfa-emp-avatar { border-color:#111827; }
        .wfa-emp-name { font-size:10px; color:#6b7280; }
        .dark .wfa-emp-name { color:#9ca3af; }
        .wfa-emp-none { font-size:10px; color:#d1d5db; font-style:italic; margin-left:10px; }
        .dark .wfa-emp-none { color:#4b5563; }

        .wfa-divider { height:1px; background:#f3f4f6; margin:20px 0; }
        .dark .wfa-divider { background:rgba(255,255,255,.06); }

        .wfa-step-label { font-size:10px; line-height:1.35; color:#6b7280; text-align:center; max-width:72px; word-break:break-word; margin:0; }
        .dark .wfa-step-label { color:#9ca3af; }

        .wfa-node {
            width:32px; height:32px; border-radius:50%; border-width:2px; border-style:solid;
            background:#fff; display:flex; align-items:center; justify-content:center;
            font-size:11px; font-weight:700; flex-shrink:0;
            box-shadow:0 1px 4px rgba(0,0,0,.08);
            cursor:pointer; transition:transform .15s, box-shadow .15s;
        }
        .wfa-node:hover { transform:scale(1.18); box-shadow:0 4px 12px rgba(0,0,0,.15); }
        .dark .wfa-node { background:#111827; }
        .dark .wfa-node:hover { box-shadow:0 4px 12px rgba(0,0,0,.5); }

        .wfa-overlay {
            position:fixed; inset:0; z-index:9999;
            background:rgba(0,0,0,.45); backdrop-filter:blur(3px);
            display:flex; align-items:center; justify-content:center; padding:16px;
        }
        .wfa-modal { background:#fff; border-radius:14px; width:100%; max-width:480px; box-shadow:0 20px 60px rgba(0,0,0,.2); overflow:hidden; }
        .dark .wfa-modal { background:#1f2937; }
        .wfa-modal-header { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid #f3f4f6; }
        .dark .wfa-modal-header { border-bottom-color:rgba(255,255,255,.07); }
        .wfa-modal-title { font-size:14px; font-weight:600; color:#111827; }
        .dark .wfa-modal-title { color:#f9fafb; }
        .wfa-modal-close { width:28px; height:28px; border-radius:6px; border:none; cursor:pointer; background:#f3f4f6; color:#6b7280; display:flex; align-items:center; justify-content:center; font-size:16px; transition:background .15s; }
        .wfa-modal-close:hover { background:#e5e7eb; }
        .dark .wfa-modal-close { background:rgba(255,255,255,.08); color:#9ca3af; }
        .wfa-modal-body { padding:20px; }
        .wfa-modal-step-name { font-size:16px; font-weight:600; color:#111827; margin:0 0 4px; }
        .dark .wfa-modal-step-name { color:#f9fafb; }
        .wfa-modal-phase { font-size:12px; color:#9ca3af; margin:0; }

        .wfa-section-label {
            font-size:11px; font-weight:700; letter-spacing:.06em; text-transform:uppercase;
            color:#9ca3af; margin:20px 0 10px;
        }
        .wfa-field-row {
            display:flex; align-items:center; gap:8px;
            padding:8px 12px; border-radius:8px;
            border:1px solid #e5e7eb; background:#f9fafb;
            margin-bottom:6px;
        }
        .dark .wfa-field-row { border-color:rgba(255,255,255,.08); background:rgba(255,255,255,.04); }
        .wfa-field-icon {
            width:28px; height:28px; border-radius:6px; flex-shrink:0;
            background:rgba(99,102,241,.12); border:1px solid rgba(99,102,241,.2);
            display:flex; align-items:center; justify-content:center; font-size:13px;
        }
        .wfa-field-name { flex:1; font-size:13px; font-weight:500; color:#374151; }
        .dark .wfa-field-name { color:#e5e7eb; }
        .wfa-field-type { font-size:10px; color:#9ca3af; padding:2px 6px; border-radius:4px; background:#f3f4f6; }
        .dark .wfa-field-type { background:rgba(255,255,255,.07); }
        .wfa-field-remove {
            width:22px; height:22px; border-radius:5px; border:none; cursor:pointer;
            background:rgba(239,68,68,.1); color:#ef4444;
            display:flex; align-items:center; justify-content:center; font-size:12px;
            transition:background .15s;
        }
        .wfa-field-remove:hover { background:rgba(239,68,68,.2); }

        .wfa-add-row { display:flex; gap:8px; margin-top:8px; }
        .wfa-add-input {
            flex:1; padding:8px 12px; border-radius:8px; font-size:13px;
            border:1px solid #d1d5db; background:#fff; color:#111827;
            outline:none; transition:border-color .15s;
        }
        .wfa-add-input:focus { border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,.1); }
        .dark .wfa-add-input { background:#374151; border-color:rgba(255,255,255,.12); color:#f9fafb; }
        .dark .wfa-add-input:focus { border-color:#818cf8; }
        .wfa-add-btn {
            padding:8px 14px; border-radius:8px; border:none; cursor:pointer; font-size:12px; font-weight:600;
            background:#6366f1; color:#fff; transition:opacity .15s;
            white-space:nowrap;
        }
        .wfa-add-btn:hover { opacity:.88; }

        .wfa-save-btn {
            display:block; width:100%; margin-top:16px;
            padding:10px; border-radius:10px; border:none; cursor:pointer;
            font-size:14px; font-weight:600; color:#fff;
            background:linear-gradient(135deg,#6366f1,#8b5cf6);
            transition:opacity .15s;
        }
        .wfa-save-btn:hover { opacity:.9; }
        .wfa-modal-footer { padding:0 20px 20px; }

        .wfa-empty-fields {
            padding:12px; border-radius:8px; border:1px dashed #d1d5db;
            text-align:center; font-size:12px; color:#9ca3af; font-style:italic;
        }
        .dark .wfa-empty-fields { border-color:rgba(255,255,255,.1); }
    </style>

    <div class="wfa-card">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
            <span style="font-size:13px; font-weight:600; color:#374151;" class="dark:text-gray-100">
                Workflow-Schritte — klicke auf einen Schritt für Details
            </span>
            <span style="font-size:11px; color:#9ca3af;">{{ $totalSteps }} Schritte</span>
        </div>

        @php $avatarColors = ['#3b82f6','#10b981','#f59e0b','#8b5cf6','#ef4444','#06b6d4','#ec4899','#f97316']; @endphp

        @forelse ($phases as $phaseIndex => $phase)
            @php
                $color = $diagramColors[$phaseIndex % count($diagramColors)];
                $items = $phase->steps;
                $count = $items->count();
                [$r, $g, $b] = sscanf($color, '#%02x%02x%02x');
                $phaseEmployees = $phase->steps->flatMap(fn ($s) => $s->employees)->unique('id')->values();
            @endphp

            @if ($count > 0)
                <div>
                    <div style="display:flex; align-items:center; flex-wrap:wrap; gap:8px; margin-bottom:16px;">
                        <div style="display:inline-flex; align-items:center; gap:6px;
                                    background:rgba({{ $r }},{{ $g }},{{ $b }},.12);
                                    border:1px solid rgba({{ $r }},{{ $g }},{{ $b }},.35);
                                    border-radius:20px; padding:3px 10px 3px 5px;">
                            <span style="display:inline-flex; align-items:center; justify-content:center;
                                         width:18px; height:18px; border-radius:50%;
                                         background:{{ $color }}; color:#fff; font-size:10px; font-weight:700;">
                                {{ $phaseIndex + 1 }}
                            </span>
                            <span style="font-size:11px; font-weight:600; color:{{ $color }};">{{ $phase->label }}</span>
                        </div>
                        <span style="width:3px; height:3px; border-radius:50%; background:#d1d5db; flex-shrink:0;"></span>
                        <span style="font-size:10px; color:#9ca3af; white-space:nowrap;">Zuständig:</span>
                        @if ($phaseEmployees->isEmpty())
                            <span class="wfa-emp-none">Niemand zugewiesen</span>
                        @else
                            <div style="display:inline-flex; align-items:center;">
                                @foreach ($phaseEmployees->take(5) as $ei => $emp)
                                    @php $initials = collect(explode(' ', $emp->name))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode(''); @endphp
                                    <span class="wfa-emp-avatar" style="background:{{ $avatarColors[$ei % count($avatarColors)] }}; z-index:{{ 10 - $ei }};" title="{{ $emp->name }}">{{ $initials }}</span>
                                @endforeach
                                @if ($phaseEmployees->count() > 5)
                                    <span class="wfa-emp-avatar" style="background:#9ca3af; z-index:5;">+{{ $phaseEmployees->count() - 5 }}</span>
                                @endif
                            </div>
                            <span class="wfa-emp-name">{{ $phaseEmployees->pluck('name')->join(', ') }}</span>
                        @endif
                    </div>

                    <div style="position:relative; padding:0 16px;">
                        <div style="position:absolute; top:15px; height:2px;
                                    background:rgba({{ $r }},{{ $g }},{{ $b }},.25); border-radius:2px;
                                    left:calc(16px + (100% - 32px) / {{ $count }} / 2);
                                    right:calc(16px + (100% - 32px) / {{ $count }} / 2);"></div>
                        <div style="display:grid; grid-template-columns:repeat({{ $count }}, 1fr); gap:4px; position:relative; z-index:1;">
                            @foreach ($items as $i => $step)
                                @php $hasFields = !empty($step->custom_fields); @endphp
                                <div style="display:flex; flex-direction:column; align-items:center; gap:8px;">
                                    <div style="position:relative; display:inline-flex;">
                                        <button type="button" wire:click="selectStep({{ $step->id }})"
                                                class="wfa-node" style="border-color:{{ $color }}; color:{{ $color }};"
                                                title="{{ $step->label }}">{{ $i + 1 }}</button>
                                        @if ($hasFields)
                                            <span style="position:absolute; top:-3px; right:-3px;
                                                         width:10px; height:10px; border-radius:50%;
                                                         background:#6366f1; border:2px solid #fff;
                                                         font-size:0;"
                                                  title="{{ count($step->custom_fields) }} Feld(er) definiert"></span>
                                        @endif
                                    </div>
                                    <p class="wfa-step-label">{{ $step->label }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @if (!$loop->last)<div class="wfa-divider"></div>@endif
            @endif
        @empty
            <p style="text-align:center; font-size:13px; color:#9ca3af; padding:20px 0;">Keine Phasen vorhanden.</p>
        @endforelse
    </div>

    {{-- Step detail modal --}}
    @if ($selectedStep)
        <div class="wfa-overlay" wire:click.self="closeModal">
            <div class="wfa-modal" style="max-height:90vh; overflow-y:auto;">
                <div class="wfa-modal-header">
                    <div>
                        <div class="wfa-modal-title">{{ $selectedStep['label'] }}</div>
                        <div class="wfa-modal-phase">Phase: {{ $selectedStep['phase'] }}</div>
                    </div>
                    <button type="button" class="wfa-modal-close" wire:click="closeModal">✕</button>
                </div>

                <div class="wfa-modal-body">

                    {{-- ── Custom input fields ─────────────────────────────── --}}
                    <div class="wfa-section-label">Pflichtfelder für diesen Schritt</div>

                    @if (empty($stepFields))
                        <div class="wfa-empty-fields">Noch keine Felder definiert.</div>
                    @else
                        @foreach ($stepFields as $fi => $field)
                            <div class="wfa-field-row">
                                <div class="wfa-field-icon">✏️</div>
                                <span class="wfa-field-name">{{ $field['label'] }}</span>
                                <span class="wfa-field-type">Text</span>
                                <button type="button" class="wfa-field-remove"
                                        wire:click="removeField({{ $fi }})"
                                        title="Feld entfernen">✕</button>
                            </div>
                        @endforeach
                    @endif

                    {{-- Add new field --}}
                    <div class="wfa-add-row">
                        <input type="text"
                               wire:model="newFieldLabel"
                               wire:keydown.enter.prevent="addField"
                               class="wfa-add-input"
                               placeholder="z.B. Teilename, Seriennummer …">
                        <button type="button" class="wfa-add-btn" wire:click="addField">
                            + Hinzufügen
                        </button>
                    </div>

                    {{-- ── Automations linked to this step ────────────────── --}}
                    @php
                        $stepRules = $rules->filter(fn($r) =>
                            $r->trigger_type === 'step_changed' &&
                            (empty($r->trigger_config['step_id']) || (int)$r->trigger_config['step_id'] === $selectedStep['id'])
                        );
                    @endphp
                    @if ($stepRules->isNotEmpty())
                        <div class="wfa-section-label">Verknüpfte Automationen</div>
                        <div style="display:flex; flex-direction:column; gap:6px;">
                            @foreach ($stepRules as $sr)
                                <div style="padding:10px 14px; border-radius:8px; background:#f9fafb; border:1px solid #e5e7eb; display:flex; align-items:center; gap:8px;">
                                    <span style="width:7px; height:7px; border-radius:50%; background:{{ $sr->is_active ? '#22c55e' : '#9ca3af' }}; flex-shrink:0;"></span>
                                    <span style="font-size:13px; font-weight:500; color:#111827; flex:1;" class="dark:text-gray-200">{{ $sr->name }}</span>
                                    <span style="font-size:11px; color:#9ca3af;">{{ $sr->actions->count() }} Aktion(en)</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                </div>

                <div class="wfa-modal-footer">
                    <button type="button" class="wfa-save-btn" wire:click="saveStepFields">
                        Speichern
                    </button>
                </div>
            </div>
        </div>
    @endif
    {{-- ── End workflow diagram ───────────────────────────────────────────── --}}

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
