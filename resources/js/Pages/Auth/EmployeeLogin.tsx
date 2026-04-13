import React, { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';

type IconProps = { className?: string };

interface LoginFormData {
    email: string;
    password: string;
    remember: boolean;
}

interface EmployeeLoginProps {
    status?: string;
}

// ── Icons ────────────────────────────────────────────────────────────────────

const IconEye = ({ className }: IconProps) => (
    <svg className={className} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
        <path strokeLinecap="round" strokeLinejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
    </svg>
);

const IconEyeOff = ({ className }: IconProps) => (
    <svg className={className} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
    </svg>
);

const MoonLogo = ({ size = 'md' }: { size?: 'sm' | 'md' }) => (
    <div className="flex items-center gap-2.5">
        <span className={`flex items-center justify-center rounded-xl overflow-hidden ${size === 'md' ? 'h-10 w-10' : 'h-8 w-8'}`}>
            <svg viewBox="0 0 40 40" fill="none" className={size === 'md' ? 'h-10 w-10' : 'h-8 w-8'}>
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
        <span className={`font-display font-normal text-white ${size === 'md' ? 'text-xl' : 'text-lg'}`}>
            Moon<span className="text-orange-400">.Repair</span>
        </span>
    </div>
);

// ── Feature row ──────────────────────────────────────────────────────────────

const Feature = ({ icon, title, desc }: { icon: React.ReactNode; title: string; desc: string }) => (
    <div className="flex items-start gap-3">
        <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/5 border border-white/8">
            {icon}
        </div>
        <div>
            <p className="text-sm font-medium text-zinc-200">{title}</p>
            <p className="text-xs text-zinc-500 mt-0.5">{desc}</p>
        </div>
    </div>
);

// ── Page ─────────────────────────────────────────────────────────────────────

export default function EmployeeLogin({ status }: EmployeeLoginProps) {
    const [showPw, setShowPw] = useState(false);

    const { data, setData, post, processing, errors, reset } = useForm<LoginFormData>({
        email: '',
        password: '',
        remember: false,
    });

    const submit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        post(route('employee.login'), { onFinish: () => reset('password') });
    };

    return (
        <div className="flex min-h-screen bg-zinc-900">
            <Head title="Mitarbeiterportal — Moon.Repair" />

            {/* ── Left brand panel ─────────────────────────────────────────── */}
            <div className="relative hidden w-[45%] flex-col justify-between overflow-hidden border-r border-white/5 p-10 lg:flex">
                {/* Glow blobs */}
                <div className="pointer-events-none absolute -top-40 -left-20 h-96 w-96 rounded-full bg-orange-600/12 blur-3xl" />
                <div className="pointer-events-none absolute -bottom-20 right-0 h-72 w-72 rounded-full bg-orange-800/10 blur-3xl" />
                {/* Grid */}
                <div className="pointer-events-none absolute inset-0 opacity-[0.03]"
                    style={{ backgroundImage: 'linear-gradient(#94a3b8 1px, transparent 1px), linear-gradient(to right, #94a3b8 1px, transparent 1px)', backgroundSize: '40px 40px' }} />

                {/* Logo */}
                <div className="relative">
                    <Link href="/" className="inline-block rounded-md focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400">
                        <MoonLogo />
                    </Link>
                </div>

                {/* Content */}
                <div className="relative space-y-8">
                    <div>
                        <div className="mb-4 inline-flex items-center gap-2 rounded-full border border-orange-500/20 bg-orange-500/10 px-3 py-1">
                            <span className="h-1.5 w-1.5 animate-pulse rounded-full bg-orange-400" />
                            <span className="text-xs font-medium text-orange-300">Nur für Mitarbeiter</span>
                        </div>
                        <h1 className="font-display text-3xl font-normal leading-tight text-white">
                            Internes<br />Mitarbeiterportal
                        </h1>
                        <p className="mt-3 text-sm leading-relaxed text-zinc-400">
                            Verwalten Sie Reparaturaufträge, Kundenanfragen und den Techniker-Workflow — alles zentral.
                        </p>
                    </div>

                    <div className="space-y-4">
                        <Feature
                            icon={<svg className="h-4 w-4 text-orange-400" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"/></svg>}
                            title="Auftragsmanagement"
                            desc="Reparaturaufträge anlegen, bearbeiten und verfolgen"
                        />
                        <Feature
                            icon={<svg className="h-4 w-4 text-orange-400" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z"/></svg>}
                            title="Techniker-Board"
                            desc="Kanban-Ansicht aller aktiven Reparaturen"
                        />
                        <Feature
                            icon={<svg className="h-4 w-4 text-orange-400" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>}
                            title="Kundenverwaltung"
                            desc="Kundendaten, Verlauf und Kommunikation"
                        />
                    </div>
                </div>

                {/* Footer */}
                <div className="relative">
                    <p className="text-xs text-zinc-700">© {new Date().getFullYear()} Moon.Repair · Forchheim</p>
                </div>
            </div>

            {/* ── Right form panel ─────────────────────────────────────────── */}
            <div className="flex flex-1 flex-col items-center justify-center px-6 py-12">
                {/* Mobile logo */}
                <div className="mb-8 lg:hidden">
                    <Link href="/"><MoonLogo /></Link>
                </div>

                <div className="w-full max-w-sm">
                    {/* Card */}
                    <div className="rounded-2xl border border-white/8 bg-white/5 overflow-hidden shadow-2xl backdrop-blur-sm">
                        {/* Orange accent bar */}
                        <div className="h-px w-full bg-gradient-to-r from-transparent via-orange-500/60 to-transparent" />

                        <div className="p-8">
                            {/* Header */}
                            <div className="mb-7">
                                <div className="mb-4 flex items-center gap-2">
                                    <svg className="h-4 w-4 text-orange-400" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                    </svg>
                                    <span className="text-xs font-semibold uppercase tracking-wider text-orange-400">Gesicherter Zugang</span>
                                </div>
                                <h1 className="font-display text-2xl font-normal text-white">Mitarbeiter-Login</h1>
                                <p className="mt-1 text-sm text-zinc-400">Internes Portal · Nur für autorisiertes Personal</p>
                            </div>

                            {status && (
                                <div className="mb-5 rounded-xl border border-green-500/20 bg-green-500/10 px-4 py-3 text-sm text-green-300">
                                    {status}
                                </div>
                            )}

                            <form onSubmit={submit} className="space-y-4">
                                {/* Email */}
                                <div>
                                    <label htmlFor="email" className="block text-xs font-medium text-zinc-400 mb-1.5">
                                        E-Mail-Adresse
                                    </label>
                                    <input
                                        id="email"
                                        type="email"
                                        value={data.email}
                                        autoComplete="username"
                                        required
                                        onChange={(e) => setData('email', e.target.value)}
                                        placeholder="mitarbeiter@moon.repair"
                                        className={`block w-full rounded-xl border bg-white/8 px-4 py-3 text-sm text-white placeholder-zinc-600 transition duration-150 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white/10 ${
                                            errors.email ? 'border-rose-500/60' : 'border-white/10 hover:border-white/20'
                                        }`}
                                    />
                                    {errors.email && <p className="mt-1.5 text-xs text-rose-400">{errors.email}</p>}
                                </div>

                                {/* Password */}
                                <div>
                                    <label htmlFor="password" className="block text-xs font-medium text-zinc-400 mb-1.5">
                                        Passwort
                                    </label>
                                    <div className="relative">
                                        <input
                                            id="password"
                                            type={showPw ? 'text' : 'password'}
                                            value={data.password}
                                            autoComplete="current-password"
                                            required
                                            onChange={(e) => setData('password', e.target.value)}
                                            placeholder="••••••••"
                                            className={`block w-full rounded-xl border bg-white/8 px-4 py-3 pr-12 text-sm text-white placeholder-zinc-600 transition duration-150 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white/10 ${
                                                errors.password ? 'border-rose-500/60' : 'border-white/10 hover:border-white/20'
                                            }`}
                                        />
                                        <button type="button" onClick={() => setShowPw((v) => !v)}
                                            className="absolute inset-y-0 right-0 flex cursor-pointer items-center px-3.5 text-zinc-500 hover:text-zinc-300 transition-colors"
                                            aria-label={showPw ? 'Passwort verbergen' : 'Passwort anzeigen'}>
                                            {showPw ? <IconEyeOff className="h-4 w-4" /> : <IconEye className="h-4 w-4" />}
                                        </button>
                                    </div>
                                    {errors.password && <p className="mt-1.5 text-xs text-rose-400">{errors.password}</p>}
                                </div>

                                {/* Remember */}
                                <label className="flex cursor-pointer items-center gap-2.5 text-sm text-zinc-400">
                                    <input type="checkbox" checked={data.remember}
                                        onChange={(e) => setData('remember', e.target.checked)}
                                        className="h-4 w-4 rounded border-white/20 bg-white/10 text-orange-500 focus:ring-orange-500 focus:ring-offset-0" />
                                    Auf diesem Gerät angemeldet bleiben
                                </label>

                                <button type="submit" disabled={processing}
                                    className="flex w-full cursor-pointer items-center justify-center gap-2 rounded-xl bg-orange-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-orange-500 active:scale-[0.98] focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400 focus-visible:ring-offset-2 focus-visible:ring-offset-zinc-900 disabled:opacity-60">
                                    {processing ? (
                                        <svg className="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"/>
                                            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                        </svg>
                                    ) : 'Anmelden'}
                                </button>
                            </form>
                        </div>
                    </div>

                    {/* Footer */}
                    <div className="mt-6 space-y-2 text-center text-xs text-zinc-600">
                        <p>
                            Kein Mitarbeiter?{' '}
                            <Link href={route('customer.login')} className="text-zinc-500 hover:text-zinc-300 transition">Kundenportal</Link>
                            {' · '}
                            <Link href={route('partner.login')} className="text-zinc-500 hover:text-zinc-300 transition">Partnerportal</Link>
                        </p>
                        <p>
                            <Link href="/" className="hover:text-zinc-400 transition">← moon.repair</Link>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    );
}
