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

const IcoPhone    = ({ cls = 'h-4 w-4' }) => <svg className={cls} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 15.75h3" /></svg>;
const IcoLaptop   = ({ cls = 'h-4 w-4' }) => <svg className={cls} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0H3" /></svg>;
const IcoTablet   = ({ cls = 'h-4 w-4' }) => <svg className={cls} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" d="M10.5 19.5h3m-6.75 2.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-15a2.25 2.25 0 0 0-2.25-2.25H6.75A2.25 2.25 0 0 0 4.5 4.5v15a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>;
const IcoWrench   = ({ cls = 'h-4 w-4' }) => <svg className={cls} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z" /></svg>;
const IcoCheck    = ({ cls = 'h-4 w-4' }) => <svg className={cls} fill="none" viewBox="0 0 24 24" strokeWidth={2} stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>;
const IcoClock    = ({ cls = 'h-4 w-4' }) => <svg className={cls} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>;
const IcoSearch   = ({ cls = 'h-4 w-4' }) => <svg className={cls} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>;
const IcoUser     = ({ cls = 'h-4 w-4' }) => <svg className={cls} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>;
const IcoChevron  = ({ cls = 'h-4 w-4' }) => <svg className={cls} fill="none" viewBox="0 0 24 24" strokeWidth={2} stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>;
const IcoBoard    = ({ cls = 'h-4 w-4' }) => <svg className={cls} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" /></svg>;
const IcoFire     = ({ cls = 'h-3.5 w-3.5' }) => <svg className={cls} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" d="M15.362 5.214A8.252 8.252 0 0 1 12 21 8.25 8.25 0 0 1 6.038 7.047 8.287 8.287 0 0 0 9 9.601a8.983 8.983 0 0 1 3.361-6.387 8.214 8.214 0 0 0 3 1.8Z" /><path strokeLinecap="round" strokeLinejoin="round" d="M12 18a3.75 3.75 0 0 0 .495-7.468 5.99 5.99 0 0 0-1.925 3.547 5.975 5.975 0 0 1-2.133-1.001A3.75 3.75 0 0 0 12 18Z" /></svg>;

// ── Types & data ──────────────────────────────────────────────────────────────

type Status = 'waiting' | 'diagnosis' | 'in_repair' | 'qa' | 'ready' | 'completed';
type Priority = 'urgent' | 'high' | 'normal';
type DeviceType = 'phone' | 'laptop' | 'tablet';

interface Device {
    id: string;
    customer: string;
    device: string;
    brand: string;
    type: DeviceType;
    issue: string;
    status: Status;
    priority: Priority;
    received: string;
    technician: string;
    color: string;
}

const STATUS_CFG: Record<Status, { label: string; dot: string; text: string; bg: string; border: string }> = {
    waiting:   { label: 'Wartend',           dot: 'bg-zinc-400',   text: 'text-zinc-300',   bg: 'rgba(113,113,122,0.12)', border: 'rgba(113,113,122,0.2)' },
    diagnosis: { label: 'In Diagnose',       dot: 'bg-blue-400',   text: 'text-blue-300',   bg: 'rgba(96,165,250,0.1)',   border: 'rgba(96,165,250,0.2)'  },
    in_repair: { label: 'In Reparatur',      dot: 'bg-orange-400', text: 'text-orange-300', bg: 'rgba(234,88,12,0.12)',   border: 'rgba(234,88,12,0.2)'   },
    qa:        { label: 'Qualitätsprüfung',  dot: 'bg-violet-400', text: 'text-violet-300', bg: 'rgba(167,139,250,0.1)',  border: 'rgba(167,139,250,0.2)' },
    ready:     { label: 'Abholbereit',       dot: 'bg-emerald-400',text: 'text-emerald-300',bg: 'rgba(16,185,129,0.1)',   border: 'rgba(16,185,129,0.2)'  },
    completed: { label: 'Abgeschlossen',     dot: 'bg-emerald-500',text: 'text-emerald-400',bg: 'rgba(16,185,129,0.08)',  border: 'rgba(16,185,129,0.15)' },
};

