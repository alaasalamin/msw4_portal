import { useState } from 'react';
import { Link, useForm } from '@inertiajs/react';
import { IconArrowRight, IconBuilding, IconEye, IconEyeOff, IconUser } from './icons';

type LoginFormData = { email: string; password: string; remember: boolean };
type Portal = 'customer' | 'partner';

interface Props {
    canResetPassword: boolean;
}

export default function LoginWidget({ canResetPassword }: Props) {
    const [tab, setTab] = useState<Portal>('customer');
    const [showPw, setShowPw] = useState(false);

    const customerForm = useForm<LoginFormData>({ email: '', password: '', remember: false });
    const partnerForm  = useForm<LoginFormData>({ email: '', password: '', remember: false });
    const activeForm   = tab === 'customer' ? customerForm : partnerForm;

    const handleTabChange = (t: Portal) => {
        setTab(t);
        setShowPw(false);
        customerForm.reset();
        partnerForm.reset();
    };

    const submit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        const loginRoute = tab === 'customer' ? route('customer.login') : route('partner.login');
        activeForm.post(loginRoute, { onFinish: () => activeForm.reset('password') });
    };

    const { data, setData, processing, errors } = activeForm;

    return (
        <div className="overflow-hidden rounded-2xl border border-white/10 bg-zinc-800/60 shadow-2xl backdrop-blur-xl">
            {/* Portal selector */}
            <div className="grid grid-cols-2">
                {(['customer', 'partner'] as Portal[]).map((t) => (
                    <button
                        key={t}
                        onClick={() => handleTabChange(t)}
                        className={`relative cursor-pointer px-4 py-4 text-sm font-medium transition-all duration-200 ${
                            tab === t ? 'bg-zinc-700/60 text-white' : 'text-zinc-500 hover:text-zinc-300'
                        }`}
                    >
                        <span className="flex items-center justify-center gap-2">
                            {t === 'customer' ? <IconUser className="h-4 w-4" /> : <IconBuilding className="h-4 w-4" />}
                            {t === 'customer' ? 'Kundenportal' : 'Partnerportal'}
                        </span>
                        {tab === t && <span className="absolute bottom-0 left-0 right-0 h-0.5 bg-orange-500" />}
                    </button>
                ))}
            </div>

            <div className="p-6">
                <div className={`mb-5 rounded-xl px-4 py-3 text-xs ${
                    tab === 'customer'
                        ? 'bg-zinc-700/50 text-zinc-400'
                        : 'bg-orange-600/10 border border-orange-500/20 text-orange-300'
                }`}>
                    {tab === 'customer'
                        ? 'Melden Sie sich an, um Reparaturen zu verfolgen und Aufträge zu verwalten.'
                        : 'B2B-Portal — Massenreparaturen, SLA-Übersicht und konsolidierte Abrechnung.'}
                </div>

                <form onSubmit={submit} className="space-y-4">
                    <div>
                        <label htmlFor="login-email" className="mb-1.5 block text-xs font-medium text-zinc-400">E-Mail-Adresse</label>
                        <input
                            id="login-email"
                            type="email"
                            value={data.email}
                            autoComplete="username"
                            required
                            onChange={(e) => setData('email', e.target.value)}
                            className={`block w-full rounded-xl border bg-zinc-700/50 px-4 py-3 text-sm text-white placeholder-zinc-500 transition focus:outline-none focus:ring-2 focus:ring-orange-500 ${errors.email ? 'border-rose-500' : 'border-white/8'}`}
                            placeholder="name@beispiel.de"
                        />
                        {errors.email && <p className="mt-1.5 text-xs text-rose-400">{errors.email}</p>}
                    </div>

                    <div>
                        <label htmlFor="login-password" className="mb-1.5 block text-xs font-medium text-zinc-400">Passwort</label>
                        <div className="relative">
                            <input
                                id="login-password"
                                type={showPw ? 'text' : 'password'}
                                value={data.password}
                                autoComplete="current-password"
                                required
                                onChange={(e) => setData('password', e.target.value)}
                                className={`block w-full rounded-xl border bg-zinc-700/50 px-4 py-3 pr-11 text-sm text-white placeholder-zinc-500 transition focus:outline-none focus:ring-2 focus:ring-orange-500 ${errors.password ? 'border-rose-500' : 'border-white/8'}`}
                                placeholder="••••••••"
                            />
                            <button
                                type="button"
                                onClick={() => setShowPw((v) => !v)}
                                className="absolute inset-y-0 right-0 flex cursor-pointer items-center px-3.5 text-zinc-400 hover:text-zinc-200"
                                aria-label={showPw ? 'Passwort verbergen' : 'Passwort anzeigen'}
                            >
                                {showPw ? <IconEyeOff className="h-4 w-4" /> : <IconEye className="h-4 w-4" />}
                            </button>
                        </div>
                        {errors.password && <p className="mt-1.5 text-xs text-rose-400">{errors.password}</p>}
                    </div>

                    <div className="flex items-center justify-between">
                        <label className="flex cursor-pointer items-center gap-2 text-xs text-zinc-400">
                            <input
                                type="checkbox"
                                checked={data.remember}
                                onChange={(e) => setData('remember', e.target.checked)}
                                className="h-3.5 w-3.5 rounded border-white/20 bg-white/10 text-orange-500 focus:ring-orange-500 focus:ring-offset-0"
                            />
                            Angemeldet bleiben
                        </label>
                        {canResetPassword && (
                            <Link href={route('password.request')} className="text-xs text-orange-400 hover:text-orange-300 focus:outline-none focus-visible:underline">
                                Passwort vergessen?
                            </Link>
                        )}
                    </div>

                    <button
                        type="submit"
                        disabled={processing}
                        className="flex w-full cursor-pointer items-center justify-center gap-2 rounded-xl bg-orange-600 px-4 py-3 text-sm font-semibold text-white transition-all duration-150 hover:bg-orange-500 active:scale-[0.98] focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400 focus-visible:ring-offset-2 focus-visible:ring-offset-zinc-800 disabled:opacity-60"
                    >
                        {processing ? (
                            <svg className="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                                <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                            </svg>
                        ) : (
                            <>
                                {tab === 'customer' ? 'Im Kundenportal anmelden' : 'Im Partnerportal anmelden'}
                                <IconArrowRight className="h-4 w-4" />
                            </>
                        )}
                    </button>
                </form>
            </div>
        </div>
    );
}
