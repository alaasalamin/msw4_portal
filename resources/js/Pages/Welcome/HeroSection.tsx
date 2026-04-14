import { Link } from '@inertiajs/react';
import { IconArrowRight, IconCheck, IconStar } from './icons';
import { HomepageContent } from './types';
import LoginWidget from './LoginWidget';
import LoggedInCard from './LoggedInCard';

interface Props {
    hero: HomepageContent['hero'];
    canRegister: boolean;
    canResetPassword: boolean;
    portalLink: { href: string; label: string } | null;
    auth: { customer?: { name: string }; partner?: { name: string }; employee?: { name: string } };
}

export default function HeroSection({ hero, canRegister, canResetPassword, portalLink, auth }: Props) {
    return (
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
                    {/* Left column */}
                    <div>
                        <div className="mb-6 inline-flex items-center gap-2 rounded-full border border-orange-500/20 bg-orange-500/10 px-4 py-1.5">
                            <span className="h-1.5 w-1.5 animate-pulse rounded-full bg-orange-400" />
                            <span className="text-xs font-medium text-orange-300">{hero.badge}</span>
                        </div>

                        <h1
                            className="font-display text-4xl font-normal leading-tight text-white sm:text-5xl lg:text-6xl"
                            dangerouslySetInnerHTML={{ __html: hero.title }}
                        />

                        <p className="mt-6 text-lg leading-relaxed text-zinc-400">{hero.subtitle}</p>

                        <ul className="mt-8 space-y-3">
                            {hero.bullets.map((item) => (
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
                                <strong className="text-white">{hero.rating}</strong> — {hero.repairsCount}
                            </span>
                        </div>
                    </div>

                    {/* Right column — login / dashboard card */}
                    <div id="login" className="lg:pl-8">
                        {portalLink ? (
                            <LoggedInCard
                                name={(auth.customer ?? auth.partner ?? auth.employee)!.name}
                                portalLink={portalLink}
                                portalType={auth.customer ? 'customer' : auth.partner ? 'partner' : 'employee'}
                            />
                        ) : (
                            <>
                                <LoginWidget canResetPassword={canResetPassword} />
                                {canRegister && (
                                    <p className="mt-4 text-center text-sm text-zinc-500">
                                        Noch kein Konto?{' '}
                                        <Link href={route('customer.register')} className="font-medium text-orange-400 hover:text-orange-300 focus:outline-none focus-visible:underline">
                                            Jetzt registrieren
                                        </Link>
                                        {' '}·{' '}
                                        <Link href={route('partner.register')} className="font-medium text-zinc-400 hover:text-zinc-300 focus:outline-none focus-visible:underline">
                                            Partner werden
                                        </Link>
                                    </p>
                                )}
                            </>
                        )}
                    </div>
                </div>
            </div>
        </section>
    );
}
