interface Member {
    name?: string;
    jobTitle?: string;
    bio?: string;
    photo?: string | null;
}

interface TeamSettings {
    heading?: string;
    subtitle?: string;
    members?: Member[];
    columns?: number | string;
    bg?: string;
    fg?: string;
    cardBg?: string;
    mutedFg?: string;
}

function initials(name?: string): string {
    if (!name) return '?';
    return name
        .split(' ')
        .filter(Boolean)
        .slice(0, 2)
        .map((p) => p.charAt(0).toUpperCase())
        .join('');
}

export default function DynamicTeam({ settings }: { settings: TeamSettings }) {
    const members = settings.members ?? [];
    const cols = Math.max(2, Math.min(4, Number(settings.columns) || 3));
    const fg = settings.fg ?? '#0f172a';
    const mutedFg = settings.mutedFg ?? '#64748b';
    const cardBg = settings.cardBg ?? '#f8fafc';

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
                    <div style={{ textAlign: 'center', marginBottom: 'clamp(32px, 5vw, 56px)' }}>
                        {settings.heading && (
                            <h2
                                style={{
                                    fontSize: 'clamp(1.75rem, 3.5vw, 2.5rem)',
                                    fontWeight: 700,
                                    letterSpacing: '-0.025em',
                                    margin: 0,
                                    lineHeight: 1.15,
                                }}
                            >
                                {settings.heading}
                            </h2>
                        )}
                        {settings.subtitle && (
                            <p
                                style={{
                                    fontSize: 'clamp(1rem, 1.3vw, 1.125rem)',
                                    color: mutedFg,
                                    margin: '12px auto 0',
                                    maxWidth: '52ch',
                                    lineHeight: 1.6,
                                }}
                            >
                                {settings.subtitle}
                            </p>
                        )}
                    </div>
                )}

                {members.length > 0 ? (
                    <div
                        className={`tb-team-grid tb-team-cols-${cols}`}
                        style={{
                            display: 'grid',
                            gap: '24px',
                            gridTemplateColumns: `repeat(${cols}, minmax(0, 1fr))`,
                        }}
                    >
                        {members.map((m, i) => (
                            <article
                                key={i}
                                style={{
                                    background: cardBg,
                                    borderRadius: '16px',
                                    padding: 'clamp(20px, 2.5vw, 28px)',
                                    textAlign: 'center',
                                    display: 'flex',
                                    flexDirection: 'column',
                                    alignItems: 'center',
                                    gap: '14px',
                                }}
                            >
                                <div
                                    style={{
                                        width: 'clamp(96px, 14vw, 128px)',
                                        height: 'clamp(96px, 14vw, 128px)',
                                        borderRadius: '9999px',
                                        background: '#e2e8f0',
                                        color: '#475569',
                                        display: 'flex',
                                        alignItems: 'center',
                                        justifyContent: 'center',
                                        fontSize: '2rem',
                                        fontWeight: 700,
                                        overflow: 'hidden',
                                        flexShrink: 0,
                                    }}
                                >
                                    {m.photo ? (
                                        <img
                                            src={`/storage/${m.photo}`}
                                            alt={m.name ?? 'Team member'}
                                            style={{
                                                width: '100%',
                                                height: '100%',
                                                objectFit: 'cover',
                                                display: 'block',
                                            }}
                                        />
                                    ) : (
                                        initials(m.name)
                                    )}
                                </div>
                                <div>
                                    {m.name && (
                                        <h3
                                            style={{
                                                margin: 0,
                                                fontSize: '1.125rem',
                                                fontWeight: 700,
                                                color: fg,
                                                letterSpacing: '-0.01em',
                                            }}
                                        >
                                            {m.name}
                                        </h3>
                                    )}
                                    {m.jobTitle && (
                                        <p
                                            style={{
                                                margin: '4px 0 0',
                                                fontSize: '0.875rem',
                                                color: mutedFg,
                                                fontWeight: 500,
                                            }}
                                        >
                                            {m.jobTitle}
                                        </p>
                                    )}
                                </div>
                                {m.bio && (
                                    <p
                                        style={{
                                            margin: 0,
                                            fontSize: '0.9375rem',
                                            lineHeight: 1.6,
                                            color: mutedFg,
                                        }}
                                    >
                                        {m.bio}
                                    </p>
                                )}
                            </article>
                        ))}
                    </div>
                ) : (
                    <p style={{ textAlign: 'center', color: mutedFg, margin: 0 }}>
                        No team members yet — add some in the Theme Builder.
                    </p>
                )}
            </div>

            <style>{`
                @media (max-width: 900px) {
                    .tb-team-grid.tb-team-cols-3,
                    .tb-team-grid.tb-team-cols-4 {
                        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                    }
                }
                @media (max-width: 560px) {
                    .tb-team-grid {
                        grid-template-columns: 1fr !important;
                    }
                }
            `}</style>
        </section>
    );
}
