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

const IconLock = () => (
    <svg className="h-5 w-5 text-amber-400" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
    </svg>
);

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
        <div className="relative flex min-h-screen flex-col items-center justify-center overflow-hidden bg-zinc-900 px-4 py-12">
            <Head title="Staff Login — Moonrepair" />

            {/* Background */}
            <div className="pointer-events-none absolute -top-40 left-1/2 h-[500px] w-[500px] -translate-x-1/2 rounded-full bg-amber-600/8 blur-3xl" />
            <div className="pointer-events-none absolute -bottom-40 left-1/4 h-80 w-80 rounded-full bg-indigo-600/6 blur-3xl" />
            <div
                className="pointer-events-none absolute inset-0 opacity-[0.02]"
                style={{
                    backgroundImage: 'linear-gradient(#94a3b8 1px, transparent 1px), linear-gradient(to right, #94a3b8 1px, transparent 1px)',
                    backgroundSize: '40px 40px',
                }}
            />

            {/* Logo */}
            <div className="relative mb-8">
                <Link href="/" className="flex items-center gap-2.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 rounded-md">
                    <span className="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-600 shadow-lg shadow-amber-600/30">
                        <svg className="h-5 w-5 text-white" viewBox="0 0 32 32" fill="none">
                            <circle cx="14.5" cy="16" r="8.5" fill="white" />
                            <circle cx="18.5" cy="12.5" r="7" fill="#F59E0B" />
                        </svg>
                    </span>
                    <span className="font-display text-2xl font-normal text-white">
                        Moon<span className="text-amber-400">repair</span>
                    </span>
                </Link>
            </div>

            {/* Card */}
            <div className="relative w-full max-w-sm">
                <div className="rounded-2xl border border-white/8 bg-white/5 p-8 shadow-2xl backdrop-blur-xl">

                    {/* Staff badge */}
                    <div className="mb-6 flex items-center gap-2">
                        <IconLock />
                        <span className="text-sm font-medium text-amber-300">Staff Access Only</span>
                    </div>

                    <h1 className="font-display text-2xl font-normal text-white mb-1">
                        Employee Sign In
                    </h1>
                    <p className="mb-6 text-sm text-zinc-400">
                        Internal portal. Authorised personnel only.
                    </p>

                    {status && (
                        <div className="mb-4 rounded-lg border border-green-500/20 bg-green-500/10 px-4 py-3 text-sm text-green-300">
                            {status}
                        </div>
                    )}

                    <form onSubmit={submit} className="space-y-4">
                        {/* Email */}
                        <div>
                            <label htmlFor="email" className="block text-xs font-medium text-zinc-400 mb-1.5">
                                Email address
                            </label>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value={data.email}
                                autoComplete="username"
                                required
                                onChange={(e) => setData('email', e.target.value)}
                                placeholder="staff@moonrepair.com"
                                className={`block w-full rounded-lg border bg-white/10 px-3.5 py-2.5 text-sm text-white placeholder-zinc-500 transition focus:outline-none focus:ring-2 focus:ring-amber-500 ${
                                    errors.email ? 'border-rose-500' : 'border-white/10'
                                }`}
                            />
                            {errors.email && (
                                <p className="mt-1.5 text-xs text-rose-400">{errors.email}</p>
                            )}
                        </div>

                        {/* Password */}
                        <div>
                            <label htmlFor="password" className="block text-xs font-medium text-zinc-400 mb-1.5">
                                Password
                            </label>
                            <div className="relative">
                                <input
                                    id="password"
                                    type={showPw ? 'text' : 'password'}
                                    name="password"
                                    value={data.password}
                                    autoComplete="current-password"
                                    required
                                    onChange={(e) => setData('password', e.target.value)}
                                    placeholder="••••••••"
                                    className={`block w-full rounded-lg border bg-white/10 px-3.5 py-2.5 pr-11 text-sm text-white placeholder-zinc-500 transition focus:outline-none focus:ring-2 focus:ring-amber-500 ${
                                        errors.password ? 'border-rose-500' : 'border-white/10'
                                    }`}
                                />
                                <button
                                    type="button"
                                    onClick={() => setShowPw((v) => !v)}
                                    className="absolute inset-y-0 right-0 flex cursor-pointer items-center px-3 text-zinc-400 hover:text-zinc-200"
                                    aria-label={showPw ? 'Hide password' : 'Show password'}
                                >
                                    {showPw ? <IconEyeOff className="h-4 w-4" /> : <IconEye className="h-4 w-4" />}
                                </button>
                            </div>
                            {errors.password && (
                                <p className="mt-1.5 text-xs text-rose-400">{errors.password}</p>
                            )}
                        </div>

                        {/* Remember */}
                        <label className="flex cursor-pointer items-center gap-2 text-sm text-zinc-400">
                            <input
                                type="checkbox"
                                checked={data.remember}
                                onChange={(e) => setData('remember', e.target.checked)}
                                className="h-4 w-4 rounded border-white/20 bg-white/10 text-amber-500 focus:ring-amber-500 focus:ring-offset-0 focus:ring-offset-transparent"
                            />
                            Keep me signed in on this device
                        </label>

                        <button
                            type="submit"
                            disabled={processing}
                            className="flex w-full cursor-pointer items-center justify-center gap-2 rounded-lg bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-amber-500 active:scale-[0.98] focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 focus-visible:ring-offset-2 focus-visible:ring-offset-zinc-900 disabled:opacity-60"
                        >
                            {processing ? (
                                <svg className="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                                    <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                </svg>
                            ) : 'Sign In'}
                        </button>
                    </form>
                </div>

                {/* Footer links */}
                <div className="mt-6 space-y-2 text-center text-xs text-zinc-600">
                    <p>
                        Not an employee?{' '}
                        <Link href={route('customer.login')} className="text-zinc-400 hover:text-zinc-200 transition">
                            Customer login
                        </Link>
                        {' · '}
                        <Link href={route('partner.login')} className="text-zinc-400 hover:text-zinc-200 transition">
                            Partner login
                        </Link>
                    </p>
                    <p>
                        <Link href="/" className="hover:text-zinc-400 transition">
                            ← moonrepair.com
                        </Link>
                    </p>
                </div>
            </div>
        </div>
    );
}
