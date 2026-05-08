interface CtaSettings {
    variant?: 'centered' | 'split' | 'gradient' | 'boxed' | 'inset';
    heading?: string;
    subtitle?: string;
    primaryText?: string;
    primaryHref?: string;
    secondaryText?: string;
    secondaryHref?: string;
    /** Legacy field — old rows used align='center' / 'split'. Falls through to variant. */
    align?: 'center' | 'split';
    bg?: string;
    fg?: string;
    mutedFg?: string;
    primaryBtnBg?: string;
    primaryBtnFg?: string;
    secondaryBtnBg?: string;
    secondaryBtnFg?: string;
    // Gradient variant
    gradientFrom?: string;
    gradientTo?: string;
    gradientAngle?: number | string;
}

// ── Dispatcher ──────────────────────────────────────────────────────────────

export default function DynamicCta({ settings }: { settings: CtaSettings }) {
    // Back-compat: existing saved sections used the `align` field for layout.
    const variant = settings.variant ?? (settings.align === 'split' ? 'split' : 'centered');
    switch (variant) {
        case 'split':    return <SplitCta    settings={settings} />;
        case 'gradient': return <GradientCta settings={settings} />;
        case 'boxed':    return <BoxedCta    settings={settings} />;
        case 'inset':    return <InsetCta    settings={settings} />;
        case 'centered':
        default:         return <CenteredCta settings={settings} />;
    }
}

// ── Helpers / shared bits ──────────────────────────────────────────────────

function useDerived(settings: CtaSettings) {
    const fg      = settings.fg ?? '#ffffff';
    const mutedFg = settings.mutedFg ?? '#cbd5e1';
    const primary = (settings.primaryText ?? '').trim();
    const secondary = (settings.secondaryText ?? '').trim();
    const primaryBtnBg = settings.primaryBtnBg ?? '#ffffff';
    const primaryBtnFg = settings.primaryBtnFg ?? '#0f172a';
    const secondaryBtnBg = settings.secondaryBtnBg ?? 'transparent';
    const secondaryBtnFg = settings.secondaryBtnFg ?? '#ffffff';
    const secondaryIsGhost = !secondaryBtnBg || secondaryBtnBg === 'transparent';

    return { fg, mutedFg, primary, secondary, primaryBtnBg, primaryBtnFg, secondaryBtnBg, secondaryBtnFg, secondaryIsGhost };
}

function Buttons({
    settings,
    align,
}: {
    settings: CtaSettings;
    align: 'center' | 'flex-start' | 'flex-end';
}) {
    const d = useDerived(settings);
    if (!d.primary && !d.secondary) return null;
    return (
        <div
            className="tb-cta-buttons"
            style={{
                display: 'inline-flex',
                flexWrap: 'wrap',
                gap: 12,
                justifyContent: align,
            }}
        >
            {d.primary && (
                <a href={settings.primaryHref || '#'} style={{
                    display: 'inline-flex', alignItems: 'center', gap: 6,
                    background: d.primaryBtnBg, color: d.primaryBtnFg,
                    padding: '14px 24px', borderRadius: 10,
                    fontWeight: 600, fontSize: '0.9375rem', textDecoration: 'none',
                    boxShadow: '0 1px 2px rgba(0,0,0,0.10)',
                    transition: 'transform .12s ease',
                }}>{d.primary}</a>
            )}
            {d.secondary && (
                <a href={settings.secondaryHref || '#'} style={{
                    display: 'inline-flex', alignItems: 'center', gap: 6,
                    background: d.secondaryBtnBg, color: d.secondaryBtnFg,
                    padding: '14px 24px', borderRadius: 10,
                    fontWeight: 600, fontSize: '0.9375rem', textDecoration: 'none',
                    border: d.secondaryIsGhost
                        ? `1px solid color-mix(in srgb, ${d.secondaryBtnFg} 30%, transparent)`
                        : 'none',
                }}>{d.secondary}</a>
            )}
        </div>
    );
}

// ── Variant: centered ──────────────────────────────────────────────────────

