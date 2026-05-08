import { useCallback, useEffect, useRef, useState } from 'react';
import { usePage } from '@inertiajs/react';

interface PostCategoryRef { name: string; slug: string; href: string }
interface Post {
    slug?: string;
    title: string;
    excerpt?: string | null;
    href: string;
    image?: string | null;
    publishedAt?: string | null;
    category?: PostCategoryRef | null;
}

interface BlogCarouselSettings {
    heading?: string;
    subtitle?: string;
    source?: 'latest' | 'category' | 'manual';
    limit?: number | string;
    categorySlug?: string | null;
    manualSlugs?: string[];
    aspect?: '16:9' | '4:3' | '21:9' | '3:2';
    showCategory?: boolean;
    showDate?: boolean;
    showExcerpt?: boolean;
    showReadMore?: boolean;
    readMoreLabel?: string;
    autoplay?: boolean | string | number;
    autoplayDelay?: number | string;
    showArrows?: boolean | string | number;
    showDots?: boolean | string | number;
    bg?: string;
    fg?: string;
    mutedFg?: string;
    accent?: string;
}

interface SharedProps { homepage?: { posts?: Post[] } }

const ASPECT: Record<string, string> = {
    '16:9': '16 / 9',
    '4:3':  '4 / 3',
    '21:9': '21 / 9',
    '3:2':  '3 / 2',
};

function formatDate(iso?: string | null): string {
    if (!iso) return '';
    const d = new Date(iso);
    if (isNaN(d.getTime())) return '';
    return d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
}

function pickPosts(all: Post[], settings: BlogCarouselSettings): Post[] {
    const limit = Math.max(1, Math.min(20, Number(settings.limit) || 6));
    const source = settings.source ?? 'latest';
    if (source === 'manual') {
        const wanted = settings.manualSlugs ?? [];
        // Preserve the admin-defined order rather than the natural feed order.
        const bySlug = new Map(all.filter((p) => p.slug).map((p) => [p.slug as string, p]));
        return wanted.map((s) => bySlug.get(s)).filter((p): p is Post => !!p);
    }
    if (source === 'category' && settings.categorySlug) {
        return all.filter((p) => p.category?.slug === settings.categorySlug).slice(0, limit);
    }
    return all.slice(0, limit);
}

