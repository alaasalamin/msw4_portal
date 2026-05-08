import { useEffect, useState, useCallback } from 'react';

interface GalleryImage {
    image?: string;
    caption?: string;
    alt?: string;
}

interface GallerySettings {
    heading?: string;
    subtitle?: string;
    images?: GalleryImage[];
    columns?: number | string;
    aspect?: 'square' | '4:3' | '16:9' | '3:4' | 'auto';
    gap?: number | string;
    showCaptions?: boolean;
    enableLightbox?: boolean;
    bg?: string;
    fg?: string;
    mutedFg?: string;
}

const ASPECT_RATIOS: Record<string, string | undefined> = {
    'square': '1 / 1',
    '4:3':    '4 / 3',
    '16:9':   '16 / 9',
    '3:4':    '3 / 4',
    'auto':   undefined,
};

function imageSrc(path?: string): string | undefined {
    if (!path) return undefined;
    // Already a full URL?
    if (/^https?:\/\//i.test(path)) return path;
    return `/storage/${path}`;
}

export default function DynamicGallery({ settings }: { settings: GallerySettings }) {
    const images = (settings.images ?? []).filter((it) => !!it.image);
    const cols   = Math.max(2, Math.min(5, Number(settings.columns) || 3));
    const gap    = Math.max(0, Math.min(64, Number(settings.gap) || 12));
    const aspect = ASPECT_RATIOS[settings.aspect ?? 'square'];
    const showCaptions   = settings.showCaptions   !== false;
    const enableLightbox = settings.enableLightbox !== false;

    const fg      = settings.fg      ?? '#0f172a';
    const mutedFg = settings.mutedFg ?? '#64748b';

    const [activeIndex, setActiveIndex] = useState<number | null>(null);
    const open  = useCallback((i: number) => setActiveIndex(i), []);
    const close = useCallback(() => setActiveIndex(null), []);
    const prev  = useCallback(() => setActiveIndex((i) => (i === null ? null : (i - 1 + images.length) % images.length)), [images.length]);
    const next  = useCallback(() => setActiveIndex((i) => (i === null ? null : (i + 1) % images.length)), [images.length]);

    // Keyboard nav for the lightbox.
    useEffect(() => {
        if (activeIndex === null) return;
        const onKey = (e: KeyboardEvent) => {
            if (e.key === 'Escape') close();
            else if (e.key === 'ArrowLeft') prev();
            else if (e.key === 'ArrowRight') next();
        };
        document.addEventListener('keydown', onKey);
        // Lock body scroll while open.
        const prevOverflow = document.body.style.overflow;
        document.body.style.overflow = 'hidden';
        return () => {
            document.removeEventListener('keydown', onKey);
            document.body.style.overflow = prevOverflow;
        };
    }, [activeIndex, close, prev, next]);

    const active = activeIndex !== null ? images[activeIndex] : null;

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

                {images.length === 0 ? (
                    <p style={{ textAlign: 'center', color: mutedFg, margin: 0 }}>
                        No images yet — add some in the Website Builder.
                    </p>
                ) : (
                    <div
                        className={`tb-gallery-grid tb-gallery-cols-${cols}`}
                        style={{
                            display: 'grid',
                            gridTemplateColumns: `repeat(${cols}, minmax(0, 1fr))`,
                            gap: `${gap}px`,
                        }}
                    >
                        {images.map((img, i) => {
                            const src = imageSrc(img.image);
                            return (
                                <figure key={i} style={{ margin: 0 }}>
                                    <div
                                        className="tb-gallery-tile"
                                        role={enableLightbox ? 'button' : undefined}
                                        tabIndex={enableLightbox ? 0 : undefined}
                                        onClick={enableLightbox ? () => open(i) : undefined}
                                        onKeyDown={enableLightbox ? (e) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); open(i); } } : undefined}
                                        style={{
                                            position: 'relative',
                                            overflow: 'hidden',
                                            borderRadius: 12,
                                            background: '#f1f5f9',
                                            aspectRatio: aspect,
                                            cursor: enableLightbox ? 'zoom-in' : 'default',
                                        }}
                                    >
                                        {src && (
                                            <img
                                                src={src}
                                                alt={img.alt ?? img.caption ?? ''}
                                                loading="lazy"
                                                style={{
                                                    width: '100%',
                                                    height: aspect ? '100%' : 'auto',
                                                    objectFit: aspect ? 'cover' : 'contain',
                                                    display: 'block',
                                                    transition: 'transform .35s ease',
                                                }}
                                            />
                                        )}
                                    </div>
                                    {showCaptions && img.caption && (
                                        <figcaption style={{
                                            margin: '8px 2px 0',
                                            fontSize: '0.8125rem',
                                            color: mutedFg,
                                            lineHeight: 1.45,
                                        }}>
                                            {img.caption}
                                        </figcaption>
                                    )}
                                </figure>
                            );
                        })}
                    </div>
                )}
            </div>

            {/* ── Lightbox ─────────────────────────────────────── */}
            {enableLightbox && active && (
                <div
                    role="dialog"
                    aria-modal="true"
                    aria-label={active.alt ?? active.caption ?? 'Image'}
                    onClick={close}
                    style={{
                        position: 'fixed',
                        inset: 0,
                        zIndex: 9999,
                        background: 'rgba(15,23,42,0.92)',
                        display: 'flex',
                        flexDirection: 'column',
                        alignItems: 'center',
                        justifyContent: 'center',
                        padding: 'clamp(20px, 4vw, 40px)',
                    }}
                >
                    {/* Close */}
                    <button
                        type="button"
                        aria-label="Close"
                        onClick={(e) => { e.stopPropagation(); close(); }}
                        style={{
                            position: 'absolute',
                            top: 16,
                            right: 16,
                            width: 40,
                            height: 40,
                            borderRadius: 9999,
                            border: 'none',
                            background: 'rgba(255,255,255,0.10)',
                            color: '#ffffff',
                            cursor: 'pointer',
                            display: 'inline-flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                        }}
                    >
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>

                    {/* Prev */}
                    {images.length > 1 && (
                        <button
                            type="button"
                            aria-label="Previous"
                            onClick={(e) => { e.stopPropagation(); prev(); }}
                            style={{
                                position: 'absolute',
                                left: 16,
                                top: '50%',
                                transform: 'translateY(-50%)',
                                width: 44,
                                height: 44,
                                borderRadius: 9999,
                                border: 'none',
                                background: 'rgba(255,255,255,0.10)',
                                color: '#ffffff',
                                cursor: 'pointer',
                                display: 'inline-flex',
                                alignItems: 'center',
                                justifyContent: 'center',
                            }}
                        >
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2">
                                <path strokeLinecap="round" strokeLinejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                            </svg>
                        </button>
                    )}

                    {/* Next */}
                    {images.length > 1 && (
                        <button
                            type="button"
                            aria-label="Next"
                            onClick={(e) => { e.stopPropagation(); next(); }}
                            style={{
                                position: 'absolute',
                                right: 16,
                                top: '50%',
                                transform: 'translateY(-50%)',
                                width: 44,
                                height: 44,
                                borderRadius: 9999,
                                border: 'none',
                                background: 'rgba(255,255,255,0.10)',
                                color: '#ffffff',
                                cursor: 'pointer',
                                display: 'inline-flex',
                                alignItems: 'center',
                                justifyContent: 'center',
                            }}
                        >
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2">
                                <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </button>
                    )}

                    <img
                        onClick={(e) => e.stopPropagation()}
                        src={imageSrc(active.image)}
                        alt={active.alt ?? active.caption ?? ''}
                        style={{
                            maxWidth: '92vw',
                            maxHeight: '82vh',
                            objectFit: 'contain',
                            display: 'block',
                            borderRadius: 8,
                            boxShadow: '0 18px 40px -10px rgba(0,0,0,0.6)',
                        }}
                    />
                    {active.caption && (
                        <figcaption style={{
                            marginTop: 16,
                            color: 'rgba(255,255,255,0.85)',
                            fontSize: '0.9375rem',
                            textAlign: 'center',
                            maxWidth: '70ch',
                        }}>
                            {active.caption}
                        </figcaption>
                    )}
                    <div style={{
                        marginTop: 8,
                        color: 'rgba(255,255,255,0.55)',
                        fontSize: '0.75rem',
                        letterSpacing: '0.05em',
                    }}>
                        {(activeIndex ?? 0) + 1} / {images.length}
                    </div>
                </div>
            )}

            <style>{`
                .tb-gallery-tile:hover img { transform: scale(1.04); }
                .tb-gallery-tile:focus-visible {
                    outline: 2px solid #0284c7;
                    outline-offset: 2px;
                }
                @media (max-width: 900px) {
                    .tb-gallery-grid.tb-gallery-cols-4,
                    .tb-gallery-grid.tb-gallery-cols-5 {
                        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
                    }
                }
                @media (max-width: 640px) {
                    .tb-gallery-grid {
                        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                    }
                }
            `}</style>
        </section>
    );
}
