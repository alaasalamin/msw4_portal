import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.AdminEcho = new Echo({
    broadcaster:       'reverb',
    key:               import.meta.env.VITE_REVERB_APP_KEY,
    wsHost:            import.meta.env.VITE_REVERB_HOST,
    wsPort:            import.meta.env.VITE_REVERB_PORT ?? 8080,
    wssPort:           import.meta.env.VITE_REVERB_PORT ?? 8080,
    forceTLS:         (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
    enabledTransports: ['ws', 'wss'],
    authEndpoint:      '/admin/broadcasting/auth',
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
        },
    },
});

// ── Audio beep ────────────────────────────────────────────────────────────────
let _audioCtx = null;

function _ensureAudio() {
    if (!_audioCtx) {
        _audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    }
    if (_audioCtx.state === 'suspended') {
        _audioCtx.resume();
    }
    return _audioCtx;
}

// Unlock on any user interaction
document.addEventListener('click',    () => _ensureAudio(), { passive: true });
document.addEventListener('keydown',  () => _ensureAudio(), { passive: true });
document.addEventListener('touchend', () => _ensureAudio(), { passive: true });

async function playBeep() {
    try {
        const ctx = _ensureAudio();
        if (ctx.state === 'suspended') await ctx.resume();

        const osc  = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.type = 'sine';
        osc.frequency.setValueAtTime(1046, ctx.currentTime);
        osc.frequency.setValueAtTime(880,  ctx.currentTime + 0.12);
        gain.gain.setValueAtTime(0.3, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.45);
        osc.start(ctx.currentTime);
        osc.stop(ctx.currentTime + 0.45);
    } catch (e) {
        console.warn('[AdminEcho] beep error:', e);
    }
}

async function playBell() {
    try {
        const ctx = _ensureAudio();
        if (ctx.state === 'suspended') await ctx.resume();

        const t = ctx.currentTime;

        [
            { freq: 660,  amp: 0.5,  decay: 2.8 },
            { freq: 1100, amp: 0.35, decay: 2.0 },
            { freq: 1760, amp: 0.25, decay: 1.4 },
            { freq: 2420, amp: 0.15, decay: 1.0 },
            { freq: 3300, amp: 0.08, decay: 0.7 },
        ].forEach(({ freq, amp, decay }) => {
            const osc  = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.type = 'sine';
            osc.frequency.setValueAtTime(freq, t);
            gain.gain.setValueAtTime(amp, t);
            gain.gain.exponentialRampToValueAtTime(0.0001, t + decay);
            osc.start(t);
            osc.stop(t + decay);
        });

        console.log('[AdminEcho] bell played, ctx.state=', ctx.state);
    } catch (e) {
        console.error('[AdminEcho] bell error:', e);
    }
}

// Expose for manual console testing
window._playAdminBeep = playBeep;
window._playAdminBell = playBell;

// ── Channel subscription ──────────────────────────────────────────────────────
function subscribeUser(userId) {
    window.AdminEcho
        .private('App.Models.User.' + userId)
        .listen(
            '.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated',
            (data) => {
                console.log('[AdminEcho] Notification received', data);

                // Play beep
                playBeep();

                // Dispatch global Livewire event — received by #[On] in NotificationBell
                // Wrap under 'notification' key so PHP receives it as array $notification
                if (typeof Livewire !== 'undefined') {
                    Livewire.dispatch('notificationReceived', {
                        notification: {
                            id:        data.id        ?? '',
                            message:   data.message   ?? '',
                            device_id: data.device_id ?? null,
                        },
                    });
                }
            }
        );
}

// Wait for the auth-user-id meta tag (injected by admin-echo-setup blade)
function trySubscribe(attempts = 0) {
    const meta = document.querySelector('meta[name="auth-user-id"]');
    if (meta) {
        console.log('[AdminEcho] Subscribing for user', meta.content);
        subscribeUser(parseInt(meta.content));
        return;
    }
    if (attempts < 50) setTimeout(() => trySubscribe(attempts + 1), 100);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => trySubscribe());
} else {
    trySubscribe();
}

document.addEventListener('livewire:navigated', () => trySubscribe());
