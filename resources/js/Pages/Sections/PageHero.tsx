interface Props {
    badge?: string;
    title: string;
    subtitle?: string;
    cta_label?: string;
    cta_url?: string;
    cta_secondary_label?: string;
    cta_secondary_url?: string;
    theme?: 'dark' | 'light';
}

export default function PageHero({
    badge, title, subtitle,
    cta_label, cta_url,
    cta_secondary_label, cta_secondary_url,
    theme = 'dark',
}: Props) {
    const isDark = theme === 'dark';

    return (
        <section className={`relative overflow-hidden py-24 sm:py-32 ${isDark ? 'bg-zinc-900' : 'bg-white'}`}>
            {isDark && (
                <>
                    <div className="pointer-events-none absolute inset-0 opacity-[0.03]"
                        style={{
                            backgroundImage: 'linear-gradient(#94a3b8 1px, transparent 1px), linear-gradient(to right, #94a3b8 1px, transparent 1px)',
                            backgroundSize: '48px 48px',
                        }}
                    />
                    <div className="pointer-events-none absolute -top-40 -right-40 h-[500px] w-[500px] rounded-full bg-orange-600/10 blur-3xl" />
                    <div className="pointer-events-none absolute -bottom-40 -left-40 h-[400px] w-[400px] rounded-full bg-orange-600/10 blur-3xl" />
                </>
            )}

            <div className="relative mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8">
                {badge && (
                    <div className="mb-6 inline-flex items-center gap-2 rounded-full border border-orange-500/20 bg-orange-500/10 px-4 py-1.5">
                        <span className="h-1.5 w-1.5 animate-pulse rounded-full bg-orange-400" />
                        <span className="text-xs font-medium text-orange-300">{badge}</span>
                    </div>
                )}

                <h1
                    className={`font-display text-4xl font-normal leading-tight sm:text-5xl lg:text-6xl ${isDark ? 'text-white' : 'text-zinc-900'}`}
                    dangerouslySetInnerHTML={{ __html: title }}
                />

                {subtitle && (
                    <p className={`mx-auto mt-6 max-w-2xl text-lg leading-relaxed ${isDark ? 'text-zinc-400' : 'text-zinc-500'}`}>
                        {subtitle}
                    </p>
                )}

                {(cta_label || cta_secondary_label) && (
                    <div className="mt-10 flex flex-wrap items-center justify-center gap-4">
                        {cta_label && cta_url && (
                            <a
                                href={cta_url}
                                className="inline-flex items-center gap-2 rounded-lg bg-orange-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-orange-500 active:scale-[0.98]"
                            >
                                {cta_label}
                                <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth={2} stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                </svg>
                            </a>
                        )}
                        {cta_secondary_label && cta_secondary_url && (
                            <a
                                href={cta_secondary_url}
                                className={`inline-flex items-center gap-2 rounded-lg border px-6 py-3 text-sm font-medium transition ${
                                    isDark
                                        ? 'border-white/10 text-zinc-300 hover:border-white/20 hover:text-white'
                                        : 'border-zinc-200 text-zinc-600 hover:border-zinc-300 hover:text-zinc-900'
                                }`}
                            >
                                {cta_secondary_label}
                            </a>
                        )}
                    </div>
                )}
            </div>
        </section>
    );
}
