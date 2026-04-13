import React, { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';

type IconProps = { className?: string };
type Tab = 'login' | 'register';
type RegisterStep = 1 | 2;

interface LoginFormData {
    email: string;
    password: string;
    remember: boolean;
}

interface RegisterFormData {
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
}

interface AddressData {
    street: string;
    house_number: string;
    postal_code: string;
    city: string;
}

interface LoginFormProps {
    canResetPassword: boolean;
}

interface CustomerLoginProps {
    status?: string;
    defaultTab?: Tab;
}

// ── Icons ────────────────────────────────────────────────────────────────────

const MoonLogo = () => (
    <div className="flex items-center gap-2.5">
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
        <span className="font-display text-xl font-normal text-white">
            Moon<span className="text-orange-400">.Repair</span>
        </span>
    </div>
);

const IconCheck = () => (
    <svg className="h-4 w-4 shrink-0 text-orange-400" fill="none" viewBox="0 0 24 24" strokeWidth={2.5} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="m4.5 12.75 6 6 9-13.5" />
    </svg>
);

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

// ── Shared input ─────────────────────────────────────────────────────────────

interface FieldProps {
    id: string;
    label: string;
    type?: string;
    value: string;
    onChange: (e: React.ChangeEvent<HTMLInputElement>) => void;
    error?: string;
    autoComplete?: string;
    placeholder?: string;
    required?: boolean;
    children?: React.ReactNode;
}

const Field = ({ id, label, type = 'text', value, onChange, error, autoComplete, placeholder, required, children }: FieldProps) => (
    <div>
        <label htmlFor={id} className="block text-sm font-medium text-zinc-700 mb-1.5">
            {label}{required && <span className="ml-0.5 text-orange-500">*</span>}
        </label>
        <div className="relative">
            <input
                id={id}
                type={type}
                value={value}
                autoComplete={autoComplete}
                required={required}
                onChange={onChange}
                placeholder={placeholder}
                className={`block w-full rounded-xl border px-4 py-3 text-sm text-zinc-900 placeholder-zinc-400 transition duration-150 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 ${
                    error ? 'border-rose-400 bg-rose-50' : 'border-zinc-200 bg-white hover:border-zinc-300'
                }`}
            />
            {children}
        </div>
        {error && (
            <p className="mt-1.5 flex items-center gap-1 text-xs text-rose-600">
                <svg className="h-3 w-3 shrink-0" viewBox="0 0 16 16" fill="currentColor"><path d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1zm-.75 3.75a.75.75 0 0 1 1.5 0v3.5a.75.75 0 0 1-1.5 0v-3.5zm.75 7a.875.875 0 1 1 0-1.75.875.875 0 0 1 0 1.75z"/></svg>
                {error}
            </p>
        )}
    </div>
);

// ── Step indicator ───────────────────────────────────────────────────────────

const StepIndicator = ({ current, labels }: { current: number; labels: string[] }) => (
    <div className="flex items-center mb-7">
        {labels.map((label, i) => (
            <React.Fragment key={i}>
                <div className="flex flex-col items-center gap-1">
                    <div className={`flex h-7 w-7 items-center justify-center rounded-full text-xs font-semibold transition-colors duration-200 ${
                        i + 1 < current ? 'bg-orange-600 text-white' :
                        i + 1 === current ? 'bg-orange-600 text-white ring-4 ring-orange-100' :
                        'bg-zinc-100 text-zinc-400'
                    }`}>
                        {i + 1 < current ? (
                            <svg className="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" strokeWidth={3} stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                        ) : i + 1}
                    </div>
                    <span className={`text-xs ${i + 1 === current ? 'text-orange-600 font-medium' : 'text-zinc-400'}`}>{label}</span>
                </div>
                {i < labels.length - 1 && (
                    <div className={`flex-1 h-0.5 mx-3 mb-4 transition-colors duration-300 ${i + 1 < current ? 'bg-orange-500' : 'bg-zinc-200'}`} />
                )}
            </React.Fragment>
        ))}
    </div>
);

// ── Login form ───────────────────────────────────────────────────────────────

function LoginForm({ canResetPassword }: LoginFormProps) {
    const [showPw, setShowPw] = useState(false);
    const { data, setData, post, processing, errors, reset } = useForm<LoginFormData>({
        email: '',
        password: '',
        remember: false,
    });

    const submit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        post(route('customer.login'), { onFinish: () => reset('password') });
    };

    return (
        <form onSubmit={submit} className="space-y-4">
            <Field
                id="login-email" label="E-Mail-Adresse" type="email"
                value={data.email} autoComplete="username" required
                placeholder="deine@email.de" error={errors.email}
                onChange={(e) => setData('email', e.target.value)}
            />
            <Field
                id="login-password" label="Passwort"
                type={showPw ? 'text' : 'password'}
                value={data.password} autoComplete="current-password" required
                placeholder="••••••••" error={errors.password}
                onChange={(e) => setData('password', e.target.value)}
            >
                <button type="button" onClick={() => setShowPw((v) => !v)}
                    className="absolute inset-y-0 right-0 flex cursor-pointer items-center px-3.5 text-zinc-400 hover:text-zinc-600 transition-colors"
                    aria-label={showPw ? 'Passwort verbergen' : 'Passwort anzeigen'}>
                    {showPw ? <IconEyeOff className="h-4 w-4" /> : <IconEye className="h-4 w-4" />}
                </button>
            </Field>

            <div className="flex items-center justify-between">
                <label className="flex cursor-pointer items-center gap-2 text-sm text-zinc-600">
                    <input type="checkbox" checked={data.remember}
                        onChange={(e) => setData('remember', e.target.checked)}
                        className="h-4 w-4 rounded border-zinc-300 text-orange-600 focus:ring-orange-500" />
                    Angemeldet bleiben
                </label>
                {canResetPassword && (
                    <Link href={route('password.request')}
                        className="text-sm text-orange-600 hover:text-orange-700 focus:outline-none focus-visible:underline transition-colors">
                        Passwort vergessen?
                    </Link>
                )}
            </div>

            <button type="submit" disabled={processing}
                className="flex w-full cursor-pointer items-center justify-center gap-2 rounded-xl bg-orange-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-orange-700 active:scale-[0.98] focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 focus-visible:ring-offset-2 disabled:opacity-60">
                {processing ? (
                    <svg className="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                        <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"/>
                        <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                ) : 'Anmelden'}
            </button>
        </form>
    );
}

