import { useState, type ReactNode } from 'react';
import { usePage } from '@inertiajs/react';

interface NavLink { label: string; href: string }
interface Brand   { prefix: string; suffix: string }
interface NavCategory { name: string; slug: string; posts: { title: string; href: string }[] }

interface HeaderSettings {
    variant?: 'classic' | 'centered' | 'split' | 'minimal';
    logo?: string;
    logoImage?: string | null;
    logoHeight?: number | string;
    linksMode?: 'auto' | 'custom';
    links?: NavLink[];
    bg?: string;
    fg?: string;
    linkColor?: string;
    sticky?: boolean | string | number;
    ctaText?: string;
    ctaHref?: string;
    showCategoriesBar?: boolean | string | number;
    categoriesBarBg?: string;
    categoriesBarFg?: string;
}

export default function DynamicHeader({ settings }: { settings: HeaderSettings }) {
    const variant = settings.variant ?? 'classic';
    const showBar = settings.showCategoriesBar === true || settings.showCategoriesBar === '1' || settings.showCategoriesBar === 1;
    // Categories bar is rendered as the last child INSIDE the <header> so
    // there is no sibling boundary between the two — they're flush, share
    // the same sticky behavior, and the Filament settings don't need a
    // brittle "stick at 64px" hack to align with the actual header height.
    const bottom: ReactNode = showBar ? <CategoriesBar settings={settings} /> : null;
    switch (variant) {
        case 'centered': return <CenteredHeader settings={settings} bottom={bottom} />;
        case 'split':    return <SplitHeader    settings={settings} bottom={bottom} />;
        case 'minimal':  return <MinimalHeader  settings={settings} bottom={bottom} />;
        case 'classic':
        default:         return <ClassicHeader  settings={settings} bottom={bottom} />;
    }
}

// ── Categories bar with per-category hover dropdowns ────────────────────────

function CategoriesBar({ settings }: { settings: HeaderSettings }) {
    const { nav_categories = [] } = usePage<{ nav_categories: NavCategory[] }>().props;
    const [openSlug, setOpenSlug] = useState<string | null>(null);
    if (!nav_categories.length) return null;

    const bg = settings.categoriesBarBg ?? '#1f2937';
    const fg = settings.categoriesBarFg ?? '#e5e7eb';

    return (
        <div
            style={{
                background: bg,
                color: fg,
                borderTop: '1px solid rgba(255,255,255,.06)',
                fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
            }}
        >
            <div
                className="tb-cat-bar"
                style={{
                    maxWidth: '1200px',
                    margin: '0 auto',
                    padding: '0 clamp(16px, 4vw, 32px)',
                    display: 'flex',
                    flexWrap: 'wrap',
                    alignItems: 'stretch',
                    gap: 0,
                }}
            >
                {nav_categories.map((cat) => {
                    const isOpen = openSlug === cat.slug;
                    return (
                        <div
                            key={cat.slug}
                            onMouseEnter={() => setOpenSlug(cat.slug)}
                            onMouseLeave={() => setOpenSlug((s) => (s === cat.slug ? null : s))}
                            style={{ position: 'relative' }}
                        >
                            <a
                                href={`/blog/category/${cat.slug}`}
                                onClick={(e) => {
                                    if (!isOpen && cat.posts.length > 0) {
                                        e.preventDefault();
                                        setOpenSlug(cat.slug);
                                    }
                                }}
                                aria-haspopup={cat.posts.length > 0 ? 'menu' : undefined}
                                aria-expanded={cat.posts.length > 0 ? isOpen : undefined}
                                style={{
                                    color: fg,
                                    textDecoration: 'none',
                                    fontSize: '0.875rem',
                                    fontWeight: 500,
                                    padding: '12px 16px',
                                    display: 'inline-flex',
                                    alignItems: 'center',
                                    gap: 6,
                                    background: isOpen ? 'rgba(255,255,255,.06)' : 'transparent',
                                    transition: 'background 120ms ease',
                                }}
                            >
                                {cat.name}
                                {cat.posts.length > 0 && (
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" style={{ opacity: 0.7 }}>
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M6 9l6 6 6-6" />
                                    </svg>
                                )}
                            </a>

                            {isOpen && cat.posts.length > 0 && (
                                <div
                                    role="menu"
                                    style={{
                                        position: 'absolute',
                                        top: '100%',
                                        left: 0,
                                        minWidth: 280,
                                        background: '#ffffff',
                                        color: '#0f172a',
                                        boxShadow: '0 10px 32px rgba(0,0,0,.18)',
                                        borderRadius: 8,
                                        padding: '8px 0',
                                        marginTop: 0,
                                        zIndex: 60,
                                    }}
                                >
                                    {cat.posts.slice(0, 8).map((p, i) => (
                                        <a
                                            key={i}
                                            href={p.href}
                                            role="menuitem"
                                            style={{
                                                display: 'block',
                                                padding: '8px 16px',
                                                color: '#0f172a',
                                                textDecoration: 'none',
                                                fontSize: '0.875rem',
                                                lineHeight: 1.4,
                                                whiteSpace: 'nowrap',
                                                overflow: 'hidden',
                                                textOverflow: 'ellipsis',
                                                maxWidth: 360,
                                            }}
                                            onMouseEnter={(e) => (e.currentTarget.style.background = '#f1f5f9')}
                                            onMouseLeave={(e) => (e.currentTarget.style.background = 'transparent')}
                                        >
                                            {p.title}
                                        </a>
                                    ))}
                                    {cat.posts.length > 8 && (
                                        <a
                                            href={`/blog/category/${cat.slug}`}
                                            role="menuitem"
                                            style={{
                                                display: 'block',
                                                padding: '8px 16px',
                                                color: 'var(--primary, #0284c7)',
                                                textDecoration: 'none',
                                                fontSize: '0.8125rem',
                                                fontWeight: 600,
                                                borderTop: '1px solid #e2e8f0',
                                                marginTop: 4,
                                            }}
                                        >
                                            View all in {cat.name} →
                                        </a>
                                    )}
                                </div>
                            )}
                        </div>
                    );
                })}
            </div>
            <style>{`
                @media (max-width: 720px) {
                    .tb-cat-bar { overflow-x: auto; flex-wrap: nowrap !important; }
                    .tb-cat-bar > div > a { white-space: nowrap; }
                }
            `}</style>
        </div>
    );
}

