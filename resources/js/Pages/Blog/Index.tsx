import { Head, Link } from '@inertiajs/react';
import { PageProps } from '@/types';

// ── Icons ─────────────────────────────────────────────────────────────────────

const MoonLogo = () => (
    <svg viewBox="0 0 40 40" fill="none" className="h-8 w-8">
        <rect width="40" height="40" fill="#1C0800"/>
        <circle cx="20" cy="24" r="24" fill="#EA580C" opacity="0.22"/>
        <circle cx="20" cy="20" r="17" fill="#EDE0C4"/>
        <circle cx="22" cy="18" r="17" fill="#C8B48A" opacity="0.22"/>
        <circle cx="28" cy="11" r="4.5" fill="#C0A878"/><circle cx="28" cy="11" r="3" fill="#A8906A"/><circle cx="27.4" cy="10.4" r="1.4" fill="#DDD0B0" fillOpacity="0.7"/>
        <circle cx="10" cy="21" r="3.2" fill="#C0A878"/><circle cx="10" cy="21" r="1.9" fill="#A8906A"/><circle cx="9.6" cy="20.6" r="0.9" fill="#DDD0B0" fillOpacity="0.6"/>
        <circle cx="27" cy="30" r="3.5" fill="#C0A878"/><circle cx="27" cy="30" r="2.2" fill="#A8906A"/><circle cx="26.5" cy="29.5" r="1" fill="#DDD0B0" fillOpacity="0.6"/>
    </svg>
);

const IcoPen = () => (
    <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
    </svg>
);

const IcoCalendar = () => (
    <svg className="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
    </svg>
);

const IcoUser = () => (
    <svg className="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
    </svg>
);

const IcoArrow = () => (
    <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
    </svg>
);

const IcoChevronLeft = () => (
    <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth={2} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
    </svg>
);

const IcoChevronRight = () => (
    <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth={2} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
    </svg>
);

const IcoTag = () => (
    <svg className="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" /><path strokeLinecap="round" strokeLinejoin="round" d="M6 6h.008v.008H6V6Z" />
    </svg>
);

// ── Types ─────────────────────────────────────────────────────────────────────

interface Category {
    id: number;
    name: string;
    slug: string;
    posts_count?: number;
}

interface Post {
    id: number;
    title: string;
    slug: string;
    excerpt: string | null;
    featured_image: string | null;
    published_at: string;
    author: { name: string };
    category: Category | null;
}

interface Props extends PageProps {
    posts: {
        data: Post[];
        links: { url: string | null; label: string; active: boolean }[];
        total: number;
    };
    categories: Category[];
    activeCategory?: Category | null;
}

// ── Helpers ───────────────────────────────────────────────────────────────────

function formatDate(iso: string) {
    return new Date(iso).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
}

function postHref(post: Post): string {
    if (post.category) {
        return route('blog.show', { category: post.category.slug, slug: post.slug });
    }
    return route('blog.show.legacy', { slug: post.slug });
}

const GRADIENTS = [
    'linear-gradient(135deg, #1a1035 0%, #2d1b69 50%, #0f0a1e 100%)',
    'linear-gradient(135deg, #0f1e35 0%, #1b3a69 50%, #0a0f1e 100%)',
    'linear-gradient(135deg, #1e0f0f 0%, #6b1f1f 50%, #0f0a0a 100%)',
    'linear-gradient(135deg, #0a1e14 0%, #1b6941 50%, #0a1e0f 100%)',
    'linear-gradient(135deg, #1e1a0f 0%, #694e1b 50%, #0f0e0a 100%)',
    'linear-gradient(135deg, #0f0a1e 0%, #3d1b69 50%, #1a0f35 100%)',
];

// ── Component ─────────────────────────────────────────────────────────────────

