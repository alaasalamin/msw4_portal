import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';

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

export default function PartnerDashboard() {
    const { post } = useForm({});

    const logout = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('partner.logout'));
    };

    return (
        <div className="min-h-screen bg-zinc-900 text-white">
            <Head title="Partnerportal — Moon.Repair" />

            <header className="border-b border-white/5 bg-zinc-900/90 backdrop-blur-md">
                <div className="mx-auto flex h-14 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                    <Link href="/" className="flex items-center gap-2.5">
                        <span className="flex h-8 w-8 items-center justify-center rounded-lg overflow-hidden">
                            <MoonLogo />
                        </span>
                        <span className="font-display text-lg font-normal">Moon<span className="text-orange-400">.Repair</span></span>
                    </Link>
                    <div className="flex items-center gap-4">
                        <span className="hidden text-xs font-medium text-zinc-500 sm:block uppercase tracking-wider">Partnerportal</span>
                        <form onSubmit={logout}>
                            <button type="submit"
                                className="cursor-pointer rounded-lg border border-white/10 px-3 py-1.5 text-xs font-medium text-zinc-400 transition hover:border-white/20 hover:text-white">
                                Abmelden
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main className="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
                <div className="mb-8">
                    <h1 className="font-display text-2xl font-normal text-white">Willkommen im Partnerportal</h1>
                    <p className="mt-1 text-sm text-zinc-400">Verwalten Sie Massenreparaturen, Sendungen und SLA-Berichte.</p>
                </div>

                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    {[
                        {
                            href: '/shipments',
                            title: 'Sendungen',
                            desc: 'Eingehende Sendungen und Reparaturaufträge verwalten',
                            icon: (
                                <svg className="h-5 w-5 text-orange-400" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                                </svg>
                            ),
                        },
                        {
                            href: '/shipments/create',
                            title: 'Neue Sendung',
                            desc: 'Massenreparatur-Auftrag erstellen und Geräte einsenden',
                            icon: (
                                <svg className="h-5 w-5 text-orange-400" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            ),
                        },
                        {
                            href: '/profile',
                            title: 'Firmenprofil',
                            desc: 'Firmendaten, Rechnungsadresse und Zugangsdaten',
                            icon: (
                                <svg className="h-5 w-5 text-orange-400" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                                </svg>
                            ),
                        },
                    ].map(({ href, title, desc, icon }) => (
                        <Link key={href} href={href}
                            className="group rounded-2xl border border-white/8 bg-white/5 p-6 transition hover:bg-white/8 hover:border-white/15 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500">
                            <div className="mb-4 flex h-10 w-10 items-center justify-center rounded-xl bg-orange-600/15 border border-orange-500/20">
                                {icon}
                            </div>
                            <h2 className="font-medium text-white group-hover:text-orange-300 transition-colors">{title}</h2>
                            <p className="mt-1 text-sm text-zinc-500">{desc}</p>
                        </Link>
                    ))}
                </div>
            </main>
        </div>
    );
}
