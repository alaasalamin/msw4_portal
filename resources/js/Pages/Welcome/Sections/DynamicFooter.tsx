import { usePage } from '@inertiajs/react';

interface FooterSettings {
    tagline?: string;
    small?: string;
    showAddress?: boolean;
    showPages?: boolean;
    showSitemap?: boolean;
    showSocials?: boolean;
    bg?: string;
    fg?: string;
    mutedFg?: string;
    headingFg?: string;
}

interface FooterPage     { title: string; href: string }
interface FooterPost     { title: string; href: string }
interface FooterCategory { name: string; slug: string; posts: FooterPost[] }
interface Socials        {
    facebook?: string;
    instagram?: string;
    twitter?: string;
    linkedin?: string;
    youtube?: string;
    tiktok?: string;
    whatsapp?: string;
}
interface Company {
    name?: string;
    email?: string;
    phone?: string;
    street?: string;
    houseNumber?: string;
    postalCode?: string;
    city?: string;
    country?: string;
}
interface SharedProps {
    footer_pages: FooterPage[];
    footer_categories: FooterCategory[];
    site: { socials?: Socials };
    company?: Company;
}

// ── Social icons (single-color glyphs that take currentColor) ────────────────
const SocialIcon = ({ platform }: { platform: string }) => {
    const cls = { width: 18, height: 18, display: 'block' } as const;
    switch (platform) {
        case 'facebook':
            return (<svg style={cls} fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>);
        case 'instagram':
            return (<svg style={cls} fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162S8.597 18.163 12 18.163s6.162-2.759 6.162-6.162S15.403 5.838 12 5.838zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>);
        case 'twitter':
            return (<svg style={cls} fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>);
        case 'linkedin':
            return (<svg style={cls} fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>);
        case 'youtube':
            return (<svg style={cls} fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>);
        case 'tiktok':
            return (<svg style={cls} fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>);
        case 'whatsapp':
            return (<svg style={cls} fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>);
        default:
            return null;
    }
};

function buildAddressLines(c: Company): string[] {
    const lines: string[] = [];
    const street = [c.street, c.houseNumber].filter(Boolean).join(' ').trim();
    if (street) lines.push(street);
    const cityLine = [c.postalCode, c.city].filter(Boolean).join(' ').trim();
    if (cityLine) lines.push(cityLine);
    if (c.country) lines.push(c.country);
    return lines;
}

/** Replace {{company}} and {{year}} in user-provided strings. */
function interpolate(text: string | undefined, vars: Record<string, string>): string {
    if (!text) return '';
    return text.replace(/\{\{\s*(\w+)\s*\}\}/g, (_m, key) => vars[key] ?? '');
}