function CenteredCta({ settings }: { settings: CtaSettings }) {
    const d = useDerived(settings);
    return (
        <section style={{
            background: settings.bg ?? '#0f172a',
            color: d.fg,
            padding: 'clamp(48px, 8vw, 88px) clamp(16px, 4vw, 32px)',
            fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
        }}>
            <div style={{
                maxWidth: '1100px', margin: '0 auto',
                textAlign: 'center', display: 'flex',
                flexDirection: 'column', alignItems: 'center', gap: 16,
            }}>
                {settings.heading && <Heading text={settings.heading} centered />}
                {settings.subtitle && <Subtitle text={settings.subtitle} mutedFg={d.mutedFg} />}
                <div style={{ marginTop: 8 }}><Buttons settings={settings} align="center" /></div>
            </div>
        </section>
    );
}

// ── Variant: split (heading left, buttons right) ───────────────────────────

function SplitCta({ settings }: { settings: CtaSettings }) {
    const d = useDerived(settings);
    return (
        <section style={{
            background: settings.bg ?? '#0f172a',
            color: d.fg,
            padding: 'clamp(48px, 8vw, 88px) clamp(16px, 4vw, 32px)',
            fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
        }}>
            <div className="tb-cta-split" style={{
                maxWidth: '1100px', margin: '0 auto',
                display: 'grid',
                gridTemplateColumns: '1.4fr 1fr',
                alignItems: 'center',
                gap: 'clamp(24px, 4vw, 48px)',
            }}>
                <div>
                    {settings.heading && <Heading text={settings.heading} />}
                    {settings.subtitle && <Subtitle text={settings.subtitle} mutedFg={d.mutedFg} />}
                </div>
                <div style={{ textAlign: 'right' }}>
                    <Buttons settings={settings} align="flex-end" />
                </div>
            </div>
            <style>{`
                @media (max-width: 720px) {
                    .tb-cta-split { grid-template-columns: 1fr !important; text-align: center; }
                    .tb-cta-split > div:last-child { text-align: center !important; }
                    .tb-cta-split .tb-cta-buttons { justify-content: center !important; }
                }
            `}</style>
        </section>
    );
}

// ── Variant: gradient (vivid primary-driven gradient bg) ───────────────────

function GradientCta({ settings }: { settings: CtaSettings }) {
    const d = useDerived(settings);
    // Force readable defaults if user is still on dark-on-dark from the centered preset.
    const fg = settings.fg && settings.fg !== '#000000' ? settings.fg : '#ffffff';
    const mutedFg = settings.mutedFg && settings.mutedFg !== '#cbd5e1' ? settings.mutedFg : 'rgba(255,255,255,0.85)';

    // Honor the user-picked gradient when set; otherwise auto-derive from
    // var(--primary) so it tracks the brand color.
    const angle = Math.max(0, Math.min(360, Number(settings.gradientAngle) || 135));
    const fromColor = settings.gradientFrom?.trim();
    const toColor   = settings.gradientTo?.trim();
    const gradient = (fromColor && toColor)
        ? `linear-gradient(${angle}deg, ${fromColor} 0%, ${toColor} 100%)`
        : `linear-gradient(${angle}deg, var(--primary, #0284c7) 0%, color-mix(in srgb, var(--primary, #0284c7) 60%, black) 100%)`;

    return (
        <section style={{
            position: 'relative',
            color: fg,
            padding: 'clamp(56px, 9vw, 104px) clamp(16px, 4vw, 32px)',
            fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
            background: gradient,
            overflow: 'hidden',
        }}>
            {/* Soft corner glow for depth */}
            <div aria-hidden="true" style={{
                position: 'absolute',
                top: '-120px',
                right: '-120px',
                width: 360,
                height: 360,
                borderRadius: '50%',
                background: 'rgba(255,255,255,0.18)',
                filter: 'blur(60px)',
                pointerEvents: 'none',
            }} />
            <div style={{
                position: 'relative',
                maxWidth: '1100px', margin: '0 auto',
                textAlign: 'center',
                display: 'flex', flexDirection: 'column', alignItems: 'center', gap: 18,
            }}>
                {settings.heading && <Heading text={settings.heading} centered />}
                {settings.subtitle && <Subtitle text={settings.subtitle} mutedFg={mutedFg} />}
                <div style={{ marginTop: 8 }}><Buttons settings={settings} align="center" /></div>
            </div>
        </section>
    );
}

