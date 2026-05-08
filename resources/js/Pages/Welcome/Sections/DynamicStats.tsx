import { useEffect, useRef, useState } from 'react';

interface Stat {
    value?: string;
    prefix?: string;
    suffix?: string;
    label?: string;
    description?: string;
}

interface StatsSettings {
    variant?: 'row' | 'cards' | 'dividers' | 'gradient';
    heading?: string;
    subtitle?: string;
    stats?: Stat[];
    columns?: number | string;
    align?: 'center' | 'left';
    animate?: boolean;
    bg?: string;
    fg?: string;
    mutedFg?: string;
    accent?: string;
}

/** Parses something like "13,810", "98.6" or "24" → number; returns null otherwise. */
function parseNumeric(raw?: string): { num: number; decimals: number } | null {
    if (!raw) return null;
    const trimmed = raw.trim().replace(/[, ]/g, '');
    if (!/^-?\d+(\.\d+)?$/.test(trimmed)) return null;
    const num = parseFloat(trimmed);
    if (!Number.isFinite(num)) return null;
    const decIdx = trimmed.indexOf('.');
    const decimals = decIdx === -1 ? 0 : trimmed.length - decIdx - 1;
    return { num, decimals };
}

function formatNumber(n: number, decimals: number): string {
    return n.toLocaleString(undefined, {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals,
    });
}

function CountUp({ targetRaw, animate, duration = 1400 }: { targetRaw?: string; animate: boolean; duration?: number }) {
    const ref = useRef<HTMLSpanElement | null>(null);
    const parsed = parseNumeric(targetRaw);
    const target = parsed?.num ?? 0;
    const decimals = parsed?.decimals ?? 0;
    const [value, setValue] = useState<number>(animate && parsed ? 0 : target);
    const startedRef = useRef(false);

    useEffect(() => {
        if (!animate || !parsed) return;
        const el = ref.current;
        if (!el) return;
        if (typeof IntersectionObserver === 'undefined') {
            setValue(target);
            return;
        }

        const observer = new IntersectionObserver((entries) => {
            for (const entry of entries) {
                if (entry.isIntersecting && !startedRef.current) {
                    startedRef.current = true;
                    const start = performance.now();
                    const tick = (t: number) => {
                        const elapsed = t - start;
                        const progress = Math.min(1, elapsed / duration);
                        const eased = 1 - Math.pow(1 - progress, 3);
                        setValue(target * eased);
                        if (progress < 1) requestAnimationFrame(tick);
                        else setValue(target);
                    };
                    requestAnimationFrame(tick);
                    observer.disconnect();
                }
            }
        }, { threshold: 0.25 });

        observer.observe(el);
        return () => observer.disconnect();
    }, [target, decimals, animate, duration, parsed]);

    if (!parsed) return <span ref={ref}>{targetRaw}</span>;
    return <span ref={ref}>{formatNumber(value, decimals)}</span>;
}

// ── Dispatcher ─────────────────────────────────────────────────────────────

export default function DynamicStats({ settings }: { settings: StatsSettings }) {
    switch (settings.variant) {
        case 'cards':    return <Cards    settings={settings} />;
        case 'dividers': return <Dividers settings={settings} />;
        case 'gradient': return <Gradient settings={settings} />;
        case 'row':
        default:         return <Row settings={settings} />;
    }
}

// ── Shared chrome ──────────────────────────────────────────────────────────

function SectionHeader({ heading, subtitle, mutedFg, align, color }: { heading?: string; subtitle?: string; mutedFg: string; align: 'center' | 'left'; color?: string }) {
    if (!heading && !subtitle) return null;
    return (
        <div style={{ textAlign: align === 'center' ? 'center' : 'left', marginBottom: 'clamp(28px, 4vw, 48px)' }}>
            {heading && (
                <h2 style={{
                    fontSize: 'clamp(1.75rem, 3.5vw, 2.5rem)',
                    fontWeight: 700,
                    letterSpacing: '-0.025em',
                    margin: 0,
                    lineHeight: 1.15,
                    color: color ?? 'inherit',
                }}>{heading}</h2>
            )}
            {subtitle && (
                <p style={{
                    fontSize: 'clamp(1rem, 1.3vw, 1.125rem)',
                    color: mutedFg,
                    margin: '12px auto 0',
                    maxWidth: '52ch',
                    lineHeight: 1.6,
                    marginInline: align === 'center' ? 'auto' : '0',
                }}>{subtitle}</p>
            )}
        </div>
    );
}

