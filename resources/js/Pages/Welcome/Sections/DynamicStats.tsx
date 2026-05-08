import { useEffect, useRef, useState } from 'react';

interface Stat {
    value?: string;
    prefix?: string;
    suffix?: string;
    label?: string;
    description?: string;
}

interface StatsSettings {
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

/** Formats a number with locale-correct thousands separators and the same decimal precision. */
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
                        // Ease-out cubic — fast then settles.
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

    if (!parsed) {
        // Non-numeric value (e.g. "Custom") — render verbatim, no animation.
        return <span ref={ref}>{targetRaw}</span>;
    }
    return <span ref={ref}>{formatNumber(value, decimals)}</span>;
}

export default function DynamicStats({ settings }: { settings: StatsSettings }) {
    const stats   = settings.stats ?? [];
    const cols    = Math.max(2, Math.min(6, Number(settings.columns) || 4));
    const align   = settings.align ?? 'center';
    const animate = settings.animate !== false;

    const fg      = settings.fg ?? '#0f172a';
    const mutedFg = settings.mutedFg ?? '#64748b';
    const accent  = settings.accent ?? '#0284c7';

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
                    <div style={{
                        textAlign: align === 'center' ? 'center' : 'left',
                        marginBottom: 'clamp(28px, 4vw, 48px)',
                    }}>
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
                                marginInline: align === 'center' ? 'auto' : '0',
                            }}>{settings.subtitle}</p>
                        )}
                    </div>
                )}

                {stats.length > 0 ? (
                    <div
                        className={`tb-stats-grid tb-stats-cols-${cols}`}
                        style={{
                            display: 'grid',
                            gridTemplateColumns: `repeat(${cols}, minmax(0, 1fr))`,
                            gap: 'clamp(20px, 3vw, 40px)',
                        }}
                    >
                        {stats.map((s, i) => (
                            <div
                                key={i}
                                style={{
                                    textAlign: align === 'center' ? 'center' : 'left',
                                    display: 'flex',
                                    flexDirection: 'column',
                                    gap: 6,
                                }}
                            >
                                <div style={{
                                    display: 'flex',
                                    alignItems: 'baseline',
                                    gap: 2,
                                    justifyContent: align === 'center' ? 'center' : 'flex-start',
                                    color: fg,
                                    lineHeight: 1,
                                    flexWrap: 'wrap',
                                }}>
                                    {s.prefix && (
                                        <span style={{ fontSize: 'clamp(1.25rem, 2vw, 1.625rem)', fontWeight: 700, color: accent }}>
                                            {s.prefix}
                                        </span>
                                    )}
                                    <span style={{
                                        fontSize: 'clamp(2.25rem, 5vw, 3.5rem)',
                                        fontWeight: 800,
                                        letterSpacing: '-0.03em',
                                        fontVariantNumeric: 'tabular-nums',
                                    }}>
                                        <CountUp targetRaw={s.value} animate={animate} />
                                    </span>
                                    {s.suffix && (
                                        <span style={{ fontSize: 'clamp(1.25rem, 2vw, 1.625rem)', fontWeight: 700, color: accent }}>
                                            {s.suffix}
                                        </span>
                                    )}
                                </div>
                                {s.label && (
                                    <div style={{
                                        marginTop: 6,
                                        fontSize: '0.9375rem',
                                        fontWeight: 600,
                                        color: fg,
                                        letterSpacing: '-0.005em',
                                    }}>
                                        {s.label}
                                    </div>
                                )}
                                {s.description && (
                                    <div style={{
                                        fontSize: '0.8125rem',
                                        color: mutedFg,
                                        lineHeight: 1.5,
                                    }}>
                                        {s.description}
                                    </div>
                                )}
                            </div>
                        ))}
                    </div>
                ) : (
                    <p style={{ textAlign: 'center', color: mutedFg, margin: 0 }}>
                        No stats yet — add some in the Website Builder.
                    </p>
                )}
            </div>

            <style>{`
                @media (max-width: 900px) {
                    .tb-stats-grid.tb-stats-cols-4,
                    .tb-stats-grid.tb-stats-cols-5,
                    .tb-stats-grid.tb-stats-cols-6 {
                        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                    }
                    .tb-stats-grid.tb-stats-cols-3 {
                        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
                    }
                }
                @media (max-width: 560px) {
                    .tb-stats-grid {
                        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                    }
                }
            `}</style>
        </section>
    );
}
