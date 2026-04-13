import React from 'react';
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

const IconPhone = () => (
    <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 15.75h3" />
    </svg>
);

const IconLaptop = () => (
    <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0H3" />
    </svg>
);

const IconShield = () => (
    <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
    </svg>
);

const IconReceipt = () => (
    <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M9 14.25l6 0m-6-3.75H15M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
    </svg>
);

const IconWrench = () => (
    <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z" />
    </svg>
);

const IconCheck = () => (
    <svg className="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" strokeWidth={2.5} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="m4.5 12.75 6 6 9-13.5" />
    </svg>
);

const IconArrow = () => (
    <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth={2} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
    </svg>
);

const IconDownload = () => (
    <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
    </svg>
);

const IconClock = () => (
    <svg className="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" strokeWidth={2} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
    </svg>
);

// ── Status badge ───────────────────────────────────────────────────────────────

type StatusType = 'in_progress' | 'completed' | 'waiting' | 'active' | 'paid' | 'open';

const STATUS_CONFIG: Record<StatusType, { label: string; dot: string; text: string; bg: string }> = {
    in_progress: { label: 'In Reparatur',   dot: 'bg-orange-400', text: 'text-orange-300', bg: 'bg-orange-500/10 border-orange-500/20' },
    completed:   { label: 'Abgeschlossen',  dot: 'bg-emerald-400', text: 'text-emerald-300', bg: 'bg-emerald-500/10 border-emerald-500/20' },
    waiting:     { label: 'Wartet',          dot: 'bg-zinc-400', text: 'text-zinc-400', bg: 'bg-zinc-500/10 border-zinc-500/20' },
    active:      { label: 'Aktiv',           dot: 'bg-emerald-400', text: 'text-emerald-300', bg: 'bg-emerald-500/10 border-emerald-500/20' },
    paid:        { label: 'Bezahlt',         dot: 'bg-emerald-400', text: 'text-emerald-300', bg: 'bg-emerald-500/10 border-emerald-500/20' },
    open:        { label: 'Offen',           dot: 'bg-orange-400', text: 'text-orange-300', bg: 'bg-orange-500/10 border-orange-500/20' },
};

function StatusBadge({ status }: { status: StatusType }) {
    const s = STATUS_CONFIG[status];
    return (
        <span className={`inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs font-medium ${s.bg} ${s.text}`}>
            <span className={`h-1.5 w-1.5 rounded-full ${s.dot}`} />
            {s.label}
        </span>
    );
}

// ── Mock data ─────────────────────────────────────────────────────────────────

const DEVICES = [
    {
        id: 'REP-2024-041',
        name: 'iPhone 14 Pro',
        brand: 'Apple',
        issue: 'Displayschaden – Riss im Display, Touch reagiert teilweise nicht',
        status: 'in_progress' as StatusType,
        submitted: '08. Apr 2025',
        eta: '15. Apr 2025',
        icon: <IconPhone />,
        steps: ['Eingang', 'Diagnose', 'Reparatur', 'Qualitätsprüfung', 'Abholung'],
        currentStep: 2,
    },
    {
        id: 'REP-2024-028',
        name: 'Samsung Galaxy S23',
        brand: 'Samsung',
        issue: 'Akkutausch – Akku hält nur noch 2–3 Stunden',
        status: 'completed' as StatusType,
        submitted: '21. Mär 2025',
        eta: '24. Mär 2025',
        icon: <IconPhone />,
        steps: ['Eingang', 'Diagnose', 'Reparatur', 'Qualitätsprüfung', 'Abholung'],
        currentStep: 5,
    },
];

const INSURANCE = {
    plan: 'Moon.Shield Plus',
    number: 'INS-2024-00187',
    coverage: ['Display & Glas', 'Wasserschaden', 'Akkutausch', 'Datenrettung (bis 50 GB)'],
    validUntil: '31. Dez 2025',
    monthlyFee: '4,99 €',
    devicesCount: 2,
};

const INVOICES = [
    {
        id: 'INV-2025-002',
        description: 'Display-Reparatur · iPhone 14 Pro',
        date: '08. Apr 2025',
        amount: '149,00 €',
        status: 'open' as StatusType,
    },
    {
        id: 'INV-2025-001',
        description: 'Akkutausch · Samsung Galaxy S23',
        date: '24. Mär 2025',
        amount: '89,90 €',
        status: 'paid' as StatusType,
    },
];

