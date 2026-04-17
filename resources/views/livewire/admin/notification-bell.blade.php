<div style="position:relative; display:inline-flex; align-items:center; margin-right:4px;" x-data>

    {{-- Bell button --}}
    <button
        wire:click="toggle"
        type="button"
        id="notif-bell-btn"
        style="position:relative; display:flex; align-items:center; justify-content:center;
               width:36px; height:36px; border-radius:8px; border:none; cursor:pointer;
               background:transparent; color:#6b7280; transition:background 0.15s, color 0.15s;"
        onmouseenter="var d=document.documentElement.classList.contains('dark');this.style.background=d?'rgba(255,255,255,0.1)':'rgba(0,0,0,0.05)';this.style.color=d?'#f9fafb':'#111827';"
        onmouseleave="this.style.background='transparent';this.style.color='#6b7280';"
        title="Benachrichtigungen"
    >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="1.6" stroke="currentColor"
             style="width:20px; height:20px;">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
        </svg>

        @if($unreadCount > 0)
            <span id="notif-badge" style="position:absolute; top:4px; right:4px;
                         min-width:16px; height:16px; padding:0 4px;
                         border-radius:999px; font-size:9px; font-weight:700; line-height:16px;
                         background:#ef4444; color:#fff; text-align:center;
                         border:2px solid #fff; box-sizing:content-box;">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @else
            <span id="notif-badge" style="position:absolute; top:4px; right:4px;
                         min-width:16px; height:16px; padding:0 4px;
                         border-radius:999px; font-size:9px; font-weight:700; line-height:16px;
                         background:#6b7280; color:#fff; text-align:center;
                         border:2px solid #fff; box-sizing:content-box;">
                0
            </span>
        @endif
    </button>

    {{-- Dropdown panel --}}
    @if($open)
        {{-- Backdrop --}}
        <div wire:click="toggle"
             style="position:fixed; inset:0; z-index:9998;"></div>

        <div id="notif-panel" style="
            position:absolute; top:calc(100% + 8px); right:0;
            width:320px; border-radius:12px; z-index:9999;
            box-shadow:0 8px 32px rgba(0,0,0,0.18);
            overflow:hidden;"
             class="notif-panel">

            {{-- Header --}}
            <div class="notif-panel-header" style="
                display:flex; align-items:center; justify-content:space-between;
                padding:12px 16px 10px;">
                <span style="font-size:14px; font-weight:600;">Benachrichtigungen</span>
                @if($unreadCount > 0)
                    <button wire:click="markAllRead" type="button" class="notif-mark-read"
                            style="font-size:12px; border:none; background:none; cursor:pointer; padding:0;">
                        Alle gelesen
                    </button>
                @endif
            </div>

            {{-- Notification list --}}
            <div style="max-height:320px; overflow-y:auto;">
                @forelse($notifications as $notif)
                    <div
                        wire:click="openNotification('{{ $notif['id'] }}')"
                        class="notif-item {{ $notif['read'] ? 'notif-read' : 'notif-unread' }}"
                        style="padding:10px 16px; border-top:1px solid rgba(0,0,0,0.06);
                               display:flex; gap:10px; align-items:flex-start;
                               cursor:{{ $notif['device_id'] ? 'pointer' : 'default' }};"
                    >
                        {{-- Status dot: red = unread, green = read --}}
                        <div style="flex-shrink:0; margin-top:4px;">
                            @if($notif['read'])
                                <div style="width:8px; height:8px; border-radius:50%; background:#22c55e;"></div>
                            @else
                                <div style="width:8px; height:8px; border-radius:50%; background:#ef4444;"></div>
                            @endif
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div class="notif-message" style="font-size:13px; line-height:1.4; word-break:break-word;
                                 font-weight:{{ $notif['read'] ? '400' : '500' }};">
                                {{ $notif['message'] }}
                            </div>
                            <div class="notif-time" style="font-size:11px; margin-top:3px; opacity:0.5;">
                                {{ $notif['time'] }}
                            </div>
                        </div>
                        @if($notif['device_id'] && ! $notif['read'])
                            <div style="flex-shrink:0; align-self:center; opacity:0.35;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="2" stroke="currentColor" style="width:14px;height:14px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                @empty
                    <div style="padding:24px 16px; text-align:center; font-size:13px; opacity:0.5;">
                        Keine Benachrichtigungen
                    </div>
                @endforelse
            </div>
        </div>
    @endif
</div>


<style>
    /* Light mode */
    .notif-panel {
        background: #fff;
        color: #111827;
    }
    .notif-panel-header {
        background: #f9fafb;
        border-bottom: 1px solid rgba(0,0,0,0.08);
    }
    .notif-mark-read { color: #6366f1; }
    .notif-unread { background: rgba(99,102,241,0.04); }
    .notif-message { color: #111827; }
    .notif-time { color: #6b7280; }

    /* Dark mode */
    .dark .notif-panel {
        background: #1e2533;
        color: #f3f4f6;
        box-shadow: 0 8px 32px rgba(0,0,0,0.5) !important;
    }
    .dark .notif-panel-header {
        background: #161d2d;
        border-bottom-color: rgba(255,255,255,0.08) !important;
    }
    .dark .notif-mark-read { color: #818cf8; }
    .dark .notif-item { border-top-color: rgba(255,255,255,0.06) !important; }
    .dark .notif-unread { background: rgba(99,102,241,0.1); }
    .dark .notif-message { color: #f3f4f6; }

    /* Bell button dark */
    .dark [title="Benachrichtigungen"] {
        color: #9ca3af !important;
    }
    .dark [title="Benachrichtigungen"]:hover {
        background: rgba(255,255,255,0.07) !important;
        color: #f9fafb !important;
    }
    .dark #notif-badge {
        border-color: #1f2937 !important;
    }
</style>
