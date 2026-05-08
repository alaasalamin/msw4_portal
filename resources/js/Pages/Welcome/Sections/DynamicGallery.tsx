import { useEffect, useState, useCallback, useRef } from 'react';

interface GalleryImage {
    image?: string;
    caption?: string;
    alt?: string;
}

interface GallerySettings {
    variant?: 'grid' | 'masonry' | 'carousel' | 'collage';
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
    if (/^https?:\/\//i.test(path)) return path;
    return `/storage/${path}`;
}

// ── Top-level component (handles lightbox + dispatch) ──────────────────────

export default function DynamicGallery({ settings }: { settings: GallerySettings }) {
    const images = (settings.images ?? []).filter((it) => !!it.image);
    const enableLightbox = settings.enableLightbox !== false;

    const [activeIndex, setActiveIndex] = useState<number | null>(null);
    const open  = useCallback((i: number) => setActiveIndex(i), []);
    const close = useCallback(() => setActiveIndex(null), []);
    const prev  = useCallback(() => setActiveIndex((i) => (i === null ? null : (i - 1 + images.length) % images.length)), [images.length]);
    const next  = useCallback(() => setActiveIndex((i) => (i === null ? null : (i + 1) % images.length)), [images.length]);

    useEffect(() => {
        if (activeIndex === null) return;
        const onKey = (e: KeyboardEvent) => {
            if (e.key === 'Escape') close();
            else if (e.key === 'ArrowLeft') prev();
            else if (e.key === 'ArrowRight') next();
        };
        document.addEventListener('keydown', onKey);
        const prevOverflow = document.body.style.overflow;
        document.body.style.overflow = 'hidden';
        return () => {
            document.removeEventListener('keydown', onKey);
            document.body.style.overflow = prevOverflow;
        };
    }, [activeIndex, close, prev, next]);

    const variant = settings.variant ?? 'grid';
    const tileProps = { settings, images, enableLightbox, onOpen: open };
    const layout = (() => {
        switch (variant) {
            case 'masonry':  return <Masonry  {...tileProps} />;
            case 'carousel': return <Carousel {...tileProps} />;
            case 'collage':  return <Collage  {...tileProps} />;
            case 'grid':
            default:         return <Grid     {...tileProps} />;
        }
    })();

    const active = activeIndex !== null ? images[activeIndex] : null;

    return (
        <>
            <SectionWrap settings={settings}>
                <SectionHeader settings={settings} />
                {images.length === 0 ? (
                    <p style={{ textAlign: 'center', color: settings.mutedFg ?? '#64748b', margin: 0 }}>
                        No images yet — add some in the Website Builder.
                    </p>
                ) : layout}
            </SectionWrap>

            {enableLightbox && active && (
                <Lightbox
                    image={active}
                    indexLabel={`${(activeIndex ?? 0) + 1} / ${images.length}`}
                    onClose={close}
                    onPrev={images.length > 1 ? prev : undefined}
                    onNext={images.length > 1 ? next : undefined}
                />
            )}
        </>
    );
}

// ── Shared chrome ──────────────────────────────────────────────────────────

function SectionWrap({ settings, children }: { settings: GallerySettings; children: React.ReactNode }) {
    return (
        <section
            style={{
                background: settings.bg ?? '#ffffff',
                color: settings.fg ?? '#0f172a',
                padding: 'clamp(48px, 8vw, 96px) clamp(16px, 4vw, 32px)',
                fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
            }}
        >
            <div style={{ maxWidth: '1200px', margin: '0 auto' }}>{children}</div>
        </section>
    );
}

function SectionHeader({ settings }: { settings: GallerySettings }) {
    const mutedFg = settings.mutedFg ?? '#64748b';
    if (!settings.heading && !settings.subtitle) return null;
    return (
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
    );
}

interface TileProps {
    settings: GallerySettings;
    images: GalleryImage[];
    enableLightbox: boolean;
    onOpen: (i: number) => void;
}

interface ImageTileProps {
    img: GalleryImage;
    index: number;
    enableLightbox: boolean;
    onOpen: (i: number) => void;
    aspect?: string;
    natural?: boolean; // for masonry
    style?: React.CSSProperties;
    radius?: number;
}

function ImageTile({ img, index, enableLightbox, onOpen, aspect, natural, style, radius = 12 }: ImageTileProps) {
    const src = imageSrc(img.image);
    const wrapperStyle: React.CSSProperties = {
        position: 'relative',
        overflow: 'hidden',
        borderRadius: radius,
        background: '#f1f5f9',
        cursor: enableLightbox ? 'zoom-in' : 'default',
        ...(natural ? {} : { aspectRatio: aspect }),
        ...style,
    };
    const imgStyle: React.CSSProperties = natural
        ? { width: '100%', height: 'auto', display: 'block', transition: 'transform .35s ease' }
        : { position: 'absolute', inset: 0, width: '100%', height: aspect ? '100%' : 'auto', objectFit: aspect ? 'cover' : 'contain', display: 'block', transition: 'transform .35s ease' };

    return (
        <div
            className="tb-gallery-tile"
            role={enableLightbox ? 'button' : undefined}
            tabIndex={enableLightbox ? 0 : undefined}
            onClick={enableLightbox ? () => onOpen(index) : undefined}
            onKeyDown={enableLightbox ? (e) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); onOpen(index); } } : undefined}
            style={wrapperStyle}
        >
            {src && <img src={src} alt={img.alt ?? img.caption ?? ''} loading="lazy" style={imgStyle} />}
        </div>
    );
}

