interface Props {
    title: string;
    subtitle?: string;
    button_label?: string;
    button_url?: string;
    button_secondary_label?: string;
    button_secondary_url?: string;
    theme?: 'dark' | 'light' | 'orange';
}

export default function CtaBanner({
    title, subtitle,
    button_label, button_url,
    button_secondary_label, button_secondary_url,
    theme = 'dark',
}: Props) {
    const bg = theme === 'orange' ? 'bg-orange-600' : theme === 'dark' ? 'bg-zinc-900' : 'bg-white';
    const headingColor = theme === 'light' ? 'text-zinc-900' : 'text-white';
    const subtitleColor = theme === 'orange' ? 'text-orange-100' : theme === 'dark' ? 'text-zinc-400' : 'text-zinc-500';

    return (
        <section className={`relative overflow-hidden py-16 sm:py-24 ${bg}`}>
            {theme === 'dark' && (
                <div className="pointer-events-none absolute -top-32 right-0 h-[400px] w-[400px] rounded-full bg-orange-600/10 blur-3xl" />
            )}

            <div className="relative mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8">
                <h2 className={`font-display text-3xl font-normal sm:text-4xl ${headingColor}`}
                    dangerouslySetInnerHTML={{ __html: title }} />

                {subtitle && (
                    <p className={`mx-auto mt-4 max-w-xl text-lg leading-relaxed ${subtitleColor}`}>{subtitle}</p>
                )}

                {(button_label || button_secondary_label) && (
                    <div className="mt-10 flex flex-wrap items-center justify-center gap-4">
                        {button_label && button_url && (
                            <a
                                href={button_url}
                                className={`inline-flex items-center gap-2 rounded-lg px-6 py-3 text-sm font-semibold transition active:scale-[0.98] ${
                                    theme === 'orange'
                                        ? 'bg-white text-orange-600 hover:bg-orange-50'
                                        : 'bg-orange-600 text-white hover:bg-orange-500'
                                }`}
                            >
                                {button_label}
                                <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth={2} stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                </svg>
                            </a>
                        )}
                        {button_secondary_label && button_secondary_url && (
                            <a
                                href={button_secondary_url}
                                className={`inline-flex items-center gap-2 rounded-lg border px-6 py-3 text-sm font-medium transition ${
                                    theme === 'light'
                                        ? 'border-zinc-200 text-zinc-600 hover:border-zinc-300 hover:text-zinc-900'
                                        : 'border-white/20 text-white/80 hover:border-white/40 hover:text-white'
                                }`}
                            >
                                {button_secondary_label}
                            </a>
                        )}
                    </div>
                )}
            </div>
        </section>
    );
}