export default function BlogIndex({ posts, site, categories, activeCategory }: Props) {
    const pageTitle = activeCategory ? `${activeCategory.name} — Blog` : 'Blog';

    return (
        <>
            <Head title={pageTitle} />

            <div
                className="min-h-dvh"
                style={{ background: 'linear-gradient(135deg, #09090b 0%, #0f0a1e 50%, #09090b 100%)' }}
            >
                {/* Ambient orbs */}
                <div className="pointer-events-none fixed inset-0 overflow-hidden">
                    <div style={{ position:'absolute', top:'-10%', left:'-5%', width:'45%', height:'45%', background:'radial-gradient(circle, rgba(234,88,12,0.06) 0%, transparent 70%)', borderRadius:'50%' }} />
                    <div style={{ position:'absolute', bottom:'10%', right:'-5%', width:'40%', height:'40%', background:'radial-gradient(circle, rgba(99,102,241,0.05) 0%, transparent 70%)', borderRadius:'50%' }} />
                </div>

                {/* ── Nav ─────────────────────────────────────────────────── */}
                <nav style={{ borderBottom:'1px solid rgba(255,255,255,0.06)', backdropFilter:'blur(16px)', background:'rgba(9,9,11,0.7)' }} className="sticky top-0 z-40">
                    <div className="mx-auto max-w-6xl px-6 h-14 flex items-center justify-between">
                        <Link href="/" className="flex items-center gap-2.5">
                            <MoonLogo />
                            <span className="text-sm font-semibold tracking-wide text-white/90">
                                {site?.name ?? 'MSW4'}
                            </span>
                        </Link>
                        <div className="flex items-center gap-4">
                            <Link href="/" className="text-xs text-zinc-400 hover:text-white transition-colors duration-200">Home</Link>
                            <Link
                                href={route('customer.login')}
                                style={{ background:'rgba(234,88,12,0.15)', border:'1px solid rgba(234,88,12,0.3)' }}
                                className="rounded-lg px-3 py-1.5 text-xs font-medium text-orange-300 hover:bg-orange-500/20 transition-all duration-200"
                            >
                                Customer Portal
                            </Link>
                        </div>
                    </div>
                </nav>

                {/* ── Hero ────────────────────────────────────────────────── */}
                <div className="mx-auto max-w-6xl px-6 pt-16 pb-10 text-center">
                    <div
                        className="inline-flex items-center gap-2 rounded-full px-3.5 py-1.5 mb-5"
                        style={{ background:'rgba(234,88,12,0.1)', border:'1px solid rgba(234,88,12,0.2)' }}
                    >
                        <IcoPen />
                        <span className="text-xs font-medium text-orange-300 tracking-wider uppercase">Journal</span>
                    </div>

                    {activeCategory ? (
                        <>
                            <h1 className="text-4xl sm:text-5xl font-bold tracking-tight text-white">
                                {activeCategory.name}
                            </h1>
                            <p className="mt-3 text-zinc-500 text-sm">
                                <Link href={route('blog.index')} className="hover:text-orange-400 transition-colors">Blog</Link>
                                <span className="mx-2 text-zinc-700">/</span>
                                {activeCategory.name}
                            </p>
                        </>
                    ) : (
                        <h1 className="text-4xl sm:text-5xl font-bold tracking-tight text-white">
                            Latest from the{' '}
                            <span
                                style={{ background:'linear-gradient(90deg, #fb923c, #f59e0b)', WebkitBackgroundClip:'text', WebkitTextFillColor:'transparent' }}
                            >
                                MSW4 Blog
                            </span>
                        </h1>
                    )}

                    {site?.description && !activeCategory && (
                        <p className="mt-4 text-zinc-400 max-w-xl mx-auto text-base leading-relaxed">{site.description}</p>
                    )}
                    {posts.total > 0 && (
                        <p className="mt-3 text-xs text-zinc-600">
                            {posts.total} {posts.total === 1 ? 'article' : 'articles'}
                        </p>
                    )}
                </div>

                {/* ── Category tabs ───────────────────────────────────────── */}
                {categories.length > 0 && (
                    <div className="mx-auto max-w-6xl px-6 pb-8">
                        <div className="flex flex-wrap gap-2">
                            <Link
                                href={route('blog.index')}
                                className="inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 text-xs font-medium transition-all duration-200"
                                style={!activeCategory
                                    ? { background:'rgba(234,88,12,0.2)', border:'1px solid rgba(234,88,12,0.4)', color:'#fb923c' }
                                    : { background:'rgba(255,255,255,0.05)', border:'1px solid rgba(255,255,255,0.09)', color:'#71717a' }
                                }
                            >
                                All
                            </Link>
                            {categories.map((cat) => (
                                <Link
                                    key={cat.id}
                                    href={route('blog.category', { category: cat.slug })}
                                    className="inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 text-xs font-medium transition-all duration-200"
                                    style={activeCategory?.id === cat.id
                                        ? { background:'rgba(234,88,12,0.2)', border:'1px solid rgba(234,88,12,0.4)', color:'#fb923c' }
                                        : { background:'rgba(255,255,255,0.05)', border:'1px solid rgba(255,255,255,0.09)', color:'#71717a' }
                                    }
                                >
                                    <IcoTag />
                                    {cat.name}
                                    {cat.posts_count !== undefined && (
                                        <span className="opacity-50">{cat.posts_count}</span>
                                    )}
                                </Link>
                            ))}
                        </div>
                    </div>
                )}

                {/* ── Posts grid ──────────────────────────────────────────── */}
                <div className="mx-auto max-w-6xl px-6 pb-20">
                    {posts.data.length === 0 ? (
                        <div
                            className="rounded-2xl p-16 text-center"
                            style={{ background:'rgba(255,255,255,0.03)', border:'1px solid rgba(255,255,255,0.07)' }}
                        >
                            <p className="text-zinc-500 text-sm">No articles published yet.</p>
                            {activeCategory && (
                                <Link href={route('blog.index')} className="mt-3 inline-block text-xs text-orange-400 hover:text-orange-300">
                                    View all posts
                                </Link>
                            )}
                        </div>
                    ) : (
                        <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            {posts.data.map((post, i) => (
                                <PostCard key={post.id} post={post} gradientIndex={i % GRADIENTS.length} href={postHref(post)} />
                            ))}
                        </div>
                    )}

                    {/* ── Pagination ──────────────────────────────────────── */}
                    {posts.links.length > 3 && (
                        <div className="mt-14 flex justify-center items-center gap-1.5">
                            {posts.links.map((link, i) => {
                                const isPrev = i === 0;
                                const isNext = i === posts.links.length - 1;

                                if (!link.url) {
                                    return (
                                        <span
                                            key={i}
                                            className="flex items-center justify-center h-9 px-3 rounded-lg text-sm text-zinc-700 cursor-not-allowed"
                                            style={{ border:'1px solid rgba(255,255,255,0.05)' }}
                                        >
                                            {isPrev ? <IcoChevronLeft /> : isNext ? <IcoChevronRight /> : (
                                                <span dangerouslySetInnerHTML={{ __html: link.label }} />
                                            )}
                                        </span>
                                    );
                                }

                                return (
                                    <Link
                                        key={i}
                                        href={link.url}
                                        className="flex items-center justify-center h-9 px-3 rounded-lg text-sm transition-all duration-200"
                                        style={link.active
                                            ? { background:'rgba(234,88,12,0.2)', border:'1px solid rgba(234,88,12,0.4)', color:'#fb923c' }
                                            : { background:'rgba(255,255,255,0.04)', border:'1px solid rgba(255,255,255,0.08)', color:'#a1a1aa' }
                                        }
                                    >
                                        {isPrev ? <IcoChevronLeft /> : isNext ? <IcoChevronRight /> : (
                                            <span dangerouslySetInnerHTML={{ __html: link.label }} />
                                        )}
                                    </Link>
                                );
                            })}
                        </div>
                    )}
                </div>
            </div>
        </>
    );
}