function Caption({ caption, mutedFg }: { caption?: string; mutedFg: string }) {
    if (!caption) return null;
    return (
        <figcaption style={{
            margin: '8px 2px 0',
            fontSize: '0.8125rem',
            color: mutedFg,
            lineHeight: 1.45,
        }}>{caption}</figcaption>
    );
}

// ── Variant: grid (existing) ───────────────────────────────────────────────

function Grid({ settings, images, enableLightbox, onOpen }: TileProps) {
    const cols = Math.max(2, Math.min(5, Number(settings.columns) || 3));
    const gap = Math.max(0, Math.min(64, Number(settings.gap) || 12));
    const aspect = ASPECT_RATIOS[settings.aspect ?? 'square'];
    const showCaptions = settings.showCaptions !== false;
    const mutedFg = settings.mutedFg ?? '#64748b';

    return (
        <>
            <div className={`tb-gallery-grid tb-gallery-cols-${cols}`} style={{
                display: 'grid',
                gap: `${gap}px`,
                gridTemplateColumns: `repeat(${cols}, minmax(0, 1fr))`,
            }}>
                {images.map((img, i) => (
                    <figure key={i} style={{ margin: 0 }}>
                        <ImageTile img={img} index={i} enableLightbox={enableLightbox} onOpen={onOpen} aspect={aspect} />
                        {showCaptions && <Caption caption={img.caption} mutedFg={mutedFg} />}
                    </figure>
                ))}
            </div>
            <SharedHoverStyles />
            <style>{`
                @media (max-width: 900px) {
                    .tb-gallery-grid.tb-gallery-cols-4,
                    .tb-gallery-grid.tb-gallery-cols-5 { grid-template-columns: repeat(3, minmax(0, 1fr)) !important; }
                }
                @media (max-width: 640px) {
                    .tb-gallery-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
                }
            `}</style>
        </>
    );
}

// ── Variant: masonry (CSS columns) ─────────────────────────────────────────

function Masonry({ settings, images, enableLightbox, onOpen }: TileProps) {
    const cols = Math.max(2, Math.min(5, Number(settings.columns) || 3));
    const gap = Math.max(0, Math.min(64, Number(settings.gap) || 12));
    const showCaptions = settings.showCaptions !== false;
    const mutedFg = settings.mutedFg ?? '#64748b';

    return (
        <>
            <div className={`tb-gallery-masonry tb-gallery-masonry-cols-${cols}`} style={{
                columnCount: cols,
                columnGap: `${gap}px`,
            }}>
                {images.map((img, i) => (
                    <figure key={i} style={{
                        margin: 0,
                        marginBottom: `${gap}px`,
                        breakInside: 'avoid',
                    }}>
                        <ImageTile img={img} index={i} enableLightbox={enableLightbox} onOpen={onOpen} natural />
                        {showCaptions && <Caption caption={img.caption} mutedFg={mutedFg} />}
                    </figure>
                ))}
            </div>
            <SharedHoverStyles />
            <style>{`
                @media (max-width: 900px) {
                    .tb-gallery-masonry.tb-gallery-masonry-cols-4,
                    .tb-gallery-masonry.tb-gallery-masonry-cols-5 { column-count: 3 !important; }
                }
                @media (max-width: 640px) {
                    .tb-gallery-masonry { column-count: 2 !important; }
                }
            `}</style>
        </>
    );
}

