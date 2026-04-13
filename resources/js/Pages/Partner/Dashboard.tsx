import React, { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';

/* ─── Logo ─── */
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

/* ─── Types ─── */
type ShipmentStatus = 'waiting' | 'in_transit' | 'processing' | 'completed' | 'cancelled';
type Priority = 'normal' | 'high' | 'urgent';

interface Shipment {
    id: string;
    company: string;
    contact: string;
    devices: number;
    status: ShipmentStatus;
    priority: Priority;
    created: string;
    eta: string;
    issue: string;
    value: number;
}

interface Activity {
    id: number;
    type: 'shipment' | 'completed' | 'invoice' | 'message';
    text: string;
    time: string;
    unread: boolean;
}

type ActivityIcon = Record<Activity['type'], React.ReactElement>;

/* ─── Mock Data ─── */
const SHIPMENTS: Shipment[] = [
    { id: 'SEND-087', company: 'TechCorp GmbH',    contact: 'Markus Berger',  devices: 12, status: 'processing', priority: 'urgent', created: '12.04.2026', eta: '15.04.2026', issue: 'Display-Schäden, Batterieprobleme',    value: 2880 },
    { id: 'SEND-086', company: 'MediaHouse AG',    contact: 'Sandra Vogt',    devices:  8, status: 'in_transit', priority: 'normal', created: '11.04.2026', eta: '14.04.2026', issue: 'Fallschäden, Kamerafehler',           value: 1760 },
    { id: 'SEND-085', company: 'StartupXYZ GmbH',  contact: 'Felix Kramer',   devices: 25, status: 'completed',  priority: 'normal', created: '08.04.2026', eta: '12.04.2026', issue: 'Software-Updates, Reinigung',        value: 3500 },
    { id: 'SEND-084', company: 'Retail One KG',    contact: 'Lisa Winkler',   devices:  6, status: 'waiting',    priority: 'high',   created: '10.04.2026', eta: '16.04.2026', issue: 'Ladekabel-Defekte, Touch-Probleme',  value: 1140 },
    { id: 'SEND-083', company: 'BlueTech Solutions',contact: 'Hans Müller',   devices: 18, status: 'completed',  priority: 'normal', created: '05.04.2026', eta: '10.04.2026', issue: 'Gehäuseschäden, Buttons',            value: 3960 },
    { id: 'SEND-082', company: 'PureDigital AG',   contact: 'Petra Fischer',  devices:  4, status: 'cancelled',  priority: 'normal', created: '07.04.2026', eta: '—',           issue: 'Wasserschäden',                     value:  880 },
];

const ACTIVITIES: Activity[] = [
    { id: 1, type: 'shipment',  text: 'Sendung SEND-087 von TechCorp GmbH ist angekommen', time: 'Vor 2 Std.',   unread: true  },
    { id: 2, type: 'completed', text: 'Sendung SEND-085 (25 Geräte) vollständig abgeschlossen', time: 'Vor 5 Std.',   unread: true  },
    { id: 3, type: 'invoice',   text: 'Rechnung #INV-2026-041 wurde erstellt — 3.500 €',     time: 'Gestern',     unread: false },
    { id: 4, type: 'message',   text: 'Neue Nachricht von Sandra Vogt (MediaHouse AG)',       time: 'Gestern',     unread: false },
    { id: 5, type: 'completed', text: 'Sendung SEND-083 (18 Geräte) abgeschlossen',           time: 'Vor 3 Tagen', unread: false },
];

/* ─── Config maps ─── */
const STATUS_CFG: Record<ShipmentStatus, { label: string; dot: string; bg: string; text: string }> = {
    waiting:    { label: 'Wartend',       dot: 'bg-amber-400',   bg: 'rgba(245,158,11,0.12)',  text: '#fbbf24' },
    in_transit: { label: 'Unterwegs',     dot: 'bg-blue-400',    bg: 'rgba(96,165,250,0.12)',  text: '#93c5fd' },
    processing: { label: 'In Bearbeitung',dot: 'bg-orange-400',  bg: 'rgba(251,146,60,0.12)', text: '#fb923c' },
    completed:  { label: 'Abgeschlossen', dot: 'bg-emerald-400', bg: 'rgba(52,211,153,0.12)', text: '#6ee7b7' },
    cancelled:  { label: 'Storniert',     dot: 'bg-red-400',     bg: 'rgba(248,113,113,0.12)', text: '#fca5a5' },
};

const PRIORITY_CFG: Record<Priority, { label: string; color: string }> = {
    normal: { label: 'Normal', color: '#6b7280' },
    high:   { label: 'Hoch',   color: '#f59e0b' },
    urgent: { label: 'Dringend', color: '#ef4444' },
};

const ACTIVITY_ICON: ActivityIcon = {
    shipment: (
        <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
        </svg>
    ),
    completed: (
        <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>
    ),
    invoice: (
        <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
        </svg>
    ),
    message: (
        <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
        </svg>
    ),
};

/* ─── SLA Bar ─── */
const SlaBar = ({ label, value, color }: { label: string; value: number; color: string }) => (
    <div className="space-y-1.5">
        <div className="flex items-center justify-between">
            <span style={{ color: 'rgba(255,255,255,0.55)', fontSize: 13 }}>{label}</span>
            <span style={{ color: 'rgba(255,255,255,0.9)', fontSize: 13, fontWeight: 600 }}>{value}%</span>
        </div>
        <div style={{ height: 6, borderRadius: 999, background: 'rgba(255,255,255,0.08)', overflow: 'hidden' }}>
            <div style={{ width: `${value}%`, height: '100%', borderRadius: 999, background: color, transition: 'width 600ms ease' }} />
        </div>
    </div>
);

/* ─── Main Component ─── */
export default function PartnerDashboard() {
    const { post } = useForm({});
    const [expandedId, setExpandedId] = useState<string | null>(null);
    const [activeFilter, setActiveFilter] = useState<ShipmentStatus | 'all'>('all');
    const [search, setSearch] = useState('');
    const [dismissedActivities, setDismissedActivities] = useState<number[]>([]);

    const logout = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('partner.logout'));
    };

    const toggleRow = (id: string) => setExpandedId(prev => prev === id ? null : id);

    const filtered = SHIPMENTS.filter(s => {
        const matchStatus = activeFilter === 'all' || s.status === activeFilter;
        const q = search.toLowerCase();
        const matchSearch = !q || s.id.toLowerCase().includes(q) || s.company.toLowerCase().includes(q) || s.contact.toLowerCase().includes(q);
        return matchStatus && matchSearch;
    });

    const counts: Record<ShipmentStatus | 'all', number> = {
        all:        SHIPMENTS.length,
        waiting:    SHIPMENTS.filter(s => s.status === 'waiting').length,
        in_transit: SHIPMENTS.filter(s => s.status === 'in_transit').length,
        processing: SHIPMENTS.filter(s => s.status === 'processing').length,
        completed:  SHIPMENTS.filter(s => s.status === 'completed').length,
        cancelled:  SHIPMENTS.filter(s => s.status === 'cancelled').length,
    };

    const urgentCount = SHIPMENTS.filter(s => s.priority === 'urgent' && s.status !== 'completed' && s.status !== 'cancelled').length;
    const devicesInRepair = SHIPMENTS.filter(s => s.status === 'processing').reduce((a, s) => a + s.devices, 0);
    const completedThisMonth = SHIPMENTS.filter(s => s.status === 'completed').reduce((a, s) => a + s.devices, 0);
    const totalRevenue = SHIPMENTS.filter(s => s.status !== 'cancelled').reduce((a, s) => a + s.value, 0);
    const visibleActivities = ACTIVITIES.filter(a => !dismissedActivities.includes(a.id));

    const FILTERS: { key: ShipmentStatus | 'all'; label: string }[] = [
        { key: 'all',        label: 'Alle' },
        { key: 'waiting',    label: 'Wartend' },
        { key: 'in_transit', label: 'Unterwegs' },
        { key: 'processing', label: 'In Bearbeitung' },
        { key: 'completed',  label: 'Abgeschlossen' },
    ];

    return (
        <div style={{ minHeight: '100dvh', background: 'linear-gradient(135deg, #08080d 0%, #0c0a12 50%, #080d0d 100%)', color: '#fff', fontFamily: 'system-ui, sans-serif' }}>
            <Head title="Partnerportal — Moon.Repair" />

            {/* Ambient glows */}
            <div style={{ position: 'fixed', inset: 0, pointerEvents: 'none', zIndex: 0, overflow: 'hidden' }}>
                <div style={{ position: 'absolute', top: '-10%', left: '-5%',  width: 600, height: 600, borderRadius: '50%', background: 'radial-gradient(circle, rgba(99,102,241,0.07) 0%, transparent 70%)' }} />
                <div style={{ position: 'absolute', top: '30%',  right: '-8%', width: 500, height: 500, borderRadius: '50%', background: 'radial-gradient(circle, rgba(234,88,12,0.06) 0%, transparent 70%)' }} />
                <div style={{ position: 'absolute', bottom: '-5%', left: '35%', width: 550, height: 550, borderRadius: '50%', background: 'radial-gradient(circle, rgba(52,211,153,0.05) 0%, transparent 70%)' }} />
            </div>

            <div style={{ position: 'relative', zIndex: 1 }}>
                {/* ── Header ── */}
                <header style={{ borderBottom: '1px solid rgba(255,255,255,0.06)', background: 'rgba(8,8,13,0.85)', backdropFilter: 'blur(16px)', position: 'sticky', top: 0, zIndex: 50 }}>
                    <div style={{ maxWidth: 1280, margin: '0 auto', display: 'flex', alignItems: 'center', justifyContent: 'space-between', height: 56, padding: '0 24px' }}>
                        <Link href="/" style={{ display: 'flex', alignItems: 'center', gap: 10, textDecoration: 'none' }}>
                            <span style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', borderRadius: 10, overflow: 'hidden', width: 32, height: 32 }}>
                                <MoonLogo />
                            </span>
                            <span style={{ fontSize: 18, fontWeight: 400, color: '#fff' }}>Moon<span style={{ color: '#fb923c' }}>.Repair</span></span>
                        </Link>
                        <div style={{ display: 'flex', alignItems: 'center', gap: 16 }}>
                            <span style={{ fontSize: 11, fontWeight: 600, color: 'rgba(255,255,255,0.3)', textTransform: 'uppercase', letterSpacing: '0.1em', display: 'none' }} className="sm-block">Partnerportal</span>
                            <form onSubmit={logout}>
                                <button type="submit" style={{ cursor: 'pointer', border: '1px solid rgba(255,255,255,0.1)', borderRadius: 8, padding: '6px 14px', fontSize: 12, fontWeight: 500, color: 'rgba(255,255,255,0.5)', background: 'transparent', transition: 'all 200ms' }}
                                    onMouseEnter={e => { (e.target as HTMLButtonElement).style.color = '#fff'; (e.target as HTMLButtonElement).style.borderColor = 'rgba(255,255,255,0.25)'; }}
                                    onMouseLeave={e => { (e.target as HTMLButtonElement).style.color = 'rgba(255,255,255,0.5)'; (e.target as HTMLButtonElement).style.borderColor = 'rgba(255,255,255,0.1)'; }}>
                                    Abmelden
                                </button>
                            </form>
                        </div>
                    </div>
                </header>

                <main style={{ maxWidth: 1280, margin: '0 auto', padding: '32px 24px 80px' }}>

                    {/* ── Urgent Banner ── */}
                    {urgentCount > 0 && (
                        <div style={{ marginBottom: 24, borderRadius: 12, border: '1px solid rgba(239,68,68,0.3)', background: 'rgba(239,68,68,0.08)', padding: '12px 18px', display: 'flex', alignItems: 'center', gap: 10 }}>
                            <svg style={{ width: 16, height: 16, color: '#f87171', flexShrink: 0 }} fill="none" viewBox="0 0 24 24" strokeWidth={2} stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                            </svg>
                            <span style={{ fontSize: 13, color: '#fca5a5' }}>
                                <strong style={{ fontWeight: 600 }}>{urgentCount} dringende Sendung{urgentCount > 1 ? 'en' : ''}</strong> erfordern sofortige Aufmerksamkeit.
                            </span>
                        </div>
                    )}

                    {/* ── Welcome ── */}
                    <div style={{ marginBottom: 28 }}>
                        <h1 style={{ fontSize: 22, fontWeight: 400, color: '#fff', margin: 0 }}>Willkommen im Partnerportal</h1>
                        <p style={{ marginTop: 4, fontSize: 13, color: 'rgba(255,255,255,0.4)' }}>Verwalten Sie Sendungen, Reparaturaufträge und SLA-Berichte.</p>
                    </div>

                    {/* ── Stats ── */}
                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(220px, 1fr))', gap: 16, marginBottom: 28 }}>
                        {[
                            {
                                label: 'Aktive Sendungen',
                                value: SHIPMENTS.filter(s => s.status !== 'completed' && s.status !== 'cancelled').length,
                                sub: `${SHIPMENTS.filter(s => s.status === 'waiting').length} wartend`,
                                gradient: 'linear-gradient(135deg, rgba(99,102,241,0.18), rgba(99,102,241,0.06))',
                                border: 'rgba(99,102,241,0.25)',
                                accent: '#818cf8',
                                icon: (
                                    <svg style={{ width: 20, height: 20 }} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                                    </svg>
                                ),
                            },
                            {
                                label: 'Geräte in Reparatur',
                                value: devicesInRepair,
                                sub: 'aktuell in Bearbeitung',
                                gradient: 'linear-gradient(135deg, rgba(234,88,12,0.18), rgba(234,88,12,0.06))',
                                border: 'rgba(234,88,12,0.25)',
                                accent: '#fb923c',
                                icon: (
                                    <svg style={{ width: 20, height: 20 }} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z" />
                                    </svg>
                                ),
                            },
                            {
                                label: 'Abgeschlossen',
                                value: completedThisMonth,
                                sub: 'Geräte diesen Monat',
                                gradient: 'linear-gradient(135deg, rgba(52,211,153,0.18), rgba(52,211,153,0.06))',
                                border: 'rgba(52,211,153,0.25)',
                                accent: '#6ee7b7',
                                icon: (
                                    <svg style={{ width: 20, height: 20 }} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                ),
                            },
                            {
                                label: 'Umsatz (Apr)',
                                value: `${totalRevenue.toLocaleString('de-DE')} €`,
                                sub: 'laufender Monat',
                                gradient: 'linear-gradient(135deg, rgba(251,191,36,0.18), rgba(251,191,36,0.06))',
                                border: 'rgba(251,191,36,0.25)',
                                accent: '#fbbf24',
                                icon: (
                                    <svg style={{ width: 20, height: 20 }} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                    </svg>
                                ),
                            },
                        ].map(({ label, value, sub, gradient, border, accent, icon }) => (
                            <div key={label} style={{ borderRadius: 16, border: `1px solid ${border}`, background: gradient, padding: '20px 22px' }}>
                                <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginBottom: 14 }}>
                                    <span style={{ fontSize: 12, fontWeight: 500, color: 'rgba(255,255,255,0.45)', textTransform: 'uppercase', letterSpacing: '0.08em' }}>{label}</span>
                                    <span style={{ color: accent, opacity: 0.8 }}>{icon}</span>
                                </div>
                                <div style={{ fontSize: 28, fontWeight: 700, color: '#fff', lineHeight: 1, fontVariantNumeric: 'tabular-nums' }}>{value}</div>
                                <div style={{ marginTop: 6, fontSize: 12, color: 'rgba(255,255,255,0.35)' }}>{sub}</div>
                            </div>
                        ))}
                    </div>

                    {/* ── Main Grid ── */}
                    <div style={{ display: 'grid', gridTemplateColumns: '1fr', gap: 20 }}>

                        {/* ── Shipments Table ── */}
                        <div style={{ borderRadius: 18, border: '1px solid rgba(255,255,255,0.07)', background: 'rgba(255,255,255,0.03)', overflow: 'hidden' }}>
                            {/* Table Header */}
                            <div style={{ padding: '20px 22px 0', borderBottom: '1px solid rgba(255,255,255,0.06)' }}>
                                <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginBottom: 16, flexWrap: 'wrap', gap: 12 }}>
                                    <h2 style={{ fontSize: 15, fontWeight: 600, color: '#fff', margin: 0 }}>Sendungen</h2>
                                    {/* Search */}
                                    <div style={{ position: 'relative' }}>
                                        <svg style={{ position: 'absolute', left: 10, top: '50%', transform: 'translateY(-50%)', width: 14, height: 14, color: 'rgba(255,255,255,0.3)' }} fill="none" viewBox="0 0 24 24" strokeWidth={2} stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                        </svg>
                                        <input
                                            type="text"
                                            placeholder="Suchen..."
                                            value={search}
                                            onChange={e => setSearch(e.target.value)}
                                            style={{ paddingLeft: 30, paddingRight: 12, paddingTop: 7, paddingBottom: 7, background: 'rgba(255,255,255,0.05)', border: '1px solid rgba(255,255,255,0.1)', borderRadius: 8, fontSize: 13, color: '#fff', outline: 'none', width: 200 }}
                                        />
                                    </div>
                                </div>

                                {/* Filter tabs */}
                                <div style={{ display: 'flex', gap: 4, overflowX: 'auto', paddingBottom: 0 }}>
                                    {FILTERS.map(f => (
                                        <button key={f.key} onClick={() => setActiveFilter(f.key)}
                                            style={{ cursor: 'pointer', padding: '7px 14px', borderRadius: '8px 8px 0 0', fontSize: 12, fontWeight: 500, border: 'none', background: activeFilter === f.key ? 'rgba(99,102,241,0.18)' : 'transparent', color: activeFilter === f.key ? '#a5b4fc' : 'rgba(255,255,255,0.4)', borderBottom: activeFilter === f.key ? '2px solid #818cf8' : '2px solid transparent', transition: 'all 180ms', whiteSpace: 'nowrap' }}>
                                            {f.label}
                                            <span style={{ marginLeft: 6, fontSize: 11, background: 'rgba(255,255,255,0.1)', padding: '1px 6px', borderRadius: 10 }}>{counts[f.key]}</span>
                                        </button>
                                    ))}
                                </div>
                            </div>

                            {/* Desktop Table */}
                            <div style={{ overflowX: 'auto', display: 'none' }} className="lg-table">
                                <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: 13 }}>
                                    <thead>
                                        <tr style={{ borderBottom: '1px solid rgba(255,255,255,0.06)' }}>
                                            {['Sendungs-ID', 'Firma', 'Kontakt', 'Geräte', 'Problem', 'Priorität', 'Status', 'ETA', ''].map(h => (
                                                <th key={h} style={{ padding: '12px 16px', textAlign: 'left', fontSize: 11, fontWeight: 600, color: 'rgba(255,255,255,0.3)', textTransform: 'uppercase', letterSpacing: '0.08em', whiteSpace: 'nowrap' }}>{h}</th>
                                            ))}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {filtered.map(s => {
                                            const sc = STATUS_CFG[s.status];
                                            const pc = PRIORITY_CFG[s.priority];
                                            const isOpen = expandedId === s.id;
                                            return (
                                                <React.Fragment key={s.id}>
                                                    <tr onClick={() => toggleRow(s.id)} style={{ borderBottom: '1px solid rgba(255,255,255,0.04)', cursor: 'pointer', background: isOpen ? 'rgba(99,102,241,0.06)' : 'transparent', transition: 'background 150ms' }}
                                                        onMouseEnter={e => { if (!isOpen) (e.currentTarget as HTMLTableRowElement).style.background = 'rgba(255,255,255,0.03)'; }}
                                                        onMouseLeave={e => { if (!isOpen) (e.currentTarget as HTMLTableRowElement).style.background = 'transparent'; }}>
                                                        <td style={{ padding: '14px 16px', color: '#a5b4fc', fontWeight: 600, fontFamily: 'monospace' }}>{s.id}</td>
                                                        <td style={{ padding: '14px 16px', color: '#fff', fontWeight: 500 }}>{s.company}</td>
                                                        <td style={{ padding: '14px 16px', color: 'rgba(255,255,255,0.5)' }}>{s.contact}</td>
                                                        <td style={{ padding: '14px 16px', color: '#fff', fontWeight: 700, fontVariantNumeric: 'tabular-nums' }}>{s.devices}</td>
                                                        <td style={{ padding: '14px 16px', color: 'rgba(255,255,255,0.45)', maxWidth: 200 }}>
                                                            <span style={{ display: 'block', overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>{s.issue}</span>
                                                        </td>
                                                        <td style={{ padding: '14px 16px' }}>
                                                            <span style={{ fontSize: 11, fontWeight: 600, color: pc.color }}>{pc.label}</span>
                                                        </td>
                                                        <td style={{ padding: '14px 16px' }}>
                                                            <span style={{ display: 'inline-flex', alignItems: 'center', gap: 5, padding: '3px 10px', borderRadius: 999, background: sc.bg, border: `1px solid ${sc.text}22` }}>
                                                                <span style={{ width: 5, height: 5, borderRadius: '50%', background: sc.text }} />
                                                                <span style={{ fontSize: 11, fontWeight: 600, color: sc.text, whiteSpace: 'nowrap' }}>{sc.label}</span>
                                                            </span>
                                                        </td>
                                                        <td style={{ padding: '14px 16px', color: 'rgba(255,255,255,0.4)', fontSize: 12 }}>{s.eta}</td>
                                                        <td style={{ padding: '14px 16px' }}>
                                                            <svg style={{ width: 14, height: 14, color: 'rgba(255,255,255,0.3)', transform: isOpen ? 'rotate(180deg)' : 'rotate(0)', transition: 'transform 200ms' }} fill="none" viewBox="0 0 24 24" strokeWidth={2} stroke="currentColor">
                                                                <path strokeLinecap="round" strokeLinejoin="round" d="m19 9-7 7-7-7" />
                                                            </svg>
                                                        </td>
                                                    </tr>
                                                    {isOpen && (
                                                        <tr>
                                                            <td colSpan={9} style={{ padding: '0 16px 16px', background: 'rgba(99,102,241,0.04)' }}>
                                                                <div style={{ borderRadius: 12, border: '1px solid rgba(99,102,241,0.2)', background: 'rgba(99,102,241,0.06)', padding: '16px 20px' }}>
                                                                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(160px, 1fr))', gap: 16, marginBottom: 16 }}>
                                                                        {[
                                                                            { l: 'Sendungs-ID', v: s.id },
                                                                            { l: 'Erstellt', v: s.created },
                                                                            { l: 'Geräte', v: `${s.devices} Stück` },
                                                                            { l: 'Wert', v: `${s.value.toLocaleString('de-DE')} €` },
                                                                        ].map(({ l, v }) => (
                                                                            <div key={l}>
                                                                                <div style={{ fontSize: 11, color: 'rgba(255,255,255,0.3)', marginBottom: 4, textTransform: 'uppercase', letterSpacing: '0.06em' }}>{l}</div>
                                                                                <div style={{ fontSize: 14, color: '#fff', fontWeight: 500 }}>{v}</div>
                                                                            </div>
                                                                        ))}
                                                                    </div>
                                                                    <div style={{ marginBottom: 16 }}>
                                                                        <div style={{ fontSize: 11, color: 'rgba(255,255,255,0.3)', marginBottom: 4, textTransform: 'uppercase', letterSpacing: '0.06em' }}>Problem</div>
                                                                        <div style={{ fontSize: 13, color: 'rgba(255,255,255,0.7)' }}>{s.issue}</div>
                                                                    </div>
                                                                    <div style={{ display: 'flex', gap: 8, flexWrap: 'wrap' }}>
                                                                        <button style={{ cursor: 'pointer', padding: '8px 18px', borderRadius: 8, background: 'rgba(99,102,241,0.25)', border: '1px solid rgba(99,102,241,0.4)', color: '#a5b4fc', fontSize: 12, fontWeight: 600 }}>
                                                                            Details öffnen
                                                                        </button>
                                                                        <button style={{ cursor: 'pointer', padding: '8px 18px', borderRadius: 8, background: 'rgba(255,255,255,0.06)', border: '1px solid rgba(255,255,255,0.1)', color: 'rgba(255,255,255,0.6)', fontSize: 12, fontWeight: 500 }}>
                                                                            Status ändern
                                                                        </button>
                                                                        <button style={{ cursor: 'pointer', padding: '8px 18px', borderRadius: 8, background: 'rgba(255,255,255,0.06)', border: '1px solid rgba(255,255,255,0.1)', color: 'rgba(255,255,255,0.6)', fontSize: 12, fontWeight: 500 }}>
                                                                            Rechnung
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    )}
                                                </React.Fragment>
                                            );
                                        })}
                                    </tbody>
                                </table>
                            </div>

                            {/* Mobile Card List */}
                            <div style={{ padding: '8px 16px 16px' }} className="mobile-cards">
                                {filtered.map(s => {
                                    const sc = STATUS_CFG[s.status];
                                    const pc = PRIORITY_CFG[s.priority];
                                    const isOpen = expandedId === s.id;
                                    return (
                                        <div key={s.id} style={{ marginTop: 8 }}>
                                            <div onClick={() => toggleRow(s.id)} style={{ cursor: 'pointer', borderRadius: 12, border: `1px solid ${isOpen ? 'rgba(99,102,241,0.3)' : 'rgba(255,255,255,0.07)'}`, background: isOpen ? 'rgba(99,102,241,0.07)' : 'rgba(255,255,255,0.03)', padding: '14px 16px', transition: 'all 180ms' }}>
                                                <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginBottom: 8 }}>
                                                    <span style={{ fontFamily: 'monospace', fontSize: 13, color: '#a5b4fc', fontWeight: 600 }}>{s.id}</span>
                                                    <span style={{ display: 'inline-flex', alignItems: 'center', gap: 4, padding: '3px 10px', borderRadius: 999, background: sc.bg }}>
                                                        <span style={{ width: 5, height: 5, borderRadius: '50%', background: sc.text }} />
                                                        <span style={{ fontSize: 11, color: sc.text, fontWeight: 600 }}>{sc.label}</span>
                                                    </span>
                                                </div>
                                                <div style={{ fontSize: 14, fontWeight: 600, color: '#fff', marginBottom: 2 }}>{s.company}</div>
                                                <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginTop: 8 }}>
                                                    <span style={{ fontSize: 12, color: 'rgba(255,255,255,0.4)' }}>{s.devices} Geräte · ETA {s.eta}</span>
                                                    <span style={{ fontSize: 11, fontWeight: 600, color: pc.color }}>{pc.label}</span>
                                                </div>
                                            </div>
                                            {isOpen && (
                                                <div style={{ margin: '0 4px', borderRadius: '0 0 12px 12px', border: '1px solid rgba(99,102,241,0.2)', borderTop: 'none', background: 'rgba(99,102,241,0.04)', padding: '14px 16px' }}>
                                                    <p style={{ fontSize: 13, color: 'rgba(255,255,255,0.6)', marginBottom: 12 }}>{s.issue}</p>
                                                    <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 10, marginBottom: 14 }}>
                                                        {[
                                                            { l: 'Erstellt', v: s.created },
                                                            { l: 'Wert', v: `${s.value.toLocaleString('de-DE')} €` },
                                                        ].map(({ l, v }) => (
                                                            <div key={l}>
                                                                <div style={{ fontSize: 10, color: 'rgba(255,255,255,0.3)', textTransform: 'uppercase', letterSpacing: '0.06em', marginBottom: 2 }}>{l}</div>
                                                                <div style={{ fontSize: 13, color: '#fff', fontWeight: 500 }}>{v}</div>
                                                            </div>
                                                        ))}
                                                    </div>
                                                    <div style={{ display: 'flex', gap: 8 }}>
                                                        <button style={{ cursor: 'pointer', flex: 1, padding: '9px', borderRadius: 8, background: 'rgba(99,102,241,0.2)', border: '1px solid rgba(99,102,241,0.35)', color: '#a5b4fc', fontSize: 12, fontWeight: 600 }}>Details</button>
                                                        <button style={{ cursor: 'pointer', flex: 1, padding: '9px', borderRadius: 8, background: 'rgba(255,255,255,0.05)', border: '1px solid rgba(255,255,255,0.1)', color: 'rgba(255,255,255,0.55)', fontSize: 12 }}>Rechnung</button>
                                                    </div>
                                                </div>
                                            )}
                                        </div>
                                    );
                                })}
                                {filtered.length === 0 && (
                                    <div style={{ padding: '32px 0', textAlign: 'center', color: 'rgba(255,255,255,0.3)', fontSize: 14 }}>
                                        Keine Sendungen gefunden.
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* ── Bottom Grid: Activity + SLA ── */}
                        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(280px, 1fr))', gap: 20 }}>

                            {/* Activity Feed */}
                            <div style={{ borderRadius: 18, border: '1px solid rgba(255,255,255,0.07)', background: 'rgba(255,255,255,0.03)', padding: '20px 22px' }}>
                                <h2 style={{ fontSize: 15, fontWeight: 600, color: '#fff', marginBottom: 18 }}>Aktivitäten</h2>
                                {visibleActivities.length === 0 ? (
                                    <p style={{ fontSize: 13, color: 'rgba(255,255,255,0.3)', textAlign: 'center', padding: '16px 0' }}>Keine Aktivitäten.</p>
                                ) : (
                                    <div style={{ display: 'flex', flexDirection: 'column', gap: 4 }}>
                                        {visibleActivities.map(a => {
                                            const colorMap: Record<Activity['type'], string> = {
                                                shipment:  'rgba(99,102,241,0.15)',
                                                completed: 'rgba(52,211,153,0.12)',
                                                invoice:   'rgba(251,191,36,0.12)',
                                                message:   'rgba(255,255,255,0.08)',
                                            };
                                            const iconColorMap: Record<Activity['type'], string> = {
                                                shipment:  '#818cf8',
                                                completed: '#6ee7b7',
                                                invoice:   '#fbbf24',
                                                message:   'rgba(255,255,255,0.5)',
                                            };
                                            return (
                                                <div key={a.id} style={{ display: 'flex', alignItems: 'flex-start', gap: 12, padding: '12px 14px', borderRadius: 10, background: colorMap[a.type], position: 'relative' }}>
                                                    {a.unread && <span style={{ position: 'absolute', top: 10, right: 12, width: 6, height: 6, borderRadius: '50%', background: '#fb923c' }} />}
                                                    <span style={{ color: iconColorMap[a.type], marginTop: 1, flexShrink: 0 }}>{ACTIVITY_ICON[a.type]}</span>
                                                    <div style={{ flex: 1, minWidth: 0 }}>
                                                        <p style={{ margin: 0, fontSize: 13, color: 'rgba(255,255,255,0.8)', lineHeight: 1.5 }}>{a.text}</p>
                                                        <span style={{ fontSize: 11, color: 'rgba(255,255,255,0.3)', marginTop: 2, display: 'block' }}>{a.time}</span>
                                                    </div>
                                                    <button onClick={() => setDismissedActivities(prev => [...prev, a.id])} aria-label="Schließen"
                                                        style={{ cursor: 'pointer', background: 'none', border: 'none', color: 'rgba(255,255,255,0.25)', padding: 2, flexShrink: 0, marginTop: 2 }}
                                                        onMouseEnter={e => (e.currentTarget.style.color = 'rgba(255,255,255,0.6)')}
                                                        onMouseLeave={e => (e.currentTarget.style.color = 'rgba(255,255,255,0.25)')}>
                                                        <svg style={{ width: 12, height: 12 }} fill="none" viewBox="0 0 24 24" strokeWidth={2.5} stroke="currentColor">
                                                            <path strokeLinecap="round" strokeLinejoin="round" d="M6 18 18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            );
                                        })}
                                    </div>
                                )}
                            </div>

                            {/* SLA & Quick Actions */}
                            <div style={{ display: 'flex', flexDirection: 'column', gap: 20 }}>

                                {/* SLA Card */}
                                <div style={{ borderRadius: 18, border: '1px solid rgba(255,255,255,0.07)', background: 'rgba(255,255,255,0.03)', padding: '20px 22px' }}>
                                    <h2 style={{ fontSize: 15, fontWeight: 600, color: '#fff', marginBottom: 6 }}>SLA-Performance</h2>
                                    <p style={{ fontSize: 12, color: 'rgba(255,255,255,0.3)', marginBottom: 20 }}>Laufender Monat</p>
                                    <div style={{ display: 'flex', flexDirection: 'column', gap: 14 }}>
                                        <SlaBar label="Pünktliche Lieferung" value={94} color="linear-gradient(90deg, #6ee7b7, #34d399)" />
                                        <SlaBar label="Reparaturqualität"    value={98} color="linear-gradient(90deg, #818cf8, #6366f1)" />
                                        <SlaBar label="Reaktionszeit"        value={87} color="linear-gradient(90deg, #fb923c, #ea580c)" />
                                        <SlaBar label="Kundenzufriedenheit"  value={96} color="linear-gradient(90deg, #fbbf24, #f59e0b)" />
                                    </div>
                                </div>

                                {/* Quick Actions */}
                                <div style={{ borderRadius: 18, border: '1px solid rgba(255,255,255,0.07)', background: 'rgba(255,255,255,0.03)', padding: '20px 22px' }}>
                                    <h2 style={{ fontSize: 15, fontWeight: 600, color: '#fff', marginBottom: 16 }}>Schnellzugriff</h2>
                                    <div style={{ display: 'flex', flexDirection: 'column', gap: 8 }}>
                                        {[
                                            {
                                                label: 'Neue Sendung erstellen',
                                                accent: '#818cf8',
                                                bg: 'rgba(99,102,241,0.12)',
                                                border: 'rgba(99,102,241,0.25)',
                                                icon: <svg style={{ width: 16, height: 16 }} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>,
                                            },
                                            {
                                                label: 'Firmenprofil bearbeiten',
                                                accent: 'rgba(255,255,255,0.55)',
                                                bg: 'rgba(255,255,255,0.05)',
                                                border: 'rgba(255,255,255,0.1)',
                                                icon: <svg style={{ width: 16, height: 16 }} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg>,
                                            },
                                            {
                                                label: 'Rechnungen herunterladen',
                                                accent: '#fbbf24',
                                                bg: 'rgba(251,191,36,0.08)',
                                                border: 'rgba(251,191,36,0.2)',
                                                icon: <svg style={{ width: 16, height: 16 }} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>,
                                            },
                                        ].map(({ label, accent, bg, border, icon }) => (
                                            <button key={label} style={{ cursor: 'pointer', display: 'flex', alignItems: 'center', gap: 12, padding: '12px 16px', borderRadius: 10, background: bg, border: `1px solid ${border}`, textAlign: 'left', transition: 'opacity 150ms' }}
                                                onMouseEnter={e => (e.currentTarget.style.opacity = '0.8')}
                                                onMouseLeave={e => (e.currentTarget.style.opacity = '1')}>
                                                <span style={{ color: accent }}>{icon}</span>
                                                <span style={{ fontSize: 13, fontWeight: 500, color: accent }}>{label}</span>
                                            </button>
                                        ))}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>

            {/* Responsive helpers via inline style tag */}
            <style>{`
                @media (min-width: 1024px) {
                    .lg-table { display: block !important; }
                    .mobile-cards { display: none !important; }
                    .sm-block { display: block !important; }
                }
            `}</style>
        </div>
    );
}
