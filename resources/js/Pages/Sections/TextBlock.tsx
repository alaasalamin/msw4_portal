interface Props {
    heading?: string;
    body: string;
    align?: 'left' | 'center';
    theme?: 'light' | 'dark' | 'muted';
}

export default function TextBlock({ heading, body, align = 'left', theme = 'light' }: Props) {
    const bg = theme === 'dark' ? 'bg-zinc-900' : theme === 'muted' ? 'bg-zinc-50' : 'bg-white';
    const headingColor = theme === 'dark' ? 'text-white' : 'text-zinc-900';
    const bodyColor = theme === 'dark' ? 'text-zinc-400' : 'text-zinc-600';
    const alignClass = align === 'center' ? 'text-center mx-auto' : '';

    return (
        <section className={`py-16 sm:py-20 ${bg}`}>
            <div className="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <div className={`prose prose-zinc max-w-none ${alignClass} ${theme === 'dark' ? 'prose-invert' : ''}`}>
                    {heading && (
                        <h2 className={`font-display text-3xl font-normal sm:text-4xl ${headingColor} ${alignClass}`}>
                            {heading}
                        </h2>
                    )}
                    <div
                        className={`mt-6 text-base leading-relaxed ${bodyColor}`}
                        dangerouslySetInnerHTML={{ __html: body }}
                    />
                </div>
            </div>
        </section>
    );
}