const PRIORITY_CFG: Record<Priority, { label: string; color: string; icon: React.ReactNode }> = {
    urgent: { label: 'Dringend', color: 'text-red-400',    icon: <IcoFire cls="h-3 w-3" /> },
    high:   { label: 'Hoch',     color: 'text-orange-400', icon: <IcoFire cls="h-3 w-3" /> },
    normal: { label: 'Normal',   color: 'text-zinc-500',   icon: null },
};

const DEVICE_ICON: Record<DeviceType, React.ReactNode> = {
    phone:  <IcoPhone  cls="h-4 w-4" />,
    laptop: <IcoLaptop cls="h-4 w-4" />,
    tablet: <IcoTablet cls="h-4 w-4" />,
};

const DEVICE_COLOR: Record<DeviceType, string> = {
    phone:  '#1a1a2e',
    laptop: '#0d1b2a',
    tablet: '#1a2e1a',
};

const DEVICES: Device[] = [
    { id:'REP-041', customer:'Max Müller',      device:'iPhone 14 Pro',     brand:'Apple',   type:'phone',  issue:'Displayschaden – Riss im Display, kein Touch',         status:'in_repair', priority:'urgent', received:'08. Apr',  technician:'Lukas M.',  color:'#1D1D1F' },
    { id:'REP-040', customer:'Anna Schmidt',    device:'Galaxy S23 Ultra',  brand:'Samsung', type:'phone',  issue:'Akkutausch – hält nur noch 2–3 Stunden',               status:'qa',        priority:'normal', received:'07. Apr',  technician:'Lukas M.',  color:'#1A3C5E' },
    { id:'REP-039', customer:'Thomas Wagner',   device:'MacBook Pro 14"',   brand:'Apple',   type:'laptop', issue:'Tastatur defekt – mehrere Tasten reagieren nicht',      status:'waiting',   priority:'high',   received:'06. Apr',  technician:'Sarah K.',  color:'#2D2D2D' },
    { id:'REP-038', customer:'Julia Fischer',   device:'iPad Pro 12.9"',    brand:'Apple',   type:'tablet', issue:'Display gebrochen – Touch funktioniert teilweise',      status:'diagnosis', priority:'high',   received:'05. Apr',  technician:'Tim R.',    color:'#1D1D1F' },
    { id:'REP-037', customer:'Klaus Weber',     device:'OnePlus 12',        brand:'OnePlus', type:'phone',  issue:'Wasserschaden – Gerät startet nicht mehr',              status:'in_repair', priority:'urgent', received:'04. Apr',  technician:'Sarah K.',  color:'#1a1a2e' },
    { id:'REP-036', customer:'Maria Braun',     device:'iPhone 13',         brand:'Apple',   type:'phone',  issue:'Lautsprecher defekt – kein Sound bei Anrufen',          status:'ready',     priority:'normal', received:'03. Apr',  technician:'Tim R.',    color:'#1D1D1F' },
    { id:'REP-035', customer:'Peter Hoffmann',  device:'Huawei P50 Pro',    brand:'Huawei',  type:'phone',  issue:'Ladeanschluss defekt – lädt nicht mehr',               status:'completed', priority:'normal', received:'01. Apr',  technician:'Lukas M.',  color:'#1C3244' },
    { id:'REP-034', customer:'Sabine Koch',     device:'Samsung Tab S9',    brand:'Samsung', type:'tablet', issue:'Display flackert – Helligkeit lässt sich nicht steuern',status:'diagnosis', priority:'normal', received:'31. Mär',  technician:'Sarah K.',  color:'#1A3C5E' },
];

const FILTER_TABS: { key: Status | 'all'; label: string }[] = [
    { key: 'all',      label: 'Alle'             },
    { key: 'waiting',  label: 'Wartend'          },
    { key: 'diagnosis',label: 'Diagnose'         },
    { key: 'in_repair',label: 'In Reparatur'     },
    { key: 'qa',       label: 'QA'               },
    { key: 'ready',    label: 'Abholbereit'      },
    { key: 'completed',label: 'Abgeschlossen'    },
];

// ── Component ─────────────────────────────────────────────────────────────────