// ── Progress steps ─────────────────────────────────────────────────────────────

function RepairProgress({ steps, current }: { steps: string[]; current: number }) {
    return (
        <div className="mt-4">
            <div className="flex items-center gap-0">
                {steps.map((step, i) => {
                    const done = i < current;
                    const active = i === current - 1;
                    return (
                        <React.Fragment key={i}>
                            <div className="flex flex-col items-center gap-1">
                                <div className={`flex h-6 w-6 items-center justify-center rounded-full text-[10px] font-semibold transition-colors ${
                                    done
                                        ? 'bg-emerald-500 text-white'
                                        : active
                                        ? 'bg-orange-500 text-white ring-4 ring-orange-500/20'
                                        : 'bg-white/8 text-zinc-500'
                                }`}>
                                    {done && !active ? <IconCheck /> : i + 1}
                                </div>
                                <span className={`hidden text-[10px] sm:block ${active ? 'text-orange-400 font-medium' : done ? 'text-zinc-400' : 'text-zinc-600'}`}>
                                    {step}
                                </span>
                            </div>
                            {i < steps.length - 1 && (
                                <div className={`mb-4 h-0.5 flex-1 mx-1 transition-colors ${i < current - 1 ? 'bg-emerald-500/50' : 'bg-white/8'}`} />
                            )}
                        </React.Fragment>
                    );
                })}
            </div>
        </div>
    );
}

// ── Page ──────────────────────────────────────────────────────────────────────

