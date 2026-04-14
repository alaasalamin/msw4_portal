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

const IcoArrowLeft = () => (
    <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
    </svg>
);

const IcoUser = () => (
    <svg className="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
    </svg>
);

const IcoCalendar = () => (
    <svg className="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
    </svg>
);

const IcoTag = () => (
    <svg className="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" /><path strokeLinecap="round" strokeLinejoin="round" d="M6 6h.008v.008H6V6Z" />
    </svg>
);

// ── Types ─────────────────────────────────────────────────────────────────────

interface Post {
    id: number;
    title: string;
    slug: string;
    excerpt: string | null;
    content: string | null;
    featured_image: string | null;
    published_at: string;
    meta_title: string | null;
    meta_description: string | null;
    author: { name: string };
    category: { id: number; name: string; slug: string } | null;
}

interface Props extends PageProps {
    post: Post;
}

// ── Component ─────────────────────────────────────────────────────────────────

export default function BlogShow({ post, site }: Props) {
    const metaTitle = post.meta_title || post.title;
    const metaDesc  = post.meta_description || post.excerpt || undefined;

    return (
        <>
            <Head>
                <title>{metaTitle}</title>
                {metaDesc && <meta name="description" content={metaDesc} />}
                <meta property="og:title" content={metaTitle} />
                {metaDesc && <meta property="og:description" content={metaDesc} />}
                {post.featured_image && (
                    <meta property="og:image" content={`/storage/${post.featured_image}`} />
                )}
            </Head>

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
                            <Link
                                href={route('blog.index')}
                                className="text-xs text-zinc-400 hover:text-white transition-colors duration-200"
                            >
                                Blog
                            </Link>
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

                {/* ── Breadcrumb ──────────────────────────────────────────── */}
                <div className="mx-auto max-w-3xl px-6 pt-8">
                    <div className="flex items-center gap-2 text-sm text-zinc-600">
                        <Link href={route('blog.index')} className="hover:text-orange-400 transition-colors duration-200 flex items-center gap-1.5">
                            <IcoArrowLeft />
                            Blog
                        </Link>
                        {post.category && (
                            <>
                                <span>/</span>
                                <Link
                                    href={route('blog.category', { category: post.category.slug })}
                                    className="hover:text-orange-400 transition-colors duration-200"
                                >
                                    {post.category.name}
                                </Link>
                            </>
                        )}
                    </div>
                </div>

                {/* ── Article ─────────────────────────────────────────────── */}
                <article className="mx-auto max-w-3xl px-6 py-8 pb-24">

                    {/* Featured image */}
                    {post.featured_image && (
                        <div className="relative overflow-hidden rounded-2xl mb-10">
                            <img
                                src={`/storage/${post.featured_image}`}
                                alt={post.title}
                                className="w-full object-cover max-h-96"
                                width={768}
                                height={384}
                            />
                            <div
                                className="absolute inset-0"
                                style={{ boxShadow:'inset 0 0 0 1px rgba(255,255,255,0.08)', borderRadius:'1rem', pointerEvents:'none' }}
                            />
                        </div>
                    )}

                    {/* Meta pill */}
                    <div className="mb-5 flex items-center gap-2 flex-wrap">
                        {post.category && (
                            <Link
                                href={route('blog.category', { category: post.category.slug })}
                                className="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium transition-colors duration-200"
                                style={{ background:'rgba(234,88,12,0.12)', border:'1px solid rgba(234,88,12,0.25)', color:'#fb923c' }}
                            >
                                <IcoTag />
                                {post.category.name}
                            </Link>
                        )}
                        <span
                            className="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs text-zinc-400"
                            style={{ background:'rgba(255,255,255,0.05)', border:'1px solid rgba(255,255,255,0.09)' }}
                        >
                            <IcoUser />
                            {post.author.name}
                        </span>
                        <span
                            className="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs text-zinc-400"
                            style={{ background:'rgba(255,255,255,0.05)', border:'1px solid rgba(255,255,255,0.09)' }}
                        >
                            <IcoCalendar />
                            {new Date(post.published_at).toLocaleDateString('en-GB', {
                                day: 'numeric', month: 'long', year: 'numeric',
                            })}
                        </span>
                    </div>

                    {/* Title */}
                    <h1 className="text-3xl sm:text-4xl font-bold leading-tight text-white">
                        {post.title}
                    </h1>

                    {/* Excerpt */}
                    {post.excerpt && (
                        <p
                            className="mt-5 text-lg leading-relaxed text-zinc-400 pl-4 italic"
                            style={{ borderLeft:'3px solid rgba(234,88,12,0.5)' }}
                        >
                            {post.excerpt}
                        </p>
                    )}

                    {/* Divider */}
                    <div
                        className="my-8"
                        style={{ height:'1px', background:'linear-gradient(90deg, rgba(234,88,12,0.3), rgba(255,255,255,0.05), transparent)' }}
                    />

                    {/* Content */}
                    {post.content && (
                        <div
                            className="blog-prose"
                            dangerouslySetInnerHTML={{ __html: post.content }}
                        />
                    )}

                    {/* Back footer */}
                    <div
                        className="mt-16 pt-8"
                        style={{ borderTop:'1px solid rgba(255,255,255,0.06)' }}
                    >
                        <Link
                            href={route('blog.index')}
                            className="inline-flex items-center gap-2 text-sm font-medium text-orange-400 hover:text-orange-300 transition-colors duration-200"
                        >
                            <IcoArrowLeft />
                            All articles
                        </Link>
                    </div>
                </article>
            </div>

            {/* ── Prose styles (injected once per page) ─────────────────── */}
            <style>{`
                .blog-prose {
                    color: #a1a1aa;
                    font-size: 1.0625rem;
                    line-height: 1.8;
                }
                .blog-prose h2 {
                    font-size: 1.5rem;
                    font-weight: 700;
                    color: #fff;
                    margin-top: 2.5rem;
                    margin-bottom: 0.75rem;
                    letter-spacing: -0.01em;
                }
                .blog-prose h3 {
                    font-size: 1.2rem;
                    font-weight: 600;
                    color: #e4e4e7;
                    margin-top: 2rem;
                    margin-bottom: 0.5rem;
                }
                .blog-prose p {
                    margin-top: 0;
                    margin-bottom: 1.4rem;
                }
                .blog-prose a {
                    color: #fb923c;
                    text-decoration: underline;
                    text-underline-offset: 3px;
                    transition: color 0.15s;
                }
                .blog-prose a:hover { color: #fdba74; }
                .blog-prose strong { color: #e4e4e7; font-weight: 600; }
                .blog-prose em { color: #d4d4d8; }
                .blog-prose ul {
                    list-style: none;
                    padding: 0;
                    margin-bottom: 1.4rem;
                }
                .blog-prose ul li {
                    padding-left: 1.4rem;
                    position: relative;
                    margin-bottom: 0.4rem;
                }
                .blog-prose ul li::before {
                    content: '';
                    position: absolute;
                    left: 0;
                    top: 0.65em;
                    width: 5px;
                    height: 5px;
                    border-radius: 50%;
                    background: rgba(234,88,12,0.7);
                }
                .blog-prose ol {
                    padding-left: 1.4rem;
                    margin-bottom: 1.4rem;
                }
                .blog-prose ol li { margin-bottom: 0.4rem; color: #a1a1aa; }
                .blog-prose blockquote {
                    border-left: 3px solid rgba(234,88,12,0.4);
                    padding: 0.75rem 1.25rem;
                    margin: 1.75rem 0;
                    background: rgba(234,88,12,0.05);
                    border-radius: 0 0.5rem 0.5rem 0;
                    color: #d4d4d8;
                    font-style: italic;
                }
                .blog-prose code {
                    background: rgba(255,255,255,0.07);
                    border: 1px solid rgba(255,255,255,0.1);
                    border-radius: 0.3rem;
                    padding: 0.15em 0.45em;
                    font-size: 0.875em;
                    color: #fb923c;
                    font-family: ui-monospace, 'Cascadia Code', monospace;
                }
                .blog-prose pre {
                    background: rgba(255,255,255,0.04);
                    border: 1px solid rgba(255,255,255,0.08);
                    border-radius: 0.75rem;
                    padding: 1.25rem 1.5rem;
                    overflow-x: auto;
                    margin-bottom: 1.4rem;
                }
                .blog-prose pre code {
                    background: none;
                    border: none;
                    padding: 0;
                    color: #d4d4d8;
                    font-size: 0.9em;
                }
                .blog-prose img {
                    border-radius: 0.75rem;
                    border: 1px solid rgba(255,255,255,0.08);
                    max-width: 100%;
                    height: auto;
                    margin: 1.5rem 0;
                }
                .blog-prose hr {
                    border: none;
                    height: 1px;
                    background: linear-gradient(90deg, rgba(234,88,12,0.3), rgba(255,255,255,0.05), transparent);
                    margin: 2rem 0;
                }
            `}</style>
        </>
    );
}
