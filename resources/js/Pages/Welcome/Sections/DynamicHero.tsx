interface HeroStat { value?: string; label?: string }

interface HeroSettings {
    variant?: 'centered' | 'split' | 'bg_image' | 'minimal' | 'with_stats';
    image?: string | null;
    overlay?: number | string;
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
    stats?: HeroStat[];
}

export default function DynamicHero({ settings }: { settings: HeroSettings }) {
    switch (settings.variant) {
        case 'split':      return <SplitHero      settings={settings} />;
        case 'bg_image':   return <BgImageHero    settings={settings} />;
        case 'minimal':    return <MinimalHero    settings={settings} />;
        case 'with_stats': return <WithStatsHero  settings={settings} />;
        case 'centered':
        default:           return <CenteredHero   settings={settings} />;
    }
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

                {settings.image ? (
                    <img
                        className="tb-hero-split-image"
                        src={`/storage/${settings.image}`}
                        alt={settings.headline ?? ''}
                        style={{
                            width: '100%',
                            height: 'auto',
                            display: 'block',
                            maxWidth: '100%',
                            // No card chrome — image sits directly on the section background.
                        }}
                    />
                ) : (
                    <div
                        className="tb-hero-split-image"
                        style={{
                            position: 'relative',
                            aspectRatio: '4 / 3',
                            borderRadius: 18,
                            overflow: 'hidden',
                            background: 'linear-gradient(135deg, rgba(0,0,0,0.06) 0%, rgba(0,0,0,0.02) 100%)',
                            boxShadow: '0 18px 40px -16px rgba(15,23,42,0.25)',
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                            color: 'currentColor',
                            opacity: 0.35,
                        }}
                    >
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.4">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5z" />
                        </svg>
                    </div>
                )}
            </div>

            <style>{`
                @media (max-width: 760px) {
                    .tb-hero-split {
                        grid-template-columns: 1fr !important;
                    }
                }
            `}</style>
        </section>
    );
}

// ── Variant: bg_image (full-bleed background photo + overlay) ──────────────

function BgImageHero({ settings }: { settings: HeroSettings }) {
    const overlay = Math.max(0, Math.min(1, Number(settings.overlay) || 0));
    const fg = settings.fg && settings.fg !== '#000000' ? settings.fg : '#ffffff';

    return (
        <section
            style={{
                position: 'relative',
                background: settings.bg ?? '#0f172a',
                color: fg,
                overflow: 'hidden',
                fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
            }}
        >
            {settings.image && (
                <img
                    src={`/storage/${settings.image}`}
                    alt=""
                    aria-hidden="true"
                    style={{
                        position: 'absolute',
                        inset: 0,
                        width: '100%',
                        height: '100%',
                        objectFit: 'cover',
                        display: 'block',
                    }}
                />
            )}
            {/* Dark overlay for legibility */}
            <div style={{
                position: 'absolute',
                inset: 0,
                background: `linear-gradient(180deg, rgba(0,0,0,${overlay * 0.85}) 0%, rgba(0,0,0,${overlay}) 100%)`,
            }} />
            <div style={{
                position: 'relative',
                maxWidth: '900px',
                margin: '0 auto',
                padding: 'clamp(80px, 14vw, 180px) clamp(16px, 4vw, 32px)',
                textAlign: 'center',
                display: 'flex',
                flexDirection: 'column',
                alignItems: 'center',
                gap: '20px',
            }}>
                {settings.eyebrow && (
                    <span style={{ ...eyebrowStyle, color: fg, opacity: 0.85 }}>{settings.eyebrow}</span>
                )}
                <h1 style={{ ...headlineStyle, color: fg, maxWidth: '24ch' }}>
                    {settings.headline ?? 'A great big headline goes here'}
                </h1>
                {settings.subtitle && (
                    <p style={{ ...subtitleStyle, color: fg, opacity: 0.9 }}>{settings.subtitle}</p>
                )}
                {settings.buttonText && (
                    <a href={settings.buttonHref || '#'} style={ctaStyle(settings)}>{settings.buttonText}</a>
                )}
            </div>
        </section>
    );
}

// ── Variant: minimal (compact left-aligned headline + button) ──────────────

function MinimalHero({ settings }: { settings: HeroSettings }) {
    return (
        <section
            style={{
                background: settings.bg ?? '#ffffff',
                color: settings.fg ?? '#000000',
                padding: 'clamp(40px, 7vw, 80px) clamp(16px, 4vw, 32px)',
                fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
            }}
        >
            <div style={{ maxWidth: '900px', margin: '0 auto', display: 'flex', flexDirection: 'column', alignItems: 'flex-start', gap: 14 }}>
                <h1 style={{
                    fontSize: 'clamp(1.75rem, 4.5vw, 3rem)',
                    fontWeight: 700,
                    letterSpacing: '-0.025em',
                    lineHeight: 1.15,
                    margin: 0,
                    maxWidth: '24ch',
                }}>
                    {settings.headline ?? 'A great big headline goes here'}
                </h1>
                {settings.buttonText && (
                    <a href={settings.buttonHref || '#'} style={ctaStyle(settings)}>{settings.buttonText}</a>
                )}
            </div>
        </section>
    );
}

// ── Variant: with_stats (centered + a small row of stats) ──────────────────

function WithStatsHero({ settings }: { settings: HeroSettings }) {
    const stats = (settings.stats ?? []).filter((s) => s.value || s.label);

    return (
        <section
            style={{
                background: settings.bg ?? '#ffffff',
                color: settings.fg ?? '#000000',
                padding: 'clamp(48px, 10vw, 120px) clamp(16px, 4vw, 32px)',
                fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
            }}
        >
            <div style={{
                maxWidth: '900px',
                margin: '0 auto',
                textAlign: 'center',
                display: 'flex',
                flexDirection: 'column',
                alignItems: 'center',
                gap: 20,
            }}>
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

                {stats.length > 0 && (
                    <div style={{
                        marginTop: 28,
                        paddingTop: 24,
                        borderTop: '1px solid color-mix(in srgb, currentColor 12%, transparent)',
                        display: 'flex',
                        flexWrap: 'wrap',
                        justifyContent: 'center',
                        gap: 'clamp(24px, 4vw, 48px)',
                        rowGap: 18,
                    }}>
                        {stats.map((s, i) => (
                            <div key={i} style={{ textAlign: 'center', minWidth: 80 }}>
                                <div style={{
                                    fontSize: 'clamp(1.25rem, 2.4vw, 1.75rem)',
                                    fontWeight: 800,
                                    letterSpacing: '-0.02em',
                                    lineHeight: 1,
                                }}>
                                    {s.value}
                                </div>
                                <div style={{
                                    marginTop: 4,
                                    fontSize: '0.8125rem',
                                    opacity: 0.65,
                                    textTransform: 'uppercase',
                                    letterSpacing: '0.08em',
                                    fontWeight: 600,
                                }}>
                                    {s.label}
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>
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
