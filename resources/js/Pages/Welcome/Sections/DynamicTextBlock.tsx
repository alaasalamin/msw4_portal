type HeadingLevel = 'h1' | 'h2' | 'h3' | 'h4' | 'h5' | 'h6';

interface ContentBlock {
    type?: 'heading' | 'paragraph';
    level?: HeadingLevel;
    text?: string;
}

interface TextSettings {
    variant?: 'default' | 'two_column' | 'callout' | 'quote';
    heading?: string;
    body?: string;
    blocks?: ContentBlock[];
    attribution?: string;
    calloutColor?: 'info' | 'success' | 'warning' | 'danger' | 'note' | 'neutral' | 'brand';
    align?: 'left' | 'center' | 'right';
    bg?: string;
    bgImage?: string | null;
    fg?: string;
}

interface CalloutPreset { bg: string; border: string; fg: string; headingFg: string }

const CALLOUT_PRESETS: Record<string, CalloutPreset> = {
    info:    { bg: '#eff6ff', border: '#2563eb', fg: '#1e3a8a', headingFg: '#1e40af' },
    success: { bg: '#f0fdf4', border: '#16a34a', fg: '#14532d', headingFg: '#166534' },
    warning: { bg: '#fffbeb', border: '#d97706', fg: '#78350f', headingFg: '#92400e' },
    danger:  { bg: '#fef2f2', border: '#dc2626', fg: '#7f1d1d', headingFg: '#991b1b' },
    note:    { bg: '#f5f3ff', border: '#7c3aed', fg: '#4c1d95', headingFg: '#5b21b6' },
    neutral: { bg: '#f8fafc', border: '#475569', fg: '#1e293b', headingFg: '#0f172a' },
    // 'brand' is handled with CSS color-mix so it tracks --primary live.
};

export default function DynamicTextBlock({ settings }: { settings: TextSettings }) {
    switch (settings.variant) {
        case 'two_column': return <TwoColumn settings={settings} />;
        case 'callout':    return <Callout   settings={settings} />;
        case 'quote':      return <Quote     settings={settings} />;
        case 'default':
        default:           return <Default   settings={settings} />;
    }
}

// ── Variant: default (heading + body, configurable alignment) ──────────────

function Default({ settings }: { settings: TextSettings }) {
    const align = settings.align ?? 'left';
    const blocks = (settings.blocks ?? []).filter((b) => (b.text ?? '').trim() !== '');

    return (
        <Section settings={settings}>
            <div style={{ maxWidth: '760px', margin: '0 auto', textAlign: align }}>
                {blocks.length > 0
                    ? blocks.map((b, i) => <BlockRenderer key={i} block={b} />)
                    : (
                        <>
                            {settings.heading && <h2 style={headingStyle}>{settings.heading}</h2>}
                            {settings.body && <p style={bodyStyle}>{settings.body}</p>}
                        </>
                    )}
            </div>
        </Section>
    );
}

interface BlockRendererProps {
    block: ContentBlock;
    headingColor?: string;
    bodyColor?: string;
    compact?: boolean;
}

const HEADING_CLASSES: Record<HeadingLevel, string> = {
    h1: 'text-6xl md:text-7xl lg:text-8xl font-black tracking-tighter leading-none mb-6',
    h2: 'text-4xl md:text-5xl font-extrabold tracking-tight leading-tight mb-5',
    h3: 'text-2xl md:text-3xl font-bold tracking-tight leading-snug mb-4',
    h4: 'text-xl md:text-2xl font-semibold tracking-tight mb-3',
    h5: 'text-lg md:text-xl font-semibold mb-2',
    h6: 'text-base md:text-lg font-semibold mb-2',
};

