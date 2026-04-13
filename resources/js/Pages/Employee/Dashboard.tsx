import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';

export default function EmployeeDashboard() {
    const { post } = useForm({});

    const logout = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('employee.logout'));
    };

    return (
        <div className="min-h-screen bg-zinc-900 text-white">
            <Head title="Dashboard — Moon.Repair" />

            {/* Navbar */}
            <header className="border-b border-white/5 bg-zinc-900/90 backdrop-blur-md">
                <div className="mx-auto flex h-14 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                    <Link href="/employee/dashboard" className="flex items-center gap-2.5">
                        <span className="flex h-8 w-8 items-center justify-center rounded-lg overflow-hidden">
                            <svg viewBox="0 0 40 40" fill="none" className="h-8 w-8">
                                <rect width="40" height="40" fill="#1C0800"/>
                                <circle cx="20" cy="24" r="24" fill="#EA580C" opacity="0.22"/>
                                <circle cx="20" cy="20" r="17" fill="#EDE0C4"/>
                                <circle cx="22" cy="18" r="17" fill="#C8B48A" opacity="0.22"/>
                                <circle cx="28" cy="11" r="4.5" fill="#C0A878"/><circle cx="28" cy="11" r="3" fill="#A8906A"/>
                                <circle cx="10" cy="21" r="3.2" fill="#C0A878"/><circle cx="10" cy="21" r="1.9" fill="#A8906A"/>
                                <circle cx="27" cy="30" r="3.5" fill="#C0A878"/><circle cx="27" cy="30" r="2.2" fill="#A8906A"/>
                            </svg>
                        </span>
                        <span className="font-display text-lg font-normal">Moon<span className="text-orange-400">.Repair</span></span>
                    </Link>

                    <div className="flex items-center gap-4">
                        <span className="hidden text-xs font-medium text-zinc-500 sm:block uppercase tracking-wider">Mitarbeiterportal</span>
                        <form onSubmit={logout}>
                            <button type="submit"
                                className="cursor-pointer rounded-lg border border-white/10 px-3 py-1.5 text-xs font-medium text-zinc-400 transition hover:border-white/20 hover:text-white">
                                Abmelden
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            {/* Content */}
            <main className="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
                <div className="mb-8">
                    <h1 className="font-display text-2xl font-normal text-white">Willkommen im Mitarbeiterportal</h1>
                    <p className="mt-1 text-sm text-zinc-400">Verwalten Sie Reparaturen, Kunden und Aufgaben zentral.</p>
                </div>

                {/* Quick links */}
                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    {[
                        {
                            href: '/technician/board',
                            title: 'Techniker-Board',
                            desc: 'Aktive Reparaturen im Kanban-Board verwalten',
                            icon: (
                                <svg className="h-5 w-5 text-orange-400" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z" />
                                </svg>
                            ),
                        },
                        {
                            href: '/shipments',
                            title: 'Sendungen',
                            desc: 'Eingehende Sendungen und Reparaturaufträge',
                            icon: (
                                <svg className="h-5 w-5 text-orange-400" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                                </svg>
                            ),
                        },
                        {
                            href: '/profile',
                            title: 'Mein Profil',
                            desc: 'Persönliche Daten und Passwort ändern',
                            icon: (
                                <svg className="h-5 w-5 text-orange-400" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
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
