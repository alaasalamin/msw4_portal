import { Link, usePage } from '@inertiajs/react';
import { IconArrowRight, IconWrench } from './icons';
import { HomepageContent } from './types';

interface FooterPage     { title: string; href: string }
interface FooterPost     { title: string; href: string }
interface FooterCategory { name: string; slug: string; posts: FooterPost[] }

interface SharedProps {
    footer_pages:      FooterPage[];
    footer_categories: FooterCategory[];
}

interface Props {
    footer: HomepageContent['footer'];
}

export default function FooterSection({ footer }: Props) {
    const { footer_pages = [], footer_categories = [] } = usePage<SharedProps>().props;

    return (
        <footer className="border-t border-zinc-800 bg-zinc-900">
            <div className="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <div className="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">

                    {/* ── Col 1: Branding ─────────────────────────────────── */}
                    <div>
                        <div className="flex items-center gap-2.5">
                            <span className="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-600">
                                <IconWrench className="h-4 w-4 text-white" />
                            </span>
                            <span className="font-display text-xl font-normal text-white">
                                Moon<span className="text-orange-400">.Repair</span>
                            </span>
                        </div>
                        <p className="mt-4 text-sm leading-relaxed text-zinc-400">{footer.tagline}</p>
                    </div>

                    {/* ── Col 2: Pages ────────────────────────────────────── */}
                    {footer_pages.length > 0 && (
                        <div>
                            <h3 className="mb-4 text-xs font-semibold uppercase tracking-widest text-zinc-400">Pages</h3>
                            <ul className="space-y-2 text-sm text-zinc-500">
                                {footer_pages.map((p) => (
                                    <li key={p.href}>
                                        <Link href={p.href} className="transition hover:text-zinc-300">
                                            {p.title}
                                        </Link>
                                    </li>
                                ))}
                            </ul>
                        </div>
                    )}

                    {/* ── Col 3: Blog categories + posts ──────────────────── */}
                    {footer_categories.length > 0 && (
                        <div>
                            <h3 className="mb-4 text-xs font-semibold uppercase tracking-widest text-zinc-400">Blog</h3>
                            <div className="space-y-5">
                                {footer_categories.map((cat) => (
                                    <div key={cat.slug}>
                                        <Link
                                            href={`/blog/category/${cat.slug}`}
                                            className="mb-1.5 block text-xs font-medium text-zinc-300 transition hover:text-white"
                                        >
                                            {cat.name}
                                        </Link>
                                        <ul className="space-y-1.5 text-sm text-zinc-500">
                                            {cat.posts.map((post) => (
                                                <li key={post.href} className="flex items-start gap-1.5">
                                                    <span className="mt-1.5 h-1 w-1 shrink-0 rounded-full bg-zinc-700" />
                                                    <Link href={post.href} className="transition hover:text-zinc-300 leading-snug">
                                                        {post.title}
                                                    </Link>
                                                </li>
                                            ))}
                                        </ul>
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}

                    {/* ── Col 4: Contact ──────────────────────────────────── */}
                    <div>
                        <h3 className="mb-4 text-xs font-semibold uppercase tracking-widest text-zinc-400">Contact</h3>
                        <ul className="space-y-2 text-sm text-zinc-500">
                            <li>
                                <a href={`mailto:${footer.emailHello}`} className="transition hover:text-zinc-300">
                                    {footer.emailHello}
                                </a>
                            </li>
                            <li>
                                <a href={`mailto:${footer.emailPartners}`} className="transition hover:text-zinc-300">
                                    {footer.emailPartners}
                                </a>
                            </li>
                        </ul>
                        <div className="mt-6">
                            <a
                                href="#login"
                                className="inline-flex cursor-pointer items-center gap-2 rounded-lg bg-orange-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-orange-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400"
                            >
                                Sign in
                                <IconArrowRight className="h-3.5 w-3.5" />
                            </a>
                        </div>
                    </div>

                </div>

                <div className="mt-10 flex flex-col items-center justify-between gap-4 border-t border-zinc-800 pt-8 sm:flex-row">
                    <p className="text-xs text-zinc-500">
                        © {new Date().getFullYear()} Moon.Repair · moon.repair · All rights reserved.
                    </p>
                    <p className="text-xs text-zinc-600">Built with MSW4</p>
                </div>
            </div>
        </footer>
    );
}