export default function DynamicBlogCarousel({ settings }: { settings: BlogCarouselSettings }) {
    const { homepage } = usePage<SharedProps>().props;
    const allPosts = homepage?.posts ?? [];
    const posts = pickPosts(allPosts, settings);

    const bg      = settings.bg ?? '#0f172a';
    const fg      = settings.fg ?? '#ffffff';
    const mutedFg = settings.mutedFg ?? '#cbd5e1';
    const accent  = settings.accent ?? '#0284c7';
    const aspect  = ASPECT[settings.aspect ?? '16:9'] ?? '16 / 9';

    const showCategory = settings.showCategory !== false;
    const showDate     = settings.showDate     !== false;
    const showExcerpt  = settings.showExcerpt  !== false;
    const showReadMore = settings.showReadMore !== false;
    const showArrows   = settings.showArrows   !== false && settings.showArrows !== '0' && settings.showArrows !== 0;
    const showDots     = settings.showDots     !== false && settings.showDots   !== '0' && settings.showDots   !== 0;
    const autoplay     = settings.autoplay     !== false && settings.autoplay   !== '0' && settings.autoplay   !== 0;
    const delayMs      = Math.max(1000, Math.min(30000, (Number(settings.autoplayDelay) || 6) * 1000));
    const readMoreLabel = settings.readMoreLabel || 'Read more';

    const total = posts.length;
    const [index, setIndex] = useState(0);
    const [paused, setPaused] = useState(false);

    const prev = useCallback(() => setIndex((i) => (i - 1 + total) % total), [total]);
    const next = useCallback(() => setIndex((i) => (i + 1) % total), [total]);
    const goTo = useCallback((i: number) => setIndex(((i % total) + total) % total), [total]);

    useEffect(() => {
        if (!autoplay || paused || total <= 1) return;
        const id = window.setInterval(next, delayMs);
        return () => window.clearInterval(id);
    }, [autoplay, paused, total, delayMs, next]);

    const touchStart = useRef<number | null>(null);
    const onTouchStart = (e: React.TouchEvent) => { touchStart.current = e.touches[0].clientX; };
    const onTouchEnd = (e: React.TouchEvent) => {
        if (touchStart.current === null) return;
        const dx = e.changedTouches[0].clientX - touchStart.current;
        if (dx > 40) prev();
        else if (dx < -40) next();
        touchStart.current = null;
    };

    return (
        <section
            style={{
                background: bg,
                color: fg,
                padding: 'clamp(48px, 8vw, 96px) clamp(16px, 4vw, 32px)',
                fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
            }}
        >
            <div style={{ maxWidth: '1200px', margin: '0 auto' }}>
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

                {total === 0 ? (
                    <p style={{ textAlign: 'center', color: mutedFg, margin: 0 }}>
                        No posts to show yet — publish some posts or pick different ones in the Website Builder.
                    </p>
                ) : (
                    <>
                        <div
                            onMouseEnter={() => setPaused(true)}
                            onMouseLeave={() => setPaused(false)}
                            onFocus={() => setPaused(true)}
                            onBlur={() => setPaused(false)}
                            onTouchStart={onTouchStart}
                            onTouchEnd={onTouchEnd}
                            style={{
                                position: 'relative',
                                overflow: 'hidden',
                                borderRadius: 16,
                                aspectRatio: aspect,
                                background: '#0b1220',
                            }}
                            aria-roledescription="carousel"
                            aria-label={settings.heading || 'Blog carousel'}
                        >
                            <div
                                style={{
                                    display: 'flex',
                                    height: '100%',
                                    transform: `translate3d(-${index * 100}%, 0, 0)`,
                                    transition: 'transform 600ms cubic-bezier(0.22, 0.61, 0.36, 1)',
                                    willChange: 'transform',
                                }}
                            >
                                {posts.map((p, i) => (
                                    <Slide
                                        key={p.slug ?? p.href ?? i}
                                        post={p}
                                        active={i === index}
                                        slideIndex={i}
                                        total={total}
                                        accent={accent}
                                        mutedFg={mutedFg}
                                        showCategory={showCategory}
                                        showDate={showDate}
                                        showExcerpt={showExcerpt}
                                        showReadMore={showReadMore}
                                        readMoreLabel={readMoreLabel}
                                    />
                                ))}
                            </div>

                            {showArrows && total > 1 && (
                                <>
                                    <button
                                        type="button"
                                        aria-label="Previous post"
                                        onClick={prev}
                                        style={{ ...arrowBtnStyle, left: 16 }}
                                    >
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                                        </svg>
                                    </button>
                                    <button
                                        type="button"
                                        aria-label="Next post"
                                        onClick={next}
                                        style={{ ...arrowBtnStyle, right: 16 }}
                                    >
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                        </svg>
                                    </button>
                                </>
                            )}
                        </div>

                        {showDots && total > 1 && (
                            <div role="tablist" aria-label="Slide indicators" style={{
                                display: 'flex', justifyContent: 'center', alignItems: 'center', gap: 8, marginTop: 20,
                            }}>
                                {posts.map((_, i) => {
                                    const active = i === index;
                                    return (
                                        <button
                                            key={i}
                                            type="button"
                                            role="tab"
                                            aria-selected={active}
                                            aria-label={`Go to slide ${i + 1}`}
                                            onClick={() => goTo(i)}
                                            style={{
                                                width: active ? 28 : 8,
                                                height: 8,
                                                borderRadius: 9999,
                                                border: 'none',
                                                background: active ? accent : 'rgba(148,163,184,0.45)',
                                                cursor: 'pointer',
                                                padding: 0,
                                                transition: 'width 250ms ease, background 250ms ease',
                                            }}
                                        />
                                    );
                                })}
                            </div>
                        )}
                    </>
                )}
            </div>
        </section>
    );
}

interface SlideProps {
    post: Post;
    active: boolean;
    slideIndex: number;
    total: number;
    accent: string;
    mutedFg: string;
    showCategory: boolean;
    showDate: boolean;
    showExcerpt: boolean;
    showReadMore: boolean;
    readMoreLabel: string;
}