function BlockRenderer({ block, headingColor, bodyColor, compact = false }: BlockRendererProps) {
    const text = block.text ?? '';
    if (block.type === 'heading') {
        const level: HeadingLevel = block.level ?? 'h2';
        const className = HEADING_CLASSES[level] ?? HEADING_CLASSES.h2;
        const style: React.CSSProperties = headingColor ? { color: headingColor } : {};
        const compactStyle: React.CSSProperties = compact ? { marginBottom: '0.5rem' } : {};
        const merged = { ...style, ...compactStyle };
        switch (level) {
            case 'h1': return <h1 className={className} style={merged}>{text}</h1>;
            case 'h2': return <h2 className={className} style={merged}>{text}</h2>;
            case 'h3': return <h3 className={className} style={merged}>{text}</h3>;
            case 'h4': return <h4 className={className} style={merged}>{text}</h4>;
            case 'h5': return <h5 className={className} style={merged}>{text}</h5>;
            case 'h6': return <h6 className={className} style={merged}>{text}</h6>;
        }
    }
    const paraStyle: React.CSSProperties = {
        ...bodyStyle,
        margin: compact ? '0 0 8px' : '0 0 16px',
        ...(bodyColor ? { color: bodyColor } : {}),
        ...(compact ? { fontSize: 'clamp(0.9375rem, 1.1vw, 1rem)' } : {}),
    };
    return <p style={paraStyle}>{text}</p>;
}

// ── Variant: two_column (heading on left, body on right) ───────────────────

function TwoColumn({ settings }: { settings: TextSettings }) {
    const blocks = (settings.blocks ?? []).filter((b) => (b.text ?? '').trim() !== '');

    let leftBlocks: ContentBlock[] = [];
    let rightBlocks: ContentBlock[] = [];
    if (blocks.length > 0) {
        const firstHeadingIdx = blocks.findIndex((b) => b.type === 'heading');
        if (firstHeadingIdx >= 0) {
            leftBlocks = [blocks[firstHeadingIdx]];
            rightBlocks = blocks.filter((_, i) => i !== firstHeadingIdx);
        } else {
            rightBlocks = blocks;
        }
    }

    return (
        <Section settings={settings}>
            <div
                className="tb-text-twocol"
                style={{
                    maxWidth: '1100px',
                    margin: '0 auto',
                    display: 'grid',
                    gridTemplateColumns: '1fr 1.4fr',
                    gap: 'clamp(24px, 5vw, 64px)',
                    alignItems: 'start',
                }}
            >
                <div>
                    {leftBlocks.length > 0
                        ? leftBlocks.map((b, i) => <BlockRenderer key={i} block={b} />)
                        : settings.heading && <h2 style={{ ...headingStyle, margin: 0 }}>{settings.heading}</h2>}
                </div>
                <div>
                    {rightBlocks.length > 0
                        ? rightBlocks.map((b, i) => <BlockRenderer key={i} block={b} />)
                        : settings.body && <p style={{ ...bodyStyle, margin: 0 }}>{settings.body}</p>}
                </div>
            </div>
            <style>{`
                @media (max-width: 720px) {
                    .tb-text-twocol { grid-template-columns: 1fr !important; }
                }
            `}</style>
        </Section>
    );
}

// ── Variant: callout (highlighted box, primary-accented left border) ───────

function Callout({ settings }: { settings: TextSettings }) {
    const align  = settings.align ?? 'left';
    const colorKey = settings.calloutColor ?? 'info';
    const preset   = CALLOUT_PRESETS[colorKey];

    // 'brand' (or unknown key) → fall back to --primary CSS variable so the
    // callout tracks whatever brand color is set on the site.
    const calloutBg     = preset ? preset.bg     : 'color-mix(in srgb, var(--primary, #0284c7) 8%, transparent)';
    const calloutBorder = preset ? `color-mix(in srgb, ${preset.border} 35%, transparent)` : 'color-mix(in srgb, var(--primary, #0284c7) 22%, transparent)';
    const calloutAccent = preset ? preset.border : 'var(--primary, #0284c7)';
    const calloutBody   = preset ? preset.fg     : settings.fg ?? '#0f172a';
    const calloutHead   = preset ? preset.headingFg : 'currentColor';

    const blocks = (settings.blocks ?? []).filter((b) => (b.text ?? '').trim() !== '');

    return (
        <Section settings={settings}>
            <div
                role="note"
                style={{
                    maxWidth: '900px',
                    margin: '0 auto',
                    background: calloutBg,
                    border: `1px solid ${calloutBorder}`,
                    borderLeft: `4px solid ${calloutAccent}`,
                    borderRadius: 12,
                    padding: 'clamp(20px, 3vw, 28px) clamp(20px, 3vw, 32px)',
                    textAlign: align,
                    color: calloutBody,
                }}
            >
                {blocks.length > 0
                    ? blocks.map((b, i) => (
                        <BlockRenderer
                            key={i}
                            block={b}
                            headingColor={calloutHead}
                            bodyColor={calloutBody}
                            compact
                        />
                    ))
                    : (
                        <>
                            {settings.heading && (
                                <h2 style={{
                                    ...headingStyle,
                                    fontSize: 'clamp(1.125rem, 2vw, 1.375rem)',
                                    margin: '0 0 8px',
                                    color: calloutHead,
                                }}>
                                    {settings.heading}
                                </h2>
                            )}
                            {settings.body && (
                                <p style={{
                                    ...bodyStyle,
                                    margin: 0,
                                    fontSize: 'clamp(0.9375rem, 1.1vw, 1rem)',
                                    color: calloutBody,
                                }}>
                                    {settings.body}
                                </p>
                            )}
                        </>
                    )}
            </div>
        </Section>
    );
}