export default function DynamicFooter({ settings }: { settings: FooterSettings }) {
    const { footer_pages = [], footer_categories = [], site, company = {} } = usePage<SharedProps>().props;

    const showSocials = settings.showSocials !== false;
    const showAddress = settings.showAddress !== false;
    const showPages   = settings.showPages   !== false;
    const showSitemap = settings.showSitemap !== false;

    const socials       = (site?.socials ?? {}) as Socials;
    const socialEntries = Object.entries(socials).filter(([, url]) => !!url) as [string, string][];

    const addressLines = buildAddressLines(company);
    const tplVars = {
        company: company.name ?? '',
        year:    String(new Date().getFullYear()),
    };
    const tagline = interpolate(settings.tagline, tplVars);
    // Bottom-bar small print is fixed site-wide — not user-editable.
    const small   = 'bizo.technology';
    const hasAddress   = !!(company.name || addressLines.length || company.email || company.phone);
    const hasPages     = footer_pages.length > 0;
    const hasSitemap   = footer_categories.length > 0;

    const renderAddress = showAddress && hasAddress;
    const renderPages   = showPages   && hasPages;
    const renderSitemap = showSitemap && hasSitemap;

    const gridCols = 1 + (renderAddress ? 1 : 0) + (renderPages ? 1 : 0) + (renderSitemap ? 1 : 0);

    const fg        = settings.fg ?? '#e2e8f0';
    const mutedFg   = settings.mutedFg ?? '#94a3b8';
    const headingFg = settings.headingFg ?? '#ffffff';

    const linkStyle: React.CSSProperties = {
        color: fg,
        textDecoration: 'none',
        transition: 'opacity .12s ease',
    };
    const mutedLinkStyle: React.CSSProperties = {
        color: mutedFg,
        textDecoration: 'none',
        transition: 'opacity .12s ease',
    };

    return (
        <footer
            style={{
                background: settings.bg ?? '#111111',
                color: fg,
                padding: 'clamp(40px, 6vw, 64px) clamp(16px, 4vw, 32px) clamp(24px, 4vw, 32px)',
                fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
                fontSize: '0.9375rem',
                lineHeight: 1.55,
            }}
        >
            <div style={{ maxWidth: '1200px', margin: '0 auto' }}>
                <div
                    className="tb-footer-grid"
                    style={{
                        display: 'grid',
                        gridTemplateColumns: `repeat(${gridCols}, minmax(0, 1fr))`,
                        gap: 'clamp(24px, 3vw, 40px)',
                    }}
                >
                    {/* Brand + tagline + socials */}
                    <div>
                        {company.name && (
                            <div style={{ color: headingFg, fontWeight: 700, fontSize: '1.125rem', letterSpacing: '-0.01em', marginBottom: 8 }}>
                                {company.name}
                            </div>
                        )}
                        {tagline && (
                            <p style={{ margin: 0, color: mutedFg, lineHeight: 1.6, maxWidth: '36ch' }}>
                                {tagline}
                            </p>
                        )}
                        {showSocials && socialEntries.length > 0 && (
                            <div style={{ marginTop: 16, display: 'flex', flexWrap: 'wrap', gap: 8 }}>
                                {socialEntries.map(([platform, url]) => (
                                    <a
                                        key={platform}
                                        href={url}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        aria-label={platform}
                                        style={{
                                            display: 'inline-flex',
                                            alignItems: 'center',
                                            justifyContent: 'center',
                                            width: 36,
                                            height: 36,
                                            borderRadius: 8,
                                            border: '1px solid rgba(255,255,255,0.10)',
                                            color: mutedFg,
                                            transition: 'color .12s ease, border-color .12s ease, background .12s ease',
                                        }}
                                    >
                                        <SocialIcon platform={platform} />
                                    </a>
                                ))}
                            </div>
                        )}
                    </div>

                    {/* Address */}
                    {renderAddress && (
                        <div>
                            <h3 style={{ color: headingFg, fontSize: '0.75rem', fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.12em', margin: '0 0 14px' }}>
                                Contact
                            </h3>
                            <address style={{ fontStyle: 'normal', color: mutedFg, lineHeight: 1.7 }}>
                                {addressLines.map((line, i) => (
                                    <div key={i}>{line}</div>
                                ))}
                                {(company.email || company.phone) && <div style={{ height: 8 }} />}
                                {company.email && (
                                    <div>
                                        <a href={`mailto:${company.email}`} style={mutedLinkStyle}>{company.email}</a>
                                    </div>
                                )}
                                {company.phone && (
                                    <div>
                                        <a href={`tel:${company.phone.replace(/\s+/g, '')}`} style={mutedLinkStyle}>{company.phone}</a>
                                    </div>
                                )}
                            </address>
                        </div>
                    )}

                    {/* Site pages */}
                    {renderPages && (
                        <div>
                            <h3 style={{ color: headingFg, fontSize: '0.75rem', fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.12em', margin: '0 0 14px' }}>
                                Pages
                            </h3>
                            <ul style={{ listStyle: 'none', padding: 0, margin: 0, display: 'flex', flexDirection: 'column', gap: 6 }}>
                                {footer_pages.map((p) => (
                                    <li key={p.href}>
                                        <a href={p.href} style={mutedLinkStyle}>{p.title}</a>
                                    </li>
                                ))}
                            </ul>
                        </div>
                    )}

                    {/* Blog sitemap (categories + posts) */}
                    {renderSitemap && (
                        <div>
                            <h3 style={{ color: headingFg, fontSize: '0.75rem', fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.12em', margin: '0 0 14px' }}>
                                Blog
                            </h3>
                            <div style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>
                                {footer_categories.map((cat) => (
                                    <div key={cat.slug}>
                                        <a href={`/blog/category/${cat.slug}`} style={{ ...linkStyle, fontWeight: 600, fontSize: '0.875rem', display: 'block', marginBottom: 6 }}>
                                            {cat.name}
                                        </a>
                                        <ul style={{ listStyle: 'none', padding: 0, margin: 0, display: 'flex', flexDirection: 'column', gap: 4 }}>
                                            {cat.posts.map((post) => (
                                                <li key={post.href} style={{ display: 'flex', alignItems: 'flex-start', gap: 6 }}>
                                                    <span style={{ width: 4, height: 4, borderRadius: '9999px', background: mutedFg, marginTop: 9, flexShrink: 0, opacity: 0.6 }} />
                                                    <a href={post.href} style={{ ...mutedLinkStyle, fontSize: '0.8125rem', lineHeight: 1.5 }}>{post.title}</a>
                                                </li>
                                            ))}
                                        </ul>
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}
                </div>

                {/* Bottom bar */}
                <div
                    style={{
                        marginTop: 'clamp(28px, 4vw, 48px)',
                        paddingTop: 'clamp(20px, 2.5vw, 28px)',
                        borderTop: '1px solid rgba(255,255,255,0.08)',
                        display: 'flex',
                        flexWrap: 'wrap',
                        gap: 12,
                        justifyContent: 'space-between',
                        alignItems: 'center',
                        color: mutedFg,
                        fontSize: '0.8125rem',
                    }}
                >
                    <p style={{ margin: 0 }}>{tagline || `© ${new Date().getFullYear()}`}</p>
                    {small && <p style={{ margin: 0, opacity: 0.8 }}>{small}</p>}
                </div>
            </div>

            <style>{`
                @media (max-width: 900px) {
                    .tb-footer-grid {
                        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                    }
                }
                @media (max-width: 560px) {
                    .tb-footer-grid {
                        grid-template-columns: 1fr !important;
                    }
                }
            `}</style>
        </footer>
    );
}
