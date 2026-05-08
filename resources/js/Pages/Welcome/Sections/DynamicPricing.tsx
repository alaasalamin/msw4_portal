import { useState } from 'react';

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
    variant?: 'cards' | 'table' | 'tabs' | 'minimal';
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

// ── Dispatcher ──────────────────────────────────────────────────────────────

export default function DynamicPricing({ settings }: { settings: PricingSettings }) {
    switch (settings.variant) {
        case 'table':   return <TableView   settings={settings} />;
        case 'tabs':    return <TabsView    settings={settings} />;
        case 'minimal': return <MinimalView settings={settings} />;
        case 'cards':
        default:        return <CardsView   settings={settings} />;
    }
}

// ── Shared chrome ───────────────────────────────────────────────────────────

function SectionWrap({ settings, children }: { settings: PricingSettings; children: React.ReactNode }) {
    return (
        <section
            style={{
                background: settings.bg ?? '#ffffff',
                color: settings.fg ?? '#0f172a',
                padding: 'clamp(48px, 8vw, 96px) clamp(16px, 4vw, 32px)',
                fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
            }}
        >
            <div style={{ maxWidth: '1200px', margin: '0 auto' }}>{children}</div>
        </section>
    );
}

function SectionHeader({ heading, subtitle, mutedFg }: { heading?: string; subtitle?: string; mutedFg: string }) {
    if (!heading && !subtitle) return null;
    return (
        <div style={{ textAlign: 'center', marginBottom: 'clamp(32px, 5vw, 56px)' }}>
            {heading && (
                <h2 style={{
                    fontSize: 'clamp(1.75rem, 3.5vw, 2.5rem)',
                    fontWeight: 700,
                    letterSpacing: '-0.025em',
                    margin: 0,
                    lineHeight: 1.15,
                }}>{heading}</h2>
            )}
            {subtitle && (
                <p style={{
                    fontSize: 'clamp(1rem, 1.3vw, 1.125rem)',
                    color: mutedFg,
                    margin: '12px auto 0',
                    maxWidth: '52ch',
                    lineHeight: 1.6,
                }}>{subtitle}</p>
            )}
        </div>
    );
}

// ── Variant: cards (existing default) ──────────────────────────────────────

function CardsView({ settings }: { settings: PricingSettings }) {
    const plans = settings.plans ?? [];
    const cols  = Math.max(1, Math.min(4, Number(settings.columns) || 3));

    const fg            = settings.fg ?? '#0f172a';
    const mutedFg       = settings.mutedFg ?? '#64748b';
    const cardBg        = settings.cardBg ?? '#ffffff';
    const cardBorder    = settings.cardBorder ?? '#e5e7eb';
    const highlightedBg = settings.highlightedBg ?? '#0f172a';
    const highlightedFg = settings.highlightedFg ?? '#ffffff';

    return (
        <SectionWrap settings={settings}>
            <SectionHeader heading={settings.heading} subtitle={settings.subtitle} mutedFg={mutedFg} />
            {plans.length === 0 ? (
                <p style={{ textAlign: 'center', color: mutedFg, margin: 0 }}>No pricing plans yet — add some in the Website Builder.</p>
            ) : (
                <div className={`tb-pricing-grid tb-pricing-cols-${cols}`} style={{
                    display: 'grid',
                    gap: 'clamp(20px, 2.5vw, 28px)',
                    gridTemplateColumns: `repeat(${cols}, minmax(0, 1fr))`,
                    alignItems: 'stretch',
                }}>
                    {plans.map((plan, i) => {
                        const features = parseFeatures(plan.features);
                        const hi = plan.highlighted === true;
                        const planFg      = hi ? highlightedFg : fg;
                        const planBg      = hi ? highlightedBg : cardBg;
                        const planMutedFg = hi
                            ? `color-mix(in srgb, ${highlightedFg} 75%, transparent)`
                            : mutedFg;

                        return (
                            <article key={i} style={{
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
                            }}>
                                {plan.badge && (
                                    <span style={badgeStyle}>{plan.badge}</span>
                                )}

                                <div>
                                    {plan.name && (
                                        <h3 style={{ margin: 0, fontSize: '1.125rem', fontWeight: 700, letterSpacing: '-0.01em' }}>{plan.name}</h3>
                                    )}
                                    {plan.description && (
                                        <p style={{ margin: '4px 0 0', fontSize: '0.875rem', color: planMutedFg, lineHeight: 1.5 }}>{plan.description}</p>
                                    )}
                                </div>

                                <PriceDisplay plan={plan} mutedFg={planMutedFg} />

                                {features.length > 0 && (
                                    <ul style={{ listStyle: 'none', padding: 0, margin: 0, display: 'flex', flexDirection: 'column', gap: 8 }}>
                                        {features.map((feat, fi) => (
                                            <li key={fi} style={{ display: 'flex', alignItems: 'flex-start', gap: 10, fontSize: '0.9375rem', lineHeight: 1.55, color: planMutedFg }}>
                                                <CheckIcon color={hi ? highlightedFg : 'var(--primary, #0284c7)'} />
                                                <span>{feat}</span>
                                            </li>
                                        ))}
                                    </ul>
                                )}

                                {plan.buttonText && (
                                    <a href={plan.buttonHref || '#'} style={{
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
                                    }}>{plan.buttonText}</a>
                                )}
                            </article>
                        );
                    })}
                </div>
            )}
            <style>{`
                @media (max-width: 900px) {
                    .tb-pricing-grid.tb-pricing-cols-3,
                    .tb-pricing-grid.tb-pricing-cols-4 { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
                }
                @media (max-width: 600px) {
                    .tb-pricing-grid { grid-template-columns: 1fr !important; }
                }
            `}</style>
        </SectionWrap>
    );
}