// ── Variant: quote (pull quote with attribution) ───────────────────────────

function Quote({ settings }: { settings: TextSettings }) {
    const align = settings.align ?? 'center';
    const blockParas = (settings.blocks ?? [])
        .filter((b) => b.type !== 'heading' && (b.text ?? '').trim() !== '')
        .map((b) => b.text!.trim());
    const quoteText = blockParas.length > 0 ? blockParas.join('\n\n') : (settings.body ?? '');

    return (
        <Section settings={settings}>
            <figure style={{
                maxWidth: '800px',
                margin: '0 auto',
                textAlign: align,
                position: 'relative',
            }}>
                <span
                    aria-hidden="true"
                    style={{
                        position: 'absolute',
                        top: '-0.25em',
                        left: align === 'center' ? '50%' : 0,
                        transform: align === 'center' ? 'translateX(-50%)' : 'none',
                        fontSize: 'clamp(4rem, 9vw, 7rem)',
                        lineHeight: 1,
                        fontFamily: 'Georgia, "Times New Roman", serif',
                        color: 'var(--primary, #0284c7)',
                        opacity: 0.4,
                        userSelect: 'none',
                    }}
                >
                    “
                </span>
                <blockquote style={{
                    margin: 0,
                    paddingTop: 'clamp(28px, 4vw, 48px)',
                    fontSize: 'clamp(1.125rem, 2vw, 1.5rem)',
                    fontStyle: 'italic',
                    lineHeight: 1.55,
                    fontWeight: 500,
                    letterSpacing: '-0.005em',
                    whiteSpace: 'pre-wrap',
                }}>
                    {quoteText}
                </blockquote>
                {(settings.heading || settings.attribution) && (
                    <figcaption style={{
                        marginTop: 18,
                        fontSize: '0.9375rem',
                        opacity: 0.7,
                        fontWeight: 600,
                        letterSpacing: '0.01em',
                    }}>
                        {settings.attribution || settings.heading}
                    </figcaption>
                )}
            </figure>
        </Section>
    );
}

// ── Shared wrappers / styles ───────────────────────────────────────────────

function Section({ settings, children }: { settings: TextSettings; children: React.ReactNode }) {
    const bgColor = settings.bg ?? '#ffffff';
    const bgImage = settings.bgImage ? `url('/storage/${settings.bgImage}')` : null;

    return (
        <section
            style={{
                background: bgImage ? `${bgImage} center / cover no-repeat, ${bgColor}` : bgColor,
                color: settings.fg ?? '#0f172a',
                padding: 'clamp(40px, 7vw, 80px) clamp(16px, 4vw, 32px)',
                fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
            }}
        >
            {children}
        </section>
    );
}

const headingStyle: React.CSSProperties = {
    fontSize: 'clamp(1.5rem, 3vw, 2.25rem)',
    fontWeight: 700,
    letterSpacing: '-0.02em',
    margin: '0 0 16px',
    lineHeight: 1.2,
};

const bodyStyle: React.CSSProperties = {
    fontSize: 'clamp(1rem, 1.2vw, 1.0625rem)',
    lineHeight: 1.7,
    margin: 0,
    whiteSpace: 'pre-wrap',
};