// ── Register form (2-step) ───────────────────────────────────────────────────

function RegisterForm() {
    const [step, setStep] = useState<RegisterStep>(1);
    const [showPw, setShowPw] = useState(false);
    const [address, setAddress] = useState<AddressData>({
        street: '',
        house_number: '',
        postal_code: '',
        city: '',
    });

    const { data, setData, post, processing, errors } = useForm<RegisterFormData>({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    const goNext = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        setStep(2);
    };

    const submit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        // Address data is collected for CRM — not submitted to users table
        post(route('customer.register'));
    };

    return (
        <div>
            <StepIndicator current={step} labels={['Konto', 'Adresse']} />

            {step === 1 && (
                <form onSubmit={goNext} className="space-y-4">
                    <Field
                        id="reg-name" label="Vollständiger Name"
                        value={data.name} autoComplete="name" required
                        placeholder="Max Mustermann" error={errors.name}
                        onChange={(e) => setData('name', e.target.value)}
                    />
                    <Field
                        id="reg-email" label="E-Mail-Adresse" type="email"
                        value={data.email} autoComplete="username" required
                        placeholder="deine@email.de" error={errors.email}
                        onChange={(e) => setData('email', e.target.value)}
                    />
                    <Field
                        id="reg-password" label="Passwort"
                        type={showPw ? 'text' : 'password'}
                        value={data.password} autoComplete="new-password" required
                        placeholder="Mindestens 8 Zeichen" error={errors.password}
                        onChange={(e) => setData('password', e.target.value)}
                    >
                        <button type="button" onClick={() => setShowPw((v) => !v)}
                            className="absolute inset-y-0 right-0 flex cursor-pointer items-center px-3.5 text-zinc-400 hover:text-zinc-600 transition-colors"
                            aria-label={showPw ? 'Passwort verbergen' : 'Passwort anzeigen'}>
                            {showPw ? <IconEyeOff className="h-4 w-4" /> : <IconEye className="h-4 w-4" />}
                        </button>
                    </Field>
                    <Field
                        id="reg-confirm" label="Passwort bestätigen" type="password"
                        value={data.password_confirmation} autoComplete="new-password" required
                        placeholder="Passwort wiederholen" error={errors.password_confirmation}
                        onChange={(e) => setData('password_confirmation', e.target.value)}
                    />

                    <button type="submit"
                        className="flex w-full cursor-pointer items-center justify-center gap-2 rounded-xl bg-orange-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-orange-700 active:scale-[0.98] focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 focus-visible:ring-offset-2">
                        Weiter
                        <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth={2} stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </button>
                </form>
            )}

            {step === 2 && (
                <form onSubmit={submit} className="space-y-4">
                    {/* Section label */}
                    <div className="rounded-xl border border-orange-100 bg-orange-50 px-4 py-3">
                        <p className="text-xs font-medium text-orange-700">
                            Ihre Adresse wird für Reparatur-Abholungen und Rücksendungen verwendet.
                        </p>
                    </div>

                    {/* Street + House number */}
                    <div className="grid grid-cols-3 gap-3">
                        <div className="col-span-2">
                            <Field
                                id="addr-street" label="Straße"
                                value={address.street} autoComplete="address-line1" required
                                placeholder="Hauptstraße"
                                onChange={(e) => setAddress((a) => ({ ...a, street: e.target.value }))}
                            />
                        </div>
                        <Field
                            id="addr-house" label="Nr."
                            value={address.house_number} autoComplete="address-line2" required
                            placeholder="14"
                            onChange={(e) => setAddress((a) => ({ ...a, house_number: e.target.value }))}
                        />
                    </div>

                    {/* Postal code + City */}
                    <div className="grid grid-cols-3 gap-3">
                        <Field
                            id="addr-plz" label="PLZ"
                            value={address.postal_code} autoComplete="postal-code" required
                            placeholder="91301"
                            onChange={(e) => setAddress((a) => ({ ...a, postal_code: e.target.value }))}
                        />
                        <div className="col-span-2">
                            <Field
                                id="addr-city" label="Stadt"
                                value={address.city} autoComplete="address-level2" required
                                placeholder="Forchheim"
                                onChange={(e) => setAddress((a) => ({ ...a, city: e.target.value }))}
                            />
                        </div>
                    </div>

                    <div className="flex gap-3 pt-1">
                        <button type="button" onClick={() => setStep(1)}
                            className="flex items-center gap-1.5 rounded-xl border border-zinc-200 bg-white px-4 py-3 text-sm font-medium text-zinc-600 transition hover:border-zinc-300 hover:text-zinc-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-400 cursor-pointer">
                            <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth={2} stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                            </svg>
                            Zurück
                        </button>
                        <button type="submit" disabled={processing}
                            className="flex flex-1 cursor-pointer items-center justify-center gap-2 rounded-xl bg-orange-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-orange-700 active:scale-[0.98] focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 focus-visible:ring-offset-2 disabled:opacity-60">
                            {processing ? (
                                <svg className="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"/>
                                    <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                            ) : 'Konto erstellen'}
                        </button>
                    </div>
                </form>
            )}
        </div>
    );
}

