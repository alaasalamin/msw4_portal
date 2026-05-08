interface Member {
    name?: string;
    jobTitle?: string;
    bio?: string;
    photo?: string | null;
}

interface TeamSettings {
    variant?: 'cards' | 'list' | 'photo_focus' | 'compact';
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

// ── Dispatcher ──────────────────────────────────────────────────────────────

export default function DynamicTeam({ settings }: { settings: TeamSettings }) {
    switch (settings.variant) {
        case 'list':        return <ListView    settings={settings} />;
        case 'photo_focus': return <PhotoFocus  settings={settings} />;
        case 'compact':     return <Compact     settings={settings} />;
        case 'cards':
        default:            return <Cards       settings={settings} />;
    }
}

// ── Shared chrome ───────────────────────────────────────────────────────────

function SectionWrap({ settings, maxWidth = '1200px', children }: { settings: TeamSettings; maxWidth?: string; children: React.ReactNode }) {
    const fg = settings.fg ?? '#0f172a';
    return (
        <section style={{
            background: settings.bg ?? '#ffffff',
            color: fg,
            padding: 'clamp(48px, 8vw, 96px) clamp(16px, 4vw, 32px)',
            fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
        }}>
            <div style={{ maxWidth, margin: '0 auto' }}>{children}</div>
        </section>
    );
}

function SectionHeader({ heading, subtitle, mutedFg }: { heading?: string; subtitle?: string; mutedFg: string }) {
    if (!heading && !subtitle) return null;
    return (
        <div style={{ textAlign: 'center', marginBottom: 'clamp(32px, 5vw, 56px)' }}>
            {heading && (
                <h2 style={{
                    fontSize: 'clamp(1.75rem, 3.5vw, 2.5rem)',
                    fontWeight: 700,
                    letterSpacing: '-0.025em',
                    margin: 0,
                    lineHeight: 1.15,
                }}>{heading}</h2>
            )}
            {subtitle && (
                <p style={{
                    fontSize: 'clamp(1rem, 1.3vw, 1.125rem)',
                    color: mutedFg,
                    margin: '12px auto 0',
                    maxWidth: '52ch',
                    lineHeight: 1.6,
                }}>{subtitle}</p>
            )}
        </div>
    );
}

function Avatar({ member, size, square = false }: { member: Member; size: number; square?: boolean }) {
    return (
        <div style={{
            width: size,
            height: size,
            borderRadius: square ? 16 : 9999,
            background: '#e2e8f0',
            color: '#475569',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
            fontSize: size >= 96 ? '2rem' : size >= 64 ? '1rem' : '0.8125rem',
            fontWeight: 700,
            overflow: 'hidden',
            flexShrink: 0,
        }}>
            {member.photo ? (
                <img
                    src={`/storage/${member.photo}`}
                    alt={member.name ?? 'Team member'}
                    style={{ width: '100%', height: '100%', objectFit: 'cover', display: 'block' }}
                />
            ) : initials(member.name)}
        </div>
    );
}

// ── Variant: cards (existing) ──────────────────────────────────────────────

function Cards({ settings }: { settings: TeamSettings }) {
    const members = settings.members ?? [];
    const cols = Math.max(2, Math.min(4, Number(settings.columns) || 3));
    const fg = settings.fg ?? '#0f172a';
    const mutedFg = settings.mutedFg ?? '#64748b';
    const cardBg = settings.cardBg ?? '#f8fafc';

    return (
        <SectionWrap settings={settings}>
            <SectionHeader heading={settings.heading} subtitle={settings.subtitle} mutedFg={mutedFg} />
            {members.length === 0 ? (
                <p style={{ textAlign: 'center', color: mutedFg, margin: 0 }}>No team members yet — add some in the Website Builder.</p>
            ) : (
                <div className={`tb-team-grid tb-team-cols-${cols}`} style={{
                    display: 'grid', gap: 24,
                    gridTemplateColumns: `repeat(${cols}, minmax(0, 1fr))`,
                }}>
                    {members.map((m, i) => (
                        <article key={i} style={{
                            background: cardBg,
                            borderRadius: 16,
                            padding: 'clamp(20px, 2.5vw, 28px)',
                            textAlign: 'center',
                            display: 'flex',
                            flexDirection: 'column',
                            alignItems: 'center',
                            gap: 14,
                        }}>
                            <Avatar member={m} size={Math.max(96, Math.min(128, 128))} />
                            <div>
                                {m.name && <h3 style={{ margin: 0, fontSize: '1.125rem', fontWeight: 700, color: fg, letterSpacing: '-0.01em' }}>{m.name}</h3>}
                                {m.jobTitle && <p style={{ margin: '4px 0 0', fontSize: '0.875rem', color: mutedFg, fontWeight: 500 }}>{m.jobTitle}</p>}
                            </div>
                            {m.bio && <p style={{ margin: 0, fontSize: '0.9375rem', lineHeight: 1.6, color: mutedFg }}>{m.bio}</p>}
                        </article>
                    ))}
                </div>
            )}
            <style>{`
                @media (max-width: 900px) {
                    .tb-team-grid.tb-team-cols-3,
                    .tb-team-grid.tb-team-cols-4 { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
                }
                @media (max-width: 560px) {
                    .tb-team-grid { grid-template-columns: 1fr !important; }
                }
            `}</style>
        </SectionWrap>
    );
}

// ── Variant: list (vertical, photo + content rows) ─────────────────────────

function ListView({ settings }: { settings: TeamSettings }) {
    const members = settings.members ?? [];
    const fg = settings.fg ?? '#0f172a';
    const mutedFg = settings.mutedFg ?? '#64748b';

    return (
        <SectionWrap settings={settings} maxWidth="820px">
            <SectionHeader heading={settings.heading} subtitle={settings.subtitle} mutedFg={mutedFg} />
            {members.length === 0 ? (
                <p style={{ textAlign: 'center', color: mutedFg, margin: 0 }}>No team members yet — add some in the Website Builder.</p>
            ) : (
                <ul style={{ listStyle: 'none', padding: 0, margin: 0 }}>
                    {members.map((m, i) => (
                        <li key={i} className="tb-team-list-row" style={{
                            display: 'grid',
                            gridTemplateColumns: '88px 1fr',
                            gap: 22,
                            alignItems: 'start',
                            padding: i === 0 ? '0 0 clamp(20px, 2.4vw, 28px)' : 'clamp(20px, 2.4vw, 28px) 0',
                            borderTop: i === 0 ? 'none' : '1px solid rgba(15,23,42,0.08)',
                        }}>
                            <Avatar member={m} size={88} square />
                            <div>
                                {m.name && <h3 style={{ margin: 0, fontSize: '1.125rem', fontWeight: 700, color: fg, letterSpacing: '-0.01em' }}>{m.name}</h3>}
                                {m.jobTitle && <p style={{ margin: '4px 0 0', fontSize: '0.875rem', color: mutedFg, fontWeight: 500 }}>{m.jobTitle}</p>}
                                {m.bio && <p style={{ margin: '10px 0 0', fontSize: '0.9375rem', lineHeight: 1.6, color: mutedFg }}>{m.bio}</p>}
                            </div>
                        </li>
                    ))}
                </ul>
            )}
            <style>{`
                @media (max-width: 540px) {
                    .tb-team-list-row { grid-template-columns: 1fr !important; text-align: center; justify-items: center; }
                    .tb-team-list-row > div:last-child > * { margin-left: auto; margin-right: auto; }
                }
            `}</style>
        </SectionWrap>
    );
}

// ── Variant: photo_focus (square portraits, hover overlay) ─────────────────

function PhotoFocus({ settings }: { settings: TeamSettings }) {
    const members = settings.members ?? [];
    const cols = Math.max(2, Math.min(4, Number(settings.columns) || 3));
    const mutedFg = settings.mutedFg ?? '#64748b';

    return (
        <SectionWrap settings={settings}>
            <SectionHeader heading={settings.heading} subtitle={settings.subtitle} mutedFg={mutedFg} />
            {members.length === 0 ? (
                <p style={{ textAlign: 'center', color: mutedFg, margin: 0 }}>No team members yet — add some in the Website Builder.</p>
            ) : (
                <div className={`tb-team-photo-grid tb-team-photo-cols-${cols}`} style={{
                    display: 'grid',
                    gap: 18,
                    gridTemplateColumns: `repeat(${cols}, minmax(0, 1fr))`,
                }}>
                    {members.map((m, i) => (
                        <figure key={i} className="tb-team-photo-tile" style={{
                            margin: 0,
                            position: 'relative',
                            aspectRatio: '1 / 1',
                            borderRadius: 14,
                            overflow: 'hidden',
                            background: '#e2e8f0',
                            boxShadow: '0 1px 2px rgba(15,23,42,.05), 0 8px 20px -10px rgba(15,23,42,.18)',
                        }}>
                            {m.photo ? (
                                <img
                                    src={`/storage/${m.photo}`}
                                    alt={m.name ?? ''}
                                    loading="lazy"
                                    style={{ position: 'absolute', inset: 0, width: '100%', height: '100%', objectFit: 'cover', display: 'block', transition: 'transform .35s ease' }}
                                />
                            ) : (
                                <div style={{
                                    position: 'absolute', inset: 0,
                                    display: 'flex', alignItems: 'center', justifyContent: 'center',
                                    color: '#475569', fontSize: '2.25rem', fontWeight: 700,
                                }}>{initials(m.name)}</div>
                            )}
                            <figcaption className="tb-team-photo-caption" style={{
                                position: 'absolute', inset: 0,
                                background: 'linear-gradient(180deg, rgba(0,0,0,0) 50%, rgba(0,0,0,0.85) 100%)',
                                display: 'flex',
                                flexDirection: 'column',
                                justifyContent: 'flex-end',
                                padding: 'clamp(12px, 2vw, 18px)',
                                color: '#ffffff',
                                pointerEvents: 'none',
                            }}>
                                {m.name && <div style={{ fontSize: '1rem', fontWeight: 700, letterSpacing: '-0.01em' }}>{m.name}</div>}
                                {m.jobTitle && <div style={{ fontSize: '0.8125rem', opacity: 0.85, marginTop: 2 }}>{m.jobTitle}</div>}
                                {m.bio && (
                                    <div className="tb-team-photo-bio" style={{
                                        fontSize: '0.8125rem',
                                        lineHeight: 1.5,
                                        opacity: 0.85,
                                        marginTop: 8,
                                        maxHeight: 0,
                                        overflow: 'hidden',
                                        transition: 'max-height .3s ease',
                                    }}>{m.bio}</div>
                                )}
                            </figcaption>
                        </figure>
                    ))}
                </div>
            )}
            <style>{`
                .tb-team-photo-tile:hover img { transform: scale(1.05); }
                .tb-team-photo-tile:hover .tb-team-photo-bio { max-height: 200px; }
                @media (max-width: 900px) {
                    .tb-team-photo-grid.tb-team-photo-cols-3,
                    .tb-team-photo-grid.tb-team-photo-cols-4 { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
                }
                @media (max-width: 480px) {
                    .tb-team-photo-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
                }
            `}</style>
        </SectionWrap>
    );
}

// ── Variant: compact (small avatars + name + role only) ────────────────────

function Compact({ settings }: { settings: TeamSettings }) {
    const members = settings.members ?? [];
    const fg = settings.fg ?? '#0f172a';
    const mutedFg = settings.mutedFg ?? '#64748b';

    return (
        <SectionWrap settings={settings}>
            <SectionHeader heading={settings.heading} subtitle={settings.subtitle} mutedFg={mutedFg} />
            {members.length === 0 ? (
                <p style={{ textAlign: 'center', color: mutedFg, margin: 0 }}>No team members yet — add some in the Website Builder.</p>
            ) : (
                <div style={{
                    display: 'flex',
                    flexWrap: 'wrap',
                    justifyContent: 'center',
                    gap: 'clamp(20px, 3vw, 36px) clamp(16px, 2.5vw, 28px)',
                }}>
                    {members.map((m, i) => (
                        <div key={i} style={{
                            width: 'clamp(160px, 20vw, 200px)',
                            textAlign: 'center',
                            display: 'flex',
                            flexDirection: 'column',
                            alignItems: 'center',
                            gap: 10,
                        }}>
                            <Avatar member={m} size={84} />
                            <div style={{ minWidth: 0 }}>
                                {m.name && <div style={{ fontSize: '0.9375rem', fontWeight: 700, color: fg, letterSpacing: '-0.005em', lineHeight: 1.25 }}>{m.name}</div>}
                                {m.jobTitle && <div style={{ fontSize: '0.8125rem', color: mutedFg, marginTop: 3, lineHeight: 1.35 }}>{m.jobTitle}</div>}
                            </div>
                        </div>
                    ))}
                </div>
            )}
        </SectionWrap>
    );
}
