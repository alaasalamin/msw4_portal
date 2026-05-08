interface HeroSettings {
    variant?: 'centered' | 'split';
    image?: string | null;
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
    const variant = settings.variant ?? 'centered';
    return variant === 'split' ? <SplitHero settings={settings} /> : <CenteredHero settings={settings} />;
}

// ── Variant: centered ───────────────────────────────────────────────────────

function CenteredHero({ settings }: { settings: HeroSettings }) {
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
                    <span style={eyebrowStyle}>{settings.eyebrow}</span>
                )}
                <h1 style={{ ...headlineStyle, maxWidth: '24ch' }}>
                    {settings.headline ?? 'A great big headline goes here'}
                </h1>
                {settings.subtitle && (
                    <p style={subtitleStyle}>{settings.subtitle}</p>
                )}
                {settings.buttonText && (
                    <a href={settings.buttonHref || '#'} style={ctaStyle(settings)}>{settings.buttonText}</a>
                )}
            </div>
        </section>
    );
}

// ── Variant: split (content left, image right) ──────────────────────────────

function SplitHero({ settings }: { settings: HeroSettings }) {
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
                className="tb-hero-split"
                style={{
                    maxWidth: '1200px',
                    margin: '0 auto',
                    display: 'grid',
                    gridTemplateColumns: '1.05fr 1fr',
                    alignItems: 'center',
                    gap: 'clamp(28px, 5vw, 56px)',
                }}
            >
                <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'flex-start', gap: 18 }}>
                    {settings.eyebrow && (
                        <span style={eyebrowStyle}>{settings.eyebrow}</span>
                    )}
                    <h1 style={{ ...headlineStyle, maxWidth: '20ch' }}>
                        {settings.headline ?? 'A great big headline goes here'}
                    </h1>
                    {settings.subtitle && (
                        <p style={{ ...subtitleStyle, maxWidth: '46ch' }}>{settings.subtitle}</p>
                    )}
                    {settings.buttonText && (
                        <a href={settings.buttonHref || '#'} style={ctaStyle(settings)}>{settings.buttonText}</a>
                    )}
                </div>

                <div
                    className="tb-hero-split-image"
                    style={{
                        position: 'relative',
                        aspectRatio: '4 / 3',
                        borderRadius: 18,
                        overflow: 'hidden',
                        background: 'linear-gradient(135deg, rgba(0,0,0,0.06) 0%, rgba(0,0,0,0.02) 100%)',
                        boxShadow: '0 18px 40px -16px rgba(15,23,42,0.25)',
                    }}
                >
                    {settings.image ? (
                        <img
                            src={`/storage/${settings.image}`}
                            alt={settings.headline ?? ''}
                            style={{
                                position: 'absolute',
                                inset: 0,
                                width: '100%',
                                height: '100%',
                                objectFit: 'cover',
                                display: 'block',
                            }}
                        />
                    ) : (
                        <div style={{
                            position: 'absolute',
                            inset: 0,
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                            color: 'currentColor',
                            opacity: 0.35,
                        }}>
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.4">
                                <path strokeLinecap="round" strokeLinejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5z" />
                            </svg>
                        </div>
                    )}
                </div>
            </div>

            <style>{`
                @media (max-width: 760px) {
                    .tb-hero-split {
                        grid-template-columns: 1fr !important;
                    }
                    .tb-hero-split-image {
                        aspect-ratio: 16 / 9 !important;
                    }
                }
            `}</style>
        </section>
    );
}

// ── Shared inline style fragments ───────────────────────────────────────────

const eyebrowStyle: React.CSSProperties = {
    fontSize: '0.75rem',
    fontWeight: 600,
    textTransform: 'uppercase',
    letterSpacing: '0.12em',
    opacity: 0.7,
};

const headlineStyle: React.CSSProperties = {
    fontSize: 'clamp(2rem, 6vw, 4rem)',
    fontWeight: 700,
    letterSpacing: '-0.025em',
    lineHeight: 1.1,
    margin: 0,
};

const subtitleStyle: React.CSSProperties = {
    fontSize: 'clamp(1rem, 1.4vw, 1.125rem)',
    lineHeight: 1.6,
    opacity: 0.8,
    margin: 0,
    maxWidth: '52ch',
};

function ctaStyle(settings: HeroSettings): React.CSSProperties {
    return {
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
    };
}
