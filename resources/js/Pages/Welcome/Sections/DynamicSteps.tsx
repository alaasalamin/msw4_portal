interface Step {
    number?: string;
    title?: string;
    description?: string;
}

interface StepsSettings {
    variant?: 'horizontal' | 'vertical' | 'cards' | 'arrows' | 'timeline';
    heading?: string;
    subtitle?: string;
    steps?: Step[];
    /** Legacy field — old rows used layout='horizontal' / 'vertical'. Falls through to variant. */
    layout?: 'horizontal' | 'vertical';
    showConnectors?: boolean;
    autoNumber?: boolean;
    bg?: string;
    fg?: string;
    mutedFg?: string;
    numberBg?: string;
    numberFg?: string;
    connectorColor?: string;
}

function autoNumberLabel(i: number): string {
    return String(i + 1).padStart(2, '0');
}

// ── Dispatcher ──────────────────────────────────────────────────────────────

export default function DynamicSteps({ settings }: { settings: StepsSettings }) {
    // Back-compat: existing saved rows used the `layout` field.
    const variant = settings.variant ?? settings.layout ?? 'horizontal';
    switch (variant) {
        case 'vertical': return <Vertical settings={settings} />;
        case 'cards':    return <Cards    settings={settings} />;
        case 'arrows':   return <Arrows   settings={settings} />;
        case 'timeline': return <Timeline settings={settings} />;
        case 'horizontal':
        default:         return <Horizontal settings={settings} />;
    }
}

// ── Helpers / shared chrome ────────────────────────────────────────────────

function useDerived(settings: StepsSettings) {
    const fg             = settings.fg ?? '#0f172a';
    const mutedFg        = settings.mutedFg ?? '#64748b';
    const numberBg       = settings.numberBg ?? '#0f172a';
    const numberFg       = settings.numberFg ?? '#ffffff';
    const connectorColor = settings.connectorColor ?? '#cbd5e1';
    const autoNum        = settings.autoNumber !== false;
    const showConnectors = settings.showConnectors !== false;

    const labelFor = (s: Step, i: number): string => {
        if (s.number && s.number.trim().length) return s.number;
        return autoNum ? autoNumberLabel(i) : '';
    };

    return { fg, mutedFg, numberBg, numberFg, connectorColor, autoNum, showConnectors, labelFor };
}

