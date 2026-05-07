interface FaqItem {
    question?: string;
    answer?: string;
}

interface FaqSettings {
    heading?: string;
    subtitle?: string;
    items?: FaqItem[];
    layout?: 'accordion' | 'open' | 'two-col';
    expandFirst?: boolean;
    bg?: string;
    fg?: string;
    mutedFg?: string;
    cardBg?: string;
    borderColor?: string;
}

export default function DynamicFaq({ settings }: { settings: FaqSettings }) {
    const items   = (settings.items ?? []).filter((it) => it.question || it.answer);
    const layout  = settings.layout ?? 'accordion';
    const expandFirst = settings.expandFirst !== false;

    const fg          = settings.fg ?? '#0f172a';
    const mutedFg     = settings.mutedFg ?? '#475569';
    const cardBg      = settings.cardBg ?? '#f8fafc';
    const borderColor = settings.borderColor ?? '#e5e7eb';

    return (
        <section
            style={{
                background: settings.bg ?? '#ffffff',
                color: fg,
                padding: 'clamp(48px, 8vw, 96px) clamp(16px, 4vw, 32px)',
                fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
            }}
        >
            <div style={{ maxWidth: layout === 'two-col' ? '1100px' : '760px', margin: '0 auto' }}>
                {(settings.heading || settings.subtitle) && (
                    <div style={{ textAlign: 'center', marginBottom: 'clamp(28px, 4vw, 48px)' }}>
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

                {items.length === 0 ? (
                    <p style={{ textAlign: 'center', color: mutedFg, margin: 0 }}>
                        No questions yet — add some in the Theme Builder.
                    </p>
                ) : layout === 'accordion' ? (
                    <div style={{ display: 'flex', flexDirection: 'column', gap: 10 }}>
                        {items.map((it, i) => (
                            <details
                                key={i}
                                open={i === 0 && expandFirst}
                                className="tb-faq-item"
                                style={{
                                    background: cardBg,
                                    border: `1px solid ${borderColor}`,
                                    borderRadius: 12,
                                    overflow: 'hidden',
                                    transition: 'border-color .15s ease, box-shadow .15s ease',
                                }}
                            >
                                <summary
                                    style={{
                                        display: 'flex',
                                        alignItems: 'center',
                                        justifyContent: 'space-between',
                                        gap: 16,
                                        padding: 'clamp(14px, 1.6vw, 18px) clamp(16px, 2vw, 22px)',
                                        cursor: 'pointer',
                                        listStyle: 'none',
                                        fontWeight: 600,
                                        color: fg,
                                        fontSize: 'clamp(0.95rem, 1.1vw, 1.0625rem)',
                                        lineHeight: 1.4,
                                    }}
                                >
                                    <span>{it.question}</span>
                                    <svg className="tb-faq-chevron" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" style={{ flexShrink: 0, color: mutedFg, transition: 'transform .2s ease' }}>
                                        <path strokeLinecap="round" strokeLinejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </summary>
                                <div style={{
                                    padding: '0 clamp(16px, 2vw, 22px) clamp(16px, 2vw, 22px)',
                                    color: mutedFg,
                                    lineHeight: 1.65,
                                    fontSize: '0.9375rem',
                                    whiteSpace: 'pre-wrap',
                                }}>
                                    {it.answer}
                                </div>
                            </details>
                        ))}
                    </div>
                ) : layout === 'two-col' ? (
                    <div
                        className="tb-faq-grid"
                        style={{
                            display: 'grid',
                            gridTemplateColumns: 'repeat(2, minmax(0, 1fr))',
                            gap: 'clamp(20px, 2.5vw, 32px)',
                        }}
                    >
                        {items.map((it, i) => (
                            <div key={i} style={{
                                background: cardBg,
                                border: `1px solid ${borderColor}`,
                                borderRadius: 12,
                                padding: 'clamp(16px, 2vw, 22px)',
                            }}>
                                <h3 style={{
                                    margin: 0,
                                    fontSize: '1.0625rem',
                                    fontWeight: 700,
                                    color: fg,
                                    lineHeight: 1.3,
                                    letterSpacing: '-0.01em',
                                }}>{it.question}</h3>
                                <p style={{
                                    margin: '8px 0 0',
                                    color: mutedFg,
                                    lineHeight: 1.65,
                                    fontSize: '0.9375rem',
                                    whiteSpace: 'pre-wrap',
                                }}>{it.answer}</p>
                            </div>
                        ))}
                    </div>
                ) : (
                    /* layout = 'open' */
                    <div style={{ display: 'flex', flexDirection: 'column' }}>
                        {items.map((it, i) => (
                            <div key={i} style={{
                                padding: 'clamp(18px, 2.4vw, 26px) 0',
                                borderTop: i === 0 ? 'none' : `1px solid ${borderColor}`,
                            }}>
                                <h3 style={{
                                    margin: 0,
                                    fontSize: 'clamp(1rem, 1.3vw, 1.125rem)',
                                    fontWeight: 700,
                                    color: fg,
                                    lineHeight: 1.3,
                                    letterSpacing: '-0.01em',
                                }}>{it.question}</h3>
                                <p style={{
                                    margin: '6px 0 0',
                                    color: mutedFg,
                                    lineHeight: 1.65,
                                    fontSize: '0.9375rem',
                                    whiteSpace: 'pre-wrap',
                                }}>{it.answer}</p>
                            </div>
                        ))}
                    </div>
                )}
            </div>

            <style>{`
                .tb-faq-item summary::-webkit-details-marker { display: none; }
                .tb-faq-item[open] .tb-faq-chevron { transform: rotate(180deg); }
                .tb-faq-item:hover { border-color: rgba(0,0,0,0.15); }
                @media (max-width: 720px) {
                    .tb-faq-grid { grid-template-columns: 1fr !important; }
                }
            `}</style>
        </section>
    );
}
