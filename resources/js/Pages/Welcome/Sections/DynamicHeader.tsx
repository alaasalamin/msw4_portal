import { useState } from 'react';
import { usePage } from '@inertiajs/react';

interface NavLink { label: string; href: string }
interface Brand   { prefix: string; suffix: string }

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
}

export default function DynamicHeader({ settings }: { settings: HeaderSettings }) {
    const variant = settings.variant ?? 'classic';
    switch (variant) {
        case 'centered': return <CenteredHeader settings={settings} />;
        case 'split':    return <SplitHeader    settings={settings} />;
        case 'minimal':  return <MinimalHeader  settings={settings} />;
        case 'classic':
        default:         return <ClassicHeader  settings={settings} />;
    }
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

function ClassicHeader({ settings }: { settings: HeaderSettings }) {
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
            <style>{responsiveCss}</style>
        </header>
    );
}

// ── Variant: centered (logo above nav row) ─────────────────────────────────

function CenteredHeader({ settings }: { settings: HeaderSettings }) {
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
        </header>
    );
}

// ── Variant: split (logo / centered nav / CTA button) ──────────────────────

function SplitHeader({ settings }: { settings: HeaderSettings }) {
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

function MinimalHeader({ settings }: { settings: HeaderSettings }) {
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
        </header>
    );
}
