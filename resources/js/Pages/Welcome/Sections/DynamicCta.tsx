interface CtaSettings {
    heading?: string;
    subtitle?: string;
    primaryText?: string;
    primaryHref?: string;
    secondaryText?: string;
    secondaryHref?: string;
    align?: 'center' | 'split';
    bg?: string;
    fg?: string;
    mutedFg?: string;
    primaryBtnBg?: string;
    primaryBtnFg?: string;
    secondaryBtnBg?: string;
    secondaryBtnFg?: string;
}

export default function DynamicCta({ settings }: { settings: CtaSettings }) {
    const align   = settings.align ?? 'center';
    const fg      = settings.fg ?? '#ffffff';
    const mutedFg = settings.mutedFg ?? '#cbd5e1';

    const primary = (settings.primaryText ?? '').trim();
    const secondary = (settings.secondaryText ?? '').trim();
    const hasPrimary = primary.length > 0;
    const hasSecondary = secondary.length > 0;

    const primaryBtnBg = settings.primaryBtnBg ?? '#ffffff';
    const primaryBtnFg = settings.primaryBtnFg ?? '#0f172a';
    const secondaryBtnBg = settings.secondaryBtnBg ?? 'transparent';
    const secondaryBtnFg = settings.secondaryBtnFg ?? '#ffffff';
    const secondaryIsGhost = secondaryBtnBg === 'transparent' || secondaryBtnBg === '' || secondaryBtnBg === undefined;

    const buttons = (
        <div
            className="tb-cta-buttons"
            style={{
                display: 'inline-flex',
                flexWrap: 'wrap',
                gap: 12,
                justifyContent: align === 'center' ? 'center' : 'flex-end',
            }}
        >
            {hasPrimary && (
                <a
                    href={settings.primaryHref || '#'}
                    style={{
                        display: 'inline-flex',
                        alignItems: 'center',
                        gap: 6,
                        background: primaryBtnBg,
                        color: primaryBtnFg,
                        padding: '14px 24px',
                        borderRadius: 10,
                        fontWeight: 600,
                        fontSize: '0.9375rem',
                        letterSpacing: '0.005em',
                        textDecoration: 'none',
                        transition: 'transform .12s ease, opacity .12s ease',
                        boxShadow: '0 1px 2px rgba(0,0,0,0.10)',
                    }}
                >
                    {primary}
                </a>
            )}
            {hasSecondary && (
                <a
                    href={settings.secondaryHref || '#'}
                    style={{
                        display: 'inline-flex',
                        alignItems: 'center',
                        gap: 6,
                        background: secondaryBtnBg,
                        color: secondaryBtnFg,
                        padding: '14px 24px',
                        borderRadius: 10,
                        fontWeight: 600,
                        fontSize: '0.9375rem',
                        letterSpacing: '0.005em',
                        textDecoration: 'none',
                        border: secondaryIsGhost
                            ? `1px solid color-mix(in srgb, ${secondaryBtnFg} 30%, transparent)`
                            : 'none',
                        transition: 'transform .12s ease, opacity .12s ease',
                    }}
                >
                    {secondary}
                </a>
            )}
        </div>
    );

    return (
        <section
            style={{
                background: settings.bg ?? '#0f172a',
                color: fg,
                padding: 'clamp(48px, 8vw, 88px) clamp(16px, 4vw, 32px)',
                fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
            }}
        >
            <div style={{ maxWidth: '1100px', margin: '0 auto' }}>
                {align === 'split' ? (
                    <div
                        className="tb-cta-split"
                        style={{
                            display: 'grid',
                            gridTemplateColumns: '1.4fr 1fr',
                            alignItems: 'center',
                            gap: 'clamp(24px, 4vw, 48px)',
                        }}
                    >
                        <div>
                            {settings.heading && (
                                <h2 style={{
                                    fontSize: 'clamp(1.625rem, 3vw, 2.25rem)',
                                    fontWeight: 700,
                                    letterSpacing: '-0.025em',
                                    margin: 0,
                                    lineHeight: 1.2,
                                    color: fg,
                                }}>{settings.heading}</h2>
                            )}
                            {settings.subtitle && (
                                <p style={{
                                    fontSize: 'clamp(1rem, 1.3vw, 1.0625rem)',
                                    color: mutedFg,
                                    margin: '8px 0 0',
                                    maxWidth: '50ch',
                                    lineHeight: 1.55,
                                }}>{settings.subtitle}</p>
                            )}
                        </div>
                        {(hasPrimary || hasSecondary) && (
                            <div style={{ textAlign: 'right' }}>{buttons}</div>
                        )}
                    </div>
                ) : (
                    <div style={{ textAlign: 'center', display: 'flex', flexDirection: 'column', alignItems: 'center', gap: 16 }}>
                        {settings.heading && (
                            <h2 style={{
                                fontSize: 'clamp(1.875rem, 4vw, 2.75rem)',
                                fontWeight: 700,
                                letterSpacing: '-0.025em',
                                margin: 0,
                                lineHeight: 1.15,
                                maxWidth: '24ch',
                                color: fg,
                            }}>{settings.heading}</h2>
                        )}
                        {settings.subtitle && (
                            <p style={{
                                fontSize: 'clamp(1rem, 1.3vw, 1.125rem)',
                                color: mutedFg,
                                margin: 0,
                                maxWidth: '52ch',
                                lineHeight: 1.55,
                            }}>{settings.subtitle}</p>
                        )}
                        {(hasPrimary || hasSecondary) && (
                            <div style={{ marginTop: 8 }}>{buttons}</div>
                        )}
                    </div>
                )}
            </div>

            <style>{`
                @media (max-width: 720px) {
                    .tb-cta-split {
                        grid-template-columns: 1fr !important;
                        text-align: center;
                    }
                    .tb-cta-split > div:last-child { text-align: center !important; }
                    .tb-cta-split .tb-cta-buttons { justify-content: center !important; }
                }
            `}</style>
        </section>
    );
}
