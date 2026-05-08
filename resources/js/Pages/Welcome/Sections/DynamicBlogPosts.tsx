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
    variant?: 'cards' | 'list' | 'featured' | 'minimal';
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
        <div style={{
            position: 'absolute',
            inset: 0,
            background: `linear-gradient(135deg, ${accent}22 0%, ${accent}55 100%)`,
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
            color: accent,
        }}>
            <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
                <path strokeLinecap="round" strokeLinejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5z" />
            </svg>
        </div>
    );
}

// ── Dispatcher ──────────────────────────────────────────────────────────────

export default function DynamicBlogPosts({ settings }: { settings: BlogPostsSettings }) {
    const { homepage } = usePage<SharedProps>().props;
    const allPosts: Post[] = homepage?.posts ?? [];

    const limit = Math.max(1, Math.min(24, Number(settings.limit) || 6));
    const filterSlug = settings.categorySlug || null;
    const posts = allPosts
        .filter((p) => !filterSlug || p.category?.slug === filterSlug)
        .slice(0, limit);

    const props = { settings, posts };
    switch (settings.variant) {
        case 'list':     return <ListView    {...props} />;
        case 'featured': return <Featured    {...props} />;
        case 'minimal':  return <MinimalView {...props} />;
        case 'cards':
        default:         return <CardsView   {...props} />;
    }
}

// ── Shared chrome ──────────────────────────────────────────────────────────

interface VariantProps { settings: BlogPostsSettings; posts: Post[] }