// ── Variant: carousel (horizontal scroll-snap with prev/next) ──────────────

function Carousel({ settings, images, enableLightbox, onOpen }: TileProps) {
    const gap = Math.max(0, Math.min(64, Number(settings.gap) || 12));
    const aspect = ASPECT_RATIOS[settings.aspect ?? '16:9'] ?? '16 / 9';
    const showCaptions = settings.showCaptions !== false;
    const mutedFg = settings.mutedFg ?? '#64748b';
    const scrollerRef = useRef<HTMLDivElement | null>(null);

    const scrollBy = (dir: 1 | -1) => {
        const el = scrollerRef.current;
        if (!el) return;
        el.scrollBy({ left: dir * (el.clientWidth * 0.85), behavior: 'smooth' });
    };

    return (
        <>
            <div style={{ position: 'relative' }}>
                {images.length > 1 && (
                    <>
                        <button
                            type="button"
                            aria-label="Previous"
                            onClick={() => scrollBy(-1)}
                            style={{ ...arrowBtnStyle, left: -6 }}
                            className="tb-gallery-carousel-arrow"
                        >
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2">
                                <path strokeLinecap="round" strokeLinejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                            </svg>
                        </button>
                        <button
                            type="button"
                            aria-label="Next"
                            onClick={() => scrollBy(1)}
                            style={{ ...arrowBtnStyle, right: -6 }}
                            className="tb-gallery-carousel-arrow"
                        >
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2">
                                <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </button>
                    </>
                )}
                <div
                    ref={scrollerRef}
                    className="tb-gallery-carousel"
                    style={{
                        display: 'flex',
                        gap: `${gap}px`,
                        overflowX: 'auto',
                        overflowY: 'hidden',
                        scrollSnapType: 'x mandatory',
                        scrollPadding: 12,
                        paddingBottom: 6,
                        WebkitOverflowScrolling: 'touch',
                    }}
                >
                    {images.map((img, i) => (
                        <figure key={i} style={{
                            margin: 0,
                            flex: '0 0 auto',
                            width: 'clamp(240px, 38vw, 420px)',
                            scrollSnapAlign: 'start',
                        }}>
                            <ImageTile img={img} index={i} enableLightbox={enableLightbox} onOpen={onOpen} aspect={aspect} />
                            {showCaptions && <Caption caption={img.caption} mutedFg={mutedFg} />}
                        </figure>
                    ))}
                </div>
            </div>
            <SharedHoverStyles />
            <style>{`
                .tb-gallery-carousel { scrollbar-width: thin; scrollbar-color: rgba(15,23,42,0.25) transparent; }
                .tb-gallery-carousel::-webkit-scrollbar { height: 8px; }
                .tb-gallery-carousel::-webkit-scrollbar-thumb { background: rgba(15,23,42,0.25); border-radius: 4px; }
            `}</style>
        </>
    );
}

const arrowBtnStyle: React.CSSProperties = {
    position: 'absolute',
    top: '50%',
    transform: 'translateY(-50%)',
    zIndex: 2,
    width: 44,
    height: 44,
    borderRadius: 9999,
    border: '1px solid rgba(15,23,42,0.10)',
    background: '#ffffff',
    color: '#0f172a',
    cursor: 'pointer',
    display: 'inline-flex',
    alignItems: 'center',
    justifyContent: 'center',
    boxShadow: '0 4px 14px rgba(15,23,42,0.16)',
};

// ── Variant: collage (asymmetric — first image is bigger) ──────────────────