function SectionWrap({ settings, maxWidth = '1200px', children }: { settings: StepsSettings; maxWidth?: string; children: React.ReactNode }) {
    return (
        <section style={{
            background: settings.bg ?? '#ffffff',
            color: settings.fg ?? '#0f172a',
            padding: 'clamp(48px, 8vw, 96px) clamp(16px, 4vw, 32px)',
            fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
        }}>
            <div style={{ maxWidth, margin: '0 auto' }}>{children}</div>
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

function NumberCircle({ label, bg, fg, size = 48, square = false }: { label: string; bg: string; fg: string; size?: number; square?: boolean }) {
    return (
        <div style={{
            width: size,
            height: size,
            borderRadius: square ? 14 : 9999,
            background: bg,
            color: fg,
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
            fontWeight: 700,
            fontSize: '0.9375rem',
            fontFamily: 'ui-monospace, SFMono-Regular, Menlo, monospace',
            boxShadow: '0 1px 3px rgba(0,0,0,0.10)',
            flexShrink: 0,
        }}>
            {label}
        </div>
    );
}

const GAP_PX = 40;

// ── Variant: horizontal (existing) ─────────────────────────────────────────

function Horizontal({ settings }: { settings: StepsSettings }) {
    const steps = settings.steps ?? [];
    const d = useDerived(settings);

    return (
        <SectionWrap settings={settings}>
            <SectionHeader heading={settings.heading} subtitle={settings.subtitle} mutedFg={d.mutedFg} />
            {steps.length === 0 ? (
                <p style={{ textAlign: 'center', color: d.mutedFg, margin: 0 }}>No steps yet — add some in the Website Builder.</p>
            ) : (
                <div className="tb-steps-grid" style={{
                    display: 'grid',
                    gridTemplateColumns: `repeat(${Math.min(steps.length, 4)}, minmax(0, 1fr))`,
                    gap: `${GAP_PX}px`,
                }}>
                    {steps.map((s, i) => {
                        const isLast = i === steps.length - 1;
                        return (
                            <div key={i} style={{ position: 'relative', textAlign: 'center' }}>
                                {d.showConnectors && !isLast && (
                                    <div className="tb-step-connector" style={{
                                        position: 'absolute',
                                        top: 23,
                                        left: '50%',
                                        width: `calc(100% + ${GAP_PX}px)`,
                                        height: 0,
                                        borderTop: `2px dashed ${d.connectorColor}`,
                                        zIndex: 0,
                                        pointerEvents: 'none',
                                    }} />
                                )}
                                <div style={{ position: 'relative', zIndex: 1, margin: '0 auto', display: 'inline-flex' }}>
                                    <NumberCircle label={d.labelFor(s, i)} bg={d.numberBg} fg={d.numberFg} square />
                                </div>
                                <StepBody step={s} fg={d.fg} mutedFg={d.mutedFg} centered />
                            </div>
                        );
                    })}
                </div>
            )}
            <style>{`
                @media (max-width: 900px) {
                    .tb-steps-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
                    .tb-step-connector { display: none !important; }
                }
                @media (max-width: 560px) {
                    .tb-steps-grid { grid-template-columns: 1fr !important; }
                }
            `}</style>
        </SectionWrap>
    );
}

// ── Variant: vertical (existing) ───────────────────────────────────────────

function Vertical({ settings }: { settings: StepsSettings }) {
    const steps = settings.steps ?? [];
    const d = useDerived(settings);

    return (
        <SectionWrap settings={settings} maxWidth="760px">
            <SectionHeader heading={settings.heading} subtitle={settings.subtitle} mutedFg={d.mutedFg} />
            {steps.length === 0 ? (
                <p style={{ textAlign: 'center', color: d.mutedFg, margin: 0 }}>No steps yet — add some in the Website Builder.</p>
            ) : (
                <ol style={{ listStyle: 'none', padding: 0, margin: 0, position: 'relative' }}>
                    {steps.map((s, i) => {
                        const isLast = i === steps.length - 1;
                        return (
                            <li key={i} style={{
                                position: 'relative',
                                display: 'grid',
                                gridTemplateColumns: '56px 1fr',
                                gap: 18,
                                paddingBottom: isLast ? 0 : 'clamp(28px, 4vw, 40px)',
                            }}>
                                <div style={{ position: 'relative' }}>
                                    <div style={{ position: 'relative', zIndex: 1 }}>
                                        <NumberCircle label={d.labelFor(s, i)} bg={d.numberBg} fg={d.numberFg} square />
                                    </div>
                                    {d.showConnectors && !isLast && (
                                        <div style={{
                                            position: 'absolute',
                                            left: '50%',
                                            top: 48,
                                            bottom: -16,
                                            width: 0,
                                            borderLeft: `2px dashed ${d.connectorColor}`,
                                            transform: 'translateX(-1px)',
                                        }} />
                                    )}
                                </div>
                                <div style={{ paddingTop: 6 }}>
                                    <StepBody step={s} fg={d.fg} mutedFg={d.mutedFg} />
                                </div>
                            </li>
                        );
                    })}
                </ol>
            )}
        </SectionWrap>
    );
}

// ── Variant: cards (elevated cards, no connectors) ─────────────────────────

function Cards({ settings }: { settings: StepsSettings }) {
    const steps = settings.steps ?? [];
    const d = useDerived(settings);

    return (
        <SectionWrap settings={settings}>
            <SectionHeader heading={settings.heading} subtitle={settings.subtitle} mutedFg={d.mutedFg} />
            {steps.length === 0 ? (
                <p style={{ textAlign: 'center', color: d.mutedFg, margin: 0 }}>No steps yet — add some in the Website Builder.</p>
            ) : (
                <div className="tb-steps-cards" style={{
                    display: 'grid',
                    gridTemplateColumns: `repeat(${Math.min(steps.length, 4)}, minmax(0, 1fr))`,
                    gap: 'clamp(20px, 2.5vw, 28px)',
                }}>
                    {steps.map((s, i) => (
                        <article key={i} style={{
                            position: 'relative',
                            background: '#ffffff',
                            borderRadius: 16,
                            padding: 'clamp(22px, 2.5vw, 28px)',
                            boxShadow: '0 1px 2px rgba(15,23,42,0.05), 0 8px 20px -8px rgba(15,23,42,0.16)',
                            display: 'flex',
                            flexDirection: 'column',
                            gap: 12,
                        }}>
                            <NumberCircle label={d.labelFor(s, i)} bg="var(--primary, #0284c7)" fg="#ffffff" size={42} square />
                            {s.title && (
                                <h3 style={{ margin: 0, fontSize: '1.0625rem', fontWeight: 700, color: d.fg, letterSpacing: '-0.01em', lineHeight: 1.3 }}>
                                    {s.title}
                                </h3>
                            )}
                            {s.description && (
                                <p style={{ margin: 0, color: d.mutedFg, lineHeight: 1.6, fontSize: '0.9375rem' }}>
                                    {s.description}
                                </p>
                            )}
                        </article>
                    ))}
                </div>
            )}
            <style>{`
                @media (max-width: 900px) {
                    .tb-steps-cards { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
                }
                @media (max-width: 560px) {
                    .tb-steps-cards { grid-template-columns: 1fr !important; }
                }
            `}</style>
        </SectionWrap>
    );
}

// ── Variant: arrows (horizontal flow with chevron connectors) ──────────────

function Arrows({ settings }: { settings: StepsSettings }) {
    const steps = settings.steps ?? [];
    const d = useDerived(settings);

    return (
        <SectionWrap settings={settings}>
            <SectionHeader heading={settings.heading} subtitle={settings.subtitle} mutedFg={d.mutedFg} />
            {steps.length === 0 ? (
                <p style={{ textAlign: 'center', color: d.mutedFg, margin: 0 }}>No steps yet — add some in the Website Builder.</p>
            ) : (
                <div className="tb-steps-arrows" style={{
                    display: 'flex',
                    flexWrap: 'wrap',
                    alignItems: 'flex-start',
                    justifyContent: 'center',
                    gap: 'clamp(8px, 1.5vw, 16px)',
                }}>
                    {steps.map((s, i) => {
                        const isLast = i === steps.length - 1;
                        return (
                            <div key={i} className="tb-steps-arrows-item" style={{ display: 'flex', alignItems: 'center', gap: 'clamp(8px, 1.5vw, 16px)' }}>
                                <div style={{
                                    flex: '0 0 auto',
                                    minWidth: 'clamp(170px, 22vw, 220px)',
                                    maxWidth: 280,
                                    textAlign: 'center',
                                    display: 'flex',
                                    flexDirection: 'column',
                                    alignItems: 'center',
                                    gap: 10,
                                }}>
                                    <NumberCircle label={d.labelFor(s, i)} bg={d.numberBg} fg={d.numberFg} square />
                                    {s.title && (
                                        <h3 style={{ margin: 0, fontSize: '1rem', fontWeight: 700, color: d.fg, letterSpacing: '-0.01em', lineHeight: 1.3 }}>
                                            {s.title}
                                        </h3>
                                    )}
                                    {s.description && (
                                        <p style={{ margin: 0, color: d.mutedFg, lineHeight: 1.55, fontSize: '0.875rem' }}>
                                            {s.description}
                                        </p>
                                    )}
                                </div>
                                {!isLast && (
                                    <svg
                                        aria-hidden="true"
                                        width="36" height="36" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" strokeWidth="2"
                                        style={{
                                            flexShrink: 0,
                                            color: 'var(--primary, #0284c7)',
                                            opacity: 0.85,
                                            marginTop: 4,
                                        }}
                                    >
                                        <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                    </svg>
                                )}
                            </div>
                        );
                    })}
                </div>
            )}
            <style>{`
                @media (max-width: 720px) {
                    .tb-steps-arrows {
                        flex-direction: column !important;
                        align-items: center !important;
                        justify-content: center !important;
                    }
                    .tb-steps-arrows-item {
                        flex-direction: column !important;
                        align-items: center !important;
                        text-align: center;
                    }
                    .tb-steps-arrows-item > svg { transform: rotate(90deg); }
                }
            `}</style>
        </SectionWrap>
    );
}

// ── Variant: timeline (vertical, filled circles, continuous bar) ───────────

function Timeline({ settings }: { settings: StepsSettings }) {
    const steps = settings.steps ?? [];
    const d = useDerived(settings);

    return (
        <SectionWrap settings={settings} maxWidth="760px">
            <SectionHeader heading={settings.heading} subtitle={settings.subtitle} mutedFg={d.mutedFg} />
            {steps.length === 0 ? (
                <p style={{ textAlign: 'center', color: d.mutedFg, margin: 0 }}>No steps yet — add some in the Website Builder.</p>
            ) : (
                <ol style={{ listStyle: 'none', padding: 0, margin: 0, position: 'relative' }}>
                    {/* Continuous timeline bar that runs from the first dot to the last */}
                    <div aria-hidden="true" style={{
                        position: 'absolute',
                        left: 23,
                        top: 24,
                        bottom: 24,
                        width: 2,
                        background: `color-mix(in srgb, ${d.connectorColor} 70%, transparent)`,
                        borderRadius: 9999,
                    }} />
                    {steps.map((s, i) => (
                        <li key={i} style={{
                            position: 'relative',
                            display: 'grid',
                            gridTemplateColumns: '48px 1fr',
                            gap: 22,
                            paddingBottom: i === steps.length - 1 ? 0 : 'clamp(24px, 3.5vw, 36px)',
                        }}>
                            <div style={{
                                width: 48,
                                height: 48,
                                position: 'relative',
                                zIndex: 1,
                                borderRadius: 9999,
                                background: 'var(--primary, #0284c7)',
                                color: '#ffffff',
                                display: 'flex',
                                alignItems: 'center',
                                justifyContent: 'center',
                                boxShadow: '0 0 0 4px var(--primary-50, rgba(255,255,255,0.85))',
                                fontWeight: 700,
                                fontSize: '0.875rem',
                                fontFamily: 'ui-monospace, SFMono-Regular, Menlo, monospace',
                                flexShrink: 0,
                            }}>
                                {d.labelFor(s, i)}
                            </div>
                            <div style={{ paddingTop: 6 }}>
                                <StepBody step={s} fg={d.fg} mutedFg={d.mutedFg} />
                            </div>
                        </li>
                    ))}
                </ol>
            )}
        </SectionWrap>
    );
}

// ── Reusable text block for a step ─────────────────────────────────────────

function StepBody({ step, fg, mutedFg, centered = false }: { step: Step; fg: string; mutedFg: string; centered?: boolean }) {
    return (
        <>
            {step.title && (
                <h3 style={{
                    margin: centered ? '14px 0 0' : 0,
                    fontSize: '1.0625rem',
                    fontWeight: 700,
                    color: fg,
                    letterSpacing: '-0.01em',
                    lineHeight: 1.3,
                }}>{step.title}</h3>
            )}
            {step.description && (
                <p style={{
                    margin: centered ? '6px auto 0' : '6px 0 0',
                    color: mutedFg,
                    lineHeight: 1.6,
                    fontSize: '0.9375rem',
                    maxWidth: centered ? '32ch' : undefined,
                }}>{step.description}</p>
            )}
        </>
    );
}
