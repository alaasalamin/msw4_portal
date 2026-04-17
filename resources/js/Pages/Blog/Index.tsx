import { Head, Link, usePage } from '@inertiajs/react';
import { PageProps } from '@/types';
import { HomepageContent } from '@/Pages/Welcome/types';
import Navbar        from '@/Pages/Welcome/Navbar';
import FooterSection from '@/Pages/Welcome/FooterSection';

// ── Icons ─────────────────────────────────────────────────────────────────────

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
    canonicalUrl: string;
    homepage: HomepageContent;
}

interface SharedProps { site?: { name: string } }

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
    'linear-gradient(135deg, #fde68a 0%, #fb923c 100%)',
    'linear-gradient(135deg, #bfdbfe 0%, #6366f1 100%)',
    'linear-gradient(135deg, #bbf7d0 0%, #059669 100%)',
    'linear-gradient(135deg, #fecdd3 0%, #e11d48 100%)',
    'linear-gradient(135deg, #e9d5ff 0%, #7c3aed 100%)',
    'linear-gradient(135deg, #fed7aa 0%, #ea580c 100%)',
];

// ── Component ─────────────────────────────────────────────────────────────────

export default function BlogIndex({ auth, posts, categories, activeCategory, canonicalUrl, homepage }: Props) {
    const { site } = usePage().props as unknown as SharedProps;
    const siteName  = site?.name ?? 'Blog';
    const pageTitle = activeCategory ? `${activeCategory.name} — ${siteName}` : `Blog — ${siteName}`;
    const metaDesc  = activeCategory
        ? `Browse all articles in ${activeCategory.name}.`
        : `Insights, updates and stories from the ${siteName} team.`;

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
                <title>{pageTitle}</title>
                <meta name="description"      content={metaDesc} />
                <link rel="canonical"         href={canonicalUrl} />
                <meta property="og:type"      content="website" />
                <meta property="og:url"       content={canonicalUrl} />
                <meta property="og:site_name" content={siteName} />
                <meta property="og:title"     content={pageTitle} />
                <meta property="og:description" content={metaDesc} />
                <meta name="twitter:card"     content="summary" />
                <meta name="twitter:title"    content={pageTitle} />
                <meta name="twitter:description" content={metaDesc} />
            </Head>

            <Navbar portalLink={portalLink} canLogin={true} />

            <div className="min-h-dvh bg-zinc-50 pt-16">

                {/* ── Hero ────────────────────────────────────────────────── */}
                <div className="bg-white border-b border-zinc-200">
                    <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14 text-center">
                        <div className="inline-flex items-center gap-2 rounded-full bg-orange-50 border border-orange-200 px-3.5 py-1.5 mb-5">
                            <span className="text-orange-500"><IcoPen /></span>
                            <span className="text-xs font-semibold text-orange-600 tracking-wider uppercase">Journal</span>
                        </div>

                        {activeCategory ? (
                            <>
                                <h1 className="text-4xl sm:text-5xl font-bold tracking-tight text-zinc-900">
                                    {activeCategory.name}
                                </h1>
                                <p className="mt-3 text-zinc-400 text-sm">
                                    <Link href={route('blog.index')} className="hover:text-orange-500 transition-colors">Blog</Link>
                                    <span className="mx-2 text-zinc-300">/</span>
                                    <span className="text-zinc-500">{activeCategory.name}</span>
                                </p>
                            </>
                        ) : (
                            <>
                                <h1 className="text-4xl sm:text-5xl font-bold tracking-tight text-zinc-900">
                                    Latest from{' '}
                                    <span className="text-orange-500">{siteName}</span>
                                </h1>
                                <p className="mt-4 text-zinc-500 max-w-xl mx-auto text-base leading-relaxed">
                                    Insights, updates and stories from our team.
                                </p>
                            </>
                        )}

                        {posts.total > 0 && (
                            <p className="mt-3 text-xs text-zinc-400">
                                {posts.total} {posts.total === 1 ? 'article' : 'articles'}
                            </p>
                        )}
                    </div>
                </div>

                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">

                    {/* ── Category tabs ───────────────────────────────────── */}
                    {categories.length > 0 && (
                        <div className="flex flex-wrap gap-2 mb-10">
                            <Link
                                href={route('blog.index')}
                                className="inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 text-xs font-medium transition-all duration-200"
                                style={!activeCategory
                                    ? { background:'#fff7ed', border:'1px solid #fed7aa', color:'#ea580c' }
                                    : { background:'#fff', border:'1px solid #e4e4e7', color:'#71717a' }
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
                                        ? { background:'#fff7ed', border:'1px solid #fed7aa', color:'#ea580c' }
                                        : { background:'#fff', border:'1px solid #e4e4e7', color:'#71717a' }
                                    }
                                >
                                    <IcoTag />
                                    {cat.name}
                                    {cat.posts_count !== undefined && (
                                        <span className="opacity-60 ml-0.5">{cat.posts_count}</span>
                                    )}
                                </Link>
                            ))}
                        </div>
                    )}

                    {/* ── Posts grid ──────────────────────────────────────── */}
                    {posts.data.length === 0 ? (
                        <div className="rounded-2xl bg-white border border-zinc-200 p-16 text-center">
                            <p className="text-zinc-400 text-sm">No articles published yet.</p>
                            {activeCategory && (
                                <Link href={route('blog.index')} className="mt-3 inline-block text-xs text-orange-500 hover:text-orange-600">
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
                                            className="flex items-center justify-center h-9 px-3 rounded-lg text-sm text-zinc-300 cursor-not-allowed border border-zinc-200 bg-white"
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
                                            ? { background:'#fff7ed', border:'1px solid #fed7aa', color:'#ea580c' }
                                            : { background:'#fff', border:'1px solid #e4e4e7', color:'#52525b' }
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

            <FooterSection footer={homepage.footer} />
        </>
    );
}

// ── Post card ─────────────────────────────────────────────────────────────────

function PostCard({ post, gradientIndex, href }: { post: Post; gradientIndex: number; href: string }) {
    return (
        <Link
            href={href}
            className="group flex flex-col overflow-hidden rounded-2xl bg-white border border-zinc-200 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-zinc-200/80 hover:border-orange-200"
        >
            {/* Image / placeholder */}
            <div className="relative h-48 overflow-hidden bg-zinc-100">
                {post.featured_image ? (
                    <img
                        src={`/storage/${post.featured_image}`}
                        alt={post.title}
                        className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                        loading="lazy"
                        width={400}
                        height={192}
                    />
                ) : (
                    <div className="h-full w-full opacity-60" style={{ background: GRADIENTS[gradientIndex] }} />
                )}
                {post.category && (
                    <span className="absolute top-3 left-3 inline-flex items-center gap-1 rounded-full bg-white/90 backdrop-blur-sm border border-orange-200 px-2.5 py-1 text-xs font-medium text-orange-600 shadow-sm">
                        <IcoTag />
                        {post.category.name}
                    </span>
                )}
            </div>

            {/* Content */}
            <div className="flex flex-1 flex-col p-5">
                <h2 className="text-base font-semibold leading-snug text-zinc-900 group-hover:text-orange-600 transition-colors duration-200 line-clamp-2">
                    {post.title}
                </h2>
                {post.excerpt && (
                    <p className="mt-2 text-sm text-zinc-500 line-clamp-3 leading-relaxed">{post.excerpt}</p>
                )}

                <div className="mt-auto pt-5 flex items-center justify-between border-t border-zinc-100">
                    <div className="flex items-center gap-3 text-xs text-zinc-400">
                        <span className="flex items-center gap-1">
                            <IcoUser />
                            {post.author.name}
                        </span>
                        <span className="flex items-center gap-1">
                            <IcoCalendar />
                            {formatDate(post.published_at)}
                        </span>
                    </div>
                    <span className="text-zinc-300 group-hover:text-orange-500 transition-colors duration-200">
                        <IcoArrow />
                    </span>
                </div>
            </div>
        </Link>
    );
}