// ── Variant: table (plans as columns) ──────────────────────────────────────

function TableView({ settings }: { settings: PricingSettings }) {
    const plans = settings.plans ?? [];
    const fg = settings.fg ?? '#0f172a';
    const mutedFg = settings.mutedFg ?? '#64748b';
    const cardBorder = settings.cardBorder ?? '#e5e7eb';
    const highlightedBg = settings.highlightedBg ?? '#0f172a';
    const highlightedFg = settings.highlightedFg ?? '#ffffff';

    return (
        <SectionWrap settings={settings}>
            <SectionHeader heading={settings.heading} subtitle={settings.subtitle} mutedFg={mutedFg} />
            {plans.length === 0 ? (
                <p style={{ textAlign: 'center', color: mutedFg, margin: 0 }}>No pricing plans yet — add some in the Website Builder.</p>
            ) : (
                <div style={{ overflowX: 'auto', border: `1px solid ${cardBorder}`, borderRadius: 16 }}>
                    <table style={{
                        width: '100%',
                        borderCollapse: 'collapse',
                        minWidth: `${plans.length * 220}px`,
                    }}>
                        <thead>
                            <tr>
                                {plans.map((plan, i) => {
                                    const hi = plan.highlighted === true;
                                    return (
                                        <th key={i} style={{
                                            padding: 'clamp(20px, 2.4vw, 28px) clamp(16px, 2vw, 24px)',
                                            textAlign: 'left',
                                            verticalAlign: 'top',
                                            borderRight: i === plans.length - 1 ? 'none' : `1px solid ${cardBorder}`,
                                            background: hi ? highlightedBg : 'transparent',
                                            color: hi ? highlightedFg : fg,
                                            fontWeight: 700,
                                            position: 'relative',
                                        }}>
                                            {plan.badge && <span style={{ ...badgeStyle, position: 'static', transform: 'none', display: 'inline-flex', marginBottom: 10 }}>{plan.badge}</span>}
                                            <div style={{ fontSize: '1rem', fontWeight: 700, letterSpacing: '-0.01em' }}>{plan.name}</div>
                                            {plan.description && (
                                                <div style={{ fontSize: '0.8125rem', fontWeight: 500, marginTop: 4, color: hi ? `color-mix(in srgb, ${highlightedFg} 75%, transparent)` : mutedFg }}>{plan.description}</div>
                                            )}
                                        </th>
                                    );
                                })}
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                {plans.map((plan, i) => {
                                    const hi = plan.highlighted === true;
                                    return (
                                        <td key={i} style={{
                                            padding: 'clamp(16px, 2vw, 24px)',
                                            borderTop: `1px solid ${cardBorder}`,
                                            borderRight: i === plans.length - 1 ? 'none' : `1px solid ${cardBorder}`,
                                            background: hi ? highlightedBg : 'transparent',
                                            color: hi ? highlightedFg : fg,
                                            verticalAlign: 'top',
                                        }}>
                                            <PriceDisplay plan={plan} mutedFg={hi ? `color-mix(in srgb, ${highlightedFg} 75%, transparent)` : mutedFg} />
                                        </td>
                                    );
                                })}
                            </tr>
                            <tr>
                                {plans.map((plan, i) => {
                                    const hi = plan.highlighted === true;
                                    const features = parseFeatures(plan.features);
                                    const itemColor = hi ? `color-mix(in srgb, ${highlightedFg} 80%, transparent)` : mutedFg;
                                    return (
                                        <td key={i} style={{
                                            padding: 'clamp(16px, 2vw, 24px)',
                                            borderTop: `1px solid ${cardBorder}`,
                                            borderRight: i === plans.length - 1 ? 'none' : `1px solid ${cardBorder}`,
                                            background: hi ? highlightedBg : 'transparent',
                                            color: itemColor,
                                            verticalAlign: 'top',
                                        }}>
                                            <ul style={{ listStyle: 'none', padding: 0, margin: 0, display: 'flex', flexDirection: 'column', gap: 8 }}>
                                                {features.map((feat, fi) => (
                                                    <li key={fi} style={{ display: 'flex', alignItems: 'flex-start', gap: 8, fontSize: '0.875rem', lineHeight: 1.5 }}>
                                                        <CheckIcon color={hi ? highlightedFg : 'var(--primary, #0284c7)'} size={14} />
                                                        <span>{feat}</span>
                                                    </li>
                                                ))}
                                            </ul>
                                        </td>
                                    );
                                })}
                            </tr>
                            <tr>
                                {plans.map((plan, i) => {
                                    const hi = plan.highlighted === true;
                                    return (
                                        <td key={i} style={{
                                            padding: 'clamp(16px, 2vw, 24px)',
                                            borderTop: `1px solid ${cardBorder}`,
                                            borderRight: i === plans.length - 1 ? 'none' : `1px solid ${cardBorder}`,
                                            background: hi ? highlightedBg : 'transparent',
                                        }}>
                                            {plan.buttonText && (
                                                <a href={plan.buttonHref || '#'} style={{
                                                    display: 'inline-flex',
                                                    width: '100%',
                                                    alignItems: 'center',
                                                    justifyContent: 'center',
                                                    background: hi ? highlightedFg : 'var(--primary, #0284c7)',
                                                    color: hi ? highlightedBg : '#ffffff',
                                                    padding: '10px 16px',
                                                    borderRadius: 8,
                                                    fontWeight: 600,
                                                    fontSize: '0.875rem',
                                                    textDecoration: 'none',
                                                }}>{plan.buttonText}</a>
                                            )}
                                        </td>
                                    );
                                })}
                            </tr>
                        </tbody>
                    </table>
                </div>
            )}
        </SectionWrap>
    );
}