interface StatNumberProps {
    stat: Stat;
    align: 'center' | 'left';
    fg: string;
    accent: string;
    animate: boolean;
    size?: 'normal' | 'large';
}

function StatNumber({ stat, align, fg, accent, animate, size = 'normal' }: StatNumberProps) {
    const big   = size === 'large' ? 'clamp(2.75rem, 6vw, 4.25rem)' : 'clamp(2.25rem, 5vw, 3.5rem)';
    const fix   = size === 'large' ? 'clamp(1.5rem, 2.4vw, 2rem)'   : 'clamp(1.25rem, 2vw, 1.625rem)';
    return (
        <div style={{
            display: 'flex',
            alignItems: 'baseline',
            gap: 2,
            justifyContent: align === 'center' ? 'center' : 'flex-start',
            color: fg,
            lineHeight: 1,
            flexWrap: 'wrap',
        }}>
            {stat.prefix && <span style={{ fontSize: fix, fontWeight: 700, color: accent }}>{stat.prefix}</span>}
            <span style={{
                fontSize: big,
                fontWeight: 800,
                letterSpacing: '-0.03em',
                fontVariantNumeric: 'tabular-nums',
            }}>
                <CountUp targetRaw={stat.value} animate={animate} />
            </span>
            {stat.suffix && <span style={{ fontSize: fix, fontWeight: 700, color: accent }}>{stat.suffix}</span>}
        </div>
    );
}

function StatBody({ stat, align, fg, mutedFg }: { stat: Stat; align: 'center' | 'left'; fg: string; mutedFg: string }) {
    const ta = align === 'center' ? 'center' : 'left';
    return (
        <>
            {stat.label && (
                <div style={{
                    marginTop: 6,
                    fontSize: '0.9375rem',
                    fontWeight: 600,
                    color: fg,
                    letterSpacing: '-0.005em',
                    textAlign: ta,
                }}>{stat.label}</div>
            )}
            {stat.description && (
                <div style={{
                    fontSize: '0.8125rem',
                    color: mutedFg,
                    lineHeight: 1.5,
                    textAlign: ta,
                }}>{stat.description}</div>
            )}
        </>
    );
}

// ── Variant: row (existing default) ────────────────────────────────────────

function Row({ settings }: { settings: StatsSettings }) {
    const stats = settings.stats ?? [];
    const cols = Math.max(2, Math.min(6, Number(settings.columns) || 4));
    const align = settings.align ?? 'center';
    const animate = settings.animate !== false;
    const fg = settings.fg ?? '#0f172a';
    const mutedFg = settings.mutedFg ?? '#64748b';
    const accent = settings.accent ?? '#0284c7';

    return (
        <section style={{
            background: settings.bg ?? '#ffffff',
            color: fg,
            padding: 'clamp(48px, 8vw, 96px) clamp(16px, 4vw, 32px)',
            fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
        }}>
            <div style={{ maxWidth: '1200px', margin: '0 auto' }}>
                <SectionHeader heading={settings.heading} subtitle={settings.subtitle} mutedFg={mutedFg} align={align} />
                {stats.length === 0 ? (
                    <p style={{ textAlign: 'center', color: mutedFg, margin: 0 }}>No stats yet — add some in the Website Builder.</p>
                ) : (
                    <div className={`tb-stats-grid tb-stats-cols-${cols}`} style={{
                        display: 'grid',
                        gridTemplateColumns: `repeat(${cols}, minmax(0, 1fr))`,
                        gap: 'clamp(20px, 3vw, 40px)',
                    }}>
                        {stats.map((s, i) => (
                            <div key={i} style={{ textAlign: align === 'center' ? 'center' : 'left', display: 'flex', flexDirection: 'column', gap: 6 }}>
                                <StatNumber stat={s} align={align} fg={fg} accent={accent} animate={animate} />
                                <StatBody stat={s} align={align} fg={fg} mutedFg={mutedFg} />
                            </div>
                        ))}
                    </div>
                )}
            </div>
            <SharedResponsive />
        </section>
    );
}

