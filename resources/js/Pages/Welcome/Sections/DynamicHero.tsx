interface HeroSettings {
    eyebrow?: string;
    headline?: string;
    subtitle?: string;
    buttonText?: string;
    buttonHref?: string;
    align?: 'left' | 'center' | 'right';
    bg?: string;
    fg?: string;
    buttonBg?: string;
    buttonFg?: string;
}

export default function DynamicHero({ settings }: { settings: HeroSettings }) {
    const align = settings.align ?? 'center';

    return (
        <section
            style={{
                background: settings.bg ?? '#ffffff',
                color: settings.fg ?? '#000000',
                padding: 'clamp(48px, 10vw, 120px) clamp(16px, 4vw, 32px)',
                fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
            }}
        >
            <div
                style={{
                    maxWidth: '900px',
                    margin: '0 auto',
                    textAlign: align,
                    display: 'flex',
                    flexDirection: 'column',
                    alignItems: align === 'center' ? 'center' : align === 'right' ? 'flex-end' : 'flex-start',
                    gap: '20px',
                }}
            >
                {settings.eyebrow && (
                    <span
                        style={{
                            fontSize: '0.75rem',
                            fontWeight: 600,
                            textTransform: 'uppercase',
                            letterSpacing: '0.12em',
                            opacity: 0.7,
                        }}
                    >
                        {settings.eyebrow}
                    </span>
                )}
                <h1
                    style={{
                        fontSize: 'clamp(2rem, 6vw, 4rem)',
                        fontWeight: 700,
                        letterSpacing: '-0.025em',
                        lineHeight: 1.1,
                        margin: 0,
                        maxWidth: '24ch',
                    }}
                >
                    {settings.headline ?? 'A great big headline goes here'}
                </h1>
                {settings.subtitle && (
                    <p
                        style={{
                            fontSize: 'clamp(1rem, 1.4vw, 1.125rem)',
                            lineHeight: 1.6,
                            opacity: 0.8,
                            margin: 0,
                            maxWidth: '52ch',
                        }}
                    >
                        {settings.subtitle}
                    </p>
                )}
                {settings.buttonText && (
                    <a
                        href={settings.buttonHref || '#'}
                        style={{
                            display: 'inline-flex',
                            alignItems: 'center',
                            gap: '6px',
                            background: settings.buttonBg ?? '#000000',
                            color: settings.buttonFg ?? '#ffffff',
                            padding: '12px 24px',
                            borderRadius: '8px',
                            fontWeight: 600,
                            fontSize: '0.9375rem',
                            textDecoration: 'none',
                            marginTop: '8px',
                            transition: 'transform .12s ease, opacity .12s ease',
                        }}
                    >
                        {settings.buttonText}
                    </a>
                )}
            </div>
        </section>
    );
}
