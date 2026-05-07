import { usePage } from '@inertiajs/react';

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

interface MapSettings {
    heading?: string;
    subtitle?: string;
    addressMode?: 'auto' | 'custom';
    customAddress?: string;
    height?: number | string;
    zoom?: number | string;
    layout?: 'split' | 'full';
    showAddressText?: boolean;
    showDirectionsBtn?: boolean;
    directionsLabel?: string;
    bg?: string;
    fg?: string;
    mutedFg?: string;
}

interface SharedProps { company?: Company }

function buildAutoAddress(c: Company): string {
    return [
        [c.street, c.houseNumber].filter(Boolean).join(' ').trim(),
        [c.postalCode, c.city].filter(Boolean).join(' ').trim(),
        c.country,
    ].filter(Boolean).join(', ').trim();
}

export default function DynamicMap({ settings }: { settings: MapSettings }) {
    const { company = {} } = usePage<SharedProps>().props;

    const address = settings.addressMode === 'custom'
        ? (settings.customAddress ?? '').trim()
        : buildAutoAddress(company);

    const height  = Math.max(200, Math.min(900, Number(settings.height) || 420));
    const zoom    = Math.max(1, Math.min(20, Number(settings.zoom) || 15));
    const layout  = settings.layout ?? 'split';
    const showAddressText   = settings.showAddressText   !== false;
    const showDirectionsBtn = settings.showDirectionsBtn !== false;

    const fg      = settings.fg      ?? '#0f172a';
    const mutedFg = settings.mutedFg ?? '#64748b';

    const hasAddress = address.length > 0;
    // Google Maps "embed" URL that doesn't require an API key.
    const embedSrc     = `https://maps.google.com/maps?q=${encodeURIComponent(address)}&z=${zoom}&output=embed`;
    const directionsHref = `https://www.google.com/maps/dir/?api=1&destination=${encodeURIComponent(address)}`;

    const addressLines = settings.addressMode === 'custom'
        ? (settings.customAddress ?? '').split(/\r?\n|,\s*/).map((s) => s.trim()).filter(Boolean)
        : [
            [company.street, company.houseNumber].filter(Boolean).join(' ').trim(),
            [company.postalCode, company.city].filter(Boolean).join(' ').trim(),
            company.country,
          ].filter(Boolean) as string[];

    return (
        <section
            style={{
                background: settings.bg ?? '#ffffff',
                color: fg,
                padding: 'clamp(48px, 8vw, 96px) clamp(16px, 4vw, 32px)',
                fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
            }}
        >
            <div style={{ maxWidth: '1200px', margin: '0 auto' }}>
                {(settings.heading || settings.subtitle) && (
                    <div style={{ textAlign: 'center', marginBottom: 'clamp(28px, 4vw, 48px)' }}>
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
                )}

                {!hasAddress ? (
                    <div style={{
                        padding: 'clamp(24px, 4vw, 40px)',
                        background: '#f8fafc',
                        borderRadius: 16,
                        textAlign: 'center',
                        color: mutedFg,
                    }}>
                        <p style={{ margin: 0 }}>No address configured. Add one in <code>/admin/company-details</code> or switch to a custom address in the section settings.</p>
                    </div>
                ) : layout === 'split' && (showAddressText || showDirectionsBtn) ? (
                    <div
                        className="tb-map-grid"
                        style={{
                            display: 'grid',
                            gridTemplateColumns: '1.5fr 1fr',
                            gap: 'clamp(20px, 2.5vw, 32px)',
                            alignItems: 'stretch',
                        }}
                    >
                        <MapFrame src={embedSrc} height={height} />
                        <AddressPanel
                            company={company}
                            addressLines={addressLines}
                            directionsHref={directionsHref}
                            showAddress={showAddressText}
                            showButton={showDirectionsBtn}
                            buttonLabel={settings.directionsLabel ?? 'Get directions'}
                            fg={fg}
                            mutedFg={mutedFg}
                        />
                    </div>
                ) : (
                    <>
                        <MapFrame src={embedSrc} height={height} />
                        {(showAddressText || showDirectionsBtn) && (
                            <div style={{ marginTop: 24, textAlign: 'center' }}>
                                {showAddressText && (
                                    <address style={{ fontStyle: 'normal', color: mutedFg, lineHeight: 1.6, margin: 0 }}>
                                        {addressLines.map((line, i) => <div key={i}>{line}</div>)}
                                    </address>
                                )}
                                {showDirectionsBtn && (
                                    <DirectionsButton href={directionsHref} label={settings.directionsLabel ?? 'Get directions'} />
                                )}
                            </div>
                        )}
                    </>
                )}
            </div>

            <style>{`
                @media (max-width: 760px) {
                    .tb-map-grid { grid-template-columns: 1fr !important; }
                }
            `}</style>
        </section>
    );
}

