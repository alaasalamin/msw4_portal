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

interface CustomerLoginProps {
    status?: string;
    defaultTab?: Tab;
}

const MoonLogo = () => (
    <div className="flex items-center gap-2.5">
        <span className="flex h-8 w-8 items-center justify-center rounded-lg bg-zinc-900">
            <svg viewBox="0 0 28 28" fill="none" className="h-7 w-7">
                <circle cx="14" cy="14" r="13" fill="#EA580C" opacity="0.18"/>
                <circle cx="14" cy="14" r="10" fill="#FB923C"/>
                <rect x="9.5" y="7.5" width="9" height="13.5" rx="1.5" stroke="white" strokeWidth="1.5" fill="none"/>
                <rect x="11" y="9.5" width="6" height="8.5" rx="0.5" fill="white" fillOpacity="0.3"/>
                <rect x="12.5" y="8.4" width="3" height="0.7" rx="0.35" fill="white" fillOpacity="0.6"/>
                <circle cx="14" cy="20.2" r="0.85" fill="white" fillOpacity="0.85"/>
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
        post(route('customer.login'), { onFinish: () => reset('password') });
    };

    return (
        <form onSubmit={submit} className="space-y-4">
            <FormInput
                id="login-email"
                label="Email address"
                type="email"
                value={data.email}
                autoComplete="username"
                required
                placeholder="you@example.com"
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
                    Remember me
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
                className="flex w-full cursor-pointer items-center justify-center gap-2 rounded-lg bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-orange-700 active:scale-[0.98] focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 focus-visible:ring-offset-2 disabled:opacity-60"
            >
                {processing ? (
                    <svg className="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                        <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                        <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                    </svg>
                ) : 'Sign In'}
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
        post(route('customer.register'));
    };

    return (
        <form onSubmit={submit} className="space-y-4">
            <FormInput
                id="reg-name"
                label="Full name"
                value={data.name}
                autoComplete="name"
                required
                placeholder="Jane Smith"
                error={errors.name}
                onChange={(e) => setData('name', e.target.value)}
            />
            <FormInput
                id="reg-email"
                label="Email address"
                type="email"
                value={data.email}
                autoComplete="username"
                required
                placeholder="you@example.com"
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

            <button
                type="submit"
                disabled={processing}
                className="flex w-full cursor-pointer items-center justify-center gap-2 rounded-lg bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-orange-700 active:scale-[0.98] focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 focus-visible:ring-offset-2 disabled:opacity-60"
            >
                {processing ? (
                    <svg className="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                        <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                        <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                    </svg>
                ) : 'Create Account'}
            </button>
        </form>
    );
}

export default function CustomerLogin({ status, defaultTab = 'login' }: CustomerLoginProps) {
    const [tab, setTab] = useState<Tab>(defaultTab);

    return (
        <div className="flex min-h-screen">
            <Head title="Customer Login — Moon.Repair" />

            {/* ── Left brand panel ───────────────────────────────────────── */}
            <div className="relative hidden w-[45%] flex-col justify-between overflow-hidden bg-zinc-900 p-10 lg:flex">
                {/* Background blobs */}
                <div className="pointer-events-none absolute -top-32 -left-32 h-96 w-96 rounded-full bg-orange-600/10 blur-3xl" />
                <div className="pointer-events-none absolute -bottom-32 -right-10 h-80 w-80 rounded-full bg-orange-600/8 blur-3xl" />
                {/* Grid overlay */}
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
                    <span className="mb-4 inline-flex items-center gap-1.5 rounded-full border border-orange-500/20 bg-orange-500/10 px-3 py-1 text-xs font-medium text-orange-300">
                        <span className="h-1.5 w-1.5 animate-pulse rounded-full bg-orange-400" />
                        Customer Portal
                    </span>

                    <h1 className="font-display mt-2 text-3xl font-normal leading-tight text-white">
                        Your repairs,<br />tracked in real time
                    </h1>
                    <p className="mt-3 text-sm leading-relaxed text-zinc-400">
                        Log in to check the status of your device, view past repairs, and get updates — all in one place.
                    </p>

                    <ul className="mt-8 space-y-3">
                        {[
                            'Live repair status updates',
                            'Complete repair history',
                            'Email & SMS notifications',
                            'Warranty claim management',
                        ].map((item) => (
                            <li key={item} className="flex items-center gap-2.5 text-sm text-zinc-300">
                                <IconCheck />
                                {item}
                            </li>
                        ))}
                    </ul>

                    <div className="mt-10 flex items-center gap-3 border-t border-white/5 pt-8">
                        <div className="flex -space-x-2">
                            {['#0369A1', '#0891B2', '#0E7490', '#155E75'].map((c, i) => (
                                <div
                                    key={i}
                                    className="h-8 w-8 rounded-full border-2 border-zinc-900 flex items-center justify-center text-xs font-bold text-white"
                                    style={{ backgroundColor: c }}
                                >
                                    {String.fromCharCode(65 + i)}
                                </div>
                            ))}
                        </div>
                        <p className="text-sm text-zinc-400">
                            <strong className="text-white">48,000+</strong> customers trust Moon.Repair
                        </p>
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

                    {/* Card */}
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
                                Create Account
                            </button>
                        </div>

                        <div className="mb-5">
                            <h2 className="font-display text-xl font-normal text-zinc-900">
                                {tab === 'login' ? 'Welcome back' : 'Join Moon.Repair'}
                            </h2>
                            <p className="mt-0.5 text-sm text-zinc-500">
                                {tab === 'login'
                                    ? 'Sign in to view your repair status.'
                                    : 'Create a free account to track your repairs.'}
                            </p>
                        </div>

                        {tab === 'login'
                            ? <LoginForm canResetPassword={true} />
                            : <RegisterForm />}
                    </div>

                    {/* Cross-portal link */}
                    <p className="mt-6 text-center text-sm text-zinc-500">
                        Are you a B2B partner?{' '}
                        <Link
                            href={route('partner.login')}
                            className="font-medium text-orange-600 hover:text-orange-700 focus:outline-none focus-visible:underline"
                        >
                            Sign in to the Partner Portal →
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