// ── Page ─────────────────────────────────────────────────────────────────────

export default function CustomerLogin({ status, defaultTab = 'login' }: CustomerLoginProps) {
    const [tab, setTab] = useState<Tab>(defaultTab);

    return (
        <div className="flex min-h-screen">
            <Head title="Kundenportal — Moon.Repair" />

            {/* ── Left brand panel ─────────────────────────────────────────── */}
            <div className="relative hidden w-[42%] flex-col justify-between overflow-hidden bg-zinc-900 p-10 lg:flex">
                <div className="pointer-events-none absolute -top-32 -left-32 h-96 w-96 rounded-full bg-orange-600/10 blur-3xl" />
                <div className="pointer-events-none absolute -bottom-32 -right-10 h-80 w-80 rounded-full bg-orange-600/8 blur-3xl" />
                <div className="pointer-events-none absolute inset-0 opacity-[0.025]"
                    style={{ backgroundImage: 'linear-gradient(#94a3b8 1px, transparent 1px), linear-gradient(to right, #94a3b8 1px, transparent 1px)', backgroundSize: '40px 40px' }} />

                <div className="relative">
                    <Link href="/" className="focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400 rounded-md inline-block">
                        <MoonLogo />
                    </Link>
                </div>

                <div className="relative">
                    <span className="mb-4 inline-flex items-center gap-1.5 rounded-full border border-orange-500/20 bg-orange-500/10 px-3 py-1 text-xs font-medium text-orange-300">
                        <span className="h-1.5 w-1.5 animate-pulse rounded-full bg-orange-400" />
                        Kundenportal · Forchheim
                    </span>
                    <h1 className="font-display mt-2 text-3xl font-normal leading-tight text-white">
                        Ihre Reparatur,<br />jederzeit im Blick
                    </h1>
                    <p className="mt-3 text-sm leading-relaxed text-zinc-400">
                        Status verfolgen, Verlauf einsehen und Benachrichtigungen erhalten — alles an einem Ort.
                    </p>
                    <ul className="mt-8 space-y-3">
                        {[
                            'Live-Status Ihrer Reparatur',
                            'Vollständiger Reparaturverlauf',
                            'E-Mail & SMS Benachrichtigungen',
                            'Garantieverwaltung',
                        ].map((item) => (
                            <li key={item} className="flex items-center gap-2.5 text-sm text-zinc-300">
                                <IconCheck />
                                {item}
                            </li>
                        ))}
                    </ul>
                    <div className="mt-10 flex items-center gap-3 border-t border-white/5 pt-8">
                        <div className="flex -space-x-2">
                            {['#c2410c', '#ea580c', '#9a3412', '#7c2d12'].map((c, i) => (
                                <div key={i} className="h-8 w-8 rounded-full border-2 border-zinc-900 flex items-center justify-center text-xs font-bold text-white"
                                    style={{ backgroundColor: c }}>
                                    {String.fromCharCode(65 + i)}
                                </div>
                            ))}
                        </div>
                        <p className="text-sm text-zinc-400">
                            <strong className="text-white">13.810</strong> Kunden vertrauen Moon.Repair
                        </p>
                    </div>
                </div>

                <div className="relative">
                    <p className="text-xs text-zinc-600">© {new Date().getFullYear()} Moon.Repair · moon.repair</p>
                </div>
            </div>

            {/* ── Right form panel ─────────────────────────────────────────── */}
            <div className="flex flex-1 flex-col items-center justify-center bg-zinc-50 px-6 py-12">
                <div className="mb-8 lg:hidden">
                    <Link href="/"><MoonLogo /></Link>
                </div>

                <div className="w-full max-w-md">
                    {status && (
                        <div className="mb-6 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                            {status}
                        </div>
                    )}

                    {/* Card */}
                    <div className="rounded-2xl border border-zinc-200 bg-white shadow-sm overflow-hidden">
                        {/* Orange accent top bar */}
                        <div className="h-1 w-full bg-gradient-to-r from-orange-500 to-orange-400" />

                        <div className="p-8">
                            {/* Tabs */}
                            <div className="flex rounded-xl bg-zinc-100 p-1 mb-6 gap-1">
                                {(['login', 'register'] as Tab[]).map((t) => (
                                    <button key={t} onClick={() => setTab(t)}
                                        className={`flex-1 cursor-pointer rounded-lg px-4 py-2 text-sm font-medium transition-all duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 ${
                                            tab === t ? 'bg-white text-zinc-900 shadow-sm' : 'text-zinc-500 hover:text-zinc-700'
                                        }`}>
                                        {t === 'login' ? 'Anmelden' : 'Registrieren'}
                                    </button>
                                ))}
                            </div>

                            <div className="mb-6">
                                <h2 className="font-display text-xl font-normal text-zinc-900">
                                    {tab === 'login' ? 'Willkommen zurück' : 'Konto erstellen'}
                                </h2>
                                <p className="mt-1 text-sm text-zinc-500">
                                    {tab === 'login'
                                        ? 'Melden Sie sich an, um Ihre Reparatur zu verfolgen.'
                                        : 'Erstellen Sie ein kostenloses Konto bei Moon.Repair.'}
                                </p>
                            </div>

                            {tab === 'login' ? <LoginForm canResetPassword={true} /> : <RegisterForm />}
                        </div>
                    </div>

                    <div className="mt-6 space-y-2 text-center text-sm text-zinc-500">
                        <p>
                            B2B-Partner?{' '}
                            <Link href={route('partner.login')}
                                className="font-medium text-orange-600 hover:text-orange-700 focus:outline-none focus-visible:underline transition-colors">
                                Zum Partnerportal →
                            </Link>
                        </p>
                        <p>
                            <Link href="/" className="text-zinc-400 hover:text-zinc-600 focus:outline-none focus-visible:underline transition-colors">
                                ← Zurück zu moon.repair
                            </Link>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    );
}