// ── Shared helpers ──────────────────────────────────────────────────────────

function useDerived(settings: HeaderSettings) {
    const sticky = settings.sticky === true || settings.sticky === '1' || settings.sticky === 1;
    const fg = settings.fg ?? '#ffffff';
    const bg = settings.bg ?? '#111111';
    const linkColor = settings.linkColor ?? '#cbd5e1';

    const { nav_pages = [] } = usePage<{ nav_pages: NavLink[] }>().props;
    const mode = settings.linksMode ?? 'auto';
    const links: NavLink[] = mode === 'custom' ? (settings.links ?? []) : nav_pages;

    return { sticky, fg, bg, linkColor, links };
}

function Logo({ settings }: { settings: HeaderSettings }) {
    return (
        <a
            href="/"
            style={{
                color: settings.fg ?? '#ffffff',
                fontWeight: 700,
                fontSize: '1.125rem',
                letterSpacing: '-0.01em',
                textDecoration: 'none',
                display: 'inline-flex',
                alignItems: 'center',
                lineHeight: 1,
            }}
        >
            {settings.logoImage ? (
                <img
                    src={`/storage/${settings.logoImage}`}
                    alt={settings.logo ?? 'Logo'}
                    style={{
                        height: `${Number(settings.logoHeight) || 48}px`,
                        width: 'auto',
                        display: 'block',
                        objectFit: 'contain',
                    }}
                />
            ) : (
                settings.logo ?? 'Brand'
            )}
        </a>
    );
}

function NavLinks({ links, color, gap = 24, fontSize = '0.9375rem' }: { links: NavLink[]; color: string; gap?: number; fontSize?: string }) {
    return (
        <>
            {links.map((l, i) => (
                <a
                    key={i}
                    href={l.href}
                    style={{
                        color,
                        textDecoration: 'none',
                        fontSize,
                        fontWeight: 500,
                    }}
                >
                    {l.label}
                </a>
            ))}
        </>
    );
}

function Burger({ open, onToggle, color, className }: { open: boolean; onToggle: () => void; color: string; className?: string }) {
    return (
        <button
            type="button"
            aria-label="Toggle menu"
            aria-expanded={open}
            onClick={onToggle}
            className={className ?? 'tb-header-burger'}
            style={{
                display: className ? 'inline-flex' : 'none',
                alignItems: 'center',
                background: 'transparent',
                border: 0,
                color,
                cursor: 'pointer',
                padding: 6,
            }}
        >
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                {open
                    ? <path strokeLinecap="round" strokeLinejoin="round" d="M6 18 18 6M6 6l12 12" />
                    : <path strokeLinecap="round" strokeLinejoin="round" d="M4 6h16M4 12h16M4 18h16" />}
            </svg>
        </button>
    );
}

