<div style="position:relative; display:inline-flex; align-items:center; margin-right:4px;">

    {{-- Trigger button --}}
    <button
        wire:click="toggle"
        type="button"
        x-data
        style="position:relative; display:flex; align-items:center; justify-content:center;
               width:36px; height:36px; border-radius:8px; border:none; cursor:pointer;
               background:transparent; color:#6b7280; transition:background 0.15s, color 0.15s;"
        @mouseenter="const dark = document.documentElement.classList.contains('dark');
                     $el.style.background = dark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.05)';
                     $el.style.color = dark ? '#f9fafb' : '#111827';"
        @mouseleave="$el.style.background='transparent'; $el.style.color='#6b7280';"
        title="Navigation anpassen"
    >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="1.6" stroke="currentColor" style="width:20px; height:20px;">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
        </svg>
    </button>

    {{-- Modal --}}
    @if($open)
        <div
            style="position:fixed; inset:0; z-index:9998; background:rgba(0,0,0,0.35);"
            wire:click="toggle"
        ></div>

        <div class="nav-pref-panel"
            style="position:fixed; top:56px; right:16px; z-index:9999;
                   width:260px; border-radius:12px; padding:20px;
                   font-family:inherit;"
        >
            <p class="nav-pref-title" style="margin:0 0 14px; font-size:13px; font-weight:600;">
                Seitenleiste anpassen
            </p>

            <div style="display:flex; flex-direction:column; gap:10px;">
                @foreach($allGroups as $group)
                    <label class="nav-pref-label" style="display:flex; align-items:center; gap:10px;
                                  cursor:pointer; font-size:13px;">
                        <input
                            type="checkbox"
                            value="{{ $group }}"
                            wire:model="visible"
                            style="width:16px; height:16px; accent-color:#6366f1; cursor:pointer;"
                        />
                        {{ $group }}
                    </label>
                @endforeach
            </div>

            <button
                wire:click="save"
                type="button"
                style="margin-top:18px; width:100%; padding:8px 0; border-radius:8px;
                       border:none; cursor:pointer; font-size:13px; font-weight:600;
                       background:#6366f1; color:#fff; transition:background 0.15s;"
                onmouseenter="this.style.background='#4f46e5';"
                onmouseleave="this.style.background='#6366f1';"
            >
                Speichern
            </button>
        </div>
    @endif

</div>

<style>
    /* Light mode */
    .nav-pref-panel {
        background: #fff;
        box-shadow: 0 8px 32px rgba(0,0,0,0.18);
    }
    .nav-pref-title { color: #111827; }
    .nav-pref-label { color: #374151; }

    /* Dark mode */
    .dark .nav-pref-panel {
        background: #1e2533;
        box-shadow: 0 8px 32px rgba(0,0,0,0.5);
    }
    .dark .nav-pref-title { color: #f3f4f6; }
    .dark .nav-pref-label { color: #d1d5db; }
</style>
