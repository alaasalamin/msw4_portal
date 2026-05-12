<x-filament-panels::page>
    @php($customer = $this->getRecord())

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
        }

        .cust-primary-col {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .cust-side {
            display: flex;
            align-items: center;
        }

        .cust-devices-row {
            display: flex;
            justify-content: center;
            gap: 24px;
            flex-wrap: wrap;
        }

        .cust-line {
            width: 2px;
            height: 56px;
            background: var(--cp-line);
            border-radius: 1px;
        }

        .cust-line-h {
            width: 72px;
            height: 2px;
            background: var(--cp-line);
            border-radius: 1px;
        }

        /* ── Device tile (square) ─────────────────────────────────────── */
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
        }

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
        {{ $this->notesAction }}
        {{ $this->devicesAction }}
        {{ $this->insuranceAction }}
        {{ $this->invoicesAction }}
    </div>

    <div class="cust-panel cust-graph">
      {{-- Left branch: notes + horizontal line --}}
      <div class="cust-side">
        <button type="button" class="cust-device" title="Notes" wire:click="mountAction('notes')">
            <span class="cust-badge cust-badge--danger">3</span>
            {{-- Heroicon pencil-square --}}
            <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487 18.549 2.799a2.121 2.121 0 1 1 3 3L19.862 7.487M16.862 4.487 6.65 14.7a4.5 4.5 0 0 0-1.13 1.897l-1.04 3.46 3.46-1.04a4.5 4.5 0 0 0 1.897-1.13L19.862 7.487M16.862 4.487l3 3M5.625 21h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125Z" />
            </svg>
            <span class="cust-device-label">Notes</span>
        </button>
        <div class="cust-line-h" aria-hidden="true"></div>
      </div>

      <div class="cust-primary-col">
        {{-- Devices above --}}
        <div class="cust-devices-row">
            <button type="button" class="cust-device" title="Device" wire:click="mountAction('devices')">
                <span class="cust-badge cust-badge--zero">0</span>
                {{-- Heroicon device-phone-mobile --}}
                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                </svg>
                <span class="cust-device-label">Device</span>
            </button>
        </div>

        {{-- Connector line --}}
        <div class="cust-line" aria-hidden="true"></div>

        {{-- Customer below --}}
        <button type="button" class="cust-trigger" wire:click="mountAction('edit')">
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

            <span style="display:flex; flex-direction:column; align-items:center; gap:4px;">
                <span class="cust-name">{{ $customer->name }}</span>
                <span class="cust-email">{{ $customer->email }}</span>
                <span class="cust-hint">Click to edit</span>
            </span>
        </button>

        {{-- Connector line (downward) --}}
        <div class="cust-line" aria-hidden="true"></div>

        {{-- Insurance below --}}
        <div class="cust-devices-row">
            <button type="button" class="cust-device" title="Insurance" wire:click="mountAction('insurance')">
                <span class="cust-badge cust-badge--zero">0</span>
                {{-- Heroicon shield-check --}}
                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                </svg>
                <span class="cust-device-label">Insurance</span>
            </button>
        </div>
      </div>

      {{-- Right branch: horizontal line + invoices --}}
      <div class="cust-side">
        <div class="cust-line-h" aria-hidden="true"></div>
        <button type="button" class="cust-device" title="Invoices" wire:click="mountAction('invoices')">
            <span class="cust-badge cust-badge--danger">5</span>
            {{-- Heroicon document-text --}}
            <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 3h6m-6 3h3" />
            </svg>
            <span class="cust-device-label">Invoices</span>
        </button>
      </div>
    </div>
</x-filament-panels::page>