// ── helpers ──────────────────────────────────────────────────────────────────

function MapFrame({ src, height }: { src: string; height: number }) {
    return (
        <div style={{
            position: 'relative',
            width: '100%',
            height: `${height}px`,
            borderRadius: 16,
            overflow: 'hidden',
            boxShadow: '0 1px 2px rgba(15,23,42,.08), 0 8px 24px -12px rgba(15,23,42,.14)',
            background: '#e2e8f0',
        }}>
            <iframe
                src={src}
                title="Map"
                loading="lazy"
                referrerPolicy="no-referrer-when-downgrade"
                allowFullScreen
                style={{ position: 'absolute', inset: 0, width: '100%', height: '100%', border: 0, display: 'block' }}
            />
        </div>
    );
}

function AddressPanel({
    company, addressLines, directionsHref, showAddress, showButton, buttonLabel, fg, mutedFg,
}: {
    company: Company;
    addressLines: string[];
    directionsHref: string;
    showAddress: boolean;
    showButton: boolean;
    buttonLabel: string;
    fg: string;
    mutedFg: string;
}) {
    return (
        <div style={{
            background: '#f8fafc',
            borderRadius: 16,
            padding: 'clamp(20px, 2.5vw, 28px)',
            display: 'flex',
            flexDirection: 'column',
            justifyContent: 'center',
            gap: 14,
        }}>
            {showAddress && (
                <>
                    {company.name && (
                        <div style={{ color: fg, fontWeight: 700, fontSize: '1.0625rem', letterSpacing: '-0.01em' }}>
                            {company.name}
                        </div>
                    )}
                    <address style={{ fontStyle: 'normal', color: mutedFg, lineHeight: 1.65, margin: 0, fontSize: '0.9375rem' }}>
                        {addressLines.map((line, i) => <div key={i}>{line}</div>)}
                    </address>
                    {(company.email || company.phone) && (
                        <div style={{ color: mutedFg, fontSize: '0.875rem', lineHeight: 1.7 }}>
                            {company.phone && <div><a href={`tel:${company.phone.replace(/\s+/g, '')}`} style={{ color: 'inherit', textDecoration: 'none' }}>{company.phone}</a></div>}
                            {company.email && <div><a href={`mailto:${company.email}`} style={{ color: 'inherit', textDecoration: 'none' }}>{company.email}</a></div>}
                        </div>
                    )}
                </>
            )}
            {showButton && <DirectionsButton href={directionsHref} label={buttonLabel} />}
        </div>
    );
}

function DirectionsButton({ href, label }: { href: string; label: string }) {
    return (
        <a
            href={href}
            target="_blank"
            rel="noopener noreferrer"
            style={{
                alignSelf: 'flex-start',
                display: 'inline-flex',
                alignItems: 'center',
                gap: 6,
                background: 'var(--primary, #0284c7)',
                color: '#ffffff',
                padding: '10px 16px',
                borderRadius: 10,
                textDecoration: 'none',
                fontWeight: 600,
                fontSize: '0.875rem',
                marginTop: 4,
            }}
        >
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2">
                <path strokeLinecap="round" strokeLinejoin="round" d="M3 11.5L21 3l-8.5 18-2-7.5-7.5-2z" />
            </svg>
            {label}
        </a>
    );
}