function SectionWrap({ settings, maxWidth = '1200px', children }: { settings: BlogPostsSettings; maxWidth?: string; children: React.ReactNode }) {
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

function SectionHeader({ settings }: { settings: BlogPostsSettings }) {
    const mutedFg = settings.mutedFg ?? '#64748b';
    if (!settings.heading && !settings.subtitle) return null;
    return (
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
    );
}

function CategoryPill({ post, accent }: { post: Post; accent: string }) {
    if (!post.category) return null;
    return (
        <span style={{
            alignSelf: 'flex-start',
            fontSize: '0.6875rem',
            fontWeight: 700,
            textTransform: 'uppercase',
            letterSpacing: '0.08em',
            color: accent,
            background: `${accent}14`,
            padding: '4px 10px',
            borderRadius: 9999,
        }}>{post.category.name}</span>
    );
}

function EmptyState({ mutedFg }: { mutedFg: string }) {
    return <p style={{ textAlign: 'center', color: mutedFg, margin: 0 }}>No posts published yet.</p>;
}

// ── Variant: cards (existing) ──────────────────────────────────────────────

function CardsView({ settings, posts }: VariantProps) {
    const cols = Math.max(1, Math.min(4, Number(settings.columns) || 3));
    const showCategory = settings.showCategory !== false;
    const showExcerpt  = settings.showExcerpt  !== false;
    const showDate     = settings.showDate     !== false;
    const fg = settings.fg ?? '#0f172a';
    const mutedFg = settings.mutedFg ?? '#64748b';
    const cardBg = settings.cardBg ?? '#ffffff';
    const accent = settings.accent ?? '#0284c7';

    return (
        <SectionWrap settings={settings}>
            <SectionHeader settings={settings} />
            {posts.length === 0 ? <EmptyState mutedFg={mutedFg} /> : (
                <div className={`tb-blog-grid tb-blog-cols-${cols}`} style={{
                    display: 'grid',
                    gap: 'clamp(20px, 2.5vw, 32px)',
                    gridTemplateColumns: `repeat(${cols}, minmax(0, 1fr))`,
                }}>
                    {posts.map((post, i) => (
                        <a key={i} href={post.href} className="tb-blog-card" style={{
                            display: 'flex',
                            flexDirection: 'column',
                            background: cardBg,
                            borderRadius: 16,
                            overflow: 'hidden',
                            textDecoration: 'none',
                            color: 'inherit',
                            boxShadow: '0 1px 2px rgba(15,23,42,.06), 0 1px 3px rgba(15,23,42,.04)',
                            transition: 'transform .18s ease, box-shadow .18s ease',
                        }}>
                            <div style={{ position: 'relative', width: '100%', aspectRatio: '16 / 9', background: '#e2e8f0', overflow: 'hidden' }}>
                                {post.image
                                    ? <img src={post.image} alt={post.title} loading="lazy" style={{ position: 'absolute', inset: 0, width: '100%', height: '100%', objectFit: 'cover', display: 'block' }} />
                                    : <PlaceholderImage accent={accent} />}
                            </div>
                            <div style={{ padding: 'clamp(16px, 2vw, 22px)', display: 'flex', flexDirection: 'column', gap: 10, flex: 1 }}>
                                {showCategory && <CategoryPill post={post} accent={accent} />}
                                <h3 style={titleStyle(fg)}>{post.title}</h3>
                                {showExcerpt && post.excerpt && <p style={excerptStyle(mutedFg)}>{post.excerpt}</p>}
                                {showDate && post.publishedAt && (
                                    <time dateTime={post.publishedAt} style={{ marginTop: 'auto', fontSize: '0.8125rem', color: mutedFg, opacity: 0.8 }}>
                                        {formatDate(post.publishedAt)}
                                    </time>
                                )}
                            </div>
                        </a>
                    ))}
                </div>
            )}
            <SharedHoverStyles />
            <style>{`
                @media (max-width: 900px) {
                    .tb-blog-grid.tb-blog-cols-3,
                    .tb-blog-grid.tb-blog-cols-4 { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
                }
                @media (max-width: 560px) {
                    .tb-blog-grid { grid-template-columns: 1fr !important; }
                }
            `}</style>
        </SectionWrap>
    );
}

// ── Variant: list (image left, content right) ──────────────────────────────

function ListView({ settings, posts }: VariantProps) {
    const showCategory = settings.showCategory !== false;
    const showExcerpt  = settings.showExcerpt  !== false;
    const showDate     = settings.showDate     !== false;
    const fg = settings.fg ?? '#0f172a';
    const mutedFg = settings.mutedFg ?? '#64748b';
    const accent = settings.accent ?? '#0284c7';

    return (
        <SectionWrap settings={settings} maxWidth="900px">
            <SectionHeader settings={settings} />
            {posts.length === 0 ? <EmptyState mutedFg={mutedFg} /> : (
                <ul style={{ listStyle: 'none', padding: 0, margin: 0, display: 'flex', flexDirection: 'column', gap: 4 }}>
                    {posts.map((post, i) => (
                        <li key={i}>
                            <a href={post.href} className="tb-blog-list-row" style={{
                                display: 'grid',
                                gridTemplateColumns: '220px 1fr',
                                gap: 'clamp(16px, 2vw, 24px)',
                                alignItems: 'start',
                                padding: 'clamp(18px, 2.4vw, 24px) 0',
                                borderBottom: i === posts.length - 1 ? 'none' : '1px solid rgba(15,23,42,0.08)',
                                textDecoration: 'none',
                                color: 'inherit',
                            }}>
                                <div style={{ position: 'relative', width: '100%', aspectRatio: '16 / 9', background: '#e2e8f0', overflow: 'hidden', borderRadius: 10 }}>
                                    {post.image
                                        ? <img src={post.image} alt={post.title} loading="lazy" style={{ position: 'absolute', inset: 0, width: '100%', height: '100%', objectFit: 'cover', display: 'block' }} />
                                        : <PlaceholderImage accent={accent} />}
                                </div>
                                <div style={{ display: 'flex', flexDirection: 'column', gap: 8 }}>
                                    {showCategory && <CategoryPill post={post} accent={accent} />}
                                    <h3 style={{ ...titleStyle(fg), fontSize: 'clamp(1.0625rem, 1.4vw, 1.25rem)' }}>{post.title}</h3>
                                    {showExcerpt && post.excerpt && <p style={excerptStyle(mutedFg)}>{post.excerpt}</p>}
                                    {showDate && post.publishedAt && (
                                        <time dateTime={post.publishedAt} style={{ fontSize: '0.8125rem', color: mutedFg, opacity: 0.8 }}>
                                            {formatDate(post.publishedAt)}
                                        </time>
                                    )}
                                </div>
                            </a>
                        </li>
                    ))}
                </ul>
            )}
            <style>{`
                .tb-blog-list-row:hover h3 { color: var(--primary, #0284c7); }
                @media (max-width: 640px) {
                    .tb-blog-list-row { grid-template-columns: 1fr !important; }
                }
            `}</style>
        </SectionWrap>
    );
}

// ── Variant: featured (first post big, rest in grid) ───────────────────────

function Featured({ settings, posts }: VariantProps) {
    const showCategory = settings.showCategory !== false;
    const showExcerpt  = settings.showExcerpt  !== false;
    const showDate     = settings.showDate     !== false;
    const fg = settings.fg ?? '#0f172a';
    const mutedFg = settings.mutedFg ?? '#64748b';
    const cardBg = settings.cardBg ?? '#ffffff';
    const accent = settings.accent ?? '#0284c7';

    if (posts.length === 0) {
        return (
            <SectionWrap settings={settings}>
                <SectionHeader settings={settings} />
                <EmptyState mutedFg={mutedFg} />
            </SectionWrap>
        );
    }

    const [hero, ...rest] = posts;

    return (
        <SectionWrap settings={settings}>
            <SectionHeader settings={settings} />

            {/* Featured hero */}
            <a
                href={hero.href}
                className="tb-blog-featured tb-blog-card"
                style={{
                    display: 'grid',
                    gridTemplateColumns: '1.2fr 1fr',
                    gap: 0,
                    background: cardBg,
                    borderRadius: 18,
                    overflow: 'hidden',
                    textDecoration: 'none',
                    color: 'inherit',
                    boxShadow: '0 4px 12px -4px rgba(15,23,42,.10), 0 24px 48px -16px rgba(15,23,42,.18)',
                    marginBottom: rest.length > 0 ? 'clamp(24px, 3vw, 40px)' : 0,
                }}
            >
                <div style={{ position: 'relative', width: '100%', aspectRatio: '4 / 3', background: '#e2e8f0', overflow: 'hidden' }}>
                    {hero.image
                        ? <img src={hero.image} alt={hero.title} loading="lazy" style={{ position: 'absolute', inset: 0, width: '100%', height: '100%', objectFit: 'cover', display: 'block' }} />
                        : <PlaceholderImage accent={accent} />}
                </div>
                <div style={{ padding: 'clamp(24px, 4vw, 40px)', display: 'flex', flexDirection: 'column', justifyContent: 'center', gap: 14 }}>
                    {showCategory && <CategoryPill post={hero} accent={accent} />}
                    <h3 style={{ ...titleStyle(fg), fontSize: 'clamp(1.5rem, 2.6vw, 2rem)', lineHeight: 1.2 }}>{hero.title}</h3>
                    {showExcerpt && hero.excerpt && (
                        <p style={{ ...excerptStyle(mutedFg), fontSize: '1rem', WebkitLineClamp: 4 }}>{hero.excerpt}</p>
                    )}
                    {showDate && hero.publishedAt && (
                        <time dateTime={hero.publishedAt} style={{ fontSize: '0.8125rem', color: mutedFg, opacity: 0.8 }}>
                            {formatDate(hero.publishedAt)}
                        </time>
                    )}
                </div>
            </a>

            {/* Smaller grid for the rest */}
            {rest.length > 0 && (
                <div className="tb-blog-grid tb-blog-cols-3" style={{
                    display: 'grid',
                    gap: 'clamp(20px, 2.5vw, 28px)',
                    gridTemplateColumns: 'repeat(3, minmax(0, 1fr))',
                }}>
                    {rest.map((post, i) => (
                        <a key={i} href={post.href} className="tb-blog-card" style={{
                            display: 'flex',
                            flexDirection: 'column',
                            background: cardBg,
                            borderRadius: 14,
                            overflow: 'hidden',
                            textDecoration: 'none',
                            color: 'inherit',
                            boxShadow: '0 1px 2px rgba(15,23,42,.06), 0 1px 3px rgba(15,23,42,.04)',
                            transition: 'transform .18s ease, box-shadow .18s ease',
                        }}>
                            <div style={{ position: 'relative', width: '100%', aspectRatio: '16 / 9', background: '#e2e8f0', overflow: 'hidden' }}>
                                {post.image
                                    ? <img src={post.image} alt={post.title} loading="lazy" style={{ position: 'absolute', inset: 0, width: '100%', height: '100%', objectFit: 'cover', display: 'block' }} />
                                    : <PlaceholderImage accent={accent} />}
                            </div>
                            <div style={{ padding: 'clamp(14px, 1.6vw, 18px)', display: 'flex', flexDirection: 'column', gap: 8, flex: 1 }}>
                                {showCategory && <CategoryPill post={post} accent={accent} />}
                                <h3 style={{ ...titleStyle(fg), fontSize: '1rem' }}>{post.title}</h3>
                                {showDate && post.publishedAt && (
                                    <time dateTime={post.publishedAt} style={{ marginTop: 'auto', fontSize: '0.75rem', color: mutedFg, opacity: 0.8 }}>
                                        {formatDate(post.publishedAt)}
                                    </time>
                                )}
                            </div>
                        </a>
                    ))}
                </div>
            )}

            <SharedHoverStyles />
            <style>{`
                @media (max-width: 900px) {
                    .tb-blog-featured { grid-template-columns: 1fr !important; }
                    .tb-blog-grid.tb-blog-cols-3 { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
                }
                @media (max-width: 560px) {
                    .tb-blog-grid { grid-template-columns: 1fr !important; }
                }
            `}</style>
        </SectionWrap>
    );
}

// ── Variant: minimal (text archive, no images) ─────────────────────────────

function MinimalView({ settings, posts }: VariantProps) {
    const showCategory = settings.showCategory !== false;
    const showDate     = settings.showDate     !== false;
    const fg = settings.fg ?? '#0f172a';
    const mutedFg = settings.mutedFg ?? '#64748b';
    const accent = settings.accent ?? '#0284c7';

    return (
        <SectionWrap settings={settings} maxWidth="760px">
            <SectionHeader settings={settings} />
            {posts.length === 0 ? <EmptyState mutedFg={mutedFg} /> : (
                <ul style={{ listStyle: 'none', padding: 0, margin: 0 }}>
                    {posts.map((post, i) => (
                        <li key={i}>
                            <a href={post.href} className="tb-blog-min" style={{
                                display: 'grid',
                                gridTemplateColumns: '110px 1fr',
                                gap: 'clamp(12px, 2vw, 24px)',
                                alignItems: 'baseline',
                                padding: 'clamp(14px, 1.6vw, 18px) 0',
                                borderBottom: i === posts.length - 1 ? 'none' : '1px solid rgba(15,23,42,0.08)',
                                textDecoration: 'none',
                                color: 'inherit',
                            }}>
                                <span style={{ color: mutedFg, fontSize: '0.8125rem', whiteSpace: 'nowrap', fontVariantNumeric: 'tabular-nums' }}>
                                    {showDate && post.publishedAt ? formatDate(post.publishedAt) : ''}
                                </span>
                                <div style={{ display: 'flex', flexDirection: 'column', gap: 4 }}>
                                    <h3 style={{
                                        margin: 0,
                                        fontSize: 'clamp(1rem, 1.3vw, 1.125rem)',
                                        fontWeight: 600,
                                        letterSpacing: '-0.005em',
                                        color: fg,
                                        lineHeight: 1.4,
                                    }}>{post.title}</h3>
                                    {showCategory && post.category && (
                                        <span style={{
                                            fontSize: '0.6875rem',
                                            fontWeight: 700,
                                            textTransform: 'uppercase',
                                            letterSpacing: '0.08em',
                                            color: accent,
                                        }}>{post.category.name}</span>
                                    )}
                                </div>
                            </a>
                        </li>
                    ))}
                </ul>
            )}
            <style>{`
                .tb-blog-min:hover h3 { color: var(--primary, #0284c7); }
                @media (max-width: 540px) {
                    .tb-blog-min { grid-template-columns: 1fr !important; }
                }
            `}</style>
        </SectionWrap>
    );
}

// ── Shared bits ────────────────────────────────────────────────────────────

const titleStyle = (fg: string): React.CSSProperties => ({
    margin: 0,
    fontSize: 'clamp(1.0625rem, 1.5vw, 1.25rem)',
    fontWeight: 700,
    lineHeight: 1.3,
    letterSpacing: '-0.01em',
    color: fg,
});

const excerptStyle = (mutedFg: string): React.CSSProperties => ({
    margin: 0,
    fontSize: '0.9375rem',
    lineHeight: 1.55,
    color: mutedFg,
    display: '-webkit-box',
    WebkitLineClamp: 3,
    WebkitBoxOrient: 'vertical',
    overflow: 'hidden',
});

function SharedHoverStyles() {
    return (
        <style>{`
            .tb-blog-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 4px 14px rgba(15,23,42,.10), 0 2px 6px rgba(15,23,42,.06);
            }
        `}</style>
    );
}
