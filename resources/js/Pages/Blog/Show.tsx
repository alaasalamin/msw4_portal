import { Head, Link } from '@inertiajs/react';
import { PageProps } from '@/types';
import { HomepageContent } from '@/Pages/Welcome/types';
import Navbar        from '@/Pages/Welcome/Navbar';
import FooterSection from '@/Pages/Welcome/FooterSection';

// ── Icons ─────────────────────────────────────────────────────────────────────

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
    canonicalUrl: string;
    imageUrl: string | null;
    homepage: HomepageContent;
}

// ── Component ─────────────────────────────────────────────────────────────────

export default function BlogShow({ auth, post, canonicalUrl, imageUrl, homepage }: Props) {
    const metaTitle  = post.meta_title || post.title;
    const metaDesc   = post.meta_description || post.excerpt || '';
    const siteName   = (homepage as any)?.site?.name ?? 'MSW Repair';
    const publishedIso = post.published_at;

    const jsonLd = JSON.stringify({
        '@context': 'https://schema.org',
        '@type': 'BlogPosting',
        headline: metaTitle,
        description: metaDesc || undefined,
        image: imageUrl || undefined,
        datePublished: publishedIso,
        author: { '@type': 'Person', name: post.author.name },
        publisher: { '@type': 'Organization', name: siteName },
        url: canonicalUrl,
        mainEntityOfPage: { '@type': 'WebPage', '@id': canonicalUrl },
    });

    const portalLink = auth?.customer
        ? { href: '/customer/dashboard', label: 'Kundenbereich' }
        : auth?.partner
        ? { href: '/partner/dashboard', label: 'Partnerbereich' }
        : auth?.employee
        ? { href: '/employee/dashboard', label: 'Mitarbeiterbereich' }
        : null;

    return (
        <>
            <Head>
                <title>{metaTitle}</title>
                {metaDesc && <meta name="description" content={metaDesc} />}

                {/* Canonical */}
                <link rel="canonical" href={canonicalUrl} />

                {/* Open Graph */}
                <meta property="og:type"        content="article" />
                <meta property="og:url"         content={canonicalUrl} />
                <meta property="og:site_name"   content={siteName} />
                <meta property="og:title"       content={metaTitle} />
                {metaDesc && <meta property="og:description" content={metaDesc} />}
                {imageUrl  && <meta property="og:image"       content={imageUrl} />}
                {imageUrl  && <meta property="og:image:width"  content="1200" />}
                {imageUrl  && <meta property="og:image:height" content="630" />}
                <meta property="article:published_time" content={publishedIso} />
                <meta property="article:author"         content={post.author.name} />
                {post.category && <meta property="article:section" content={post.category.name} />}

                {/* Twitter / X */}
                <meta name="twitter:card"        content={imageUrl ? 'summary_large_image' : 'summary'} />
                <meta name="twitter:title"       content={metaTitle} />
                {metaDesc && <meta name="twitter:description" content={metaDesc} />}
                {imageUrl  && <meta name="twitter:image"       content={imageUrl} />}

                {/* JSON-LD */}
                <script type="application/ld+json">{jsonLd}</script>
            </Head>

            <Navbar portalLink={portalLink} canLogin={true} />

            <div className="min-h-dvh bg-zinc-50 pt-16">

                {/* ── Breadcrumb bar ──────────────────────────────────────── */}
                <div className="bg-white border-b border-zinc-200">
                    <div className="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 h-11 flex items-center">
                        <div className="flex items-center gap-2 text-sm text-zinc-400">
                            <Link href={route('blog.index')} className="hover:text-orange-500 transition-colors duration-200 flex items-center gap-1.5 font-medium">
                                <IcoArrowLeft />
                                Blog
                            </Link>
                            {post.category && (
                                <>
                                    <span className="text-zinc-300">/</span>
                                    <Link
                                        href={route('blog.category', { category: post.category.slug })}
                                        className="hover:text-orange-500 transition-colors duration-200"
                                    >
                                        {post.category.name}
                                    </Link>
                                </>
                            )}
                            <span className="text-zinc-300">/</span>
                            <span className="text-zinc-500 truncate max-w-xs">{post.title}</span>
                        </div>
                    </div>
                </div>

                {/* ── Article ─────────────────────────────────────────────── */}
                <article className="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 py-10 pb-24">

                    {/* Featured image */}
                    {post.featured_image && (
                        <div className="overflow-hidden rounded-2xl mb-10 shadow-sm border border-zinc-200">
                            <img
                                src={`/storage/${post.featured_image}`}
                                alt={post.title}
                                className="w-full object-cover max-h-96"
                                width={768}
                                height={384}
                            />
                        </div>
                    )}

                    {/* Meta pills */}
                    <div className="mb-5 flex items-center gap-2 flex-wrap">
                        {post.category && (
                            <Link
                                href={route('blog.category', { category: post.category.slug })}
                                className="inline-flex items-center gap-1.5 rounded-full bg-orange-50 border border-orange-200 px-3 py-1 text-xs font-medium text-orange-600 transition-colors duration-200 hover:bg-orange-100"
                            >
                                <IcoTag />
                                {post.category.name}
                            </Link>
                        )}
                        <span className="inline-flex items-center gap-1.5 rounded-full bg-white border border-zinc-200 px-3 py-1 text-xs text-zinc-500">
                            <IcoUser />
                            {post.author.name}
                        </span>
                        <span className="inline-flex items-center gap-1.5 rounded-full bg-white border border-zinc-200 px-3 py-1 text-xs text-zinc-500">
                            <IcoCalendar />
                            {new Date(post.published_at).toLocaleDateString('en-GB', {
                                day: 'numeric', month: 'long', year: 'numeric',
                            })}
                        </span>
                    </div>

                    {/* Title */}
                    <h1 className="text-3xl sm:text-4xl font-bold leading-tight text-zinc-900">
                        {post.title}
                    </h1>

                    {/* Excerpt */}
                    {post.excerpt && (
                        <p className="mt-5 text-lg leading-relaxed text-zinc-500 pl-4 border-l-2 border-orange-400">
                            {post.excerpt}
                        </p>
                    )}

                    {/* Divider */}
                    <div className="my-8 h-px bg-zinc-200" />

                    {/* Content */}
                    {post.content && (
                        <div
                            className="blog-prose"
                            dangerouslySetInnerHTML={{ __html: post.content }}
                        />
                    )}

                    {/* Back link */}
                    <div className="mt-16 pt-8 border-t border-zinc-200">
                        <Link
                            href={route('blog.index')}
                            className="inline-flex items-center gap-2 text-sm font-medium text-orange-500 hover:text-orange-600 transition-colors duration-200"
                        >
                            <IcoArrowLeft />
                            All articles
                        </Link>
                    </div>
                </article>
            </div>

            <FooterSection footer={homepage.footer} />

            {/* ── Prose styles ──────────────────────────────────────────── */}
            <style>{`
                .blog-prose {
                    color: #3f3f46;
                    font-size: 1.0625rem;
                    line-height: 1.85;
                }
                .blog-prose h2 {
                    font-size: 1.5rem;
                    font-weight: 700;
                    color: #18181b;
                    margin-top: 2.5rem;
                    margin-bottom: 0.75rem;
                    letter-spacing: -0.01em;
                }
                .blog-prose h3 {
                    font-size: 1.2rem;
                    font-weight: 600;
                    color: #27272a;
                    margin-top: 2rem;
                    margin-bottom: 0.5rem;
                }
                .blog-prose p {
                    margin-top: 0;
                    margin-bottom: 1.4rem;
                }
                .blog-prose a {
                    color: #ea580c;
                    text-decoration: underline;
                    text-underline-offset: 3px;
                    transition: color 0.15s;
                }
                .blog-prose a:hover { color: #c2410c; }
                .blog-prose strong { color: #18181b; font-weight: 600; }
                .blog-prose em { color: #52525b; }
                .blog-prose ul {
                    list-style: none;
                    padding: 0;
                    margin-bottom: 1.4rem;
                }
                .blog-prose ul li {
                    padding-left: 1.4rem;
                    position: relative;
                    margin-bottom: 0.4rem;
                    color: #3f3f46;
                }
                .blog-prose ul li::before {
                    content: '';
                    position: absolute;
                    left: 0;
                    top: 0.65em;
                    width: 5px;
                    height: 5px;
                    border-radius: 50%;
                    background: #ea580c;
                    opacity: 0.6;
                }
                .blog-prose ol {
                    padding-left: 1.4rem;
                    margin-bottom: 1.4rem;
                }
                .blog-prose ol li { margin-bottom: 0.4rem; color: #3f3f46; }
                .blog-prose blockquote {
                    border-left: 3px solid #fdba74;
                    padding: 0.75rem 1.25rem;
                    margin: 1.75rem 0;
                    background: #fff7ed;
                    border-radius: 0 0.5rem 0.5rem 0;
                    color: #78350f;
                    font-style: italic;
                }
                .blog-prose code {
                    background: #f4f4f5;
                    border: 1px solid #e4e4e7;
                    border-radius: 0.3rem;
                    padding: 0.15em 0.45em;
                    font-size: 0.875em;
                    color: #ea580c;
                    font-family: ui-monospace, 'Cascadia Code', monospace;
                }
                .blog-prose pre {
                    background: #18181b;
                    border: 1px solid #27272a;
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
                    border: 1px solid #e4e4e7;
                    max-width: 100%;
                    height: auto;
                    margin: 1.5rem 0;
                }
                .blog-prose hr {
                    border: none;
                    height: 1px;
                    background: #e4e4e7;
                    margin: 2rem 0;
                }
                .blog-prose table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 1.4rem;
                    font-size: 0.9375rem;
                }
                .blog-prose th {
                    background: #f4f4f5;
                    border: 1px solid #e4e4e7;
                    padding: 0.5rem 0.75rem;
                    text-align: left;
                    font-weight: 600;
                    color: #18181b;
                }
                .blog-prose td {
                    border: 1px solid #e4e4e7;
                    padding: 0.5rem 0.75rem;
                    color: #3f3f46;
                }
                .blog-prose tr:nth-child(even) td { background: #fafafa; }
            `}</style>
        </>
    );
}
