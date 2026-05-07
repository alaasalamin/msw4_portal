interface Plan {
    name?: string;
    price?: string;
    currency?: string;
    period?: string;
    description?: string;
    features?: string;
    buttonText?: string;
    buttonHref?: string;
    highlighted?: boolean;
    badge?: string;
}

interface PricingSettings {
    heading?: string;
    subtitle?: string;
    plans?: Plan[];
    columns?: number | string;
    bg?: string;
    fg?: string;
    mutedFg?: string;
    cardBg?: string;
    cardBorder?: string;
    highlightedBg?: string;
    highlightedFg?: string;
}

function parseFeatures(text?: string): string[] {
    if (!text) return [];
    return text.split(/\r?\n/).map((s) => s.trim()).filter(Boolean);
}

export default function DynamicPricing({ settings }: { settings: PricingSettings }) {
    const plans = settings.plans ?? [];
    const cols  = Math.max(1, Math.min(4, Number(settings.columns) || 3));

    const fg            = settings.fg ?? '#0f172a';
    const mutedFg       = settings.mutedFg ?? '#64748b';
    const cardBg        = settings.cardBg ?? '#ffffff';
    const cardBorder    = settings.cardBorder ?? '#e5e7eb';
    const highlightedBg = settings.highlightedBg ?? '#0f172a';
    const highlightedFg = settings.highlightedFg ?? '#ffffff';

    return (
        <section
            style={{
                background: settings.bg ?? '#ffffff',
                color: fg,
                padding: 'clamp(48px, 8vw, 96px) clamp(16px, 4vw, 32px)',
                fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
            }}
        >
            <div style={{ maxWidth: '1200px', margin: '0 auto' }}>
                {(settings.heading || settings.subtitle) && (
                    <div style={{ textAlign: 'center', marginBottom: 'clamp(32px, 5vw, 56px)' }}>
                        {settings.heading && (
                            <h2 style={{
                                fontSize: 'clamp(1.75rem, 3.5vw, 2.5rem)',
                                fontWeight: 700,
                                letterSpacing: '-0.025em',
                                margin: 0,
                                lineHeight: 1.15,
                            }}>{settings.heading}</h2>
                        )}
                        {settings.subtitle && (
                            <p style={{
                                fontSize: 'clamp(1rem, 1.3vw, 1.125rem)',
                                color: mutedFg,
                                margin: '12px auto 0',
                                maxWidth: '52ch',
                                lineHeight: 1.6,
                            }}>{settings.subtitle}</p>
                        )}
                    </div>
                )}

                {plans.length > 0 ? (
                    <div
                        className={`tb-pricing-grid tb-pricing-cols-${cols}`}
                        style={{
                            display: 'grid',
                            gap: 'clamp(20px, 2.5vw, 28px)',
                            gridTemplateColumns: `repeat(${cols}, minmax(0, 1fr))`,
                            alignItems: 'stretch',
                        }}
                    >
                        {plans.map((plan, i) => {
                            const features = parseFeatures(plan.features);
                            const hi = plan.highlighted === true;
                            const planFg      = hi ? highlightedFg : fg;
                            const planBg      = hi ? highlightedBg : cardBg;
                            const planMutedFg = hi
                                ? `color-mix(in srgb, ${highlightedFg} 75%, transparent)`
                                : mutedFg;

                            return (
                                <article
                                    key={i}
                                    style={{
                                        position: 'relative',
                                        background: planBg,
                                        color: planFg,
                                        border: hi ? 'none' : `1px solid ${cardBorder}`,
                                        borderRadius: 16,
                                        padding: 'clamp(24px, 3vw, 32px)',
                                        display: 'flex',
                                        flexDirection: 'column',
                                        gap: 18,
                                        transform: hi ? 'translateY(-4px)' : 'none',
                                        boxShadow: hi
                                            ? '0 12px 32px -12px rgba(15,23,42,0.35), 0 4px 12px -4px rgba(15,23,42,0.20)'
                                            : '0 1px 2px rgba(15,23,42,0.04)',
                                    }}
                                >
                                    {plan.badge && (
                                        <span style={{
                                            position: 'absolute',
                                            top: -12,
                                            left: '50%',
                                            transform: 'translateX(-50%)',
                                            background: 'var(--primary, #0284c7)',
                                            color: '#ffffff',
                                            fontSize: '0.6875rem',
                                            fontWeight: 700,
                                            letterSpacing: '0.08em',
                                            textTransform: 'uppercase',
                                            padding: '5px 12px',
                                            borderRadius: 9999,
                                            whiteSpace: 'nowrap',
                                            boxShadow: '0 4px 10px -2px rgba(0,0,0,0.25)',
                                        }}>
                                            {plan.badge}
                                        </span>
                                    )}

                                    <div>
                                        {plan.name && (
                                            <h3 style={{
                                                margin: 0,
                                                fontSize: '1.125rem',
                                                fontWeight: 700,
                                                letterSpacing: '-0.01em',
                                            }}>{plan.name}</h3>
                                        )}
                                        {plan.description && (
                                            <p style={{
                                                margin: '4px 0 0',
                                                fontSize: '0.875rem',
                                                color: planMutedFg,
                                                lineHeight: 1.5,
                                            }}>{plan.description}</p>
                                        )}
                                    </div>

                                    <div style={{ display: 'flex', alignItems: 'baseline', gap: 4, flexWrap: 'wrap' }}>
                                        {plan.currency && <span style={{ fontSize: '1.25rem', fontWeight: 600, opacity: 0.7 }}>{plan.currency}</span>}
                                        <span style={{ fontSize: 'clamp(2.25rem, 4vw, 3rem)', fontWeight: 800, letterSpacing: '-0.03em', lineHeight: 1 }}>
                                            {plan.price ?? ''}
                                        </span>
                                        {plan.period && (
                                            <span style={{ fontSize: '0.9375rem', color: planMutedFg, fontWeight: 500 }}>
                                                {plan.period}
                                            </span>
                                        )}
                                    </div>

                                    {features.length > 0 && (
                                        <ul style={{ listStyle: 'none', padding: 0, margin: 0, display: 'flex', flexDirection: 'column', gap: 8 }}>
                                            {features.map((feat, fi) => (
                                                <li key={fi} style={{
                                                    display: 'flex',
                                                    alignItems: 'flex-start',
                                                    gap: 10,
                                                    fontSize: '0.9375rem',
                                                    lineHeight: 1.55,
                                                    color: planMutedFg,
                                                }}>
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.4" style={{ flexShrink: 0, marginTop: 2, color: hi ? highlightedFg : 'var(--primary, #0284c7)' }}>
                                                        <path strokeLinecap="round" strokeLinejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                                    </svg>
                                                    <span>{feat}</span>
                                                </li>
                                            ))}
                                        </ul>
                                    )}

                                    {plan.buttonText && (
                                        <a
                                            href={plan.buttonHref || '#'}
                                            style={{
                                                marginTop: 'auto',
                                                display: 'inline-flex',
                                                alignItems: 'center',
                                                justifyContent: 'center',
                                                gap: 6,
                                                background: hi ? highlightedFg : 'var(--primary, #0284c7)',
                                                color: hi ? highlightedBg : '#ffffff',
                                                padding: '12px 18px',
                                                borderRadius: 10,
                                                fontWeight: 600,
                                                fontSize: '0.9375rem',
                                                textDecoration: 'none',
                                                transition: 'transform .12s ease, opacity .12s ease',
                                            }}
                                        >
                                            {plan.buttonText}
                                        </a>
                                    )}
                                </article>
                            );
                        })}
                    </div>
                ) : (
                    <p style={{ textAlign: 'center', color: mutedFg, margin: 0 }}>
                        No pricing plans yet — add some in the Theme Builder.
                    </p>
                )}
            </div>

            <style>{`
                @media (max-width: 900px) {
                    .tb-pricing-grid.tb-pricing-cols-3,
                    .tb-pricing-grid.tb-pricing-cols-4 {
                        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                    }
                }
                @media (max-width: 600px) {
                    .tb-pricing-grid {
                        grid-template-columns: 1fr !important;
                    }
                }
            `}</style>
        </section>
    );
}
