interface FaqItem {
    question?: string;
    answer?: string;
}

interface FaqSettings {
    variant?: 'accordion' | 'open' | 'two-col' | 'cards' | 'bordered';
    heading?: string;
    subtitle?: string;
    items?: FaqItem[];
    /** Legacy field — old rows used `layout` for accordion / open / two-col. Falls through to variant. */
    layout?: 'accordion' | 'open' | 'two-col';
    expandFirst?: boolean;
    bg?: string;
    fg?: string;
    mutedFg?: string;
    cardBg?: string;
    borderColor?: string;
}

// ── Dispatcher ──────────────────────────────────────────────────────────────

export default function DynamicFaq({ settings }: { settings: FaqSettings }) {
    const variant = settings.variant ?? settings.layout ?? 'accordion';
    switch (variant) {
        case 'open':     return <Open     settings={settings} />;
        case 'two-col':  return <TwoCol   settings={settings} />;
        case 'cards':    return <Cards    settings={settings} />;
        case 'bordered': return <Bordered settings={settings} />;
        case 'accordion':
        default:         return <Accordion settings={settings} />;
    }
}

// ── Shared chrome ──────────────────────────────────────────────────────────

function SectionWrap({ settings, maxWidth, children }: { settings: FaqSettings; maxWidth: string; children: React.ReactNode }) {
    const fg = settings.fg ?? '#0f172a';
    return (
        <section style={{
            background: settings.bg ?? '#ffffff',
            color: fg,
            padding: 'clamp(48px, 8vw, 96px) clamp(16px, 4vw, 32px)',
            fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
        }}>
            <div style={{ maxWidth, margin: '0 auto' }}>{children}</div>
        </section>
    );
}

function SectionHeader({ settings }: { settings: FaqSettings }) {
    const mutedFg = settings.mutedFg ?? '#475569';
    if (!settings.heading && !settings.subtitle) return null;
    return (
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
    );
}

function getItems(settings: FaqSettings): FaqItem[] {
    return (settings.items ?? []).filter((it) => it.question || it.answer);
}

function EmptyState({ mutedFg }: { mutedFg: string }) {
    return (
        <p style={{ textAlign: 'center', color: mutedFg, margin: 0 }}>
            No questions yet — add some in the Website Builder.
        </p>
    );
}

// ── Variant: accordion ─────────────────────────────────────────────────────

function Accordion({ settings }: { settings: FaqSettings }) {
    const items = getItems(settings);
    const fg = settings.fg ?? '#0f172a';
    const mutedFg = settings.mutedFg ?? '#475569';
    const cardBg = settings.cardBg ?? '#f8fafc';
    const borderColor = settings.borderColor ?? '#e5e7eb';
    const expandFirst = settings.expandFirst !== false;

    return (
        <SectionWrap settings={settings} maxWidth="760px">
            <SectionHeader settings={settings} />
            {items.length === 0 ? <EmptyState mutedFg={mutedFg} /> : (
                <div style={{ display: 'flex', flexDirection: 'column', gap: 10 }}>
                    {items.map((it, i) => (
                        <details key={i} open={i === 0 && expandFirst} className="tb-faq-item" style={{
                            background: cardBg,
                            border: `1px solid ${borderColor}`,
                            borderRadius: 12,
                            overflow: 'hidden',
                            transition: 'border-color .15s ease',
                        }}>
                            <SummaryRow it={it} fg={fg} mutedFg={mutedFg} />
                            <AnswerBody answer={it.answer} mutedFg={mutedFg} />
                        </details>
                    ))}
                </div>
            )}
            <SharedAccordionStyles />
        </SectionWrap>
    );
}

// ── Variant: open (single column, every Q&A visible) ───────────────────────

function Open({ settings }: { settings: FaqSettings }) {
    const items = getItems(settings);
    const fg = settings.fg ?? '#0f172a';
    const mutedFg = settings.mutedFg ?? '#475569';

    return (
        <SectionWrap settings={settings} maxWidth="760px">
            <SectionHeader settings={settings} />
            {items.length === 0 ? <EmptyState mutedFg={mutedFg} /> : (
                <div style={{ display: 'flex', flexDirection: 'column' }}>
                    {items.map((it, i) => (
                        <div key={i} style={{
                            paddingTop: i === 0 ? 0 : 'clamp(20px, 2.4vw, 28px)',
                            paddingBottom: 'clamp(20px, 2.4vw, 28px)',
                            borderBottom: i === items.length - 1 ? 'none' : '1px solid rgba(15,23,42,0.08)',
                        }}>
                            <h3 style={questionStyle(fg)}>{it.question}</h3>
                            <p style={answerStyle(mutedFg, '6px')}>{it.answer}</p>
                        </div>
                    ))}
                </div>
            )}
        </SectionWrap>
    );
}

// ── Variant: two-col (grid, always visible) ────────────────────────────────

function TwoCol({ settings }: { settings: FaqSettings }) {
    const items = getItems(settings);
    const fg = settings.fg ?? '#0f172a';
    const mutedFg = settings.mutedFg ?? '#475569';
    const cardBg = settings.cardBg ?? '#f8fafc';
    const borderColor = settings.borderColor ?? '#e5e7eb';

    return (
        <SectionWrap settings={settings} maxWidth="1100px">
            <SectionHeader settings={settings} />
            {items.length === 0 ? <EmptyState mutedFg={mutedFg} /> : (
                <div className="tb-faq-grid" style={{
                    display: 'grid',
                    gridTemplateColumns: 'repeat(2, minmax(0, 1fr))',
                    gap: 'clamp(20px, 2.5vw, 32px)',
                }}>
                    {items.map((it, i) => (
                        <div key={i} style={{
                            background: cardBg,
                            border: `1px solid ${borderColor}`,
                            borderRadius: 12,
                            padding: 'clamp(16px, 2vw, 22px)',
                        }}>
                            <h3 style={questionStyle(fg)}>{it.question}</h3>
                            <p style={answerStyle(mutedFg, '8px')}>{it.answer}</p>
                        </div>
                    ))}
                </div>
            )}
            <style>{`@media (max-width: 720px) { .tb-faq-grid { grid-template-columns: 1fr !important; } }`}</style>
        </SectionWrap>
    );
}

