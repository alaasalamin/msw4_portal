import { usePage } from '@inertiajs/react';

interface PostCategoryRef { name: string; slug: string; href: string }
interface Post {
    title: string;
    excerpt?: string | null;
    href: string;
    image?: string | null;
    publishedAt?: string | null;
    category?: PostCategoryRef | null;
}

interface BlogPostsSettings {
    heading?: string;
    subtitle?: string;
    limit?: number | string;
    categorySlug?: string | null;
    columns?: number | string;
    showCategory?: boolean;
    showExcerpt?: boolean;
    showDate?: boolean;
    bg?: string;
    fg?: string;
    cardBg?: string;
    mutedFg?: string;
    accent?: string;
}

interface SharedProps { homepage?: { posts?: Post[] } }

function formatDate(iso?: string | null): string {
    if (!iso) return '';
    const d = new Date(iso);
    if (isNaN(d.getTime())) return '';
    return d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
}

function PlaceholderImage({ accent }: { accent: string }) {
    return (
        <div
            style={{
                position: 'absolute',
                inset: 0,
                background: `linear-gradient(135deg, ${accent}22 0%, ${accent}55 100%)`,
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                color: accent,
            }}
        >
            <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
                <path strokeLinecap="round" strokeLinejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0z" />
            </svg>
        </div>
    );
}

export default function DynamicBlogPosts({ settings }: { settings: BlogPostsSettings }) {
    const { homepage } = usePage<SharedProps>().props;
    const allPosts: Post[] = homepage?.posts ?? [];

    const limit = Math.max(1, Math.min(24, Number(settings.limit) || 6));
    const cols  = Math.max(1, Math.min(4, Number(settings.columns) || 3));
    const filterSlug = settings.categorySlug || null;

    const posts = allPosts
        .filter((p) => !filterSlug || p.category?.slug === filterSlug)
        .slice(0, limit);

    const showCategory = settings.showCategory !== false;
    const showExcerpt  = settings.showExcerpt  !== false;
    const showDate     = settings.showDate     !== false;

    const fg      = settings.fg      ?? '#0f172a';
    const mutedFg = settings.mutedFg ?? '#64748b';
    const cardBg  = settings.cardBg  ?? '#ffffff';
    const accent  = settings.accent  ?? '#0284c7';

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

                {posts.length > 0 ? (
                    <div
                        className={`tb-blog-grid tb-blog-cols-${cols}`}
                        style={{
                            display: 'grid',
                            gap: 'clamp(20px, 2.5vw, 32px)',
                            gridTemplateColumns: `repeat(${cols}, minmax(0, 1fr))`,
                        }}
                    >
                        {posts.map((post, i) => (
                            <a
                                key={i}
                                href={post.href}
                                className="tb-blog-card"
                                style={{
                                    display: 'flex',
                                    flexDirection: 'column',
                                    background: cardBg,
                                    borderRadius: '16px',
                                    overflow: 'hidden',
                                    textDecoration: 'none',
                                    color: 'inherit',
                                    boxShadow: '0 1px 2px rgba(15,23,42,.06), 0 1px 3px rgba(15,23,42,.04)',
                                    transition: 'transform .18s ease, box-shadow .18s ease',
                                }}
                            >
                                {/* Featured image (always 16:9, fills available width) */}
                                <div style={{
                                    position: 'relative',
                                    width: '100%',
                                    aspectRatio: '16 / 9',
                                    background: '#e2e8f0',
                                    overflow: 'hidden',
                                }}>
                                    {post.image ? (
                                        <img
                                            src={post.image}
                                            alt={post.title}
                                            loading="lazy"
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
                                        <PlaceholderImage accent={accent} />
                                    )}
                                </div>

                                {/* Card body */}
                                <div style={{
                                    padding: 'clamp(16px, 2vw, 22px)',
                                    display: 'flex',
                                    flexDirection: 'column',
                                    gap: 10,
                                    flex: 1,
                                }}>
                                    {(showCategory && post.category) && (
                                        <span style={{
                                            alignSelf: 'flex-start',
                                            fontSize: '0.6875rem',
                                            fontWeight: 700,
                                            textTransform: 'uppercase',
                                            letterSpacing: '0.08em',
                                            color: accent,
                                            background: `${accent}14`,
                                            padding: '4px 10px',
                                            borderRadius: '9999px',
                                        }}>
                                            {post.category.name}
                                        </span>
                                    )}

                                    <h3 style={{
                                        margin: 0,
                                        fontSize: 'clamp(1.0625rem, 1.5vw, 1.25rem)',
                                        fontWeight: 700,
                                        lineHeight: 1.3,
                                        letterSpacing: '-0.01em',
                                        color: fg,
                                    }}>
                                        {post.title}
                                    </h3>

                                    {(showExcerpt && post.excerpt) && (
                                        <p style={{
                                            margin: 0,
                                            fontSize: '0.9375rem',
                                            lineHeight: 1.55,
                                            color: mutedFg,
                                            display: '-webkit-box',
                                            WebkitLineClamp: 3,
                                            WebkitBoxOrient: 'vertical',
                                            overflow: 'hidden',
                                        }}>
                                            {post.excerpt}
                                        </p>
                                    )}

                                    {(showDate && post.publishedAt) && (
                                        <time
                                            dateTime={post.publishedAt}
                                            style={{
                                                marginTop: 'auto',
                                                fontSize: '0.8125rem',
                                                color: mutedFg,
                                                opacity: 0.8,
                                            }}
                                        >
                                            {formatDate(post.publishedAt)}
                                        </time>
                                    )}
                                </div>
                            </a>
                        ))}
                    </div>
                ) : (
                    <p style={{ textAlign: 'center', color: mutedFg, margin: 0 }}>
                        No posts published yet.
                    </p>
                )}
            </div>

            <style>{`
                .tb-blog-card:hover {
                    transform: translateY(-3px);
                    box-shadow: 0 4px 14px rgba(15,23,42,.10), 0 2px 6px rgba(15,23,42,.06);
                }
                @media (max-width: 900px) {
                    .tb-blog-grid.tb-blog-cols-3,
                    .tb-blog-grid.tb-blog-cols-4 {
                        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                    }
                }
                @media (max-width: 560px) {
                    .tb-blog-grid {
                        grid-template-columns: 1fr !important;
                    }
                }
            `}</style>
        </section>
    );
}
