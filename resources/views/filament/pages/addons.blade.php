<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}
    </form>

    {{-- CRM connection status badge --}}
    @if($crmTesting)
        <div style="margin-top:-12px; padding:0 1px 16px;">
            <span style="display:inline-flex; align-items:center; gap:6px; font-size:12px; color:#6b7280;">
                <svg style="width:14px;height:14px;animation:spin 1s linear infinite;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle style="opacity:.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path style="opacity:.75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
                Testing connection…
            </span>
        </div>
    @elseif($crmStatus === 'ok')
        <div style="margin-top:-12px; padding:0 1px 16px;">
            <span style="display:inline-flex; align-items:center; gap:6px; font-size:12px;
                         color:#16a34a; background:#f0fdf4; border:1px solid #bbf7d0;
                         border-radius:6px; padding:4px 10px;">
                <svg style="width:14px;height:14px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ $crmMessage }}
            </span>
        </div>
    @elseif($crmStatus === 'error')
        <div style="margin-top:-12px; padding:0 1px 16px;">
            <span style="display:inline-flex; align-items:center; gap:6px; font-size:12px;
                         color:#dc2626; background:#fef2f2; border:1px solid #fecaca;
                         border-radius:6px; padding:4px 10px;">
                <svg style="width:14px;height:14px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                </svg>
                {{ $crmMessage }}
            </span>
        </div>
    @endif

    <x-filament-actions::modals />

    <style>
        @keyframes spin { to { transform: rotate(360deg); } }
        .dark [style*="color:#16a34a"] { color:#4ade80 !important; background:#052e16 !important; border-color:#166534 !important; }
        .dark [style*="color:#dc2626"] { color:#f87171 !important; background:#450a0a !important; border-color:#991b1b !important; }
    </style>
</x-filament-panels::page>