// ── Variant: tabs (one plan visible at a time) ─────────────────────────────

function TabsView({ settings }: { settings: PricingSettings }) {
    const plans = settings.plans ?? [];
    const fg = settings.fg ?? '#0f172a';
    const mutedFg = settings.mutedFg ?? '#64748b';
    const cardBg = settings.cardBg ?? '#ffffff';
    const cardBorder = settings.cardBorder ?? '#e5e7eb';
    const highlightedBg = settings.highlightedBg ?? '#0f172a';
    const highlightedFg = settings.highlightedFg ?? '#ffffff';

    // Default to the first highlighted plan if any.
    const initialIndex = Math.max(0, plans.findIndex((p) => p.highlighted));
    const [active, setActive] = useState(initialIndex);

    if (plans.length === 0) {
        return (
            <SectionWrap settings={settings}>
                <SectionHeader heading={settings.heading} subtitle={settings.subtitle} mutedFg={mutedFg} />
                <p style={{ textAlign: 'center', color: mutedFg, margin: 0 }}>No pricing plans yet — add some in the Website Builder.</p>
            </SectionWrap>
        );
    }

    const plan = plans[active];
    const features = parseFeatures(plan.features);

    return (
        <SectionWrap settings={settings}>
            <SectionHeader heading={settings.heading} subtitle={settings.subtitle} mutedFg={mutedFg} />
            <div role="tablist" style={{
                display: 'flex',
                flexWrap: 'wrap',
                justifyContent: 'center',
                gap: 8,
                marginBottom: 28,
                padding: 6,
                background: 'color-mix(in srgb, currentColor 6%, transparent)',
                borderRadius: 9999,
                width: 'fit-content',
                margin: '0 auto 28px',
            }}>
                {plans.map((p, i) => {
                    const isActive = i === active;
                    return (
                        <button
                            key={i}
                            role="tab"
                            type="button"
                            aria-selected={isActive}
                            onClick={() => setActive(i)}
                            style={{
                                padding: '8px 18px',
                                borderRadius: 9999,
                                border: 'none',
                                background: isActive ? cardBg : 'transparent',
                                color: isActive ? fg : mutedFg,
                                fontWeight: 600,
                                fontSize: '0.875rem',
                                cursor: 'pointer',
                                transition: 'background .15s ease, color .15s ease',
                                boxShadow: isActive ? '0 1px 2px rgba(0,0,0,.06)' : 'none',
                            }}
                        >
                            {p.name}
                        </button>
                    );
                })}
            </div>

            <article style={{
                position: 'relative',
                maxWidth: '760px',
                margin: '0 auto',
                background: plan.highlighted ? highlightedBg : cardBg,
                color: plan.highlighted ? highlightedFg : fg,
                border: plan.highlighted ? 'none' : `1px solid ${cardBorder}`,
                borderRadius: 18,
                padding: 'clamp(28px, 4vw, 40px)',
                display: 'flex',
                flexDirection: 'column',
                gap: 22,
                boxShadow: plan.highlighted
                    ? '0 12px 32px -12px rgba(15,23,42,0.35)'
                    : '0 1px 2px rgba(15,23,42,0.05), 0 8px 24px -16px rgba(15,23,42,0.18)',
            }}>
                {plan.badge && <span style={badgeStyle}>{plan.badge}</span>}
                <div>
                    {plan.name && <h3 style={{ margin: 0, fontSize: '1.5rem', fontWeight: 700, letterSpacing: '-0.015em' }}>{plan.name}</h3>}
                    {plan.description && <p style={{ margin: '6px 0 0', color: plan.highlighted ? `color-mix(in srgb, ${highlightedFg} 75%, transparent)` : mutedFg, fontSize: '0.9375rem' }}>{plan.description}</p>}
                </div>
                <PriceDisplay plan={plan} mutedFg={plan.highlighted ? `color-mix(in srgb, ${highlightedFg} 75%, transparent)` : mutedFg} />
                {features.length > 0 && (
                    <ul style={{ listStyle: 'none', padding: 0, margin: 0, display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(220px, 1fr))', gap: 10 }}>
                        {features.map((feat, fi) => (
                            <li key={fi} style={{ display: 'flex', alignItems: 'flex-start', gap: 10, fontSize: '0.9375rem', color: plan.highlighted ? `color-mix(in srgb, ${highlightedFg} 80%, transparent)` : mutedFg }}>
                                <CheckIcon color={plan.highlighted ? highlightedFg : 'var(--primary, #0284c7)'} />
                                <span>{feat}</span>
                            </li>
                        ))}
                    </ul>
                )}
                {plan.buttonText && (
                    <a href={plan.buttonHref || '#'} style={{
                        alignSelf: 'flex-start',
                        display: 'inline-flex',
                        alignItems: 'center',
                        gap: 6,
                        background: plan.highlighted ? highlightedFg : 'var(--primary, #0284c7)',
                        color: plan.highlighted ? highlightedBg : '#ffffff',
                        padding: '12px 22px',
                        borderRadius: 10,
                        fontWeight: 600,
                        fontSize: '0.9375rem',
                        textDecoration: 'none',
                    }}>{plan.buttonText}</a>
                )}
            </article>
        </SectionWrap>
    );
}

// ── Variant: minimal (compact rows) ────────────────────────────────────────

function MinimalView({ settings }: { settings: PricingSettings }) {
    const plans = settings.plans ?? [];
    const fg = settings.fg ?? '#0f172a';
    const mutedFg = settings.mutedFg ?? '#64748b';
    const cardBorder = settings.cardBorder ?? '#e5e7eb';

    return (
        <SectionWrap settings={settings}>
            <SectionHeader heading={settings.heading} subtitle={settings.subtitle} mutedFg={mutedFg} />
            {plans.length === 0 ? (
                <p style={{ textAlign: 'center', color: mutedFg, margin: 0 }}>No pricing plans yet — add some in the Website Builder.</p>
            ) : (
                <ul style={{ listStyle: 'none', padding: 0, margin: 0, maxWidth: '820px', marginInline: 'auto', borderTop: `1px solid ${cardBorder}` }}>
                    {plans.map((plan, i) => {
                        const hi = plan.highlighted === true;
                        return (
                            <li key={i} style={{
                                display: 'grid',
                                gridTemplateColumns: '1fr auto auto',
                                alignItems: 'center',
                                gap: 16,
                                padding: 'clamp(16px, 2vw, 22px) 4px',
                                borderBottom: `1px solid ${cardBorder}`,
                            }}>
                                <div style={{ minWidth: 0 }}>
                                    <div style={{ display: 'flex', alignItems: 'center', gap: 10, flexWrap: 'wrap' }}>
                                        <span style={{ fontWeight: 700, color: fg, fontSize: '1.0625rem', letterSpacing: '-0.005em' }}>{plan.name}</span>
                                        {hi && plan.badge && (
                                            <span style={{
                                                fontSize: '0.6875rem', fontWeight: 700, letterSpacing: '0.06em',
                                                textTransform: 'uppercase',
                                                color: 'var(--primary, #0284c7)',
                                                background: 'color-mix(in srgb, var(--primary, #0284c7) 12%, transparent)',
                                                padding: '3px 8px', borderRadius: 9999,
                                            }}>{plan.badge}</span>
                                        )}
                                    </div>
                                    {plan.description && (
                                        <div style={{ color: mutedFg, fontSize: '0.875rem', marginTop: 3, lineHeight: 1.5 }}>{plan.description}</div>
                                    )}
                                </div>
                                <div style={{ display: 'inline-flex', alignItems: 'baseline', gap: 2, color: fg, whiteSpace: 'nowrap' }}>
                                    {plan.currency && <span style={{ fontSize: '1rem', fontWeight: 600, opacity: 0.7 }}>{plan.currency}</span>}
                                    <span style={{ fontSize: 'clamp(1.5rem, 2.4vw, 1.875rem)', fontWeight: 800, letterSpacing: '-0.02em', lineHeight: 1 }}>{plan.price}</span>
                                    {plan.period && <span style={{ fontSize: '0.875rem', color: mutedFg, fontWeight: 500 }}>{plan.period}</span>}
                                </div>
                                <div>
                                    {plan.buttonText && (
                                        <a href={plan.buttonHref || '#'} style={{
                                            display: 'inline-flex',
                                            alignItems: 'center',
                                            gap: 6,
                                            background: hi ? 'var(--primary, #0284c7)' : 'transparent',
                                            color: hi ? '#ffffff' : 'var(--primary, #0284c7)',
                                            border: hi ? 'none' : `1px solid color-mix(in srgb, var(--primary, #0284c7) 30%, transparent)`,
                                            padding: '8px 14px',
                                            borderRadius: 8,
                                            fontWeight: 600,
                                            fontSize: '0.875rem',
                                            textDecoration: 'none',
                                            whiteSpace: 'nowrap',
                                        }}>{plan.buttonText}</a>
                                    )}
                                </div>
                            </li>
                        );
                    })}
                </ul>
            )}
        </SectionWrap>
    );
}

// ── Shared bits ────────────────────────────────────────────────────────────

function PriceDisplay({ plan, mutedFg }: { plan: Plan; mutedFg: string }) {
    return (
        <div style={{ display: 'flex', alignItems: 'baseline', gap: 4, flexWrap: 'wrap' }}>
            {plan.currency && <span style={{ fontSize: '1.25rem', fontWeight: 600, opacity: 0.7 }}>{plan.currency}</span>}
            <span style={{ fontSize: 'clamp(2.25rem, 4vw, 3rem)', fontWeight: 800, letterSpacing: '-0.03em', lineHeight: 1 }}>{plan.price ?? ''}</span>
            {plan.period && <span style={{ fontSize: '0.9375rem', color: mutedFg, fontWeight: 500 }}>{plan.period}</span>}
        </div>
    );
}

function CheckIcon({ color, size = 18 }: { color: string; size?: number }) {
    return (
        <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.4" style={{ flexShrink: 0, marginTop: 2, color }}>
            <path strokeLinecap="round" strokeLinejoin="round" d="m4.5 12.75 6 6 9-13.5" />
        </svg>
    );
}

const badgeStyle: React.CSSProperties = {
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
};
