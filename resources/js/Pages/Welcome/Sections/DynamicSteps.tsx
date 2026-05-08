interface Step {
    number?: string;
    title?: string;
    description?: string;
}

interface StepsSettings {
    heading?: string;
    subtitle?: string;
    steps?: Step[];
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

function autoNumber(i: number): string {
    return String(i + 1).padStart(2, '0');
}

export default function DynamicSteps({ settings }: { settings: StepsSettings }) {
    const steps  = settings.steps ?? [];
    const layout = settings.layout ?? 'horizontal';
    const showConnectors = settings.showConnectors !== false;
    const autoNum = settings.autoNumber !== false;

    const fg             = settings.fg ?? '#0f172a';
    const mutedFg        = settings.mutedFg ?? '#64748b';
    const numberBg       = settings.numberBg ?? '#0f172a';
    const numberFg       = settings.numberFg ?? '#ffffff';
    const connectorColor = settings.connectorColor ?? '#cbd5e1';
    // Fixed gap value, used both for the grid spacing and for the connector
    // line length so the lines land exactly on the next step's circle center.
    const GAP_PX = 40;

    const labelFor = (s: Step, i: number): string => {
        if (s.number && s.number.trim().length) return s.number;
        return autoNum ? autoNumber(i) : '';
    };

    return (
        <section
            style={{
                background: settings.bg ?? '#ffffff',
                color: fg,
                padding: 'clamp(48px, 8vw, 96px) clamp(16px, 4vw, 32px)',
                fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
            }}
        >
            <div style={{ maxWidth: layout === 'vertical' ? '760px' : '1200px', margin: '0 auto' }}>
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

                {steps.length === 0 ? (
                    <p style={{ textAlign: 'center', color: mutedFg, margin: 0 }}>
                        No steps yet — add some in the Theme Builder.
                    </p>
                ) : layout === 'vertical' ? (
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
                                        <div style={{
                                            width: 48,
                                            height: 48,
                                            borderRadius: 14,
                                            background: numberBg,
                                            color: numberFg,
                                            display: 'flex',
                                            alignItems: 'center',
                                            justifyContent: 'center',
                                            fontWeight: 700,
                                            fontSize: '0.9375rem',
                                            fontFamily: 'ui-monospace, SFMono-Regular, Menlo, monospace',
                                            boxShadow: '0 1px 3px rgba(0,0,0,0.10)',
                                            position: 'relative',
                                            zIndex: 1,
                                        }}>
                                            {labelFor(s, i)}
                                        </div>
                                        {showConnectors && !isLast && (
                                            <div style={{
                                                position: 'absolute',
                                                left: '50%',
                                                top: 48,
                                                bottom: -16,
                                                width: 0,
                                                borderLeft: `2px dashed ${connectorColor}`,
                                                transform: 'translateX(-1px)',
                                            }} />
                                        )}
                                    </div>
                                    <div style={{ paddingTop: 6 }}>
                                        {s.title && (
                                            <h3 style={{
                                                margin: 0,
                                                fontSize: '1.125rem',
                                                fontWeight: 700,
                                                color: fg,
                                                letterSpacing: '-0.01em',
                                                lineHeight: 1.3,
                                            }}>{s.title}</h3>
                                        )}
                                        {s.description && (
                                            <p style={{
                                                margin: '6px 0 0',
                                                color: mutedFg,
                                                lineHeight: 1.65,
                                                fontSize: '0.9375rem',
                                            }}>{s.description}</p>
                                        )}
                                    </div>
                                </li>
                            );
                        })}
                    </ol>
                ) : (
                    /* horizontal */
                    <div
                        className={`tb-steps-grid tb-steps-cols-${steps.length}`}
                        style={{
                            display: 'grid',
                            gridTemplateColumns: `repeat(${Math.min(steps.length, 4)}, minmax(0, 1fr))`,
                            gap: `${GAP_PX}px`,
                        }}
                    >
                        {steps.map((s, i) => {
                            const isLast = i === steps.length - 1;
                            return (
                                <div key={i} style={{ position: 'relative', textAlign: 'center' }}>
                                    {showConnectors && !isLast && (
                                        // Line starts at this step's center (left:50%) and spans
                                        // 100% + gap so it lands exactly on the next step's center.
                                        <div className="tb-step-connector" style={{
                                            position: 'absolute',
                                            top: 23,                       // (48px circle / 2) - line thickness
                                            left: '50%',
                                            width: `calc(100% + ${GAP_PX}px)`,
                                            height: 0,
                                            borderTop: `2px dashed ${connectorColor}`,
                                            zIndex: 0,
                                            pointerEvents: 'none',
                                        }} />
                                    )}
                                    <div style={{
                                        position: 'relative',
                                        zIndex: 1,
                                        margin: '0 auto',
                                        width: 48,
                                        height: 48,
                                        borderRadius: 14,
                                        background: numberBg,
                                        color: numberFg,
                                        display: 'flex',
                                        alignItems: 'center',
                                        justifyContent: 'center',
                                        fontWeight: 700,
                                        fontSize: '0.9375rem',
                                        fontFamily: 'ui-monospace, SFMono-Regular, Menlo, monospace',
                                        boxShadow: '0 1px 3px rgba(0,0,0,0.10)',
                                    }}>
                                        {labelFor(s, i)}
                                    </div>
                                    {s.title && (
                                        <h3 style={{
                                            margin: '14px 0 0',
                                            fontSize: '1.0625rem',
                                            fontWeight: 700,
                                            color: fg,
                                            letterSpacing: '-0.01em',
                                            lineHeight: 1.3,
                                        }}>{s.title}</h3>
                                    )}
                                    {s.description && (
                                        <p style={{
                                            margin: '6px auto 0',
                                            color: mutedFg,
                                            lineHeight: 1.6,
                                            fontSize: '0.9375rem',
                                            maxWidth: '32ch',
                                        }}>{s.description}</p>
                                    )}
                                </div>
                            );
                        })}
                    </div>
                )}
            </div>

            <style>{`
                @media (max-width: 900px) {
                    .tb-steps-grid {
                        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                    }
                    .tb-step-connector { display: none !important; }
                }
                @media (max-width: 560px) {
                    .tb-steps-grid {
                        grid-template-columns: 1fr !important;
                    }
                }
            `}</style>
        </section>
    );
}