export default function CustomerDashboard() {
    const { post } = useForm({});

    const logout = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('customer.logout'));
    };

    const openInvoices = INVOICES.filter((inv) => inv.status === 'open').length;
    const activeRepairs = DEVICES.filter((d) => d.status === 'in_progress').length;

    return (
        <div className="min-h-dvh bg-zinc-950 text-white">
            <Head title="Kundenportal — Moon.Repair" />

            {/* ── Header ───────────────────────────────────────────────────── */}
            <header className="sticky top-0 z-40 border-b border-white/5 bg-zinc-950/90 backdrop-blur-md">
                <div className="mx-auto flex h-14 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                    <Link href="/" className="flex items-center gap-2.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 rounded-md">
                        <span className="flex h-8 w-8 items-center justify-center rounded-lg overflow-hidden">
                            <MoonLogo />
                        </span>
                        <span className="font-display text-lg font-normal">Moon<span className="text-orange-400">.Repair</span></span>
                    </Link>

                    <div className="flex items-center gap-3">
                        <span className="hidden text-xs font-medium text-zinc-500 sm:block uppercase tracking-wider">Kundenportal</span>
                        <form onSubmit={logout}>
                            <button type="submit"
                                className="cursor-pointer rounded-lg border border-white/10 px-3 py-1.5 text-xs font-medium text-zinc-400 transition hover:border-white/20 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500">
                                Abmelden
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main className="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

                {/* ── Page heading ─────────────────────────────────────────── */}
                <div className="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 className="font-display text-2xl font-normal text-white">Mein Kundenbereich</h1>
                        <p className="mt-1 text-sm text-zinc-500">Übersicht Ihrer Reparaturen, Versicherung und Rechnungen</p>
                    </div>
                    <Link
                        href="/shipments/create"
                        className="inline-flex cursor-pointer items-center gap-2 rounded-xl bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-orange-500 active:scale-[0.98] focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400"
                    >
                        <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth={2} stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Reparatur anfragen
                    </Link>
                </div>

                {/* ── Stats row ────────────────────────────────────────────── */}
                <div className="mb-8 grid grid-cols-2 gap-3 sm:grid-cols-3">
                    {[
                        {
                            label: 'Aktive Reparaturen',
                            value: activeRepairs,
                            icon: <IconWrench />,
                            accent: activeRepairs > 0 ? 'text-orange-400' : 'text-zinc-400',
                            iconBg: activeRepairs > 0 ? 'bg-orange-600/15 border-orange-500/20 text-orange-400' : 'bg-white/5 border-white/8 text-zinc-500',
                        },
                        {
                            label: 'Versicherung',
                            value: 'Aktiv',
                            icon: <IconShield />,
                            accent: 'text-emerald-400',
                            iconBg: 'bg-emerald-500/15 border-emerald-500/20 text-emerald-400',
                        },
                        {
                            label: 'Offene Rechnungen',
                            value: openInvoices,
                            icon: <IconReceipt />,
                            accent: openInvoices > 0 ? 'text-orange-400' : 'text-zinc-400',
                            iconBg: openInvoices > 0 ? 'bg-orange-600/15 border-orange-500/20 text-orange-400' : 'bg-white/5 border-white/8 text-zinc-500',
                        },
                    ].map(({ label, value, icon, accent, iconBg }) => (
                        <div key={label} className="rounded-2xl border border-white/8 bg-white/4 p-4 sm:p-5">
                            <div className={`mb-3 inline-flex h-9 w-9 items-center justify-center rounded-xl border ${iconBg}`}>
                                {icon}
                            </div>
                            <p className={`font-display text-2xl font-normal ${accent}`}>{value}</p>
                            <p className="mt-0.5 text-xs text-zinc-500">{label}</p>
                        </div>
                    ))}
                </div>

                {/* ── Devices ──────────────────────────────────────────────── */}
                <section className="mb-6" aria-labelledby="devices-heading">
                    <div className="mb-4 flex items-center justify-between">
                        <h2 id="devices-heading" className="text-base font-semibold text-white">Meine Geräte</h2>
                        <Link href="/shipments" className="flex cursor-pointer items-center gap-1 text-xs text-zinc-500 hover:text-orange-400 transition-colors focus:outline-none focus-visible:underline">
                            Alle ansehen <IconArrow />
                        </Link>
                    </div>

                    <div className="grid gap-4 lg:grid-cols-2">
                        {DEVICES.map((device) => (
                            <article key={device.id} className="rounded-2xl border border-white/8 bg-white/4 p-5 transition hover:border-white/12 hover:bg-white/6">
                                {/* Device header */}
                                <div className="flex items-start justify-between gap-3">
                                    <div className="flex items-center gap-3">
                                        <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-white/8 bg-white/5 text-zinc-400">
                                            {device.icon}
                                        </div>
                                        <div>
                                            <p className="font-semibold text-white">{device.name}</p>
                                            <p className="text-xs text-zinc-500">{device.brand} · {device.id}</p>
                                        </div>
                                    </div>
                                    <StatusBadge status={device.status} />
                                </div>

                                {/* Issue */}
                                <p className="mt-3.5 text-sm leading-relaxed text-zinc-400 line-clamp-2">{device.issue}</p>

                                {/* Progress */}
                                <RepairProgress steps={device.steps} current={device.currentStep} />

                                {/* Footer */}
                                <div className="mt-4 flex items-center justify-between border-t border-white/5 pt-4">
                                    <div className="flex items-center gap-3 text-xs text-zinc-500">
                                        <span>Eingang: <span className="text-zinc-300">{device.submitted}</span></span>
                                        {device.status === 'in_progress' && (
                                            <>
                                                <span className="h-1 w-1 rounded-full bg-zinc-600" />
                                                <span className="flex items-center gap-1">
                                                    <IconClock />
                                                    ETA: <span className="text-orange-300">{device.eta}</span>
                                                </span>
                                            </>
                                        )}
                                        {device.status === 'completed' && (
                                            <>
                                                <span className="h-1 w-1 rounded-full bg-zinc-600" />
                                                <span className="text-emerald-400">Fertig am {device.eta}</span>
                                            </>
                                        )}
                                    </div>
                                    <Link
                                        href={`/shipments`}
                                        className="cursor-pointer text-xs font-medium text-orange-400 hover:text-orange-300 transition-colors focus:outline-none focus-visible:underline"
                                    >
                                        Details →
                                    </Link>
                                </div>
                            </article>
                        ))}
                    </div>
                </section>

                {/* ── Insurance + Invoices ─────────────────────────────────── */}
                <div className="grid gap-6 lg:grid-cols-5">

                    {/* Insurance */}
                    <section className="lg:col-span-2" aria-labelledby="insurance-heading">
                        <div className="mb-4">
                            <h2 id="insurance-heading" className="text-base font-semibold text-white">Versicherung</h2>
                        </div>
                        <div className="rounded-2xl border border-emerald-500/20 bg-emerald-500/5 p-5">
                            {/* Plan header */}
                            <div className="flex items-center gap-3 mb-4">
                                <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-emerald-500/30 bg-emerald-500/15 text-emerald-400">
                                    <IconShield />
                                </div>
                                <div>
                                    <p className="font-semibold text-white">{INSURANCE.plan}</p>
                                    <p className="text-xs text-zinc-500">{INSURANCE.number}</p>
                                </div>
                                <div className="ml-auto">
                                    <StatusBadge status="active" />
                                </div>
                            </div>

                            {/* Coverage list */}
                            <ul className="mb-4 space-y-2">
                                {INSURANCE.coverage.map((item) => (
                                    <li key={item} className="flex items-center gap-2 text-sm text-zinc-300">
                                        <span className="flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-400">
                                            <IconCheck />
                                        </span>
                                        {item}
                                    </li>
                                ))}
                            </ul>

                            {/* Footer */}
                            <div className="border-t border-white/5 pt-4 space-y-2">
                                <div className="flex items-center justify-between text-xs">
                                    <span className="text-zinc-500">Gültig bis</span>
                                    <span className="font-medium text-zinc-200">{INSURANCE.validUntil}</span>
                                </div>
                                <div className="flex items-center justify-between text-xs">
                                    <span className="text-zinc-500">Monatlicher Beitrag</span>
                                    <span className="font-medium text-zinc-200">{INSURANCE.monthlyFee}</span>
                                </div>
                                <div className="flex items-center justify-between text-xs">
                                    <span className="text-zinc-500">Versicherte Geräte</span>
                                    <span className="font-medium text-zinc-200">{INSURANCE.devicesCount}</span>
                                </div>
                            </div>
                        </div>
                    </section>

                    {/* Invoices */}
                    <section className="lg:col-span-3" aria-labelledby="invoices-heading">
                        <div className="mb-4 flex items-center justify-between">
                            <h2 id="invoices-heading" className="text-base font-semibold text-white">Rechnungen</h2>
                            <button
                                type="button"
                                className="cursor-pointer flex items-center gap-1 text-xs text-zinc-500 hover:text-orange-400 transition-colors focus:outline-none focus-visible:underline"
                            >
                                Alle ansehen <IconArrow />
                            </button>
                        </div>

                        <div className="rounded-2xl border border-white/8 bg-white/4 overflow-hidden">
                            {/* Table header */}
                            <div className="grid grid-cols-[1fr_auto_auto] gap-4 border-b border-white/5 px-5 py-3 text-xs font-medium uppercase tracking-wider text-zinc-600">
                                <span>Beschreibung</span>
                                <span className="text-right">Betrag</span>
                                <span className="text-right">Status</span>
                            </div>

                            {/* Rows */}
                            {INVOICES.map((inv, i) => (
                                <div
                                    key={inv.id}
                                    className={`grid grid-cols-[1fr_auto_auto] items-center gap-4 px-5 py-4 transition hover:bg-white/4 ${
                                        i < INVOICES.length - 1 ? 'border-b border-white/5' : ''
                                    }`}
                                >
                                    <div className="min-w-0">
                                        <p className="truncate text-sm font-medium text-white">{inv.description}</p>
                                        <p className="mt-0.5 flex items-center gap-1.5 text-xs text-zinc-500">
                                            <span className="font-mono">{inv.id}</span>
                                            <span className="h-1 w-1 rounded-full bg-zinc-700" />
                                            {inv.date}
                                        </p>
                                    </div>
                                    <p className="font-mono text-sm font-semibold tabular-nums text-white">{inv.amount}</p>
                                    <div className="flex items-center gap-2">
                                        <StatusBadge status={inv.status} />
                                        <button
                                            type="button"
                                            aria-label={`Rechnung ${inv.id} herunterladen`}
                                            className="cursor-pointer rounded-lg p-1.5 text-zinc-500 transition hover:bg-white/8 hover:text-zinc-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500"
                                        >
                                            <IconDownload />
                                        </button>
                                    </div>
                                </div>
                            ))}

                            {/* Total row */}
                            <div className="flex items-center justify-between border-t border-white/8 bg-white/3 px-5 py-3">
                                <span className="text-xs text-zinc-500">Gesamt</span>
                                <span className="font-mono text-sm font-bold tabular-nums text-white">238,90 €</span>
                            </div>
                        </div>
                    </section>

                </div>
            </main>
        </div>
    );
}
