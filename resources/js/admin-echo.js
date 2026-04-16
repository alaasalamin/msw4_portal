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

// ── Audio ─────────────────────────────────────────────────────────────────────

// Generate a bell WAV as a base64 data URI (computed once, cached)
let _bellUri = null;

function _makeBellUri() {
    const rate     = 22050;
    const duration = 2.5;
    const n        = Math.floor(rate * duration);

    // Partials: [frequency Hz, amplitude, decay seconds]
    const partials = [
        [660,  0.50, 2.0],
        [1100, 0.35, 1.4],
        [1760, 0.22, 1.0],
        [2420, 0.12, 0.7],
    ];

    const pcm = new Int16Array(n);
    for (let i = 0; i < n; i++) {
        const t = i / rate;
        let v = 0;
        for (const [f, a, d] of partials) {
            v += a * Math.sin(2 * Math.PI * f * t) * Math.exp(-t / d);
        }
        pcm[i] = Math.max(-32767, Math.min(32767, v * 26000));
    }

    // Pack WAV
    const buf = new ArrayBuffer(44 + n * 2);
    const dv  = new DataView(buf);
    const str = (s, o) => [...s].forEach((c, i) => dv.setUint8(o + i, c.charCodeAt(0)));
    str('RIFF', 0); dv.setUint32(4, 36 + n * 2, true);
    str('WAVE', 8); str('fmt ', 12);
    dv.setUint32(16, 16, true); dv.setUint16(20, 1, true);
    dv.setUint16(22, 1, true);  dv.setUint32(24, rate, true);
    dv.setUint32(28, rate * 2, true); dv.setUint16(32, 2, true);
    dv.setUint16(34, 16, true); str('data', 36);
    dv.setUint32(40, n * 2, true);
    new Int16Array(buf, 44).set(pcm);

    // base64 encode in chunks to avoid call-stack limits
    const bytes = new Uint8Array(buf);
    let bin = '';
    for (let i = 0; i < bytes.length; i += 8192) {
        bin += String.fromCharCode(...bytes.subarray(i, i + 8192));
    }
    return 'data:audio/wav;base64,' + btoa(bin);
}

function playBell() {
    if (!_bellUri) _bellUri = _makeBellUri();
    const a = new Audio(_bellUri);
    a.volume = 0.8;
    a.play().catch(e => console.warn('[AdminEcho] bell blocked:', e));
}

function playBeep() {
    // Simple two-tone beep reusing the same Audio approach
    const rate = 22050, dur = 0.45, n = Math.floor(rate * dur);
    const pcm  = new Int16Array(n);
    for (let i = 0; i < n; i++) {
        const t   = i / rate;
        const f   = t < 0.12 ? 1046 : 880;
        const env = Math.exp(-t / 0.15);
        pcm[i] = Math.max(-32767, Math.min(32767, Math.sin(2 * Math.PI * f * t) * env * 20000));
    }
    const buf = new ArrayBuffer(44 + n * 2);
    const dv  = new DataView(buf);
    const str = (s, o) => [...s].forEach((c, i) => dv.setUint8(o + i, c.charCodeAt(0)));
    str('RIFF', 0); dv.setUint32(4, 36 + n * 2, true);
    str('WAVE', 8); str('fmt ', 12);
    dv.setUint32(16, 16, true); dv.setUint16(20, 1, true);
    dv.setUint16(22, 1, true);  dv.setUint32(24, rate, true);
    dv.setUint32(28, rate * 2, true); dv.setUint16(32, 2, true);
    dv.setUint16(34, 16, true); str('data', 36);
    dv.setUint32(40, n * 2, true);
    new Int16Array(buf, 44).set(pcm);
    const bytes = new Uint8Array(buf);
    let bin = '';
    for (let i = 0; i < bytes.length; i += 8192) bin += String.fromCharCode(...bytes.subarray(i, i + 8192));
    const a = new Audio('data:audio/wav;base64,' + btoa(bin));
    a.play().catch(() => {});
}

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
