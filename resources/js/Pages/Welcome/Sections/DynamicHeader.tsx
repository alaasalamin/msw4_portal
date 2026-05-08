import { useState } from 'react';
import { usePage } from '@inertiajs/react';

interface NavLink { label: string; href: string }
interface HeaderSettings {
    logo?: string;
    logoImage?: string | null;
    logoHeight?: number | string;
    linksMode?: 'auto' | 'custom';
    links?: NavLink[];
    bg?: string;
    fg?: string;
    linkColor?: string;
    sticky?: boolean | string | number;
}

export default function DynamicHeader({ settings }: { settings: HeaderSettings }) {
    const [open, setOpen] = useState(false);
    const sticky = settings.sticky === true || settings.sticky === '1' || settings.sticky === 1;

    // Auto mode pulls from the shared `nav_pages` (published Site Pages); custom uses `settings.links`.
    const { nav_pages = [] } = usePage<{ nav_pages: NavLink[] }>().props;
    const mode = settings.linksMode ?? 'auto';
    const links: NavLink[] = mode === 'custom' ? (settings.links ?? []) : nav_pages;

    return (
        <header
            style={{
                position: sticky ? 'sticky' : 'static',
                top: 0,
                zIndex: 50,
                background: settings.bg ?? '#111111',
                color: settings.fg ?? '#ffffff',
                width: '100%',
                fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
            }}
        >
            <div
                style={{
                    maxWidth: '1200px',
                    margin: '0 auto',
                    padding: '14px clamp(16px, 4vw, 32px)',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'space-between',
                    gap: '24px',
                }}
            >
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

                <nav className="tb-header-desktop" style={{ display: 'flex', gap: '24px' }}>
                    {links.map((l, i) => (
                        <a
                            key={i}
                            href={l.href}
                            style={{
                                color: settings.linkColor ?? '#cbd5e1',
                                textDecoration: 'none',
                                fontSize: '0.9375rem',
                                fontWeight: 500,
                            }}
                        >
                            {l.label}
                        </a>
                    ))}
                </nav>

                <button
                    type="button"
                    aria-label="Toggle menu"
                    onClick={() => setOpen((v) => !v)}
                    className="tb-header-burger"
                    style={{
                        display: 'none',
                        background: 'transparent',
                        border: 0,
                        color: settings.fg ?? '#ffffff',
                        cursor: 'pointer',
                        padding: '6px',
                    }}
                >
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                        {open
                            ? <path strokeLinecap="round" strokeLinejoin="round" d="M6 18 18 6M6 6l12 12" />
                            : <path strokeLinecap="round" strokeLinejoin="round" d="M4 6h16M4 12h16M4 18h16" />}
                    </svg>
                </button>
            </div>

            {open && (
                <div className="tb-header-mobile" style={{ display: 'none', borderTop: `1px solid rgba(255,255,255,.08)` }}>
                    <nav style={{ padding: '8px 16px 16px', display: 'flex', flexDirection: 'column', gap: '4px' }}>
                        {links.map((l, i) => (
                            <a
                                key={i}
                                href={l.href}
                                onClick={() => setOpen(false)}
                                style={{
                                    color: settings.linkColor ?? '#cbd5e1',
                                    textDecoration: 'none',
                                    padding: '10px 8px',
                                    borderRadius: '6px',
                                    fontSize: '0.9375rem',
                                }}
                            >
                                {l.label}
                            </a>
                        ))}
                    </nav>
                </div>
            )}

            <style>{`
                @media (max-width: 720px) {
                    .tb-header-desktop { display: none !important; }
                    .tb-header-burger  { display: inline-flex !important; }
                    .tb-header-mobile  { display: block !important; }
                }
            `}</style>
        </header>
    );
}
