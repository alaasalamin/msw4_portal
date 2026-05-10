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

const HEADING_SIZES: Record<HeadingLevel, string> = {
    h1: 'clamp(2rem, 4vw, 3rem)',
    h2: 'clamp(1.5rem, 3vw, 2.25rem)',
    h3: 'clamp(1.25rem, 2.4vw, 1.75rem)',
    h4: 'clamp(1.125rem, 2vw, 1.375rem)',
    h5: 'clamp(1rem, 1.6vw, 1.125rem)',
    h6: 'clamp(0.9375rem, 1.4vw, 1rem)',
};

function BlockRenderer({ block }: { block: ContentBlock }) {
    const text = block.text ?? '';
    if (block.type === 'heading') {
        const level: HeadingLevel = block.level ?? 'h2';
        const style: React.CSSProperties = {
            fontSize: HEADING_SIZES[level] ?? HEADING_SIZES.h2,
            fontWeight: 700,
            letterSpacing: '-0.02em',
            lineHeight: 1.2,
            margin: '0 0 16px',
        };
        switch (level) {
            case 'h1': return <h1 style={style}>{text}</h1>;
            case 'h2': return <h2 style={style}>{text}</h2>;
            case 'h3': return <h3 style={style}>{text}</h3>;
            case 'h4': return <h4 style={style}>{text}</h4>;
            case 'h5': return <h5 style={style}>{text}</h5>;
            case 'h6': return <h6 style={style}>{text}</h6>;
        }
    }
    return <p style={{ ...bodyStyle, margin: '0 0 16px' }}>{text}</p>;
}

// ── Variant: two_column (heading on left, body on right) ───────────────────

function TwoColumn({ settings }: { settings: TextSettings }) {
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
                    {settings.heading && (
                        <h2 style={{ ...headingStyle, margin: 0 }}>{settings.heading}</h2>
                    )}
                </div>
                <div>
                    {settings.body && (
                        <p style={{ ...bodyStyle, margin: 0 }}>{settings.body}</p>
                    )}
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
            </div>
        </Section>
    );
}

// ── Variant: quote (pull quote with attribution) ───────────────────────────

function Quote({ settings }: { settings: TextSettings }) {
    const align = settings.align ?? 'center';

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
                    {settings.body}
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
    return (
        <section
            style={{
                background: settings.bg ?? '#ffffff',
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
