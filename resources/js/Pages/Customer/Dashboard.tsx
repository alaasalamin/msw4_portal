import React, { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';

// ── Icons ─────────────────────────────────────────────────────────────────────

const MoonLogo = () => (
    <svg viewBox="0 0 40 40" fill="none" className="h-8 w-8">
        <rect width="40" height="40" fill="#1C0800"/>
        <circle cx="20" cy="24" r="24" fill="#EA580C" opacity="0.22"/>
        <circle cx="20" cy="20" r="17" fill="#EDE0C4"/>
        <circle cx="22" cy="18" r="17" fill="#C8B48A" opacity="0.22"/>
        <circle cx="28" cy="11" r="4.5" fill="#C0A878"/><circle cx="28" cy="11" r="3" fill="#A8906A"/><circle cx="27.4" cy="10.4" r="1.4" fill="#DDD0B0" fillOpacity="0.7"/>
        <circle cx="10" cy="21" r="3.2" fill="#C0A878"/><circle cx="10" cy="21" r="1.9" fill="#A8906A"/><circle cx="9.6" cy="20.6" r="0.9" fill="#DDD0B0" fillOpacity="0.6"/>
        <circle cx="27" cy="30" r="3.5" fill="#C0A878"/><circle cx="27" cy="30" r="2.2" fill="#A8906A"/><circle cx="26.5" cy="29.5" r="1" fill="#DDD0B0" fillOpacity="0.6"/>
    </svg>
);

const IcoWrench = () => (
    <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z" />
    </svg>
);

const IcoShield = () => (
    <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
    </svg>
);

const IcoReceipt = () => (
    <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M9 14.25l6 0m-6-3.75H15M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
    </svg>
);

const IcoPhone = () => (
    <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 15.75h3" />
    </svg>
);

const IcoCheck = () => (
    <svg className="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" strokeWidth={2.5} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="m4.5 12.75 6 6 9-13.5" />
    </svg>
);

const IcoDownload = () => (
    <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
    </svg>
);

const IcoPlus = () => (
    <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth={2} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
    </svg>
);

const IcoChevron = () => (
    <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth={2} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
    </svg>
);

const IcoBell = () => (
    <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
    </svg>
);

const IcoChat = () => (
    <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
    </svg>
);

const IcoTruck = () => (
    <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
    </svg>
);

const IcoX = () => (
    <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth={2} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M6 18 18 6M6 6l12 12" />
    </svg>
);

// ── Data ──────────────────────────────────────────────────────────────────────

const NOTIFICATIONS = [
    {
        id: 1,
        type: 'comment',
        icon: <IcoChat />,
        title: 'Neuer Kommentar zu Ihrem Gerät',
        body: 'Techniker Lukas M.: „Display wurde getauscht. Touchscreen läuft einwandfrei. Bitte um Bestätigung."',
        device: 'iPhone 14 Pro · REP-2025-041',
        time: 'Vor 12 Min.',
        unread: true,
        accent: { bg: 'rgba(234,88,12,0.1)', border: 'rgba(234,88,12,0.2)', dot: '#EA580C', icon: 'rgba(234,88,12,0.15)', iconColor: '#fb923c' },
    },
    {
        id: 2,
        type: 'status',
        icon: <IcoTruck />,
        title: 'Ihr Gerät ist unterwegs',
        body: 'Samsung Galaxy S23 wurde versandt und wird voraussichtlich morgen geliefert.',
        device: 'Galaxy S23 · REP-2025-028',
        time: 'Gestern, 14:32',
        unread: false,
        accent: { bg: 'rgba(99,102,241,0.08)', border: 'rgba(99,102,241,0.15)', dot: '#818cf8', icon: 'rgba(99,102,241,0.12)', iconColor: '#a5b4fc' },
    },
];

const REPAIR_STAGES = [
    { key: 'received',  label: 'Eingegangen',      icon: '📥' },
    { key: 'diagnosis', label: 'Diagnose',          icon: '🔍' },
    { key: 'repair',    label: 'In Reparatur',      icon: '🔧' },
    { key: 'qa',        label: 'Qualitätsprüfung',  icon: '✅' },
    { key: 'shipping',  label: 'Versandbereit',     icon: '📦' },
    { key: 'delivered', label: 'Zugestellt',        icon: '🏠' },
];

const DEVICES = [
    {
        id: 'REP-2025-041',
        name: 'iPhone 14 Pro',
        brand: 'Apple',
        color: '#1D1D1F',
        issue: 'Displayschaden – Riss im Display, Touch reagiert teilweise nicht',
        status: 'in_progress',
        statusLabel: 'In Reparatur',
        submitted: '08. Apr 2025',
        eta: '15. Apr 2025',
        progress: 50,
        currentStage: 'Reparatur läuft',
        stageIndex: 2,
    },
    {
        id: 'REP-2025-028',
        name: 'Galaxy S23',
        brand: 'Samsung',
        color: '#1A3C5E',
        issue: 'Akkutausch – Akku hält nur noch 2–3 Stunden',
        status: 'completed',
        statusLabel: 'Abgeschlossen',
        submitted: '21. Mär 2025',
        eta: '24. Mär 2025',
        progress: 100,
        currentStage: 'Zugestellt',
        stageIndex: 5,
    },
];

const INSURANCE = {
    plan: 'Moon.Shield Plus',
    number: 'INS-2025-00187',
    status: 'Aktiv',
    coverage: ['Display & Glas', 'Wasserschaden', 'Akkutausch', 'Datenrettung bis 50 GB'],
    validUntil: '31. Dez 2025',
    fee: '4,99 € / Monat',
    devices: 2,
};

const INVOICES = [
    {
        id: 'INV-2025-002',
        desc: 'Display-Reparatur · iPhone 14 Pro',
        date: '08. Apr 2025',
        amount: '149,00 €',
        paid: false,
    },
    {
        id: 'INV-2025-001',
        desc: 'Akkutausch · Samsung Galaxy S23',
        date: '24. Mär 2025',
        amount: '89,90 €',
        paid: true,
    },
];

// ── Page ──────────────────────────────────────────────────────────────────────

export default function CustomerDashboard() {
    const { post } = useForm({});
    const logout = (e: React.FormEvent) => { e.preventDefault(); post(route('customer.logout')); };
    const [dismissed, setDismissed] = useState<number[]>([]);
    const visibleNotifs = NOTIFICATIONS.filter((n) => !dismissed.includes(n.id));

    return (
        <div className="relative min-h-dvh overflow-x-hidden" style={{ background: 'linear-gradient(135deg, #0a0a0f 0%, #0f0d14 50%, #0a0a0f 100%)' }}>
            <Head title="Kundenportal — Moon.Repair" />

            {/* Ambient glows */}
            <div className="pointer-events-none fixed inset-0 overflow-hidden">
                <div className="absolute -top-40 -right-40 h-[600px] w-[600px] rounded-full opacity-[0.07]"
                    style={{ background: 'radial-gradient(circle, #EA580C 0%, transparent 70%)' }} />
                <div className="absolute top-1/2 -left-60 h-[500px] w-[500px] rounded-full opacity-[0.05]"
                    style={{ background: 'radial-gradient(circle, #EA580C 0%, transparent 70%)' }} />
                <div className="absolute -bottom-32 right-1/3 h-[400px] w-[400px] rounded-full opacity-[0.04]"
                    style={{ background: 'radial-gradient(circle, #f59e0b 0%, transparent 70%)' }} />
            </div>

            {/* ── Header ───────────────────────────────────────────────────── */}
            <header className="sticky top-0 z-40 border-b border-white/[0.06]"
                style={{ background: 'rgba(10,10,15,0.85)', backdropFilter: 'blur(20px)' }}>
                <div className="mx-auto flex h-14 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                    <Link href="/" className="flex items-center gap-2.5 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500">
                        <span className="flex h-8 w-8 items-center justify-center rounded-lg overflow-hidden">
                            <MoonLogo />
                        </span>
                        <span className="font-display text-lg font-normal text-white">Moon<span className="text-orange-400">.Repair</span></span>
                    </Link>
                    <div className="flex items-center gap-3">
                        <span className="hidden text-xs font-medium text-white/25 sm:block tracking-widest uppercase">Kundenportal</span>
                        <form onSubmit={logout}>
                            <button type="submit"
                                className="cursor-pointer rounded-lg border border-white/[0.08] px-3 py-1.5 text-xs font-medium text-white/40 transition-all hover:border-white/20 hover:text-white/80 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500">
                                Abmelden
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main className="relative mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

                {/* ── Page title ───────────────────────────────────────────── */}
                <div className="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p className="mb-1 text-xs font-medium uppercase tracking-widest text-orange-500/70">Kundenportal</p>
                        <h1 className="text-2xl font-semibold text-white">Mein Bereich</h1>
                        <p className="mt-1 text-sm text-white/35">Reparaturen · Versicherung · Rechnungen</p>
                    </div>
                    <Link
                        href="/shipments/create"
                        className="inline-flex cursor-pointer items-center gap-2 rounded-xl bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-orange-600/20 transition-all hover:bg-orange-500 hover:shadow-orange-500/30 active:scale-[0.97] focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400"
                    >
                        <IcoPlus /> Reparatur anfragen
                    </Link>
                </div>

                {/* ── Stats ────────────────────────────────────────────────── */}
                <div className="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-3">

                    {/* Active repairs */}
                    <div className="relative overflow-hidden rounded-2xl p-5"
                        style={{ background: 'linear-gradient(135deg, rgba(234,88,12,0.15) 0%, rgba(234,88,12,0.05) 100%)', border: '1px solid rgba(234,88,12,0.2)' }}>
                        <div className="pointer-events-none absolute -right-6 -top-6 h-24 w-24 rounded-full opacity-20"
                            style={{ background: 'radial-gradient(circle, #EA580C, transparent 70%)' }} />
                        <div className="mb-4 flex h-10 w-10 items-center justify-center rounded-xl text-orange-400"
                            style={{ background: 'rgba(234,88,12,0.15)', border: '1px solid rgba(234,88,12,0.25)' }}>
                            <IcoWrench />
                        </div>
                        <p className="text-3xl font-bold text-white">1</p>
                        <p className="mt-1 text-sm text-white/40">Aktive Reparatur</p>
                        <p className="mt-3 text-xs text-orange-400/80">ETA 15. Apr 2025</p>
                    </div>

                    {/* Insurance */}
                    <div className="relative overflow-hidden rounded-2xl p-5"
                        style={{ background: 'linear-gradient(135deg, rgba(16,185,129,0.12) 0%, rgba(16,185,129,0.04) 100%)', border: '1px solid rgba(16,185,129,0.18)' }}>
                        <div className="pointer-events-none absolute -right-6 -top-6 h-24 w-24 rounded-full opacity-15"
                            style={{ background: 'radial-gradient(circle, #10b981, transparent 70%)' }} />
                        <div className="mb-4 flex h-10 w-10 items-center justify-center rounded-xl text-emerald-400"
                            style={{ background: 'rgba(16,185,129,0.12)', border: '1px solid rgba(16,185,129,0.2)' }}>
                            <IcoShield />
                        </div>
                        <p className="text-3xl font-bold text-white">Aktiv</p>
                        <p className="mt-1 text-sm text-white/40">Moon.Shield Plus</p>
                        <p className="mt-3 text-xs text-emerald-400/80">Gültig bis 31. Dez 2025</p>
                    </div>

                    {/* Invoices */}
                    <div className="relative overflow-hidden rounded-2xl p-5"
                        style={{ background: 'linear-gradient(135deg, rgba(255,255,255,0.06) 0%, rgba(255,255,255,0.02) 100%)', border: '1px solid rgba(255,255,255,0.08)' }}>
                        <div className="mb-4 flex h-10 w-10 items-center justify-center rounded-xl text-white/40"
                            style={{ background: 'rgba(255,255,255,0.06)', border: '1px solid rgba(255,255,255,0.1)' }}>
                            <IcoReceipt />
                        </div>
                        <p className="text-3xl font-bold text-white">2</p>
                        <p className="mt-1 text-sm text-white/40">Rechnungen</p>
                        <p className="mt-3 text-xs text-orange-400/80">1 offen · 149,00 €</p>
                    </div>
                </div>

                {/* ── Notifications ────────────────────────────────────────── */}
                {visibleNotifs.length > 0 && (
                    <section className="mb-8" aria-label="Benachrichtigungen">
                        <div className="mb-3 flex items-center gap-2">
                            <span className="text-sm font-semibold text-white">Benachrichtigungen</span>
                            <span className="flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-bold text-white"
                                style={{ background: '#EA580C' }}>
                                {visibleNotifs.filter(n => n.unread).length}
                            </span>
                        </div>
                        <div className="space-y-3">
                            {visibleNotifs.map((notif) => (
                                <div key={notif.id}
                                    className="relative flex items-start gap-4 rounded-2xl p-4 transition-all"
                                    style={{ background: notif.accent.bg, border: `1px solid ${notif.accent.border}`, backdropFilter: 'blur(10px)' }}
                                    role="alert"
                                    aria-live="polite"
                                >
                                    {/* Unread dot */}
                                    {notif.unread && (
                                        <span className="absolute right-4 top-4 h-2 w-2 rounded-full"
                                            style={{ background: notif.accent.dot }} />
                                    )}

                                    {/* Icon */}
                                    <div className="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl"
                                        style={{ background: notif.accent.icon, color: notif.accent.iconColor }}>
                                        {notif.icon}
                                    </div>

                                    {/* Content */}
                                    <div className="flex-1 min-w-0 pr-6">
                                        <p className="text-sm font-semibold text-white">{notif.title}</p>
                                        <p className="mt-1 text-sm leading-relaxed text-white/50 line-clamp-2">{notif.body}</p>
                                        <div className="mt-2 flex flex-wrap items-center gap-3">
                                            <span className="text-xs text-white/25 font-mono">{notif.device}</span>
                                            <span className="h-1 w-1 rounded-full bg-white/15" />
                                            <span className="text-xs text-white/25">{notif.time}</span>
                                            <Link href="/shipments"
                                                className="cursor-pointer text-xs font-semibold transition-colors focus:outline-none focus-visible:underline"
                                                style={{ color: notif.accent.iconColor }}>
                                                Jetzt ansehen →
                                            </Link>
                                        </div>
                                    </div>

                                    {/* Dismiss */}
                                    <button
                                        type="button"
                                        onClick={() => setDismissed((d) => [...d, notif.id])}
                                        aria-label="Benachrichtigung schließen"
                                        className="absolute right-3 top-3 cursor-pointer rounded-lg p-1 text-white/20 transition hover:bg-white/[0.06] hover:text-white/50 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500"
                                    >
                                        <IcoX />
                                    </button>
                                </div>
                            ))}
                        </div>
                    </section>
                )}

                {/* ── Device Status Tracker ─────────────────────────────────── */}
                <section className="mb-8" aria-labelledby="tracker-heading">
                    <h2 id="tracker-heading" className="mb-4 text-base font-semibold text-white">Reparaturstatus</h2>
                    <div className="space-y-4">
                        {DEVICES.filter(d => d.status === 'in_progress').map((device) => (
                            <div key={device.id} className="overflow-hidden rounded-2xl p-5"
                                style={{ background: 'rgba(255,255,255,0.03)', border: '1px solid rgba(255,255,255,0.07)', backdropFilter: 'blur(10px)' }}>
                                {/* Device label */}
                                <div className="mb-5 flex items-center justify-between">
                                    <div className="flex items-center gap-2.5">
                                        <div className="flex h-8 w-8 items-center justify-center rounded-xl text-white/40"
                                            style={{ background: device.color, border: '1px solid rgba(255,255,255,0.1)' }}>
                                            <IcoPhone />
                                        </div>
                                        <div>
                                            <p className="text-sm font-semibold text-white">{device.name}</p>
                                            <p className="text-xs text-white/30 font-mono">{device.id}</p>
                                        </div>
                                    </div>
                                    <span className="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium"
                                        style={{ background: 'rgba(234,88,12,0.12)', border: '1px solid rgba(234,88,12,0.2)', color: '#fdba74' }}>
                                        <span className="h-1.5 w-1.5 animate-pulse rounded-full bg-orange-400" />
                                        {device.statusLabel}
                                    </span>
                                </div>

                                {/* Stage timeline */}
                                <div className="relative flex items-start justify-between">
                                    {/* Connecting line */}
                                    <div className="absolute left-0 right-0 top-4 mx-5 h-px" style={{ background: 'rgba(255,255,255,0.06)' }} />
                                    <div className="absolute left-0 top-4 mx-5 h-px transition-all duration-700"
                                        style={{
                                            width: `${(device.stageIndex / (REPAIR_STAGES.length - 1)) * 100}%`,
                                            background: 'linear-gradient(90deg, #EA580C, #fb923c)',
                                        }} />

                                    {REPAIR_STAGES.map((stage, i) => {
                                        const done = i < device.stageIndex;
                                        const active = i === device.stageIndex;
                                        return (
                                            <div key={stage.key} className="relative z-10 flex flex-col items-center gap-2" style={{ width: `${100 / REPAIR_STAGES.length}%` }}>
                                                <div className={`flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold transition-all ${
                                                    active
                                                        ? 'text-white shadow-lg shadow-orange-600/30'
                                                        : done
                                                        ? 'text-white'
                                                        : 'text-white/20'
                                                }`}
                                                    style={{
                                                        background: active
                                                            ? 'linear-gradient(135deg, #EA580C, #fb923c)'
                                                            : done
                                                            ? 'rgba(234,88,12,0.4)'
                                                            : 'rgba(255,255,255,0.05)',
                                                        border: active
                                                            ? '2px solid rgba(234,88,12,0.5)'
                                                            : done
                                                            ? '1px solid rgba(234,88,12,0.3)'
                                                            : '1px solid rgba(255,255,255,0.08)',
                                                    }}>
                                                    {done && !active ? (
                                                        <svg className="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" strokeWidth={3} stroke="currentColor">
                                                            <path strokeLinecap="round" strokeLinejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                                        </svg>
                                                    ) : (
                                                        <span>{i + 1}</span>
                                                    )}
                                                </div>
                                                <span className={`text-center text-[10px] leading-tight hidden sm:block ${
                                                    active ? 'font-semibold text-orange-300' : done ? 'text-white/40' : 'text-white/20'
                                                }`}>
                                                    {stage.label}
                                                </span>
                                            </div>
                                        );
                                    })}
                                </div>

                                {/* Active stage callout */}
                                <div className="mt-5 rounded-xl px-4 py-3 flex items-center gap-3"
                                    style={{ background: 'rgba(234,88,12,0.07)', border: '1px solid rgba(234,88,12,0.12)' }}>
                                    <IcoWrench />
                                    <div className="text-sm">
                                        <span className="font-medium text-orange-300">Aktuell: </span>
                                        <span className="text-white/60">{device.currentStage}</span>
                                        <span className="text-white/30"> · ETA </span>
                                        <span className="text-orange-300/80">{device.eta}</span>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </section>

                {/* ── Devices ──────────────────────────────────────────────── */}
                <section className="mb-8" aria-labelledby="devices-heading">
                    <div className="mb-4 flex items-center justify-between">
                        <h2 id="devices-heading" className="text-base font-semibold text-white">Meine Geräte</h2>
                        <Link href="/shipments"
                            className="flex cursor-pointer items-center gap-1 text-xs text-white/30 transition hover:text-orange-400 focus:outline-none focus-visible:underline">
                            Alle ansehen <IcoChevron />
                        </Link>
                    </div>

                    <div className="grid gap-4 lg:grid-cols-2">
                        {DEVICES.map((device) => (
                            <article key={device.id}
                                className="group relative overflow-hidden rounded-2xl p-5 transition-all duration-200 hover:border-white/12"
                                style={{ background: 'rgba(255,255,255,0.04)', border: '1px solid rgba(255,255,255,0.07)', backdropFilter: 'blur(10px)' }}>

                                {/* Top row */}
                                <div className="flex items-start justify-between gap-3">
                                    <div className="flex items-center gap-3">
                                        {/* Device avatar */}
                                        <div className="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl text-white/50"
                                            style={{ background: device.color, border: '1px solid rgba(255,255,255,0.12)' }}>
                                            <IcoPhone />
                                        </div>
                                        <div>
                                            <p className="font-semibold text-white">{device.name}</p>
                                            <p className="text-xs text-white/35">{device.brand} · <span className="font-mono">{device.id}</span></p>
                                        </div>
                                    </div>

                                    {/* Status badge */}
                                    {device.status === 'completed' ? (
                                        <span className="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium"
                                            style={{ background: 'rgba(16,185,129,0.12)', border: '1px solid rgba(16,185,129,0.2)', color: '#6ee7b7' }}>
                                            <span className="h-1.5 w-1.5 rounded-full bg-emerald-400" />
                                            {device.statusLabel}
                                        </span>
                                    ) : (
                                        <span className="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium"
                                            style={{ background: 'rgba(234,88,12,0.12)', border: '1px solid rgba(234,88,12,0.2)', color: '#fdba74' }}>
                                            <span className="h-1.5 w-1.5 animate-pulse rounded-full bg-orange-400" />
                                            {device.statusLabel}
                                        </span>
                                    )}
                                </div>

                                {/* Issue */}
                                <p className="mt-4 text-sm leading-relaxed text-white/40 line-clamp-1">{device.issue}</p>

                                {/* Progress bar */}
                                <div className="mt-4">
                                    <div className="mb-1.5 flex items-center justify-between">
                                        <span className="text-xs text-white/35">{device.currentStage}</span>
                                        <span className="text-xs font-semibold" style={{ color: device.status === 'completed' ? '#6ee7b7' : '#fb923c' }}>
                                            {device.progress}%
                                        </span>
                                    </div>
                                    <div className="h-1.5 w-full overflow-hidden rounded-full" style={{ background: 'rgba(255,255,255,0.06)' }}>
                                        <div
                                            className="h-full rounded-full transition-all duration-500"
                                            style={{
                                                width: `${device.progress}%`,
                                                background: device.status === 'completed'
                                                    ? 'linear-gradient(90deg, #10b981, #6ee7b7)'
                                                    : 'linear-gradient(90deg, #EA580C, #fb923c)',
                                            }}
                                        />
                                    </div>
                                </div>

                                {/* Footer */}
                                <div className="mt-4 flex items-center justify-between border-t pt-4" style={{ borderColor: 'rgba(255,255,255,0.05)' }}>
                                    <div className="text-xs text-white/30">
                                        {device.status === 'in_progress'
                                            ? <>Eingereicht {device.submitted} · ETA <span className="text-orange-400/80">{device.eta}</span></>
                                            : <>Abgeschlossen am <span className="text-emerald-400/80">{device.eta}</span></>
                                        }
                                    </div>
                                    <Link href="/shipments"
                                        className="cursor-pointer text-xs font-medium text-white/30 transition hover:text-orange-400 focus:outline-none focus-visible:underline">
                                        Details →
                                    </Link>
                                </div>
                            </article>
                        ))}
                    </div>
                </section>

                {/* ── Insurance + Invoices ─────────────────────────────────── */}
                <div className="grid gap-6 lg:grid-cols-5">

                    {/* Insurance card */}
                    <section className="lg:col-span-2" aria-labelledby="insurance-heading">
                        <h2 id="insurance-heading" className="mb-4 text-base font-semibold text-white">Versicherung</h2>

                        <div className="relative overflow-hidden rounded-2xl p-5"
                            style={{ background: 'linear-gradient(135deg, rgba(16,185,129,0.1) 0%, rgba(16,185,129,0.03) 100%)', border: '1px solid rgba(16,185,129,0.15)', backdropFilter: 'blur(10px)' }}>

                            {/* Ambient glow */}
                            <div className="pointer-events-none absolute -right-10 -top-10 h-40 w-40 rounded-full opacity-10"
                                style={{ background: 'radial-gradient(circle, #10b981, transparent 70%)' }} />

                            {/* Header */}
                            <div className="relative flex items-center gap-3 mb-5">
                                <div className="flex h-11 w-11 items-center justify-center rounded-2xl text-emerald-400"
                                    style={{ background: 'rgba(16,185,129,0.15)', border: '1px solid rgba(16,185,129,0.25)' }}>
                                    <IcoShield />
                                </div>
                                <div className="flex-1 min-w-0">
                                    <p className="font-semibold text-white">{INSURANCE.plan}</p>
                                    <p className="text-xs text-white/35 font-mono">{INSURANCE.number}</p>
                                </div>
                                <span className="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium"
                                    style={{ background: 'rgba(16,185,129,0.12)', border: '1px solid rgba(16,185,129,0.2)', color: '#6ee7b7' }}>
                                    <span className="h-1.5 w-1.5 rounded-full bg-emerald-400" />
                                    Aktiv
                                </span>
                            </div>

                            {/* Coverage */}
                            <ul className="relative mb-5 space-y-2.5">
                                {INSURANCE.coverage.map((item) => (
                                    <li key={item} className="flex items-center gap-2.5 text-sm text-white/60">
                                        <span className="flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-emerald-400"
                                            style={{ background: 'rgba(16,185,129,0.15)' }}>
                                            <IcoCheck />
                                        </span>
                                        {item}
                                    </li>
                                ))}
                            </ul>

                            {/* Details */}
                            <div className="relative space-y-2 border-t pt-4" style={{ borderColor: 'rgba(255,255,255,0.06)' }}>
                                {[
                                    ['Gültig bis', INSURANCE.validUntil],
                                    ['Beitrag', INSURANCE.fee],
                                    ['Geräte', `${INSURANCE.devices} versichert`],
                                ].map(([label, val]) => (
                                    <div key={label} className="flex items-center justify-between text-sm">
                                        <span className="text-white/35">{label}</span>
                                        <span className="font-medium text-white/70">{val}</span>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </section>

                    {/* Invoices */}
                    <section className="lg:col-span-3" aria-labelledby="invoices-heading">
                        <h2 id="invoices-heading" className="mb-4 text-base font-semibold text-white">Rechnungen</h2>

                        <div className="overflow-hidden rounded-2xl"
                            style={{ background: 'rgba(255,255,255,0.03)', border: '1px solid rgba(255,255,255,0.07)', backdropFilter: 'blur(10px)' }}>

                            {/* Header row */}
                            <div className="grid grid-cols-[1fr_auto_auto] gap-4 px-5 py-3 text-xs font-medium uppercase tracking-widest"
                                style={{ borderBottom: '1px solid rgba(255,255,255,0.05)', color: 'rgba(255,255,255,0.2)' }}>
                                <span>Rechnung</span>
                                <span className="text-right">Betrag</span>
                                <span className="text-right">Status</span>
                            </div>

                            {INVOICES.map((inv, i) => (
                                <div key={inv.id}
                                    className="grid grid-cols-[1fr_auto_auto] items-center gap-4 px-5 py-4 transition-colors hover:bg-white/[0.02]"
                                    style={{ borderBottom: i < INVOICES.length - 1 ? '1px solid rgba(255,255,255,0.04)' : 'none' }}>

                                    <div className="min-w-0">
                                        <p className="truncate text-sm font-medium text-white/80">{inv.desc}</p>
                                        <p className="mt-0.5 text-xs text-white/25">
                                            <span className="font-mono">{inv.id}</span>
                                            <span className="mx-1.5 opacity-50">·</span>
                                            {inv.date}
                                        </p>
                                    </div>

                                    <p className="font-mono text-sm font-semibold tabular-nums text-white/80">{inv.amount}</p>

                                    <div className="flex items-center gap-1.5">
                                        {inv.paid ? (
                                            <span className="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium"
                                                style={{ background: 'rgba(16,185,129,0.1)', border: '1px solid rgba(16,185,129,0.18)', color: '#6ee7b7' }}>
                                                <span className="h-1.5 w-1.5 rounded-full bg-emerald-400" />
                                                Bezahlt
                                            </span>
                                        ) : (
                                            <span className="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium"
                                                style={{ background: 'rgba(234,88,12,0.1)', border: '1px solid rgba(234,88,12,0.18)', color: '#fdba74' }}>
                                                <span className="h-1.5 w-1.5 rounded-full bg-orange-400" />
                                                Offen
                                            </span>
                                        )}
                                        <button
                                            type="button"
                                            aria-label={`Rechnung ${inv.id} herunterladen`}
                                            className="cursor-pointer rounded-lg p-1.5 text-white/20 transition hover:bg-white/[0.06] hover:text-white/60 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500"
                                        >
                                            <IcoDownload />
                                        </button>
                                    </div>
                                </div>
                            ))}

                            {/* Total */}
                            <div className="flex items-center justify-between px-5 py-3.5"
                                style={{ borderTop: '1px solid rgba(255,255,255,0.06)', background: 'rgba(255,255,255,0.02)' }}>
                                <span className="text-sm text-white/30">Gesamt</span>
                                <span className="font-mono text-sm font-bold tabular-nums text-white/70">238,90 €</span>
                            </div>
                        </div>

                        {/* Pay now CTA */}
                        <div className="mt-3 overflow-hidden rounded-2xl p-4"
                            style={{ background: 'linear-gradient(135deg, rgba(234,88,12,0.1), rgba(234,88,12,0.04))', border: '1px solid rgba(234,88,12,0.15)' }}>
                            <div className="flex items-center justify-between gap-4">
                                <div>
                                    <p className="text-sm font-medium text-white/80">1 offene Rechnung</p>
                                    <p className="text-xs text-white/30 mt-0.5">INV-2025-002 · 149,00 €</p>
                                </div>
                                <button
                                    type="button"
                                    className="cursor-pointer shrink-0 rounded-xl bg-orange-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-orange-600/20 transition hover:bg-orange-500 active:scale-[0.97] focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400"
                                >
                                    Jetzt bezahlen
                                </button>
                            </div>
                        </div>
                    </section>

                </div>
            </main>
        </div>
    );
}
