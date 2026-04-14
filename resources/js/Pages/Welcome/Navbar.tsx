import { useState } from 'react';
import { Link } from '@inertiajs/react';
import { IconArrowRight, IconBuilding, IconUser } from './icons';

interface Props {
    portalLink: { href: string; label: string } | null;
    canLogin: boolean;
}

export default function Navbar({ portalLink, canLogin }: Props) {
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

    return (
        <header className="fixed inset-x-0 top-0 z-50 border-b border-white/5 bg-zinc-900/90 backdrop-blur-md">
            <div className="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                {/* Logo */}
                <Link href="/" className="flex items-center gap-2.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 rounded-md">
                    <span className="flex h-10 w-10 items-center justify-center rounded-xl overflow-hidden">
                        <svg viewBox="0 0 40 40" fill="none" className="h-10 w-10">
                            <rect width="40" height="40" fill="#1C0800"/>
                            <circle cx="20" cy="24" r="24" fill="#EA580C" opacity="0.22"/>
                            <circle cx="20" cy="20" r="17" fill="#EDE0C4"/>
                            <circle cx="22" cy="18" r="17" fill="#C8B48A" opacity="0.22"/>
                            <circle cx="28" cy="11" r="4.5" fill="#C0A878"/><circle cx="28" cy="11" r="3" fill="#A8906A"/><circle cx="27.4" cy="10.4" r="1.4" fill="#DDD0B0" fillOpacity="0.7"/>
                            <circle cx="10" cy="21" r="3.2" fill="#C0A878"/><circle cx="10" cy="21" r="1.9" fill="#A8906A"/><circle cx="9.6" cy="20.6" r="0.9" fill="#DDD0B0" fillOpacity="0.6"/>
                            <circle cx="27" cy="30" r="3.5" fill="#C0A878"/><circle cx="27" cy="30" r="2.2" fill="#A8906A"/><circle cx="26.5" cy="29.5" r="1" fill="#DDD0B0" fillOpacity="0.6"/>
                            <circle cx="13" cy="8" r="2.2" fill="#C0A878"/><circle cx="13" cy="8" r="1.2" fill="#A8906A"/>
                            <circle cx="34" cy="21" r="1.6" fill="#C0A878"/><circle cx="34" cy="21" r="0.9" fill="#A8906A"/>
                            <circle cx="15" cy="34" r="1.3" fill="#C0A878"/><circle cx="15" cy="34" r="0.7" fill="#A8906A"/>
                        </svg>
                    </span>
                    <span className="font-display text-xl font-normal text-white">Moon<span className="text-orange-400">.Repair</span></span>
                </Link>

                {/* Desktop nav — intentionally empty; add nav links via page sections */}
                <nav className="hidden items-center gap-6 sm:flex" />

                {/* Actions */}
                <div className="flex items-center gap-2">
                    {portalLink ? (
                        <Link
                            href={portalLink.href}
                            className="rounded-lg bg-orange-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-orange-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400"
                        >
                            {portalLink.label}
                        </Link>
                    ) : (
                        <>
                            {canLogin && (
                                <Link
                                    href={route('customer.login')}
                                    className="hidden cursor-pointer rounded-lg px-4 py-2 text-sm font-medium text-zinc-300 transition hover:text-white sm:block focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400"
                                >
                                    <span className="flex items-center gap-1.5"><IconUser className="h-3.5 w-3.5" />Kundenportal</span>
                                </Link>
                            )}
                            <Link
                                href={route('partner.login')}
                                className="rounded-lg bg-orange-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-orange-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400"
                            >
                                <span className="flex items-center gap-1.5"><IconBuilding className="h-3.5 w-3.5" />Partnerportal</span>
                            </Link>
                        </>
                    )}

                    {/* Mobile hamburger */}
                    <button
                        type="button"
                        onClick={() => setMobileMenuOpen((v) => !v)}
                        className="ml-1 flex cursor-pointer items-center rounded-md p-2 text-zinc-400 hover:text-white sm:hidden focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400"
                        aria-label="Toggle menu"
                    >
                        <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" strokeWidth={2} stroke="currentColor">
                            {mobileMenuOpen
                                ? <path strokeLinecap="round" strokeLinejoin="round" d="M6 18 18 6M6 6l12 12" />
                                : <path strokeLinecap="round" strokeLinejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />}
                        </svg>
                    </button>
                </div>
            </div>

            {/* Mobile menu */}
            {mobileMenuOpen && (
                <div className="border-t border-white/5 bg-zinc-900 px-4 pb-4 pt-2 sm:hidden">
                    <nav className="flex flex-col gap-1">
                        <a href="/" onClick={() => setMobileMenuOpen(false)} className="rounded-md px-3 py-2 text-sm text-zinc-300 hover:bg-white/5 hover:text-white">Home</a>
                    </nav>
                </div>
            )}
        </header>
    );
}