function Slide({ post, active, slideIndex, total, accent, mutedFg, showCategory, showDate, showExcerpt, showReadMore, readMoreLabel }: SlideProps) {
    const dateLabel = showDate ? formatDate(post.publishedAt) : '';
    const showCat = showCategory && post.category;

    return (
        <article
            aria-roledescription="slide"
            aria-label={`${slideIndex + 1} of ${total}: ${post.title}`}
            aria-hidden={!active}
            style={{
                flex: '0 0 100%',
                position: 'relative',
                height: '100%',
                background: '#0b1220',
            }}
        >
            {post.image ? (
                <img
                    src={post.image}
                    alt=""
                    loading={active ? 'eager' : 'lazy'}
                    style={{
                        position: 'absolute',
                        inset: 0,
                        width: '100%',
                        height: '100%',
                        objectFit: 'cover',
                        display: 'block',
                    }}
                />
            ) : (
                <div style={{
                    position: 'absolute',
                    inset: 0,
                    background: `linear-gradient(135deg, ${accent}33 0%, ${accent}66 100%)`,
                }} />
            )}

            {/* Bottom gradient → keeps copy legible over any photo */}
            <div style={{
                position: 'absolute',
                inset: 0,
                background: 'linear-gradient(to top, rgba(2,6,23,0.85) 0%, rgba(2,6,23,0.55) 38%, rgba(2,6,23,0.0) 70%)',
                pointerEvents: 'none',
            }} />

            <div style={{
                position: 'absolute',
                left: 0, right: 0, bottom: 0,
                padding: 'clamp(24px, 4vw, 48px) clamp(24px, 5vw, 56px)',
                color: '#ffffff',
                display: 'flex',
                flexDirection: 'column',
                gap: 12,
                maxWidth: 880,
            }}>
                {(showCat || dateLabel) && (
                    <div style={{ display: 'flex', alignItems: 'center', gap: 12, fontSize: '0.8125rem', color: 'rgba(255,255,255,0.78)' }}>
                        {showCat && post.category && (
                            <a href={post.category.href} style={{
                                background: accent,
                                color: '#ffffff',
                                padding: '4px 10px',
                                borderRadius: 9999,
                                fontWeight: 600,
                                textDecoration: 'none',
                                fontSize: '0.75rem',
                                letterSpacing: '0.02em',
                                textTransform: 'uppercase',
                            }}>{post.category.name}</a>
                        )}
                        {dateLabel && <span>{dateLabel}</span>}
                    </div>
                )}

                <h3 style={{
                    margin: 0,
                    fontSize: 'clamp(1.5rem, 3vw, 2.25rem)',
                    fontWeight: 700,
                    lineHeight: 1.2,
                    letterSpacing: '-0.02em',
                }}>
                    <a href={post.href} style={{ color: 'inherit', textDecoration: 'none' }}>
                        {post.title}
                    </a>
                </h3>

                {showExcerpt && post.excerpt && (
                    <p style={{
                        margin: 0,
                        fontSize: 'clamp(0.9375rem, 1.2vw, 1.0625rem)',
                        color: 'rgba(255,255,255,0.85)',
                        lineHeight: 1.55,
                        maxWidth: '60ch',
                        display: '-webkit-box',
                        WebkitLineClamp: 2,
                        WebkitBoxOrient: 'vertical',
                        overflow: 'hidden',
                    }}>{post.excerpt}</p>
                )}

                {showReadMore && (
                    <a
                        href={post.href}
                        style={{
                            alignSelf: 'flex-start',
                            marginTop: 4,
                            background: '#ffffff',
                            color: '#0f172a',
                            padding: '10px 18px',
                            borderRadius: 8,
                            fontWeight: 600,
                            fontSize: '0.9375rem',
                            textDecoration: 'none',
                            display: 'inline-flex',
                            alignItems: 'center',
                            gap: 6,
                        }}
                    >
                        {readMoreLabel}
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.4">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M5 12h14M13 5l7 7-7 7" />
                        </svg>
                    </a>
                )}
            </div>
        </article>
    );
}

const arrowBtnStyle: React.CSSProperties = {
    position: 'absolute',
    top: '50%',
    transform: 'translateY(-50%)',
    zIndex: 2,
    width: 44,
    height: 44,
    borderRadius: 9999,
    border: '1px solid rgba(15,23,42,0.10)',
    background: 'rgba(255,255,255,0.95)',
    color: '#0f172a',
    cursor: 'pointer',
    display: 'inline-flex',
    alignItems: 'center',
    justifyContent: 'center',
    boxShadow: '0 4px 14px rgba(15,23,42,0.18)',
    backdropFilter: 'blur(6px)',
};