export default function EmployeeDashboard() {
    const { post } = useForm({});
    const logout = (e: React.FormEvent) => { e.preventDefault(); post(route('employee.logout')); };

    const [filter, setFilter]   = useState<Status | 'all'>('all');
    const [search, setSearch]   = useState('');
    const [expanded, setExpanded] = useState<string | null>(null);

    const filtered = DEVICES.filter((d) => {
        const matchStatus = filter === 'all' || d.status === filter;
        const q = search.toLowerCase();
        const matchSearch = !q || d.customer.toLowerCase().includes(q)
            || d.device.toLowerCase().includes(q) || d.id.toLowerCase().includes(q)
            || d.issue.toLowerCase().includes(q);
        return matchStatus && matchSearch;
    });

    const counts = {
        total:     DEVICES.length,
        in_repair: DEVICES.filter(d => d.status === 'in_repair').length,
        waiting:   DEVICES.filter(d => d.status === 'waiting' || d.status === 'diagnosis').length,
        completed: DEVICES.filter(d => d.status === 'completed' || d.status === 'ready').length,
        urgent:    DEVICES.filter(d => d.priority === 'urgent').length,
    };

    return (
        <div className="relative min-h-dvh overflow-x-hidden"
            style={{ background: 'linear-gradient(160deg, #08080d 0%, #0d0b12 50%, #08080d 100%)' }}>
            <Head title="Mitarbeiterportal — Moon.Repair" />

            {/* Ambient glows */}
            <div className="pointer-events-none fixed inset-0 overflow-hidden">
                <div className="absolute -top-32 -right-32 h-[500px] w-[500px] rounded-full opacity-[0.06]"
                    style={{ background: 'radial-gradient(circle, #EA580C, transparent 70%)' }} />
                <div className="absolute bottom-0 left-1/4 h-[400px] w-[400px] rounded-full opacity-[0.04]"
                    style={{ background: 'radial-gradient(circle, #6366f1, transparent 70%)' }} />
            </div>

            {/* ── Header ───────────────────────────────────────────────────── */}
            <header className="sticky top-0 z-40 border-b border-white/[0.06]"
                style={{ background: 'rgba(8,8,13,0.88)', backdropFilter: 'blur(20px)' }}>
                <div className="mx-auto flex h-14 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                    <Link href="/" className="flex items-center gap-2.5 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500">
                        <span className="flex h-8 w-8 items-center justify-center rounded-lg overflow-hidden">
                            <MoonLogo />
                        </span>
                        <span className="font-display text-lg font-normal text-white">Moon<span className="text-orange-400">.Repair</span></span>
                    </Link>

                    <div className="flex items-center gap-2">
                        <Link href="/technician/board"
                            className="hidden cursor-pointer items-center gap-2 rounded-xl border border-white/[0.08] px-3 py-1.5 text-xs font-medium text-white/50 transition hover:border-white/15 hover:text-white/80 sm:flex focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500">
                            <IcoBoard cls="h-3.5 w-3.5" /> Kanban-Board
                        </Link>
                        <span className="hidden text-xs font-medium text-white/20 sm:block tracking-widest uppercase ml-2">Mitarbeiterportal</span>
                        <form onSubmit={logout} className="ml-2">
                            <button type="submit"
                                className="cursor-pointer rounded-lg border border-white/[0.08] px-3 py-1.5 text-xs font-medium text-white/40 transition hover:border-white/20 hover:text-white/70 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500">
                                Abmelden
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main className="relative mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

                {/* ── Page title ───────────────────────────────────────────── */}
                <div className="mb-8">
                    <p className="mb-1 text-xs font-medium uppercase tracking-widest text-orange-500/60">Mitarbeiterportal</p>
                    <h1 className="text-2xl font-semibold text-white">Geräte-Übersicht</h1>
                    <p className="mt-1 text-sm text-white/30">Alle Reparaturaufträge · Stand heute</p>
                </div>

                {/* ── Stats ────────────────────────────────────────────────── */}
                <div className="mb-8 grid grid-cols-2 gap-3 sm:grid-cols-4">
                    {[
                        { label: 'Geräte gesamt', value: counts.total,     icon: <IcoPhone cls="h-5 w-5" />, accent: 'rgba(255,255,255,0.06)', border: 'rgba(255,255,255,0.08)', text: 'text-white', iconC: 'rgba(255,255,255,0.06)', iconT: 'rgba(255,255,255,0.3)', glow: 'none' },
                        { label: 'In Reparatur',  value: counts.in_repair, icon: <IcoWrench cls="h-5 w-5" />, accent: 'rgba(234,88,12,0.1)',  border: 'rgba(234,88,12,0.18)', text: 'text-orange-300', iconC: 'rgba(234,88,12,0.15)', iconT: '#fb923c', glow: 'rgba(234,88,12,0.15)' },
                        { label: 'Wartend',       value: counts.waiting,   icon: <IcoClock  cls="h-5 w-5" />, accent: 'rgba(96,165,250,0.08)', border: 'rgba(96,165,250,0.15)', text: 'text-blue-300', iconC: 'rgba(96,165,250,0.1)', iconT: '#93c5fd', glow: 'none' },
                        { label: 'Fertiggestellt',value: counts.completed, icon: <IcoCheck  cls="h-5 w-5" />, accent: 'rgba(16,185,129,0.08)', border: 'rgba(16,185,129,0.15)', text: 'text-emerald-300', iconC: 'rgba(16,185,129,0.1)', iconT: '#6ee7b7', glow: 'none' },
                    ].map(({ label, value, icon, accent, border, text, iconC, iconT }) => (
                        <div key={label} className="relative overflow-hidden rounded-2xl p-5"
                            style={{ background: accent, border: `1px solid ${border}` }}>
                            <div className="mb-3 flex h-9 w-9 items-center justify-center rounded-xl"
                                style={{ background: iconC, color: iconT }}>
                                {icon}
                            </div>
                            <p className={`text-3xl font-bold tabular-nums ${text}`}>{value}</p>
                            <p className="mt-0.5 text-xs text-white/30">{label}</p>
                        </div>
                    ))}
                </div>

                {/* ── Urgent banner ─────────────────────────────────────────── */}
                {counts.urgent > 0 && (
                    <div className="mb-6 flex items-center gap-3 rounded-2xl px-4 py-3"
                        style={{ background: 'rgba(239,68,68,0.08)', border: '1px solid rgba(239,68,68,0.18)' }}>
                        <span className="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl text-red-400"
                            style={{ background: 'rgba(239,68,68,0.12)' }}>
                            <IcoFire cls="h-4 w-4" />
                        </span>
                        <p className="text-sm text-white/70">
                            <span className="font-semibold text-red-400">{counts.urgent} dringende Aufträge</span>
                            {' '}benötigen sofortige Aufmerksamkeit.
                        </p>
                    </div>
                )}

                {/* ── Filters + Search ─────────────────────────────────────── */}
                <div className="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    {/* Filter tabs */}
                    <div className="flex items-center gap-1 overflow-x-auto pb-1 sm:pb-0">
                        {FILTER_TABS.map(({ key, label }) => {
                            const cnt = key === 'all' ? DEVICES.length : DEVICES.filter(d => d.status === key).length;
                            const active = filter === key;
                            return (
                                <button key={key} onClick={() => setFilter(key as Status | 'all')}
                                    className={`flex shrink-0 cursor-pointer items-center gap-1.5 rounded-xl px-3 py-1.5 text-xs font-medium transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 ${
                                        active ? 'text-white' : 'text-white/35 hover:text-white/60'
                                    }`}
                                    style={active ? { background: 'rgba(234,88,12,0.15)', border: '1px solid rgba(234,88,12,0.25)', color: '#fb923c' } : { background: 'transparent', border: '1px solid transparent' }}>
                                    {label}
                                    <span className={`rounded-full px-1.5 py-0.5 text-[10px] font-bold ${active ? 'bg-orange-500/20 text-orange-300' : 'bg-white/8 text-white/25'}`}>
                                        {cnt}
                                    </span>
                                </button>
                            );
                        })}
                    </div>

                    {/* Search */}
                    <div className="relative">
                        <span className="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/25">
                            <IcoSearch cls="h-3.5 w-3.5" />
                        </span>
                        <input
                            type="search"
                            value={search}
                            onChange={e => setSearch(e.target.value)}
                            placeholder="Suchen nach Kunde, Gerät, ID…"
                            className="w-full rounded-xl py-2 pl-9 pr-4 text-sm text-white/70 placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-orange-500 sm:w-64"
                            style={{ background: 'rgba(255,255,255,0.04)', border: '1px solid rgba(255,255,255,0.08)' }}
                        />
                    </div>
                </div>

                {/* ── Device list ───────────────────────────────────────────── */}
                <div className="overflow-hidden rounded-2xl"
                    style={{ background: 'rgba(255,255,255,0.02)', border: '1px solid rgba(255,255,255,0.06)' }}>

                    {/* Table head — desktop only */}
                    <div className="hidden grid-cols-[2rem_1fr_1fr_1.2fr_auto_auto_auto] items-center gap-4 px-5 py-3 text-xs font-medium uppercase tracking-widest text-white/20 sm:grid"
                        style={{ borderBottom: '1px solid rgba(255,255,255,0.05)' }}>
                        <span />
                        <span>Auftrag</span>
                        <span>Gerät</span>
                        <span>Problem</span>
                        <span>Priorität</span>
                        <span>Status</span>
                        <span>Techniker</span>
                    </div>

                    {filtered.length === 0 && (
                        <div className="flex flex-col items-center justify-center py-16 text-center">
                            <div className="mb-3 flex h-12 w-12 items-center justify-center rounded-2xl text-white/15"
                                style={{ background: 'rgba(255,255,255,0.04)' }}>
                                <IcoSearch cls="h-6 w-6" />
                            </div>
                            <p className="text-sm text-white/30">Keine Einträge gefunden</p>
                        </div>
                    )}

                    {filtered.map((dev, i) => {
                        const s   = STATUS_CFG[dev.status];
                        const p   = PRIORITY_CFG[dev.priority];
                        const isExpanded = expanded === dev.id;
                        const isLast = i === filtered.length - 1;

                        return (
                            <div key={dev.id}>
                                {/* Row */}
                                <button
                                    type="button"
                                    onClick={() => setExpanded(isExpanded ? null : dev.id)}
                                    aria-expanded={isExpanded}
                                    className="w-full cursor-pointer text-left transition-colors hover:bg-white/[0.025] focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-orange-500"
                                    style={{ borderBottom: isLast && !isExpanded ? 'none' : '1px solid rgba(255,255,255,0.04)' }}>

                                    {/* Desktop row */}
                                    <div className="hidden grid-cols-[2rem_1fr_1fr_1.2fr_auto_auto_auto] items-center gap-4 px-5 py-4 sm:grid">
                                        {/* Device type icon */}
                                        <div className="flex h-7 w-7 items-center justify-center rounded-lg text-white/30"
                                            style={{ background: dev.color, border: '1px solid rgba(255,255,255,0.1)' }}>
                                            {DEVICE_ICON[dev.type]}
                                        </div>

                                        {/* Order + customer */}
                                        <div className="min-w-0">
                                            <p className="font-mono text-xs text-white/40">{dev.id}</p>
                                            <p className="mt-0.5 flex items-center gap-1.5 text-sm font-medium text-white/80">
                                                <IcoUser cls="h-3 w-3 text-white/25 shrink-0" />
                                                {dev.customer}
                                            </p>
                                        </div>

                                        {/* Device */}
                                        <div className="min-w-0">
                                            <p className="truncate text-sm text-white/70">{dev.device}</p>
                                            <p className="text-xs text-white/25">{dev.brand}</p>
                                        </div>

                                        {/* Issue */}
                                        <p className="truncate text-sm text-white/40">{dev.issue}</p>

                                        {/* Priority */}
                                        <span className={`flex items-center gap-1 text-xs font-medium ${p.color}`}>
                                            {p.icon}{p.label}
                                        </span>

                                        {/* Status */}
                                        <span className="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium whitespace-nowrap"
                                            style={{ background: s.bg, border: `1px solid ${s.border}`, color: s.text.replace('text-','') }}>
                                            <span className={`h-1.5 w-1.5 rounded-full ${s.dot} ${dev.status === 'in_repair' ? 'animate-pulse' : ''}`} />
                                            <span style={{ color: 'inherit' }}>{s.label}</span>
                                        </span>

                                        {/* Technician + chevron */}
                                        <div className="flex items-center gap-2">
                                            <span className="hidden text-xs text-white/30 xl:block">{dev.technician}</span>
                                            <IcoChevron cls={`h-3.5 w-3.5 text-white/20 transition-transform duration-200 ${isExpanded ? 'rotate-90' : ''}`} />
                                        </div>
                                    </div>

                                    {/* Mobile row */}
                                    <div className="flex items-start gap-3 px-4 py-4 sm:hidden">
                                        <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-white/30"
                                            style={{ background: dev.color, border: '1px solid rgba(255,255,255,0.1)' }}>
                                            {DEVICE_ICON[dev.type]}
                                        </div>
                                        <div className="flex-1 min-w-0">
                                            <div className="flex items-center justify-between gap-2">
                                                <p className="text-sm font-medium text-white/80 truncate">{dev.customer}</p>
                                                <span className="inline-flex shrink-0 items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-medium"
                                                    style={{ background: s.bg, border: `1px solid ${s.border}`, color: s.text.replace('text-', '') }}>
                                                    <span className={`h-1.5 w-1.5 rounded-full ${s.dot}`} />
                                                    <span style={{ color: 'inherit' }}>{s.label}</span>
                                                </span>
                                            </div>
                                            <p className="mt-0.5 text-xs text-white/50">{dev.device} · <span className="font-mono">{dev.id}</span></p>
                                            <p className="mt-1 text-xs text-white/30 line-clamp-1">{dev.issue}</p>
                                        </div>
                                    </div>
                                </button>

                                {/* Expanded detail panel */}
                                {isExpanded && (
                                    <div className="px-5 pb-5 pt-3"
                                        style={{ background: 'rgba(255,255,255,0.015)', borderBottom: isLast ? 'none' : '1px solid rgba(255,255,255,0.04)' }}>
                                        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                                            {[
                                                { label: 'Auftragsnummer', val: dev.id,          mono: true },
                                                { label: 'Eingegangen',    val: dev.received,    mono: false },
                                                { label: 'Techniker',      val: dev.technician,  mono: false },
                                                { label: 'Priorität',      val: p.label,         mono: false },
                                            ].map(({ label, val, mono }) => (
                                                <div key={label}>
                                                    <p className="text-xs text-white/25 mb-1">{label}</p>
                                                    <p className={`text-sm text-white/70 ${mono ? 'font-mono' : 'font-medium'}`}>{val}</p>
                                                </div>
                                            ))}
                                        </div>
                                        <div className="mt-4">
                                            <p className="text-xs text-white/25 mb-1">Problem</p>
                                            <p className="text-sm text-white/60 leading-relaxed">{dev.issue}</p>
                                        </div>
                                        <div className="mt-5 flex flex-wrap gap-2">
                                            <Link href="/technician/board"
                                                className="inline-flex cursor-pointer items-center gap-2 rounded-xl bg-orange-600 px-4 py-2 text-xs font-semibold text-white shadow-lg shadow-orange-600/20 transition hover:bg-orange-500 active:scale-[0.97] focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400">
                                                <IcoWrench cls="h-3.5 w-3.5" /> Im Board öffnen
                                            </Link>
                                            <button type="button"
                                                className="inline-flex cursor-pointer items-center gap-2 rounded-xl px-4 py-2 text-xs font-medium text-white/50 transition hover:bg-white/[0.06] hover:text-white/80 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500"
                                                style={{ border: '1px solid rgba(255,255,255,0.08)' }}>
                                                Status ändern
                                            </button>
                                        </div>
                                    </div>
                                )}
                            </div>
                        );
                    })}
                </div>

                <p className="mt-3 text-right text-xs text-white/20">
                    {filtered.length} von {DEVICES.length} Einträgen
                </p>
            </main>
        </div>
    );
}