function Collage({ settings, images, enableLightbox, onOpen }: TileProps) {
    const gap = Math.max(0, Math.min(64, Number(settings.gap) || 12));
    const showCaptions = settings.showCaptions !== false;
    const mutedFg = settings.mutedFg ?? '#64748b';

    return (
        <>
            <div className="tb-gallery-collage" style={{
                display: 'grid',
                gap: `${gap}px`,
                gridTemplateColumns: 'repeat(4, minmax(0, 1fr))',
                gridAutoRows: 'minmax(140px, 1fr)',
            }}>
                {images.map((img, i) => {
                    // First image takes 2x2; the rest are 1x1
                    const big = i === 0;
                    return (
                        <figure key={i} style={{
                            margin: 0,
                            gridColumn: big ? 'span 2' : 'span 1',
                            gridRow: big ? 'span 2' : 'span 1',
                            display: 'flex',
                            flexDirection: 'column',
                        }}>
                            <ImageTile
                                img={img}
                                index={i}
                                enableLightbox={enableLightbox}
                                onOpen={onOpen}
                                style={{ flex: 1, height: '100%' }}
                            />
                            {showCaptions && <Caption caption={img.caption} mutedFg={mutedFg} />}
                        </figure>
                    );
                })}
            </div>
            <SharedHoverStyles />
            <style>{`
                .tb-gallery-collage figure[style*="grid-column: span 2"] {
                    grid-column: span 2;
                }
                /* Force full coverage on the wrapper inside collage so spans fill */
                .tb-gallery-collage figure { min-height: 0; }
                .tb-gallery-collage figure > .tb-gallery-tile {
                    aspect-ratio: auto !important;
                    height: 100% !important;
                }
                @media (max-width: 760px) {
                    .tb-gallery-collage {
                        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                        grid-auto-rows: minmax(120px, 1fr) !important;
                    }
                }
            `}</style>
        </>
    );
}

// ── Shared hover/focus styles for tiles ────────────────────────────────────

function SharedHoverStyles() {
    return (
        <style>{`
            .tb-gallery-tile:hover img { transform: scale(1.04); }
            .tb-gallery-tile:focus-visible {
                outline: 2px solid #0284c7;
                outline-offset: 2px;
            }
        `}</style>
    );
}

// ── Lightbox ──────────────────────────────────────────────────────────────

function Lightbox({ image, indexLabel, onClose, onPrev, onNext }: {
    image: GalleryImage;
    indexLabel: string;
    onClose: () => void;
    onPrev?: () => void;
    onNext?: () => void;
}) {
    return (
        <div
            role="dialog"
            aria-modal="true"
            aria-label={image.alt ?? image.caption ?? 'Image'}
            onClick={onClose}
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
            <button type="button" aria-label="Close" onClick={(e) => { e.stopPropagation(); onClose(); }}
                style={{
                    position: 'absolute', top: 16, right: 16,
                    width: 40, height: 40, borderRadius: 9999, border: 'none',
                    background: 'rgba(255,255,255,0.10)', color: '#ffffff', cursor: 'pointer',
                    display: 'inline-flex', alignItems: 'center', justifyContent: 'center',
                }}
            >
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2">
                    <path strokeLinecap="round" strokeLinejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>

            {onPrev && (
                <button type="button" aria-label="Previous" onClick={(e) => { e.stopPropagation(); onPrev(); }}
                    style={{
                        position: 'absolute', left: 16, top: '50%', transform: 'translateY(-50%)',
                        width: 44, height: 44, borderRadius: 9999, border: 'none',
                        background: 'rgba(255,255,255,0.10)', color: '#ffffff', cursor: 'pointer',
                        display: 'inline-flex', alignItems: 'center', justifyContent: 'center',
                    }}
                >
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                </button>
            )}

            {onNext && (
                <button type="button" aria-label="Next" onClick={(e) => { e.stopPropagation(); onNext(); }}
                    style={{
                        position: 'absolute', right: 16, top: '50%', transform: 'translateY(-50%)',
                        width: 44, height: 44, borderRadius: 9999, border: 'none',
                        background: 'rgba(255,255,255,0.10)', color: '#ffffff', cursor: 'pointer',
                        display: 'inline-flex', alignItems: 'center', justifyContent: 'center',
                    }}
                >
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2">
                        <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </button>
            )}

            <img
                onClick={(e) => e.stopPropagation()}
                src={imageSrc(image.image)}
                alt={image.alt ?? image.caption ?? ''}
                style={{
                    maxWidth: '92vw', maxHeight: '82vh', objectFit: 'contain',
                    display: 'block', borderRadius: 8,
                    boxShadow: '0 18px 40px -10px rgba(0,0,0,0.6)',
                }}
            />
            {image.caption && (
                <figcaption style={{ marginTop: 16, color: 'rgba(255,255,255,0.85)', fontSize: '0.9375rem', textAlign: 'center', maxWidth: '70ch' }}>
                    {image.caption}
                </figcaption>
            )}
            <div style={{ marginTop: 8, color: 'rgba(255,255,255,0.55)', fontSize: '0.75rem', letterSpacing: '0.05em' }}>
                {indexLabel}
            </div>
        </div>
    );
}
