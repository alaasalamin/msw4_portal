import React, { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';

type IconProps = { className?: string };

type Tab = 'login' | 'register';

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

interface FormInputProps {
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

interface LoginFormProps {
    canResetPassword: boolean;
}

interface PartnerLoginProps {
    status?: string;
    defaultTab?: Tab;
}

const MoonLogo = () => (
    <div className="flex items-center gap-2.5">
        <span className="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-600">
            <svg className="h-4 w-4 text-white" viewBox="0 0 32 32" fill="none">
                <circle cx="14.5" cy="16" r="8.5" fill="white" />
                <circle cx="18.5" cy="12.5" r="7" fill="#EA580C" />
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

const FormInput = ({ id, label, type = 'text', value, onChange, error, autoComplete, placeholder, required, children }: FormInputProps) => (
    <div>
        <label htmlFor={id} className="block text-sm font-medium text-zinc-700 mb-1.5">
            {label} {required && <span className="text-rose-500">*</span>}
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
                className={`block w-full rounded-lg border px-3.5 py-2.5 text-sm text-zinc-900 placeholder-zinc-400 transition focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 ${
                    error ? 'border-rose-400 bg-rose-50' : 'border-zinc-200 bg-white'
                }`}
            />
            {children}
        </div>
        {error && <p className="mt-1.5 text-xs text-rose-600">{error}</p>}
    </div>
);

function LoginForm({ canResetPassword }: LoginFormProps) {
    const [showPw, setShowPw] = useState(false);
    const { data, setData, post, processing, errors, reset } = useForm<LoginFormData>({
        email: '',
        password: '',
        remember: false,
    });

    const submit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        post(route('partner.login'), { onFinish: () => reset('password') });
    };

    return (
        <form onSubmit={submit} className="space-y-4">
            <FormInput
                id="login-email"
                label="Work email"
                type="email"
                value={data.email}
                autoComplete="username"
                required
                placeholder="you@company.com"
                error={errors.email}
                onChange={(e) => setData('email', e.target.value)}
            />

            <FormInput
                id="login-password"
                label="Password"
                type={showPw ? 'text' : 'password'}
                value={data.password}
                autoComplete="current-password"
                required
                placeholder="••••••••"
                error={errors.password}
                onChange={(e) => setData('password', e.target.value)}
            >
                <button
                    type="button"
                    onClick={() => setShowPw((v) => !v)}
                    className="absolute inset-y-0 right-0 flex cursor-pointer items-center px-3 text-zinc-400 hover:text-zinc-600"
                    aria-label={showPw ? 'Hide password' : 'Show password'}
                >
                    {showPw ? <IconEyeOff className="h-4 w-4" /> : <IconEye className="h-4 w-4" />}
                </button>
            </FormInput>

            <div className="flex items-center justify-between">
                <label className="flex cursor-pointer items-center gap-2 text-sm text-zinc-600">
                    <input
                        type="checkbox"
                        checked={data.remember}
                        onChange={(e) => setData('remember', e.target.checked)}
                        className="h-4 w-4 rounded border-zinc-300 text-orange-600 focus:ring-orange-500"
                    />
                    Stay signed in
                </label>
                {canResetPassword && (
                    <Link
                        href={route('password.request')}
                        className="text-sm text-orange-600 hover:text-orange-700 focus:outline-none focus-visible:underline"
                    >
                        Forgot password?
                    </Link>
                )}
            </div>

            <button
                type="submit"
                disabled={processing}
                className="flex w-full cursor-pointer items-center justify-center gap-2 rounded-lg bg-zinc-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-zinc-800 active:scale-[0.98] focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-700 focus-visible:ring-offset-2 disabled:opacity-60"
            >
                {processing ? (
                    <svg className="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                        <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                        <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                    </svg>
                ) : 'Access Partner Portal'}
            </button>
        </form>
    );
}

function RegisterForm() {
    const [showPw, setShowPw] = useState(false);
    const { data, setData, post, processing, errors } = useForm<RegisterFormData>({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    const submit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        post(route('partner.register'));
    };

    return (
        <form onSubmit={submit} className="space-y-4">
            <FormInput
                id="reg-name"
                label="Company name"
                value={data.name}
                autoComplete="organization"
                required
                placeholder="Acme Repair Ltd."
                error={errors.name}
                onChange={(e) => setData('name', e.target.value)}
            />
            <FormInput
                id="reg-email"
                label="Work email"
                type="email"
                value={data.email}
                autoComplete="username"
                required
                placeholder="you@company.com"
                error={errors.email}
                onChange={(e) => setData('email', e.target.value)}
            />
            <FormInput
                id="reg-password"
                label="Password"
                type={showPw ? 'text' : 'password'}
                value={data.password}
                autoComplete="new-password"
                required
                placeholder="Min. 8 characters"
                error={errors.password}
                onChange={(e) => setData('password', e.target.value)}
            >
                <button
                    type="button"
                    onClick={() => setShowPw((v) => !v)}
                    className="absolute inset-y-0 right-0 flex cursor-pointer items-center px-3 text-zinc-400 hover:text-zinc-600"
                    aria-label={showPw ? 'Hide password' : 'Show password'}
                >
                    {showPw ? <IconEyeOff className="h-4 w-4" /> : <IconEye className="h-4 w-4" />}
                </button>
            </FormInput>
            <FormInput
                id="reg-confirm"
                label="Confirm password"
                type="password"
                value={data.password_confirmation}
                autoComplete="new-password"
                required
                placeholder="Repeat your password"
                error={errors.password_confirmation}
                onChange={(e) => setData('password_confirmation', e.target.value)}
            />

            <p className="text-xs text-zinc-500 leading-relaxed">
                By registering, you agree to our{' '}
                <a href="#" className="underline hover:text-zinc-700">Partner Terms</a>{' '}
                and{' '}
                <a href="#" className="underline hover:text-zinc-700">Privacy Policy</a>.
            </p>

            <button
                type="submit"
                disabled={processing}
                className="flex w-full cursor-pointer items-center justify-center gap-2 rounded-lg bg-zinc-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-zinc-800 active:scale-[0.98] focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-700 focus-visible:ring-offset-2 disabled:opacity-60"
            >
                {processing ? (
                    <svg className="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                        <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                        <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                    </svg>
                ) : 'Apply for Access'}
            </button>
        </form>
    );
}

export default function PartnerLogin({ status, defaultTab = 'login' }: PartnerLoginProps) {
    const [tab, setTab] = useState<Tab>(defaultTab);

    return (
        <div className="flex min-h-screen">
            <Head title="Partner Portal — Moon.Repair" />

            {/* ── Left brand panel ───────────────────────────────────────── */}
            <div className="relative hidden w-[45%] flex-col justify-between overflow-hidden bg-zinc-900 p-10 lg:flex">
                <div className="pointer-events-none absolute -top-32 right-0 h-96 w-96 rounded-full bg-orange-600/8 blur-3xl" />
                <div className="pointer-events-none absolute bottom-0 left-0 h-80 w-80 rounded-full bg-indigo-600/8 blur-3xl" />
                <div
                    className="pointer-events-none absolute inset-0 opacity-[0.025]"
                    style={{
                        backgroundImage: 'linear-gradient(#94a3b8 1px, transparent 1px), linear-gradient(to right, #94a3b8 1px, transparent 1px)',
                        backgroundSize: '40px 40px',
                    }}
                />

                <div className="relative">
                    <Link href="/" className="focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400 rounded-md inline-block">
                        <MoonLogo />
                    </Link>
                </div>

                <div className="relative">
                    <span className="mb-4 inline-flex items-center gap-1.5 rounded-full border border-indigo-500/20 bg-indigo-500/10 px-3 py-1 text-xs font-medium text-indigo-300">
                        B2B Partner Programme
                    </span>

                    <h1 className="font-display mt-2 text-3xl font-normal leading-tight text-white">
                        Scale your repair<br />operations with us
                    </h1>
                    <p className="mt-3 text-sm leading-relaxed text-zinc-400">
                        Manage bulk submissions, track SLAs, and get consolidated invoicing — all from one portal built for your business.
                    </p>

                    <ul className="mt-8 space-y-3">
                        {[
                            'Bulk device submission & tracking',
                            'Priority SLA with guaranteed turnarounds',
                            'Real-time repair status per shipment',
                            'Consolidated invoicing & reporting',
                            'Dedicated account manager',
                        ].map((item) => (
                            <li key={item} className="flex items-center gap-2.5 text-sm text-zinc-300">
                                <IconCheck />
                                {item}
                            </li>
                        ))}
                    </ul>

                    {/* Partner logos / trust signal */}
                    <div className="mt-10 border-t border-white/5 pt-8">
                        <p className="mb-3 text-xs text-zinc-500 uppercase tracking-widest">Trusted by 320+ partners</p>
                        <div className="flex flex-wrap gap-2">
                            {['Retail Chains', 'Insurers', 'Telecoms', 'MSPs'].map((cat) => (
                                <span key={cat} className="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs text-zinc-400">
                                    {cat}
                                </span>
                            ))}
                        </div>
                    </div>
                </div>

                <div className="relative">
                    <p className="text-xs text-zinc-600">© {new Date().getFullYear()} Moon.Repair · moon.repair</p>
                </div>
            </div>

            {/* ── Right form panel ───────────────────────────────────────── */}
            <div className="flex flex-1 flex-col items-center justify-center bg-zinc-50 px-6 py-12">
                {/* Mobile logo */}
                <div className="mb-8 lg:hidden">
                    <Link href="/"><MoonLogo /></Link>
                </div>

                <div className="w-full max-w-md">
                    {status && (
                        <div className="mb-6 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                            {status}
                        </div>
                    )}

                    <div className="rounded-2xl border border-zinc-200 bg-white p-8 shadow-sm">
                        {/* Tabs */}
                        <div className="flex rounded-xl bg-zinc-100 p-1 mb-6">
                            <button
                                onClick={() => setTab('login')}
                                className={`flex-1 cursor-pointer rounded-lg px-4 py-2 text-sm font-medium transition-all duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 ${
                                    tab === 'login'
                                        ? 'bg-white text-zinc-900 shadow-sm'
                                        : 'text-zinc-500 hover:text-zinc-700'
                                }`}
                            >
                                Sign In
                            </button>
                            <button
                                onClick={() => setTab('register')}
                                className={`flex-1 cursor-pointer rounded-lg px-4 py-2 text-sm font-medium transition-all duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 ${
                                    tab === 'register'
                                        ? 'bg-white text-zinc-900 shadow-sm'
                                        : 'text-zinc-500 hover:text-zinc-700'
                                }`}
                            >
                                Apply for Access
                            </button>
                        </div>

                        <div className="mb-5">
                            <h2 className="font-display text-xl font-normal text-zinc-900">
                                {tab === 'login' ? 'Partner Portal' : 'Register your business'}
                            </h2>
                            <p className="mt-0.5 text-sm text-zinc-500">
                                {tab === 'login'
                                    ? 'Sign in to access your B2B dashboard.'
                                    : 'Apply for access to our partner programme.'}
                            </p>
                        </div>

                        {tab === 'login'
                            ? <LoginForm canResetPassword={true} />
                            : <RegisterForm />}
                    </div>

                    <p className="mt-6 text-center text-sm text-zinc-500">
                        Individual customer?{' '}
                        <Link
                            href={route('customer.login')}
                            className="font-medium text-orange-600 hover:text-orange-700 focus:outline-none focus-visible:underline"
                        >
                            Sign in to the Customer Portal →
                        </Link>
                    </p>
                    <p className="mt-2 text-center text-sm text-zinc-500">
                        <Link
                            href="/"
                            className="text-zinc-400 hover:text-zinc-600 focus:outline-none focus-visible:underline"
                        >
                            ← Back to moon.repair
                        </Link>
                    </p>
                </div>
            </div>
        </div>
    );
}