function MobileMenu({ open, links, linkColor, onLinkClick }: { open: boolean; links: NavLink[]; linkColor: string; onLinkClick: () => void }) {
    if (!open) return null;
    return (
        <div className="tb-header-mobile" style={{ display: 'none', borderTop: '1px solid rgba(255,255,255,.08)' }}>
            <nav style={{ padding: '8px 16px 16px', display: 'flex', flexDirection: 'column', gap: 4 }}>
                {links.map((l, i) => (
                    <a key={i} href={l.href} onClick={onLinkClick} style={{
                        color: linkColor,
                        textDecoration: 'none',
                        padding: '10px 8px',
                        borderRadius: 6,
                        fontSize: '0.9375rem',
                    }}>{l.label}</a>
                ))}
            </nav>
        </div>
    );
}

const baseHeaderStyle = (bg: string, fg: string, sticky: boolean): React.CSSProperties => ({
    position: sticky ? 'sticky' : 'static',
    top: 0,
    zIndex: 50,
    background: bg,
    color: fg,
    width: '100%',
    fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
});

const responsiveCss = `
    @media (max-width: 720px) {
        .tb-header-desktop { display: none !important; }
        .tb-header-burger  { display: inline-flex !important; }
        .tb-header-mobile  { display: block !important; }
        .tb-header-cta     { display: none !important; }
    }
`;

// ── Variant: classic ───────────────────────────────────────────────────────

function ClassicHeader({ settings, bottom }: { settings: HeaderSettings; bottom?: ReactNode }) {
    const [open, setOpen] = useState(false);
    const d = useDerived(settings);

    return (
        <header style={baseHeaderStyle(d.bg, d.fg, d.sticky)}>
            <div style={{
                maxWidth: '1200px',
                margin: '0 auto',
                padding: '14px clamp(16px, 4vw, 32px)',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'space-between',
                gap: 24,
            }}>
                <Logo settings={settings} />
                <nav className="tb-header-desktop" style={{ display: 'flex', gap: 24 }}>
                    <NavLinks links={d.links} color={d.linkColor} />
                </nav>
                <Burger open={open} onToggle={() => setOpen((v) => !v)} color={d.fg} />
            </div>
            <MobileMenu open={open} links={d.links} linkColor={d.linkColor} onLinkClick={() => setOpen(false)} />
            {bottom}
            <style>{responsiveCss}</style>
        </header>
    );
}

// ── Variant: centered (logo above nav row) ─────────────────────────────────

function CenteredHeader({ settings, bottom }: { settings: HeaderSettings; bottom?: ReactNode }) {
    const [open, setOpen] = useState(false);
    const d = useDerived(settings);

    return (
        <header style={baseHeaderStyle(d.bg, d.fg, d.sticky)}>
            <div style={{
                position: 'relative',
                maxWidth: '1200px',
                margin: '0 auto',
                padding: '14px clamp(16px, 4vw, 32px)',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
            }}>
                <Logo settings={settings} />
                {/* Burger is always visible in this variant; the nav stays
                    behind it on every screen size. */}
                {d.links.length > 0 && (
                    <button
                        type="button"
                        aria-label="Toggle menu"
                        aria-expanded={open}
                        onClick={() => setOpen((v) => !v)}
                        style={{
                            position: 'absolute',
                            right: 14,
                            top: '50%',
                            transform: 'translateY(-50%)',
                            display: 'inline-flex',
                            alignItems: 'center',
                            background: 'transparent',
                            border: 0,
                            color: d.fg,
                            cursor: 'pointer',
                            padding: 6,
                        }}
                    >
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                            {open
                                ? <path strokeLinecap="round" strokeLinejoin="round" d="M6 18 18 6M6 6l12 12" />
                                : <path strokeLinecap="round" strokeLinejoin="round" d="M4 6h16M4 12h16M4 18h16" />}
                        </svg>
                    </button>
                )}
            </div>
            {open && (
                <div style={{ borderTop: '1px solid rgba(255,255,255,.08)' }}>
                    <nav style={{ padding: '8px 16px 16px', display: 'flex', flexDirection: 'column', gap: 4, maxWidth: '1200px', margin: '0 auto' }}>
                        {d.links.map((l, i) => (
                            <a key={i} href={l.href} onClick={() => setOpen(false)} style={{
                                color: d.linkColor,
                                textDecoration: 'none',
                                padding: '10px 8px',
                                borderRadius: 6,
                                fontSize: '0.9375rem',
                            }}>{l.label}</a>
                        ))}
                    </nav>
                </div>
            )}
            {bottom}
        </header>
    );
}

