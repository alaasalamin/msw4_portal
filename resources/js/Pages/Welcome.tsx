import { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import { PageProps } from '@/types';

// ── Icon type ──────────────────────────────────────────────────────────────────

type IconProps = { className?: string };

// ── Icons ──────────────────────────────────────────────────────────────────────

const IconWrench = ({ className }: IconProps) => (
    <svg className={className} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z" />
    </svg>
);

const IconPhone = ({ className }: IconProps) => (
    <svg className={className} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 15.75h3" />
    </svg>
);

const IconLaptop = ({ className }: IconProps) => (
    <svg className={className} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0H3" />
    </svg>
);

const IconTablet = ({ className }: IconProps) => (
    <svg className={className} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M10.5 19.5h3m-6.75 2.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-15a2.25 2.25 0 0 0-2.25-2.25H6.75A2.25 2.25 0 0 0 4.5 4.5v15a2.25 2.25 0 0 0 2.25 2.25Z" />
    </svg>
);

const IconShield = ({ className }: IconProps) => (
    <svg className={className} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
    </svg>
);

const IconClock = ({ className }: IconProps) => (
    <svg className={className} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
    </svg>
);

const IconDatabase = ({ className }: IconProps) => (
    <svg className={className} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
    </svg>
);

const IconCheck = ({ className }: IconProps) => (
    <svg className={className} fill="none" viewBox="0 0 24 24" strokeWidth={2} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="m4.5 12.75 6 6 9-13.5" />
    </svg>
);

const IconArrowRight = ({ className }: IconProps) => (
    <svg className={className} fill="none" viewBox="0 0 24 24" strokeWidth={2} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
    </svg>
);

const IconBuilding = ({ className }: IconProps) => (
    <svg className={className} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
    </svg>
);

const IconUser = ({ className }: IconProps) => (
    <svg className={className} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
    </svg>
);

const IconStar = ({ className }: IconProps) => (
    <svg className={className} viewBox="0 0 20 20" fill="currentColor">
        <path fillRule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401Z" clipRule="evenodd" />
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

// ── Services data ──────────────────────────────────────────────────────────────

type ServiceColor = 'sky' | 'violet' | 'emerald' | 'amber' | 'rose';

interface Service {
    icon: (props: IconProps) => React.ReactElement;
    title: string;
    desc: string;
    color: ServiceColor;
}

const SERVICES: Service[] = [
    {
        icon: IconPhone,
        title: 'Smartphone Repair',
        desc: 'Screen replacements, battery swaps, charging port fixes, and water damage recovery for all major brands.',
        color: 'sky',
    },
    {
        icon: IconLaptop,
        title: 'Laptop & PC Repair',
        desc: 'Keyboard, display, motherboard diagnostics, SSD upgrades, and OS recovery for Windows and macOS.',
        color: 'violet',
    },
    {
        icon: IconTablet,
        title: 'Tablet Repair',
        desc: 'iPad and Android tablet glass, digitizer, and battery replacement with original components.',
        color: 'emerald',
    },
    {
        icon: IconShield,
        title: 'Warranty Service',
        desc: 'Authorised warranty repairs and post-warranty service with certified parts and documented diagnostics.',
        color: 'amber',
    },
    {
        icon: IconDatabase,
        title: 'Data Recovery',
        desc: 'Professional data extraction from damaged drives, broken phones, and failed storage media.',
        color: 'rose',
    },
    {
        icon: IconClock,
        title: 'Express Turnaround',
        desc: 'Same-day diagnosis and priority repair lanes for business customers and urgent individual requests.',
        color: 'sky',
    },
];

const colorMap: Record<ServiceColor, { bg: string; icon: string; border: string }> = {
    sky:     { bg: 'bg-orange-50',     icon: 'text-orange-600',     border: 'border-orange-100' },
    violet:  { bg: 'bg-orange-50',  icon: 'text-orange-600',  border: 'border-orange-100' },
    emerald: { bg: 'bg-emerald-50', icon: 'text-emerald-600', border: 'border-emerald-100' },
    amber:   { bg: 'bg-orange-50',   icon: 'text-orange-600',   border: 'border-orange-100' },
    rose:    { bg: 'bg-rose-50',    icon: 'text-rose-600',    border: 'border-rose-100' },
};

const STEPS = [
    { num: '01', title: 'Submit Your Device',  desc: 'Drop off in-store, or your B2B partner ships the device through our portal.' },
    { num: '02', title: 'Diagnosis & Quote',   desc: 'Our certified technicians diagnose the issue and send you a transparent repair quote.' },
    { num: '03', title: 'Repair & Return',     desc: 'Approved repairs are completed with genuine parts and shipped back or ready for collection.' },
];

const PARTNER_BENEFITS = [
    'Bulk repair submissions via dedicated portal',
    'Priority SLA with guaranteed turnaround times',
    'Real-time device tracking per shipment',
    'Consolidated invoicing and reporting',
    'Dedicated account manager',
    'API integration available',
];

// ── Login form ─────────────────────────────────────────────────────────────────

type LoginFormData = { email: string; password: string; remember: boolean };
type Portal = 'customer' | 'partner';

interface LoginWidgetProps {
    canResetPassword: boolean;
}

function LoginWidget({ canResetPassword }: LoginWidgetProps) {
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
        <div className="rounded-2xl border border-white/10 bg-white/5 p-1 backdrop-blur-xl shadow-2xl">
            {/* Tabs */}
            <div className="flex rounded-xl bg-white/5 p-1 gap-1">
                <button
                    onClick={() => handleTabChange('customer')}
                    className={`flex flex-1 cursor-pointer items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium transition-all duration-200 ${
                        tab === 'customer'
                            ? 'bg-white text-zinc-900 shadow-sm'
                            : 'text-zinc-400 hover:text-white'
                    }`}
                >
                    <IconUser className="h-4 w-4" />
                    Customer
                </button>
                <button
                    onClick={() => handleTabChange('partner')}
                    className={`flex flex-1 cursor-pointer items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium transition-all duration-200 ${
                        tab === 'partner'
                            ? 'bg-orange-600 text-white shadow-sm'
                            : 'text-zinc-400 hover:text-white'
                    }`}
                >
                    <IconBuilding className="h-4 w-4" />
                    Partner
                </button>
            </div>

            {/* Context line */}
            <div className="px-4 pt-4 pb-2">
                {tab === 'customer' ? (
                    <p className="text-sm text-zinc-400">Track your repairs and manage your devices.</p>
                ) : (
                    <p className="text-sm text-orange-400">Access the B2B portal — bulk submissions, SLAs, and reporting.</p>
                )}
            </div>

            {/* Form */}
            <form onSubmit={submit} className="space-y-3 px-4 pb-4 pt-2">
                <div>
                    <label htmlFor="login-email" className="block text-xs font-medium text-zinc-400 mb-1">
                        Email address
                    </label>
                    <input
                        id="login-email"
                        type="email"
                        name="email"
                        value={data.email}
                        autoComplete="username"
                        required
                        onChange={(e) => setData('email', e.target.value)}
                        className={`block w-full rounded-lg border bg-white/10 px-3 py-2.5 text-sm text-white placeholder-zinc-500 transition focus:outline-none focus:ring-2 focus:ring-orange-500 ${
                            errors.email ? 'border-rose-500' : 'border-white/10'
                        }`}
                        placeholder="you@example.com"
                    />
                    {errors.email && <p className="mt-1 text-xs text-rose-400">{errors.email}</p>}
                </div>

                <div>
                    <label htmlFor="login-password" className="block text-xs font-medium text-zinc-400 mb-1">
                        Password
                    </label>
                    <div className="relative">
                        <input
                            id="login-password"
                            type={showPw ? 'text' : 'password'}
                            name="password"
                            value={data.password}
                            autoComplete="current-password"
                            required
                            onChange={(e) => setData('password', e.target.value)}
                            className={`block w-full rounded-lg border bg-white/10 px-3 py-2.5 pr-10 text-sm text-white placeholder-zinc-500 transition focus:outline-none focus:ring-2 focus:ring-orange-500 ${
                                errors.password ? 'border-rose-500' : 'border-white/10'
                            }`}
                            placeholder="••••••••"
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
                    {errors.password && <p className="mt-1 text-xs text-rose-400">{errors.password}</p>}
                </div>

                <div className="flex items-center justify-between">
                    <label className="flex cursor-pointer items-center gap-2 text-xs text-zinc-400">
                        <input
                            type="checkbox"
                            checked={data.remember}
                            onChange={(e) => setData('remember', e.target.checked)}
                            className="h-3.5 w-3.5 rounded border-white/20 bg-white/10 text-orange-500 focus:ring-orange-500 focus:ring-offset-0"
                        />
                        Remember me
                    </label>
                    {canResetPassword && (
                        <Link
                            href={route('password.request')}
                            className="text-xs text-orange-400 hover:text-orange-300 focus:outline-none focus-visible:underline"
                        >
                            Forgot password?
                        </Link>
                    )}
                </div>

                <button
                    type="submit"
                    disabled={processing}
                    className={`flex w-full cursor-pointer items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-semibold text-white transition-all duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400 focus-visible:ring-offset-2 focus-visible:ring-offset-zinc-900 disabled:opacity-60 ${
                        tab === 'partner'
                            ? 'bg-orange-600 hover:bg-orange-500 active:scale-[0.98]'
                            : 'bg-zinc-600 hover:bg-zinc-500 active:scale-[0.98]'
                    }`}
                >
                    {processing ? (
                        <svg className="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                    ) : (
                        <>
                            Sign in
                            <IconArrowRight className="h-4 w-4" />
                        </>
                    )}
                </button>
            </form>
        </div>
    );
}

// ── Main component ─────────────────────────────────────────────────────────────

type WelcomeProps = PageProps<{
    canLogin: boolean;
    canRegister: boolean;
    canResetPassword: boolean;
}>;

export default function Welcome({ auth, canLogin, canRegister, canResetPassword }: WelcomeProps) {
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

    return (
        <>
            <Head title="Moon.Repair — Handy Reparatur & Datenrettung in Forchheim" />

            {/* ── Sticky Navbar ─────────────────────────────────────────────── */}
            <header className="fixed inset-x-0 top-0 z-50 border-b border-white/5 bg-zinc-900/90 backdrop-blur-md">
                <div className="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
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

                    <nav className="hidden items-center gap-6 sm:flex">
                        <a href="#services" className="text-sm text-zinc-400 transition hover:text-white">Reparaturen</a>
                        <a href="#how-it-works" className="text-sm text-zinc-400 transition hover:text-white">So funktioniert's</a>
                        <a href="#login" className="text-sm text-zinc-400 transition hover:text-white">Repair Anfrage</a>
                    </nav>

                    <div className="flex items-center gap-2">
                        {auth?.user ? (
                            <Link
                                href={route('dashboard')}
                                className="rounded-lg bg-orange-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-orange-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400"
                            >
                                Dashboard
                            </Link>
                        ) : (
                            <>
                                {canLogin && (
                                    <Link
                                        href={route('customer.login')}
                                        className="hidden cursor-pointer rounded-lg px-4 py-2 text-sm font-medium text-zinc-300 transition hover:text-white sm:block focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400"
                                    >
                                        Customer Login
                                    </Link>
                                )}
                                <Link
                                    href={route('partner.login')}
                                    className="rounded-lg bg-orange-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-orange-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400"
                                >
                                    Partner Portal
                                </Link>
                            </>
                        )}

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

                {mobileMenuOpen && (
                    <div className="border-t border-white/5 bg-zinc-900 px-4 pb-4 pt-2 sm:hidden">
                        <nav className="flex flex-col gap-1">
                            <a href="#services"     onClick={() => setMobileMenuOpen(false)} className="rounded-md px-3 py-2 text-sm text-zinc-300 hover:bg-white/5 hover:text-white">Reparaturen</a>
                            <a href="#how-it-works" onClick={() => setMobileMenuOpen(false)} className="rounded-md px-3 py-2 text-sm text-zinc-300 hover:bg-white/5 hover:text-white">So funktioniert's</a>
                            <a href="#login"        onClick={() => setMobileMenuOpen(false)} className="rounded-md px-3 py-2 text-sm text-zinc-300 hover:bg-white/5 hover:text-white">Repair Anfrage</a>
                        </nav>
                    </div>
                )}
            </header>

            {/* ── Hero ──────────────────────────────────────────────────────── */}
            <section className="relative min-h-screen overflow-hidden bg-zinc-900 pt-16">
                <div
                    className="pointer-events-none absolute inset-0 opacity-[0.03]"
                    style={{
                        backgroundImage: 'linear-gradient(#94a3b8 1px, transparent 1px), linear-gradient(to right, #94a3b8 1px, transparent 1px)',
                        backgroundSize: '48px 48px',
                    }}
                />
                <div className="pointer-events-none absolute -top-40 -right-40 h-[600px] w-[600px] rounded-full bg-orange-600/10 blur-3xl" />
                <div className="pointer-events-none absolute -bottom-40 -left-40 h-[500px] w-[500px] rounded-full bg-orange-600/10 blur-3xl" />

                <div className="relative mx-auto max-w-7xl px-4 py-20 sm:px-6 sm:py-28 lg:px-8 lg:py-32">
                    <div className="grid items-center gap-12 lg:grid-cols-2">
                        <div>
                            <div className="mb-6 inline-flex items-center gap-2 rounded-full border border-orange-500/20 bg-orange-500/10 px-4 py-1.5">
                                <span className="h-1.5 w-1.5 animate-pulse rounded-full bg-orange-400" />
                                <span className="text-xs font-medium text-orange-300">Forchheim, Bayern · Zertifizierter Reparaturbetrieb</span>
                            </div>

                            <h1 className="font-display text-4xl font-normal leading-tight text-white sm:text-5xl lg:text-6xl">
                                Handy Reparatur &amp;{' '}
                                <span className="text-orange-400">Datenrettung</span>
                            </h1>

                            <p className="mt-6 text-lg leading-relaxed text-zinc-400">
                                Moon.Repair ist dein professionelles Reparatur- und Datenrettungszentrum in Forchheim.
                                Ob Displayschaden, Akkutausch oder verlorene Daten — wir reparieren schnell, zuverlässig und mit Garantie.
                            </p>

                            <ul className="mt-8 space-y-3">
                                {[
                                    'Diagnose meist noch am selben Tag',
                                    '90 Tage Garantie auf alle Reparaturen',
                                    'Professionelle Datenrettung bei Wasserschäden & Defekten',
                                    'Transparente Preise — keine versteckten Kosten',
                                ].map((item) => (
                                    <li key={item} className="flex items-center gap-3 text-sm text-zinc-300">
                                        <span className="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-orange-600/20">
                                            <IconCheck className="h-3 w-3 text-orange-400" />
                                        </span>
                                        {item}
                                    </li>
                                ))}
                            </ul>

                            <div className="mt-10 flex items-center gap-3">
                                <div className="flex">
                                    {[...Array(5)].map((_, i) => (
                                        <IconStar key={i} className="h-4 w-4 text-orange-400" />
                                    ))}
                                </div>
                                <span className="text-sm text-zinc-400">
                                    <strong className="text-white">4.7/5</strong> — über 13.810 Reparaturen
                                </span>
                            </div>
                        </div>

                        <div id="login" className="lg:pl-8">
                            <p className="mb-3 text-center text-xs font-medium uppercase tracking-widest text-zinc-500">
                                Repair Anfrage / Kundenportal
                            </p>
                            <LoginWidget canResetPassword={canResetPassword} />
                            {canRegister && (
                                <p className="mt-4 text-center text-sm text-zinc-500">
                                    Noch kein Konto?{' '}
                                    <Link
                                        href={route('customer.register')}
                                        className="font-medium text-orange-400 hover:text-orange-300 focus:outline-none focus-visible:underline"
                                    >
                                        Kostenlos registrieren
                                    </Link>
                                </p>
                            )}
                        </div>
                    </div>
                </div>
            </section>

            {/* ── Stats bar ─────────────────────────────────────────────────── */}
            <section className="border-y border-zinc-200 bg-white">
                <div className="mx-auto grid max-w-7xl grid-cols-2 gap-px bg-zinc-200 sm:grid-cols-4">
                    {[
                        { value: '13,810',  label: 'Devices Repaired' },
                        { value: '320+',    label: 'B2B Partners' },
                        { value: '98.6%',   label: 'Satisfaction Rate' },
                        { value: '< 24h',   label: 'Avg. Turnaround' },
                    ].map(({ value, label }) => (
                        <div key={label} className="flex flex-col items-center justify-center bg-white px-6 py-8 text-center">
                            <span className="font-display text-3xl font-normal text-zinc-900">{value}</span>
                            <span className="mt-1 text-sm text-zinc-500">{label}</span>
                        </div>
                    ))}
                </div>
            </section>

            {/* ── Services ──────────────────────────────────────────────────── */}
            <section id="services" className="bg-zinc-50 py-20 sm:py-28">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div className="mx-auto max-w-2xl text-center">
                        <p className="text-sm font-semibold uppercase tracking-widest text-orange-600">What we repair</p>
                        <h2 className="font-display mt-3 text-3xl font-normal text-zinc-900 sm:text-4xl">Every device, every problem</h2>
                        <p className="mt-4 text-lg text-zinc-500">
                            Our certified technicians handle everything from cracked screens to complex motherboard issues.
                        </p>
                    </div>

                    <div className="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        {SERVICES.map(({ icon: Icon, title, desc, color }) => {
                            const c = colorMap[color];
                            return (
                                <div
                                    key={title}
                                    className={`group rounded-2xl border ${c.border} ${c.bg} p-6 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md`}
                                >
                                    <div className="mb-4 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-white shadow-sm">
                                        <Icon className={`h-5 w-5 ${c.icon}`} />
                                    </div>
                                    <h3 className="mb-2 font-semibold text-zinc-900">{title}</h3>
                                    <p className="text-sm leading-relaxed text-zinc-500">{desc}</p>
                                </div>
                            );
                        })}
                    </div>
                </div>
            </section>

            {/* ── How it works ──────────────────────────────────────────────── */}
            <section id="how-it-works" className="bg-white py-20 sm:py-28">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div className="mx-auto max-w-2xl text-center">
                        <p className="text-sm font-semibold uppercase tracking-widest text-orange-600">The process</p>
                        <h2 className="font-display mt-3 text-3xl font-normal text-zinc-900 sm:text-4xl">Simple from start to finish</h2>
                    </div>

                    <div className="mt-16 grid gap-8 sm:grid-cols-3">
                        {STEPS.map(({ num, title, desc }, i) => (
                            <div key={num} className="relative">
                                {i < STEPS.length - 1 && (
                                    <div className="absolute left-[calc(50%+3rem)] top-6 hidden h-px w-[calc(100%-6rem)] border-t border-dashed border-zinc-200 sm:block" />
                                )}
                                <div className="flex flex-col items-center text-center">
                                    <div className="flex h-12 w-12 items-center justify-center rounded-2xl bg-zinc-900 font-mono text-sm font-medium text-white shadow">
                                        {num}
                                    </div>
                                    <h3 className="mt-4 font-semibold text-zinc-900">{title}</h3>
                                    <p className="mt-2 text-sm leading-relaxed text-zinc-500">{desc}</p>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* ── B2B Partner section ────────────────────────────────────────── */}
            <section id="partners" className="relative overflow-hidden bg-zinc-900 py-20 sm:py-28">
                <div className="pointer-events-none absolute -top-32 right-0 h-[500px] w-[500px] rounded-full bg-orange-600/10 blur-3xl" />

                <div className="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div className="grid items-center gap-12 lg:grid-cols-2">
                        <div>
                            <span className="inline-flex items-center gap-2 rounded-full border border-orange-500/20 bg-orange-500/10 px-3 py-1 text-xs font-medium text-orange-300">
                                <IconBuilding className="h-3.5 w-3.5" />
                                B2B Partner Programme
                            </span>
                            <h2 className="font-display mt-4 text-3xl font-normal text-white sm:text-4xl">
                                Scale your repair operations with a trusted partner
                            </h2>
                            <p className="mt-4 text-lg text-zinc-400">
                                Retail chains, insurers, and telecom operators rely on Moon.Repair (moon.repair) to handle high-volume
                                device repairs with predictable SLAs and complete visibility.
                            </p>

                            <ul className="mt-8 grid gap-3 sm:grid-cols-2">
                                {PARTNER_BENEFITS.map((benefit) => (
                                    <li key={benefit} className="flex items-start gap-2.5 text-sm text-zinc-300">
                                        <span className="mt-0.5 flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-orange-600/30">
                                            <IconCheck className="h-2.5 w-2.5 text-orange-400" />
                                        </span>
                                        {benefit}
                                    </li>
                                ))}
                            </ul>

                            <div className="mt-10 flex flex-wrap gap-3">
                                <a
                                    href="#login"
                                    className="inline-flex cursor-pointer items-center gap-2 rounded-lg bg-orange-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-orange-500 active:scale-[0.98] focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400"
                                >
                                    Access Partner Portal
                                    <IconArrowRight className="h-4 w-4" />
                                </a>
                                <a
                                    href="mailto:partners@moon.repair"
                                    className="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-white/10 px-6 py-3 text-sm font-medium text-zinc-300 transition hover:border-white/20 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400"
                                >
                                    Contact Sales
                                </a>
                            </div>
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                            {[
                                { title: 'Bulk Submissions', desc: 'Upload device lists via CSV or API integration.' },
                                { title: 'Live Tracking',    desc: 'Real-time status updates per device and shipment.' },
                                { title: 'SLA Dashboard',   desc: 'Monitor repair SLAs and escalate instantly.' },
                                { title: 'Auto Invoicing',  desc: 'Consolidated monthly invoices with full line items.' },
                            ].map(({ title, desc }) => (
                                <div key={title} className="rounded-xl border border-white/5 bg-white/5 p-5">
                                    <h3 className="mb-1 text-sm font-semibold text-white">{title}</h3>
                                    <p className="text-xs leading-relaxed text-zinc-400">{desc}</p>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </section>

            {/* ── Testimonials ──────────────────────────────────────────────── */}
            <section className="bg-zinc-50 py-16 sm:py-24">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div className="grid gap-6 sm:grid-cols-3">
                        {[
                            { quote: 'Moon.Repair turned our device repair backlog from weeks into days. The partner portal is genuinely excellent.', name: 'Sarah M.', role: 'Operations Lead, TelecomPlus' },
                            { quote: 'Dropped my phone in the morning, had a diagnosis by noon and it was ready by closing time. Incredible.',        name: 'Ahmed K.', role: 'Individual Customer' },
                            { quote: 'The real-time tracking and consolidated invoicing save us hours every month. Highly recommended.',              name: 'Lisa T.',  role: 'Procurement Manager, RetailChain AG' },
                        ].map(({ quote, name, role }) => (
                            <blockquote key={name} className="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm">
                                <div className="mb-4 flex">
                                    {[...Array(5)].map((_, i) => (
                                        <IconStar key={i} className="h-4 w-4 text-orange-400" />
                                    ))}
                                </div>
                                <p className="text-sm leading-relaxed text-zinc-600">"{quote}"</p>
                                <footer className="mt-4">
                                    <p className="text-sm font-semibold text-zinc-900">{name}</p>
                                    <p className="text-xs text-zinc-400">{role}</p>
                                </footer>
                            </blockquote>
                        ))}
                    </div>
                </div>
            </section>

            {/* ── Footer ────────────────────────────────────────────────────── */}
            <footer className="border-t border-zinc-800 bg-zinc-900">
                <div className="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                    <div className="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <div className="flex items-center gap-2.5">
                                <span className="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-600">
                                    <IconWrench className="h-4 w-4 text-white" />
                                </span>
                                <span className="font-display text-xl font-normal text-white">Moon<span className="text-orange-400">.Repair</span></span>
                            </div>
                            <p className="mt-4 text-sm leading-relaxed text-zinc-400">
                                Professional device repair at moon.repair — trusted by individuals and businesses. Certified technicians, genuine parts.
                            </p>
                        </div>

                        <div>
                            <h3 className="mb-4 text-xs font-semibold uppercase tracking-widest text-zinc-400">Services</h3>
                            <ul className="space-y-2 text-sm text-zinc-500">
                                {['Smartphone Repair', 'Laptop Repair', 'Tablet Repair', 'Data Recovery', 'Warranty Service'].map((s) => (
                                    <li key={s}><a href="#services" className="transition hover:text-zinc-300">{s}</a></li>
                                ))}
                            </ul>
                        </div>

                        <div>
                            <h3 className="mb-4 text-xs font-semibold uppercase tracking-widest text-zinc-400">Company</h3>
                            <ul className="space-y-2 text-sm text-zinc-500">
                                {['About Us', 'Partner Programme', 'Careers', 'Privacy Policy', 'Terms of Service'].map((s) => (
                                    <li key={s}><a href="#" className="transition hover:text-zinc-300">{s}</a></li>
                                ))}
                            </ul>
                        </div>

                        <div>
                            <h3 className="mb-4 text-xs font-semibold uppercase tracking-widest text-zinc-400">Contact</h3>
                            <ul className="space-y-2 text-sm text-zinc-500">
                                <li><a href="mailto:hello@moon.repair"    className="transition hover:text-zinc-300">hello@moon.repair</a></li>
                                <li><a href="mailto:partners@moon.repair" className="transition hover:text-zinc-300">partners@moon.repair</a></li>
                            </ul>
                            <div className="mt-6">
                                <a
                                    href="#login"
                                    className="inline-flex cursor-pointer items-center gap-2 rounded-lg bg-orange-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-orange-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400"
                                >
                                    Sign in
                                    <IconArrowRight className="h-3.5 w-3.5" />
                                </a>
                            </div>
                        </div>
                    </div>

                    <div className="mt-10 flex flex-col items-center justify-between gap-4 border-t border-zinc-800 pt-8 sm:flex-row">
                        <p className="text-xs text-zinc-500">© {new Date().getFullYear()} Moon.Repair · moon.repair · All rights reserved.</p>
                        <p className="text-xs text-zinc-600">Built with Laravel · Inertia.js · React</p>
                    </div>
                </div>
            </footer>
        </>
    );
}