// ── Post card ─────────────────────────────────────────────────────────────────

function PostCard({ post, gradientIndex, href }: { post: Post; gradientIndex: number; href: string }) {
    return (
        <Link
            href={href}
            className="group flex flex-col overflow-hidden rounded-2xl transition-all duration-300 hover:-translate-y-1"
            style={{ background:'rgba(255,255,255,0.04)', border:'1px solid rgba(255,255,255,0.08)' }}
            onMouseEnter={(e) => {
                (e.currentTarget as HTMLAnchorElement).style.border = '1px solid rgba(234,88,12,0.25)';
                (e.currentTarget as HTMLAnchorElement).style.boxShadow = '0 8px 32px rgba(234,88,12,0.08)';
            }}
            onMouseLeave={(e) => {
                (e.currentTarget as HTMLAnchorElement).style.border = '1px solid rgba(255,255,255,0.08)';
                (e.currentTarget as HTMLAnchorElement).style.boxShadow = 'none';
            }}
        >
            {/* Image / placeholder */}
            <div className="relative h-44 overflow-hidden">
                {post.featured_image ? (
                    <img
                        src={`/storage/${post.featured_image}`}
                        alt={post.title}
                        className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                        loading="lazy"
                        width={400}
                        height={176}
                    />
                ) : (
                    <div className="h-full w-full" style={{ background: GRADIENTS[gradientIndex] }} />
                )}
                <div
                    className="absolute inset-x-0 bottom-0 h-16"
                    style={{ background:'linear-gradient(to top, rgba(9,9,11,0.9), transparent)' }}
                />
                {post.category && (
                    <span
                        className="absolute top-3 left-3 inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium"
                        style={{ background:'rgba(9,9,11,0.8)', border:'1px solid rgba(255,255,255,0.12)', color:'#fb923c', backdropFilter:'blur(8px)' }}
                    >
                        <IcoTag />
                        {post.category.name}
                    </span>
                )}
            </div>

            {/* Content */}
            <div className="flex flex-1 flex-col p-5">
                <h2 className="text-base font-semibold leading-snug text-white/90 group-hover:text-orange-300 transition-colors duration-200 line-clamp-2">
                    {post.title}
                </h2>
                {post.excerpt && (
                    <p className="mt-2 text-sm text-zinc-500 line-clamp-3 leading-relaxed">{post.excerpt}</p>
                )}

                <div className="mt-auto pt-5 flex items-center justify-between">
                    <div className="flex items-center gap-3 text-xs text-zinc-600">
                        <span className="flex items-center gap-1">
                            <IcoUser />
                            <span className="text-zinc-500">{post.author.name}</span>
                        </span>
                        <span className="flex items-center gap-1">
                            <IcoCalendar />
                            <span>{formatDate(post.published_at)}</span>
                        </span>
                    </div>
                    <span className="text-orange-400/60 group-hover:text-orange-400 transition-colors duration-200">
                        <IcoArrow />
                    </span>
                </div>
            </div>
        </Link>
    );
}