// ── Variant: boxed (tinted section + elevated card) ────────────────────────

function BoxedCta({ settings }: { settings: CtaSettings }) {
    const d = useDerived(settings);
    return (
        <section style={{
            background: '#f8fafc',
            color: '#0f172a',
            padding: 'clamp(48px, 8vw, 88px) clamp(16px, 4vw, 32px)',
            fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
        }}>
            <div style={{
                maxWidth: '1000px', margin: '0 auto',
                background: settings.bg ?? '#0f172a',
                color: d.fg,
                borderRadius: 24,
                padding: 'clamp(36px, 5vw, 64px)',
                textAlign: 'center',
                display: 'flex', flexDirection: 'column', alignItems: 'center', gap: 16,
                boxShadow: '0 4px 12px -4px rgba(15,23,42,0.16), 0 24px 48px -16px rgba(15,23,42,0.30)',
            }}>
                {settings.heading && <Heading text={settings.heading} centered />}
                {settings.subtitle && <Subtitle text={settings.subtitle} mutedFg={d.mutedFg} />}
                <div style={{ marginTop: 8 }}><Buttons settings={settings} align="center" /></div>
            </div>
        </section>
    );
}

// ── Variant: inset (calm bg, contained card) ───────────────────────────────

function InsetCta({ settings }: { settings: CtaSettings }) {
    const d = useDerived(settings);
    // Force light-on-light defaults so this reads as a calm card.
    const fg = settings.fg && settings.fg !== '#ffffff' ? settings.fg : '#0f172a';
    const mutedFg = settings.mutedFg && settings.mutedFg !== '#cbd5e1' ? settings.mutedFg : '#64748b';
    return (
        <section style={{
            background: settings.bg ?? '#ffffff',
            color: fg,
            padding: 'clamp(40px, 6vw, 72px) clamp(16px, 4vw, 32px)',
            fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
        }}>
            <div style={{
                maxWidth: '760px', margin: '0 auto',
                background: 'color-mix(in srgb, var(--primary, #0284c7) 5%, transparent)',
                border: '1px solid color-mix(in srgb, var(--primary, #0284c7) 18%, transparent)',
                borderRadius: 16,
                padding: 'clamp(24px, 3vw, 36px)',
                display: 'flex',
                flexDirection: 'column',
                gap: 14,
                alignItems: 'flex-start',
            }}>
                {settings.heading && (
                    <h2 style={{
                        margin: 0,
                        fontSize: 'clamp(1.375rem, 2.4vw, 1.75rem)',
                        fontWeight: 700,
                        letterSpacing: '-0.015em',
                        lineHeight: 1.25,
                        color: fg,
                    }}>{settings.heading}</h2>
                )}
                {settings.subtitle && (
                    <p style={{
                        margin: 0,
                        fontSize: '0.9375rem',
                        lineHeight: 1.55,
                        color: mutedFg,
                        maxWidth: '52ch',
                    }}>{settings.subtitle}</p>
                )}
                <div style={{ marginTop: 6 }}><Buttons settings={settings} align="flex-start" /></div>
            </div>
        </section>
    );
}

// ── Shared text bits ───────────────────────────────────────────────────────

function Heading({ text, centered = false }: { text: string; centered?: boolean }) {
    return (
        <h2 style={{
            fontSize: centered ? 'clamp(1.875rem, 4vw, 2.75rem)' : 'clamp(1.625rem, 3vw, 2.25rem)',
            fontWeight: 700,
            letterSpacing: '-0.025em',
            margin: 0,
            lineHeight: centered ? 1.15 : 1.2,
            maxWidth: centered ? '24ch' : undefined,
            color: 'inherit',
        }}>{text}</h2>
    );
}

function Subtitle({ text, mutedFg }: { text: string; mutedFg: string }) {
    return (
        <p style={{
            fontSize: 'clamp(1rem, 1.3vw, 1.125rem)',
            color: mutedFg,
            margin: 0,
            maxWidth: '50ch',
            lineHeight: 1.55,
        }}>{text}</p>
    );
}
