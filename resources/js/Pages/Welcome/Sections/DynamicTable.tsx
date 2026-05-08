interface TableColumn {
    label?: string;
    align?: 'left' | 'center' | 'right';
}

interface TableRow {
    cells?: string;
}

interface TableSettings {
    variant?: 'striped' | 'bordered' | 'minimal';
    heading?: string;
    subtitle?: string;
    columns?: TableColumn[];
    rows?: TableRow[];
    firstColEmphasis?: boolean;
    bg?: string;
    fg?: string;
    mutedFg?: string;
    headerBg?: string;
    headerFg?: string;
    altRowBg?: string;
    borderColor?: string;
}

function parseCells(text?: string): string[] {
    if (!text) return [];
    return text.split(/\r?\n/).map((s) => s.trim());
}

export default function DynamicTable({ settings }: { settings: TableSettings }) {
    const variant = settings.variant ?? 'striped';
    const columns = (settings.columns ?? []).filter((c) => c.label?.trim().length);
    const rawRows = settings.rows ?? [];
    const colCount = columns.length || 1;

    const fg          = settings.fg          ?? '#0f172a';
    const mutedFg     = settings.mutedFg     ?? '#64748b';
    const headerBg    = settings.headerBg    ?? '#0f172a';
    const headerFg    = settings.headerFg    ?? '#ffffff';
    const altRowBg    = settings.altRowBg    ?? '#f8fafc';
    const borderColor = settings.borderColor ?? '#e5e7eb';
    const emphasizeFirstCol = settings.firstColEmphasis !== false;

    // Pad each row's cell list out to the column count so layouts always line up.
    const rows: string[][] = rawRows
        .map((r) => {
            const cells = parseCells(r.cells);
            const padded = [...cells];
            while (padded.length < colCount) padded.push('');
            return padded.slice(0, colCount);
        });

    // ── Variant-specific cell border + bg helpers ──────────────────────────
    const cellBorderRight = variant === 'bordered' ? `1px solid ${borderColor}` : 'none';
    const cellBorderBottom = (variant === 'bordered' || variant === 'minimal') ? `1px solid ${borderColor}` : 'none';
    const headerBorderRight = variant === 'bordered' ? '1px solid rgba(255,255,255,0.10)' : 'none';
    const rowBg = (i: number) => variant === 'striped' && i % 2 === 1 ? altRowBg : 'transparent';

    return (
        <section style={{
            background: settings.bg ?? '#ffffff',
            color: fg,
            padding: 'clamp(48px, 8vw, 96px) clamp(16px, 4vw, 32px)',
            fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
        }}>
            <div style={{ maxWidth: '1100px', margin: '0 auto' }}>
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

                {columns.length === 0 || rows.length === 0 ? (
                    <p style={{ textAlign: 'center', color: mutedFg, margin: 0 }}>
                        No table data yet — add columns and rows in the Website Builder.
                    </p>
                ) : (
                    <div style={{
                        overflowX: 'auto',
                        WebkitOverflowScrolling: 'touch',
                        borderRadius: 12,
                        border: variant === 'bordered' ? `1px solid ${borderColor}` : 'none',
                    }}>
                        <table style={{
                            width: '100%',
                            borderCollapse: 'collapse',
                            minWidth: `${colCount * 140}px`,
                            fontSize: '0.9375rem',
                            color: fg,
                        }}>
                            <thead>
                                <tr>
                                    {columns.map((col, i) => (
                                        <th key={i} style={{
                                            background: headerBg,
                                            color: headerFg,
                                            textAlign: col.align ?? 'left',
                                            padding: '14px clamp(12px, 1.6vw, 20px)',
                                            fontSize: '0.75rem',
                                            fontWeight: 700,
                                            textTransform: 'uppercase',
                                            letterSpacing: '0.06em',
                                            borderRight: i === columns.length - 1 ? 'none' : headerBorderRight,
                                        }}>
                                            {col.label}
                                        </th>
                                    ))}
                                </tr>
                            </thead>
                            <tbody>
                                {rows.map((row, ri) => (
                                    <tr key={ri} style={{ background: rowBg(ri) }}>
                                        {row.map((cell, ci) => {
                                            const align = columns[ci]?.align ?? 'left';
                                            const bold = emphasizeFirstCol && ci === 0;
                                            return (
                                                <td key={ci} style={{
                                                    padding: '14px clamp(12px, 1.6vw, 20px)',
                                                    textAlign: align,
                                                    verticalAlign: 'middle',
                                                    fontWeight: bold ? 600 : 400,
                                                    color: bold ? fg : (variant === 'minimal' ? fg : fg),
                                                    borderRight: ci === row.length - 1 ? 'none' : cellBorderRight,
                                                    borderBottom: ri === rows.length - 1 ? 'none' : cellBorderBottom,
                                                }}>
                                                    {cell || <span style={{ color: mutedFg, opacity: 0.6 }}>—</span>}
                                                </td>
                                            );
                                        })}
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                )}
            </div>
        </section>
    );
}