// ── Variant: cards (each stat in an elevated rounded card) ─────────────────

function Cards({ settings }: { settings: StatsSettings }) {
    const stats = settings.stats ?? [];
    const cols = Math.max(2, Math.min(6, Number(settings.columns) || 4));
    const align = settings.align ?? 'center';
    const animate = settings.animate !== false;
    const fg = settings.fg ?? '#0f172a';
    const mutedFg = settings.mutedFg ?? '#64748b';
    const accent = settings.accent ?? '#0284c7';

    return (
        <section style={{
            background: settings.bg ?? '#f8fafc',
            color: fg,
            padding: 'clamp(48px, 8vw, 96px) clamp(16px, 4vw, 32px)',
            fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
        }}>
            <div style={{ maxWidth: '1200px', margin: '0 auto' }}>
                <SectionHeader heading={settings.heading} subtitle={settings.subtitle} mutedFg={mutedFg} align={align} />
                {stats.length === 0 ? (
                    <p style={{ textAlign: 'center', color: mutedFg, margin: 0 }}>No stats yet — add some in the Website Builder.</p>
                ) : (
                    <div className={`tb-stats-grid tb-stats-cols-${cols}`} style={{
                        display: 'grid',
                        gridTemplateColumns: `repeat(${cols}, minmax(0, 1fr))`,
                        gap: 'clamp(16px, 2vw, 24px)',
                    }}>
                        {stats.map((s, i) => (
                            <article key={i} style={{
                                background: '#ffffff',
                                borderRadius: 16,
                                padding: 'clamp(20px, 2.4vw, 28px)',
                                textAlign: align === 'center' ? 'center' : 'left',
                                display: 'flex',
                                flexDirection: 'column',
                                gap: 6,
                                boxShadow: '0 1px 2px rgba(15,23,42,.05), 0 8px 20px -10px rgba(15,23,42,.18)',
                            }}>
                                <StatNumber stat={s} align={align} fg={fg} accent={accent} animate={animate} />
                                <StatBody stat={s} align={align} fg={fg} mutedFg={mutedFg} />
                            </article>
                        ))}
                    </div>
                )}
            </div>
            <SharedResponsive />
        </section>
    );
}

// ── Variant: dividers (vertical hairlines between stats) ───────────────────

function Dividers({ settings }: { settings: StatsSettings }) {
    const stats = settings.stats ?? [];
    const cols = Math.max(2, Math.min(6, Number(settings.columns) || 4));
    const animate = settings.animate !== false;
    const fg = settings.fg ?? '#0f172a';
    const mutedFg = settings.mutedFg ?? '#64748b';
    const accent = settings.accent ?? '#0284c7';

    return (
        <section style={{
            background: settings.bg ?? '#ffffff',
            color: fg,
            padding: 'clamp(48px, 8vw, 96px) clamp(16px, 4vw, 32px)',
            fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
        }}>
            <div style={{ maxWidth: '1200px', margin: '0 auto' }}>
                <SectionHeader heading={settings.heading} subtitle={settings.subtitle} mutedFg={mutedFg} align="center" />
                {stats.length === 0 ? (
                    <p style={{ textAlign: 'center', color: mutedFg, margin: 0 }}>No stats yet — add some in the Website Builder.</p>
                ) : (
                    <div className={`tb-stats-divs tb-stats-divs-cols-${cols}`} style={{
                        display: 'grid',
                        gridTemplateColumns: `repeat(${cols}, minmax(0, 1fr))`,
                        borderTop: '1px solid rgba(15,23,42,0.10)',
                        borderBottom: '1px solid rgba(15,23,42,0.10)',
                    }}>
                        {stats.map((s, i) => (
                            <div key={i} style={{
                                padding: 'clamp(24px, 3vw, 36px) clamp(12px, 2vw, 20px)',
                                textAlign: 'center',
                                display: 'flex',
                                flexDirection: 'column',
                                gap: 6,
                                borderLeft: i === 0 ? 'none' : '1px solid rgba(15,23,42,0.10)',
                            }}>
                                <StatNumber stat={s} align="center" fg={fg} accent={accent} animate={animate} />
                                <StatBody stat={s} align="center" fg={fg} mutedFg={mutedFg} />
                            </div>
                        ))}
                    </div>
                )}
            </div>
            <style>{`
                @media (max-width: 900px) {
                    .tb-stats-divs.tb-stats-divs-cols-4,
                    .tb-stats-divs.tb-stats-divs-cols-5,
                    .tb-stats-divs.tb-stats-divs-cols-6 {
                        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                    }
                    .tb-stats-divs.tb-stats-divs-cols-3 {
                        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                    }
                    /* Re-figure left borders for the 2-col wrap */
                    .tb-stats-divs > div:nth-child(odd) { border-left: none !important; }
                    .tb-stats-divs > div:nth-child(even) { border-left: 1px solid rgba(15,23,42,0.10) !important; }
                    .tb-stats-divs > div:nth-child(n+3) { border-top: 1px solid rgba(15,23,42,0.10); }
                }
                @media (max-width: 560px) {
                    .tb-stats-divs { grid-template-columns: 1fr !important; }
                    .tb-stats-divs > div { border-left: none !important; }
                    .tb-stats-divs > div + div { border-top: 1px solid rgba(15,23,42,0.10) !important; }
                }
            `}</style>
        </section>
    );
}

