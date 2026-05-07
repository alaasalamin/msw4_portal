interface TextSettings {
    heading?: string;
    body?: string;
    align?: 'left' | 'center' | 'right';
    bg?: string;
    fg?: string;
}

export default function DynamicTextBlock({ settings }: { settings: TextSettings }) {
    const align = settings.align ?? 'left';

    return (
        <section
            style={{
                background: settings.bg ?? '#ffffff',
                color: settings.fg ?? '#0f172a',
                padding: 'clamp(40px, 7vw, 80px) clamp(16px, 4vw, 32px)',
                fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
            }}
        >
            <div style={{ maxWidth: '760px', margin: '0 auto', textAlign: align }}>
                {settings.heading && (
                    <h2
                        style={{
                            fontSize: 'clamp(1.5rem, 3vw, 2.25rem)',
                            fontWeight: 700,
                            letterSpacing: '-0.02em',
                            margin: '0 0 16px',
                            lineHeight: 1.2,
                        }}
                    >
                        {settings.heading}
                    </h2>
                )}
                {settings.body && (
                    <p
                        style={{
                            fontSize: 'clamp(1rem, 1.2vw, 1.0625rem)',
                            lineHeight: 1.7,
                            margin: 0,
                            whiteSpace: 'pre-wrap',
                        }}
                    >
                        {settings.body}
                    </p>
                )}
            </div>
        </section>
    );
}
