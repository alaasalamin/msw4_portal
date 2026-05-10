import { useState } from 'react';

interface Review {
    name?: string;
    role?: string;
    rating?: number | string;
    quote?: string;
    date?: string;
    photo?: string | null;
}

interface ReviewsSettings {
    variant?: 'cards' | 'single_featured' | 'bubbles' | 'list';
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

function Avatar({ review, mutedFg, fg, sizePx = 40 }: { review: Review; mutedFg: string; fg: string; sizePx?: number }) {
    return (
        <div style={{
            width: sizePx,
            height: sizePx,
            flexShrink: 0,
            borderRadius: '9999px',
            background: '#e2e8f0',
            color: '#475569',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
            fontSize: sizePx >= 56 ? '1rem' : '0.8125rem',
            fontWeight: 700,
            overflow: 'hidden',
        }}>
            {review.photo ? (
                <img src={`/storage/${review.photo}`} alt={review.name ?? 'Reviewer'} style={{ width: '100%', height: '100%', objectFit: 'cover', display: 'block' }} />
            ) : initials(review.name)}
        </div>
    );
}

// ── Dispatcher ──────────────────────────────────────────────────────────────

export default function DynamicReviews({ settings }: { settings: ReviewsSettings }) {
    const variant = settings.variant ?? 'cards';
    switch (variant) {
        case 'single_featured': return <Single   settings={settings} />;
        case 'bubbles':         return <Bubbles  settings={settings} />;
        case 'list':            return <ListView settings={settings} />;
        case 'cards':
        default:                return <Cards    settings={settings} />;
    }
}

// ── Shared header chrome ────────────────────────────────────────────────────

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

function Wrapper({ settings, children }: { settings: ReviewsSettings; children: React.ReactNode }) {
    return (
        <section
            style={{
                background: settings.bg ?? '#f8fafc',
                color: settings.fg ?? '#0f172a',
                padding: 'clamp(48px, 8vw, 96px) clamp(16px, 4vw, 32px)',
                fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
            }}
        >
            <div style={{ maxWidth: '1200px', margin: '0 auto' }}>{children}</div>
        </section>
    );
}

// ── Variant: cards (existing default) ───────────────────────────────────────

function Cards({ settings }: { settings: ReviewsSettings }) {
    const reviews = settings.reviews ?? [];
    const cols = Math.max(1, Math.min(3, Number(settings.columns) || 3));
    const fg = settings.fg ?? '#0f172a';
    const mutedFg = settings.mutedFg ?? '#64748b';
    const cardBg = settings.cardBg ?? '#ffffff';
    const starColor = settings.starColor ?? '#facc15';
    const showPhotos = settings.showPhotos !== false;
    const showStars = settings.showStars !== false;
    const showRole = settings.showRole !== false;
    const showDate = settings.showDate !== false;

    // Carousel: show `cols` reviews at a time. Once there are more
    // reviews than visible slots, prev/next arrows step the window
    // through the list (wrapping around at both ends) so visitors can
    // read every review without the section growing unbounded.
    const [start, setStart] = useState(0);
    const total = reviews.length;
    const isCarousel = total > cols;
    const visible: Review[] = isCarousel
        ? Array.from({ length: cols }, (_, k) => reviews[(start + k) % total])
        : reviews;
    const next = () => setStart((s) => (s + 1) % Math.max(total, 1));
    const prev = () => setStart((s) => (s - 1 + Math.max(total, 1)) % Math.max(total, 1));

    return (
        <Wrapper settings={settings}>
            <SectionHeader heading={settings.heading} subtitle={settings.subtitle} mutedFg={mutedFg} />
            {reviews.length === 0 ? (
                <p style={{ textAlign: 'center', color: mutedFg, margin: 0 }}>No reviews yet — add some in the Website Builder.</p>
            ) : (
                <div style={{ position: 'relative' }}>
                {isCarousel && (
                    <CarouselArrow direction="prev" onClick={prev} fg={fg} cardBg={cardBg} />
                )}
                <div className={`tb-reviews-grid tb-reviews-cols-${cols}`} style={{
                    display: 'grid',
                    gap: 'clamp(20px, 2.5vw, 28px)',
                    gridTemplateColumns: `repeat(${cols}, minmax(0, 1fr))`,
                }}>
                    {visible.map((r, i) => (
                        <article key={i} style={{
                            background: cardBg,
                            borderRadius: 16,
                            padding: 'clamp(20px, 2.5vw, 28px)',
                            display: 'flex',
                            flexDirection: 'column',
                            gap: 14,
                            boxShadow: '0 1px 2px rgba(15,23,42,.05), 0 4px 12px -4px rgba(15,23,42,.08)',
                        }}>
                            {showStars && r.rating != null && (
                                <StarRow value={Number(r.rating) || 0} color={starColor} mutedColor="rgba(0,0,0,0.12)" />
                            )}
                            <p style={{ margin: 0, fontSize: '0.9375rem', lineHeight: 1.65, color: mutedFg }}>
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
                                {showPhotos && <Avatar review={r} mutedFg={mutedFg} fg={fg} />}
                                <div style={{ minWidth: 0, flex: 1, lineHeight: 1.3 }}>
                                    {r.name && (
                                        <div style={{ color: fg, fontWeight: 700, fontSize: '0.9375rem', letterSpacing: '-0.005em' }}>{r.name}</div>
                                    )}
                                    {showRole && r.role && (
                                        <div style={{ color: mutedFg, fontSize: '0.8125rem', marginTop: 1 }}>{r.role}</div>
                                    )}
                                </div>
                                {showDate && r.date && (
                                    <div style={{ color: mutedFg, fontSize: '0.75rem', flexShrink: 0 }}>{r.date}</div>
                                )}
                            </div>
                        </article>
                    ))}
                </div>
                {isCarousel && (
                    <CarouselArrow direction="next" onClick={next} fg={fg} cardBg={cardBg} />
                )}
                </div>
            )}
            {isCarousel && (
                <div style={{ marginTop: 24, display: 'flex', justifyContent: 'center', gap: 8 }}>
                    {reviews.map((_, i) => {
                        const active = i === start;
                        return (
                            <button
                                key={i}
                                type="button"
                                onClick={() => setStart(i)}
                                aria-label={`Show review ${i + 1}`}
                                style={{
                                    width: active ? 24 : 8,
                                    height: 8,
                                    borderRadius: 9999,
                                    border: 'none',
                                    cursor: 'pointer',
                                    background: active ? fg : 'rgba(15,23,42,0.2)',
                                    transition: 'width 200ms ease, background 200ms ease',
                                }}
                            />
                        );
                    })}
                </div>
            )}
            <style>{`
                @media (max-width: 900px) {
                    .tb-reviews-grid.tb-reviews-cols-3 { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
                }
                @media (max-width: 560px) {
                    .tb-reviews-grid { grid-template-columns: 1fr !important; }
                }
            `}</style>
        </Wrapper>
    );
}

function CarouselArrow({ direction, onClick, fg, cardBg }: {
    direction: 'prev' | 'next';
    onClick: () => void;
    fg: string;
    cardBg: string;
}) {
    const isPrev = direction === 'prev';
    return (
        <button
            type="button"
            onClick={onClick}
            aria-label={isPrev ? 'Previous reviews' : 'Next reviews'}
            style={{
                position: 'absolute',
                top: '50%',
                [isPrev ? 'left' : 'right']: 'clamp(-20px, -1.5vw, -8px)',
                transform: 'translateY(-50%)',
                width: 44,
                height: 44,
                borderRadius: 9999,
                border: '1px solid rgba(15,23,42,0.10)',
                background: cardBg,
                color: fg,
                cursor: 'pointer',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                boxShadow: '0 2px 6px rgba(15,23,42,0.10), 0 8px 24px -8px rgba(15,23,42,0.18)',
                zIndex: 2,
            }}
        >
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                {isPrev
                    ? <polyline points="15 18 9 12 15 6" />
                    : <polyline points="9 18 15 12 9 6" />}
            </svg>
        </button>
    );
}

// ── Variant: single_featured (one rotating quote with prev/next) ───────────

function Single({ settings }: { settings: ReviewsSettings }) {
    const reviews = settings.reviews ?? [];
    const fg = settings.fg ?? '#0f172a';
    const mutedFg = settings.mutedFg ?? '#64748b';
    const cardBg = settings.cardBg ?? '#ffffff';
    const starColor = settings.starColor ?? '#facc15';
    const showPhotos = settings.showPhotos !== false;
    const showStars = settings.showStars !== false;
    const showRole = settings.showRole !== false;

    const [idx, setIdx] = useState(0);
    if (reviews.length === 0) {
        return (
            <Wrapper settings={settings}>
                <SectionHeader heading={settings.heading} subtitle={settings.subtitle} mutedFg={mutedFg} />
                <p style={{ textAlign: 'center', color: mutedFg, margin: 0 }}>No reviews yet — add some in the Website Builder.</p>
            </Wrapper>
        );
    }

    const i = ((idx % reviews.length) + reviews.length) % reviews.length;
    const r = reviews[i];
    const total = reviews.length;
    const prev = () => setIdx((n) => n - 1);
    const next = () => setIdx((n) => n + 1);

    return (
        <Wrapper settings={settings}>
            <SectionHeader heading={settings.heading} subtitle={settings.subtitle} mutedFg={mutedFg} />
            <article style={{
                position: 'relative',
                maxWidth: '900px',
                margin: '0 auto',
                background: cardBg,
                borderRadius: 20,
                padding: 'clamp(28px, 4vw, 56px)',
                display: 'flex',
                flexDirection: 'column',
                alignItems: 'center',
                gap: 20,
                textAlign: 'center',
                boxShadow: '0 2px 4px rgba(15,23,42,.06), 0 14px 28px -14px rgba(15,23,42,.16)',
            }}>
                <span aria-hidden="true" style={{
                    position: 'absolute', top: 12, left: 24,
                    fontSize: 'clamp(4rem, 8vw, 6rem)',
                    fontFamily: 'Georgia, "Times New Roman", serif',
                    color: 'var(--primary, #0284c7)',
                    opacity: 0.2,
                    lineHeight: 1,
                    userSelect: 'none',
                }}>“</span>
                {showStars && r.rating != null && (
                    <StarRow value={Number(r.rating) || 0} color={starColor} mutedColor="rgba(0,0,0,0.12)" />
                )}
                <blockquote style={{
                    margin: 0,
                    fontSize: 'clamp(1.125rem, 2vw, 1.5rem)',
                    fontStyle: 'italic',
                    fontWeight: 500,
                    lineHeight: 1.55,
                    color: fg,
                    maxWidth: '52ch',
                }}>
                    {r.quote ?? ''}
                </blockquote>
                <div style={{ display: 'flex', alignItems: 'center', gap: 12, marginTop: 6 }}>
                    {showPhotos && <Avatar review={r} mutedFg={mutedFg} fg={fg} sizePx={56} />}
                    <div style={{ textAlign: 'left' }}>
                        {r.name && (
                            <div style={{ color: fg, fontWeight: 700, fontSize: '1rem', letterSpacing: '-0.005em' }}>{r.name}</div>
                        )}
                        {showRole && r.role && (
                            <div style={{ color: mutedFg, fontSize: '0.875rem', marginTop: 2 }}>{r.role}</div>
                        )}
                    </div>
                </div>

                {total > 1 && (
                    <div style={{ marginTop: 4, display: 'flex', alignItems: 'center', gap: 16 }}>
                        <button
                            type="button"
                            aria-label="Previous review"
                            onClick={prev}
                            style={btnRound(mutedFg)}
                        >
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2"><path strokeLinecap="round" strokeLinejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
                        </button>
                        <span style={{ fontSize: '0.75rem', color: mutedFg, letterSpacing: '0.05em' }}>{i + 1} / {total}</span>
                        <button
                            type="button"
                            aria-label="Next review"
                            onClick={next}
                            style={btnRound(mutedFg)}
                        >
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2"><path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                        </button>
                    </div>
                )}
            </article>
        </Wrapper>
    );
}

function btnRound(color: string): React.CSSProperties {
    return {
        width: 36,
        height: 36,
        borderRadius: 9999,
        border: `1px solid color-mix(in srgb, ${color} 35%, transparent)`,
        background: 'transparent',
        color,
        cursor: 'pointer',
        display: 'inline-flex',
        alignItems: 'center',
        justifyContent: 'center',
    };
}

// ── Variant: bubbles (chat-bubble style with a tail) ───────────────────────

function Bubbles({ settings }: { settings: ReviewsSettings }) {
    const reviews = settings.reviews ?? [];
    const cols = Math.max(1, Math.min(3, Number(settings.columns) || 3));
    const fg = settings.fg ?? '#0f172a';
    const mutedFg = settings.mutedFg ?? '#64748b';
    const cardBg = settings.cardBg ?? '#ffffff';
    const starColor = settings.starColor ?? '#facc15';
    const showPhotos = settings.showPhotos !== false;
    const showStars = settings.showStars !== false;
    const showRole = settings.showRole !== false;

    return (
        <Wrapper settings={settings}>
            <SectionHeader heading={settings.heading} subtitle={settings.subtitle} mutedFg={mutedFg} />
            {reviews.length === 0 ? (
                <p style={{ textAlign: 'center', color: mutedFg, margin: 0 }}>No reviews yet — add some in the Website Builder.</p>
            ) : (
                <div className={`tb-reviews-bubbles-grid tb-reviews-bubbles-cols-${cols}`} style={{
                    display: 'grid',
                    gap: 'clamp(28px, 3.5vw, 44px)',
                    gridTemplateColumns: `repeat(${cols}, minmax(0, 1fr))`,
                }}>
                    {reviews.map((r, i) => (
                        <div key={i} style={{ display: 'flex', flexDirection: 'column', gap: 18 }}>
                            <div style={{
                                position: 'relative',
                                background: cardBg,
                                borderRadius: 18,
                                padding: 'clamp(18px, 2.4vw, 24px)',
                                color: fg,
                                boxShadow: '0 1px 2px rgba(15,23,42,.05), 0 6px 14px -8px rgba(15,23,42,.16)',
                            }}>
                                {showStars && r.rating != null && (
                                    <div style={{ marginBottom: 10 }}>
                                        <StarRow value={Number(r.rating) || 0} color={starColor} mutedColor="rgba(0,0,0,0.12)" />
                                    </div>
                                )}
                                <p style={{ margin: 0, fontSize: '0.9375rem', lineHeight: 1.6, color: mutedFg }}>
                                    {r.quote ?? ''}
                                </p>
                                {/* tail */}
                                <span aria-hidden="true" style={{
                                    position: 'absolute',
                                    bottom: -10,
                                    left: 28,
                                    width: 20,
                                    height: 20,
                                    background: cardBg,
                                    transform: 'rotate(45deg)',
                                    boxShadow: '4px 4px 8px -3px rgba(15,23,42,0.10)',
                                    zIndex: 0,
                                }} />
                            </div>
                            <div style={{ display: 'flex', alignItems: 'center', gap: 12, paddingLeft: 14 }}>
                                {showPhotos && <Avatar review={r} mutedFg={mutedFg} fg={fg} />}
                                <div>
                                    {r.name && (
                                        <div style={{ color: fg, fontWeight: 700, fontSize: '0.9375rem' }}>{r.name}</div>
                                    )}
                                    {showRole && r.role && (
                                        <div style={{ color: mutedFg, fontSize: '0.8125rem', marginTop: 2 }}>{r.role}</div>
                                    )}
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            )}
            <style>{`
                @media (max-width: 900px) {
                    .tb-reviews-bubbles-grid.tb-reviews-bubbles-cols-3 { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
                }
                @media (max-width: 768px) {
                    .tb-reviews-bubbles-grid.tb-reviews-bubbles-cols-1,
                    .tb-reviews-bubbles-grid.tb-reviews-bubbles-cols-2,
                    .tb-reviews-bubbles-grid.tb-reviews-bubbles-cols-3 { grid-template-columns: 1fr !important; }
                }
            `}</style>
        </Wrapper>
    );
}

// ── Variant: list (vertical, hairline dividers, no card bg) ────────────────

function ListView({ settings }: { settings: ReviewsSettings }) {
    const reviews = settings.reviews ?? [];
    const fg = settings.fg ?? '#0f172a';
    const mutedFg = settings.mutedFg ?? '#64748b';
    const starColor = settings.starColor ?? '#facc15';
    const showPhotos = settings.showPhotos !== false;
    const showStars = settings.showStars !== false;
    const showRole = settings.showRole !== false;
    const showDate = settings.showDate !== false;

    return (
        <Wrapper settings={settings}>
            <SectionHeader heading={settings.heading} subtitle={settings.subtitle} mutedFg={mutedFg} />
            <div style={{ maxWidth: '820px', margin: '0 auto' }}>
                {reviews.length === 0 ? (
                    <p style={{ textAlign: 'center', color: mutedFg, margin: 0 }}>No reviews yet — add some in the Website Builder.</p>
                ) : (
                    <ul style={{ listStyle: 'none', padding: 0, margin: 0 }}>
                        {reviews.map((r, i) => (
                            <li key={i} style={{
                                paddingTop: i === 0 ? 0 : 'clamp(20px, 2.4vw, 28px)',
                                paddingBottom: 'clamp(20px, 2.4vw, 28px)',
                                borderBottom: i === reviews.length - 1 ? 'none' : '1px solid rgba(15,23,42,0.08)',
                                display: 'flex',
                                gap: 18,
                                alignItems: 'flex-start',
                            }}>
                                {showPhotos && <Avatar review={r} mutedFg={mutedFg} fg={fg} sizePx={48} />}
                                <div style={{ flex: 1, minWidth: 0 }}>
                                    <div style={{ display: 'flex', alignItems: 'baseline', gap: 12, flexWrap: 'wrap' }}>
                                        {r.name && (
                                            <span style={{ color: fg, fontWeight: 700, fontSize: '0.9375rem' }}>{r.name}</span>
                                        )}
                                        {showRole && r.role && (
                                            <span style={{ color: mutedFg, fontSize: '0.8125rem' }}>{r.role}</span>
                                        )}
                                        {showDate && r.date && (
                                            <span style={{ color: mutedFg, fontSize: '0.75rem', marginLeft: 'auto' }}>{r.date}</span>
                                        )}
                                    </div>
                                    {showStars && r.rating != null && (
                                        <div style={{ marginTop: 6 }}>
                                            <StarRow value={Number(r.rating) || 0} color={starColor} mutedColor="rgba(0,0,0,0.12)" />
                                        </div>
                                    )}
                                    <p style={{ margin: '8px 0 0', color: mutedFg, lineHeight: 1.65, fontSize: '0.9375rem' }}>
                                        {r.quote ?? ''}
                                    </p>
                                </div>
                            </li>
                        ))}
                    </ul>
                )}
            </div>
        </Wrapper>
    );
}