// ── Variant: gradient (vivid primary-driven background) ────────────────────

function Gradient({ settings }: { settings: StatsSettings }) {
    const stats = settings.stats ?? [];
    const cols = Math.max(2, Math.min(6, Number(settings.columns) || 4));
    const align = settings.align ?? 'center';
    const animate = settings.animate !== false;
    // Light-on-dark defaults; respect explicit overrides.
    const fg      = settings.fg && settings.fg !== '#0f172a' ? settings.fg : '#ffffff';
    const mutedFg = settings.mutedFg && settings.mutedFg !== '#64748b' ? settings.mutedFg : 'rgba(255,255,255,0.85)';
    const accent  = settings.accent && settings.accent !== '#0284c7' ? settings.accent : '#ffffff';

    return (
        <section style={{
            position: 'relative',
            color: fg,
            padding: 'clamp(56px, 9vw, 104px) clamp(16px, 4vw, 32px)',
            fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
            background: 'linear-gradient(135deg, var(--primary, #0284c7) 0%, color-mix(in srgb, var(--primary, #0284c7) 60%, black) 100%)',
            overflow: 'hidden',
        }}>
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
            <div style={{ position: 'relative', maxWidth: '1200px', margin: '0 auto' }}>
                <SectionHeader heading={settings.heading} subtitle={settings.subtitle} mutedFg={mutedFg} align={align} color={fg} />
                {stats.length === 0 ? (
                    <p style={{ textAlign: 'center', color: mutedFg, margin: 0 }}>No stats yet — add some in the Website Builder.</p>
                ) : (
                    <div className={`tb-stats-grid tb-stats-cols-${cols}`} style={{
                        display: 'grid',
                        gridTemplateColumns: `repeat(${cols}, minmax(0, 1fr))`,
                        gap: 'clamp(20px, 3vw, 40px)',
                    }}>
                        {stats.map((s, i) => (
                            <div key={i} style={{ textAlign: align === 'center' ? 'center' : 'left', display: 'flex', flexDirection: 'column', gap: 6 }}>
                                <StatNumber stat={s} align={align} fg={fg} accent={accent} animate={animate} />
                                <StatBody stat={s} align={align} fg={fg} mutedFg={mutedFg} />
                            </div>
                        ))}
                    </div>
                )}
            </div>
            <SharedResponsive />
        </section>
    );
}

// ── Shared responsive rules for the row-style grids ────────────────────────

function SharedResponsive() {
    return (
        <style>{`
            @media (max-width: 900px) {
                .tb-stats-grid.tb-stats-cols-4,
                .tb-stats-grid.tb-stats-cols-5,
                .tb-stats-grid.tb-stats-cols-6 { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
                .tb-stats-grid.tb-stats-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)) !important; }
            }
            @media (max-width: 560px) {
                .tb-stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
            }
        `}</style>
    );
}