// ── Variant: cards (each Q&A in an elevated card, always open) ─────────────

function Cards({ settings }: { settings: FaqSettings }) {
    const items = getItems(settings);
    const fg = settings.fg ?? '#0f172a';
    const mutedFg = settings.mutedFg ?? '#475569';
    const cardBg = settings.cardBg ?? '#ffffff';

    return (
        <SectionWrap settings={settings} maxWidth="900px">
            <SectionHeader settings={settings} />
            {items.length === 0 ? <EmptyState mutedFg={mutedFg} /> : (
                <div style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>
                    {items.map((it, i) => (
                        <article key={i} style={{
                            position: 'relative',
                            background: cardBg,
                            borderRadius: 14,
                            padding: 'clamp(20px, 2.5vw, 28px)',
                            paddingLeft: 'calc(clamp(20px, 2.5vw, 28px) + 36px)',
                            boxShadow: '0 1px 2px rgba(15,23,42,.05), 0 6px 14px -8px rgba(15,23,42,.16)',
                        }}>
                            <span aria-hidden="true" style={{
                                position: 'absolute',
                                top: 'clamp(20px, 2.5vw, 28px)',
                                left: 'clamp(20px, 2.5vw, 28px)',
                                width: 24,
                                height: 24,
                                borderRadius: 9999,
                                background: 'color-mix(in srgb, var(--primary, #0284c7) 12%, transparent)',
                                color: 'var(--primary, #0284c7)',
                                display: 'inline-flex',
                                alignItems: 'center',
                                justifyContent: 'center',
                            }}>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M9 9.75a3 3 0 1 1 4.36 2.673c-.5.27-.86.81-.86 1.4V15M12 18.75h.008v.008H12v-.008Z" />
                                </svg>
                            </span>
                            <h3 style={questionStyle(fg)}>{it.question}</h3>
                            <p style={answerStyle(mutedFg, '8px')}>{it.answer}</p>
                        </article>
                    ))}
                </div>
            )}
        </SectionWrap>
    );
}

// ── Variant: bordered (accordion with colored left bar) ────────────────────

function Bordered({ settings }: { settings: FaqSettings }) {
    const items = getItems(settings);
    const fg = settings.fg ?? '#0f172a';
    const mutedFg = settings.mutedFg ?? '#475569';
    const cardBg = settings.cardBg ?? '#ffffff';
    const borderColor = settings.borderColor ?? '#e5e7eb';
    const expandFirst = settings.expandFirst !== false;

    return (
        <SectionWrap settings={settings} maxWidth="760px">
            <SectionHeader settings={settings} />
            {items.length === 0 ? <EmptyState mutedFg={mutedFg} /> : (
                <div style={{ display: 'flex', flexDirection: 'column', gap: 12 }}>
                    {items.map((it, i) => (
                        <details key={i} open={i === 0 && expandFirst} className="tb-faq-item" style={{
                            background: cardBg,
                            border: `1px solid ${borderColor}`,
                            borderLeft: `4px solid var(--primary, #0284c7)`,
                            borderRadius: 12,
                            overflow: 'hidden',
                        }}>
                            <SummaryRow it={it} fg={fg} mutedFg={mutedFg} />
                            <AnswerBody answer={it.answer} mutedFg={mutedFg} />
                        </details>
                    ))}
                </div>
            )}
            <SharedAccordionStyles />
        </SectionWrap>
    );
}

// ── Reusable bits ──────────────────────────────────────────────────────────

function SummaryRow({ it, fg, mutedFg }: { it: FaqItem; fg: string; mutedFg: string }) {
    return (
        <summary style={{
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
        }}>
            <span>{it.question}</span>
            <svg className="tb-faq-chevron" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" style={{ flexShrink: 0, color: mutedFg, transition: 'transform .2s ease' }}>
                <path strokeLinecap="round" strokeLinejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
            </svg>
        </summary>
    );
}

function AnswerBody({ answer, mutedFg }: { answer?: string; mutedFg: string }) {
    return (
        <div style={{
            padding: '0 clamp(16px, 2vw, 22px) clamp(16px, 2vw, 22px)',
            color: mutedFg,
            lineHeight: 1.65,
            fontSize: '0.9375rem',
            whiteSpace: 'pre-wrap',
        }}>
            {answer}
        </div>
    );
}

function SharedAccordionStyles() {
    return (
        <style>{`
            .tb-faq-item summary::-webkit-details-marker { display: none; }
            .tb-faq-item[open] .tb-faq-chevron { transform: rotate(180deg); }
            .tb-faq-item:hover { border-color: rgba(0,0,0,0.15); }
        `}</style>
    );
}

const questionStyle = (fg: string): React.CSSProperties => ({
    margin: 0,
    fontSize: '1.0625rem',
    fontWeight: 700,
    color: fg,
    lineHeight: 1.3,
    letterSpacing: '-0.01em',
});

const answerStyle = (mutedFg: string, marginTop: string): React.CSSProperties => ({
    margin: `${marginTop} 0 0`,
    color: mutedFg,
    lineHeight: 1.65,
    fontSize: '0.9375rem',
    whiteSpace: 'pre-wrap',
});
