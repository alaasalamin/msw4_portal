interface Review {
    name?: string;
    role?: string;
    rating?: number | string;
    quote?: string;
    date?: string;
    photo?: string | null;
}

interface ReviewsSettings {
    heading?: string;
    subtitle?: string;
    reviews?: Review[];
    columns?: number | string;
    showPhotos?: boolean;
    showStars?: boolean;
    showRole?: boolean;
    showDate?: boolean;
    bg?: string;
    fg?: string;
    cardBg?: string;
    mutedFg?: string;
    starColor?: string;
}

function initials(name?: string): string {
    if (!name) return '?';
    return name
        .split(' ')
        .filter(Boolean)
        .slice(0, 2)
        .map((p) => p.charAt(0).toUpperCase())
        .join('');
}

function StarRow({ value, color, mutedColor }: { value: number; color: string; mutedColor: string }) {
    const v = Math.max(0, Math.min(5, Math.round(value)));
    return (
        <div style={{ display: 'inline-flex', gap: 2, color }}>
            {[1, 2, 3, 4, 5].map((i) => (
                <svg key={i} width="16" height="16" viewBox="0 0 24 24" fill={i <= v ? 'currentColor' : 'none'} stroke="currentColor" strokeWidth="1.5" style={{ color: i <= v ? color : mutedColor }}>
                    <path strokeLinecap="round" strokeLinejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                </svg>
            ))}
        </div>
    );
}

export default function DynamicReviews({ settings }: { settings: ReviewsSettings }) {
    const reviews = settings.reviews ?? [];
    const cols = Math.max(1, Math.min(3, Number(settings.columns) || 3));
    const showPhotos = settings.showPhotos !== false;
    const showStars  = settings.showStars  !== false;
    const showRole   = settings.showRole   !== false;
    const showDate   = settings.showDate   !== false;

    const fg        = settings.fg        ?? '#0f172a';
    const mutedFg   = settings.mutedFg   ?? '#64748b';
    const cardBg    = settings.cardBg    ?? '#ffffff';
    const starColor = settings.starColor ?? '#facc15';

    return (
        <section
            style={{
                background: settings.bg ?? '#f8fafc',
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

                {reviews.length > 0 ? (
                    <div
                        className={`tb-reviews-grid tb-reviews-cols-${cols}`}
                        style={{
                            display: 'grid',
                            gap: 'clamp(20px, 2.5vw, 28px)',
                            gridTemplateColumns: `repeat(${cols}, minmax(0, 1fr))`,
                        }}
                    >
                        {reviews.map((r, i) => (
                            <article
                                key={i}
                                style={{
                                    background: cardBg,
                                    borderRadius: 16,
                                    padding: 'clamp(20px, 2.5vw, 28px)',
                                    display: 'flex',
                                    flexDirection: 'column',
                                    gap: 14,
                                    boxShadow: '0 1px 2px rgba(15,23,42,.05), 0 4px 12px -4px rgba(15,23,42,.08)',
                                }}
                            >
                                {showStars && r.rating != null && (
                                    <StarRow value={Number(r.rating) || 0} color={starColor} mutedColor="rgba(0,0,0,0.12)" />
                                )}

                                <p style={{
                                    margin: 0,
                                    fontSize: '0.9375rem',
                                    lineHeight: 1.65,
                                    color: mutedFg,
                                }}>
                                    “{r.quote ?? ''}”
                                </p>

                                <div style={{
                                    marginTop: 'auto',
                                    paddingTop: 12,
                                    borderTop: '1px solid rgba(15,23,42,0.06)',
                                    display: 'flex',
                                    alignItems: 'center',
                                    gap: 12,
                                }}>
                                    {showPhotos && (
                                        <div style={{
                                            width: 40,
                                            height: 40,
                                            flexShrink: 0,
                                            borderRadius: '9999px',
                                            background: '#e2e8f0',
                                            color: '#475569',
                                            display: 'flex',
                                            alignItems: 'center',
                                            justifyContent: 'center',
                                            fontSize: '0.8125rem',
                                            fontWeight: 700,
                                            overflow: 'hidden',
                                        }}>
                                            {r.photo ? (
                                                <img
                                                    src={`/storage/${r.photo}`}
                                                    alt={r.name ?? 'Reviewer'}
                                                    style={{ width: '100%', height: '100%', objectFit: 'cover', display: 'block' }}
                                                />
                                            ) : (
                                                initials(r.name)
                                            )}
                                        </div>
                                    )}
                                    <div style={{ minWidth: 0, flex: 1, lineHeight: 1.3 }}>
                                        {r.name && (
                                            <div style={{ color: fg, fontWeight: 700, fontSize: '0.9375rem', letterSpacing: '-0.005em' }}>
                                                {r.name}
                                            </div>
                                        )}
                                        {showRole && r.role && (
                                            <div style={{ color: mutedFg, fontSize: '0.8125rem', marginTop: 1 }}>
                                                {r.role}
                                            </div>
                                        )}
                                    </div>
                                    {showDate && r.date && (
                                        <div style={{ color: mutedFg, fontSize: '0.75rem', flexShrink: 0 }}>
                                            {r.date}
                                        </div>
                                    )}
                                </div>
                            </article>
                        ))}
                    </div>
                ) : (
                    <p style={{ textAlign: 'center', color: mutedFg, margin: 0 }}>
                        No reviews yet — add some in the Theme Builder.
                    </p>
                )}
            </div>

            <style>{`
                @media (max-width: 900px) {
                    .tb-reviews-grid.tb-reviews-cols-3 {
                        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                    }
                }
                @media (max-width: 560px) {
                    .tb-reviews-grid {
                        grid-template-columns: 1fr !important;
                    }
                }
            `}</style>
        </section>
    );
}