// ── Variant: split (logo / centered nav / CTA button) ──────────────────────

function SplitHeader({ settings, bottom }: { settings: HeaderSettings; bottom?: ReactNode }) {
    const [open, setOpen] = useState(false);
    const d = useDerived(settings);
    const ctaText = (settings.ctaText ?? '').trim();

    return (
        <header style={baseHeaderStyle(d.bg, d.fg, d.sticky)}>
            <div style={{
                maxWidth: '1200px',
                margin: '0 auto',
                padding: '14px clamp(16px, 4vw, 32px)',
                display: 'grid',
                gridTemplateColumns: 'auto 1fr auto',
                alignItems: 'center',
                gap: 24,
            }}>
                <Logo settings={settings} />
                <nav className="tb-header-desktop" style={{ display: 'flex', gap: 28, justifyContent: 'center' }}>
                    <NavLinks links={d.links} color={d.linkColor} />
                </nav>
                <div style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
                    {ctaText && (
                        <a
                            href={settings.ctaHref || '#'}
                            className="tb-header-cta"
                            style={{
                                background: 'var(--primary, #0284c7)',
                                color: '#ffffff',
                                padding: '9px 18px',
                                borderRadius: 9,
                                fontWeight: 600,
                                fontSize: '0.875rem',
                                textDecoration: 'none',
                                letterSpacing: '0.005em',
                                whiteSpace: 'nowrap',
                            }}
                        >
                            {ctaText}
                        </a>
                    )}
                    <Burger open={open} onToggle={() => setOpen((v) => !v)} color={d.fg} />
                </div>
            </div>
            <MobileMenu open={open} links={d.links} linkColor={d.linkColor} onLinkClick={() => setOpen(false)} />
            {bottom}
            <style>{`
                ${responsiveCss}
                @media (max-width: 720px) {
                    .tb-header-cta { display: inline-flex !important; }
                }
            `}</style>
        </header>
    );
}

// ── Variant: minimal (logo only, nav always behind hamburger) ──────────────

function MinimalHeader({ settings, bottom }: { settings: HeaderSettings; bottom?: ReactNode }) {
    const [open, setOpen] = useState(false);
    const d = useDerived(settings);

    return (
        <header style={baseHeaderStyle(d.bg, d.fg, d.sticky)}>
            <div style={{
                maxWidth: '1200px',
                margin: '0 auto',
                padding: '14px clamp(16px, 4vw, 32px)',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'space-between',
                gap: 24,
            }}>
                <Logo settings={settings} />
                {/* No desktop nav — every link goes through the panel */}
                {d.links.length > 0 && (
                    <button
                        type="button"
                        aria-label="Toggle menu"
                        aria-expanded={open}
                        onClick={() => setOpen((v) => !v)}
                        style={{
                            display: 'inline-flex',
                            alignItems: 'center',
                            background: 'transparent',
                            border: 0,
                            color: d.fg,
                            cursor: 'pointer',
                            padding: 6,
                        }}
                    >
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                            {open
                                ? <path strokeLinecap="round" strokeLinejoin="round" d="M6 18 18 6M6 6l12 12" />
                                : <path strokeLinecap="round" strokeLinejoin="round" d="M4 6h16M4 12h16M4 18h16" />}
                        </svg>
                    </button>
                )}
            </div>
            {open && (
                <div style={{ borderTop: '1px solid rgba(255,255,255,.08)' }}>
                    <nav style={{ padding: '8px 16px 16px', display: 'flex', flexDirection: 'column', gap: 4, maxWidth: '1200px', margin: '0 auto' }}>
                        {d.links.map((l, i) => (
                            <a key={i} href={l.href} onClick={() => setOpen(false)} style={{
                                color: d.linkColor,
                                textDecoration: 'none',
                                padding: '10px 8px',
                                borderRadius: 6,
                                fontSize: '0.9375rem',
                            }}>{l.label}</a>
                        ))}
                    </nav>
                </div>
            )}
            {bottom}
        </header>
    );
}
