import FormSection from '@/Pages/Sections/FormSection';

interface FormField {
    id: number;
    label: string;
    type: string;
    placeholder: string | null;
    is_required: boolean;
    col_span: 'full' | 'half';
    options: Array<{ label: string; value: string }> | null;
    sort_order: number;
}

interface HydratedForm {
    id: number;
    name: string;
    success_message: string | null;
    redirect_url: string | null;
    fields: FormField[];
}

interface FormSettings {
    variant?: 'default' | 'split' | 'boxed' | 'bg_image';
    heading?: string;
    paragraph?: string;
    form_id?: number | string | null;
    form?: HydratedForm;
    theme?: 'light' | 'dark' | 'muted';
    align?: 'left' | 'center';
    bg?: string;
    fg?: string;
    mutedFg?: string;
    image?: string | null;
    overlay?: number | string;
}

// ── Dispatcher ──────────────────────────────────────────────────────────────

export default function DynamicForm({ settings }: { settings: FormSettings }) {
    switch (settings.variant) {
        case 'split':    return <Split    settings={settings} />;
        case 'boxed':    return <Boxed    settings={settings} />;
        case 'bg_image': return <BgImage  settings={settings} />;
        case 'default':
        default:         return <Default  settings={settings} />;
    }
}

// ── Helpers ────────────────────────────────────────────────────────────────

function FormBody({ settings, theme }: { settings: FormSettings; theme?: FormSettings['theme'] }) {
    const formId = settings.form_id ? Number(settings.form_id) : null;
    const mutedFg = settings.mutedFg ?? '#64748b';

    if (!formId) {
        return (
            <div style={{
                padding: 'clamp(24px, 4vw, 40px)',
                background: '#f8fafc',
                borderRadius: 16,
                textAlign: 'center',
                color: mutedFg,
            }}>
                No form selected — pick one in the Website Builder.
            </div>
        );
    }
    if (!settings.form) {
        return (
            <div style={{
                padding: 'clamp(24px, 4vw, 40px)',
                background: '#fff7ed',
                border: '1px solid #fed7aa',
                borderRadius: 16,
                textAlign: 'center',
                color: '#9a3412',
            }}>
                Form #{formId} couldn't be loaded. It may have been deleted.
            </div>
        );
    }
    return (
        <FormSection
            form_id={formId}
            form={settings.form}
            theme={theme ?? settings.theme ?? 'light'}
            page_slug="home"
        />
    );
}

function Heading({ text, color }: { text: string; color: string }) {
    return (
        <h2 style={{
            fontSize: 'clamp(1.75rem, 3.5vw, 2.5rem)',
            fontWeight: 700,
            letterSpacing: '-0.025em',
            margin: 0,
            lineHeight: 1.15,
            color,
        }}>{text}</h2>
    );
}

function Paragraph({ text, color, align }: { text: string; color: string; align: 'left' | 'center' }) {
    return (
        <p style={{
            fontSize: 'clamp(1rem, 1.3vw, 1.0625rem)',
            color,
            margin: '12px 0 0',
            maxWidth: '52ch',
            lineHeight: 1.6,
            marginInline: align === 'center' ? 'auto' : '0',
        }}>{text}</p>
    );
}

// ── Variant: default (existing) ────────────────────────────────────────────

function Default({ settings }: { settings: FormSettings }) {
    const align   = settings.align ?? 'center';
    const fg      = settings.fg ?? '#0f172a';
    const mutedFg = settings.mutedFg ?? '#64748b';
    const hasContent = !!settings.heading || !!settings.paragraph;

    return (
        <section style={{
            background: settings.bg ?? '#ffffff',
            color: fg,
            padding: 'clamp(48px, 8vw, 96px) clamp(16px, 4vw, 32px) clamp(24px, 4vw, 48px)',
            fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
        }}>
            <div style={{ maxWidth: '720px', margin: '0 auto' }}>
                {hasContent && (
                    <div style={{ textAlign: align, marginBottom: 'clamp(24px, 4vw, 36px)' }}>
                        {settings.heading && <Heading text={settings.heading} color={fg} />}
                        {settings.paragraph && <Paragraph text={settings.paragraph} color={mutedFg} align={align} />}
                    </div>
                )}
                <FormBody settings={settings} />
            </div>
        </section>
    );
}

// ── Variant: split (heading left, form right) ──────────────────────────────

function Split({ settings }: { settings: FormSettings }) {
    const fg = settings.fg ?? '#0f172a';
    const mutedFg = settings.mutedFg ?? '#64748b';

    return (
        <section style={{
            background: settings.bg ?? '#ffffff',
            color: fg,
            padding: 'clamp(48px, 8vw, 96px) clamp(16px, 4vw, 32px)',
            fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
        }}>
            <div className="tb-form-split" style={{
                maxWidth: '1200px',
                margin: '0 auto',
                display: 'grid',
                gridTemplateColumns: '1fr 1.2fr',
                alignItems: 'start',
                gap: 'clamp(28px, 5vw, 56px)',
            }}>
                <div style={{ position: 'sticky', top: 24 }}>
                    {settings.heading && <Heading text={settings.heading} color={fg} />}
                    {settings.paragraph && <Paragraph text={settings.paragraph} color={mutedFg} align="left" />}
                </div>
                <div>
                    <FormBody settings={settings} />
                </div>
            </div>
            <style>{`
                @media (max-width: 760px) {
                    .tb-form-split { grid-template-columns: 1fr !important; }
                    .tb-form-split > div:first-child { position: static !important; }
                }
            `}</style>
        </section>
    );
}

// ── Variant: boxed (form in elevated card on tinted bg) ────────────────────

function Boxed({ settings }: { settings: FormSettings }) {
    const align = settings.align ?? 'center';
    const fg = settings.fg ?? '#0f172a';
    const mutedFg = settings.mutedFg ?? '#64748b';
    const hasContent = !!settings.heading || !!settings.paragraph;

    return (
        <section style={{
            background: '#f8fafc',
            color: fg,
            padding: 'clamp(48px, 8vw, 96px) clamp(16px, 4vw, 32px)',
            fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
        }}>
            <div style={{
                maxWidth: '720px',
                margin: '0 auto',
                background: settings.bg ?? '#ffffff',
                borderRadius: 20,
                padding: 'clamp(28px, 4vw, 48px)',
                boxShadow: '0 4px 12px -4px rgba(15,23,42,0.10), 0 24px 48px -16px rgba(15,23,42,0.18)',
            }}>
                {hasContent && (
                    <div style={{ textAlign: align, marginBottom: 'clamp(24px, 4vw, 32px)' }}>
                        {settings.heading && <Heading text={settings.heading} color={fg} />}
                        {settings.paragraph && <Paragraph text={settings.paragraph} color={mutedFg} align={align} />}
                    </div>
                )}
                <FormBody settings={settings} />
            </div>
        </section>
    );
}

// ── Variant: bg_image (full-bleed photo, form floats in a card) ────────────

function BgImage({ settings }: { settings: FormSettings }) {
    const align = settings.align ?? 'center';
    const overlay = Math.max(0, Math.min(1, Number(settings.overlay) || 0.55));
    const hasContent = !!settings.heading || !!settings.paragraph;

    return (
        <section style={{
            position: 'relative',
            background: settings.bg ?? '#0f172a',
            color: '#ffffff',
            padding: 'clamp(64px, 11vw, 144px) clamp(16px, 4vw, 32px)',
            fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
            overflow: 'hidden',
        }}>
            {settings.image && (
                <img
                    src={`/storage/${settings.image}`}
                    alt=""
                    aria-hidden="true"
                    style={{
                        position: 'absolute',
                        inset: 0,
                        width: '100%',
                        height: '100%',
                        objectFit: 'cover',
                        display: 'block',
                    }}
                />
            )}
            <div aria-hidden="true" style={{
                position: 'absolute',
                inset: 0,
                background: `linear-gradient(180deg, rgba(0,0,0,${overlay * 0.85}) 0%, rgba(0,0,0,${overlay}) 100%)`,
            }} />

            <div style={{ position: 'relative', maxWidth: '720px', margin: '0 auto' }}>
                {hasContent && (
                    <div style={{ textAlign: align, marginBottom: 'clamp(24px, 4vw, 36px)' }}>
                        {settings.heading && (
                            <h2 style={{
                                fontSize: 'clamp(1.75rem, 3.5vw, 2.5rem)',
                                fontWeight: 700,
                                letterSpacing: '-0.025em',
                                margin: 0,
                                lineHeight: 1.15,
                                color: '#ffffff',
                            }}>{settings.heading}</h2>
                        )}
                        {settings.paragraph && (
                            <p style={{
                                fontSize: 'clamp(1rem, 1.3vw, 1.0625rem)',
                                color: 'rgba(255,255,255,0.9)',
                                margin: '12px 0 0',
                                maxWidth: '52ch',
                                lineHeight: 1.6,
                                marginInline: align === 'center' ? 'auto' : '0',
                            }}>{settings.paragraph}</p>
                        )}
                    </div>
                )}

                <div style={{
                    background: '#ffffff',
                    borderRadius: 20,
                    padding: 'clamp(24px, 4vw, 40px)',
                    boxShadow: '0 4px 12px -4px rgba(0,0,0,0.30), 0 30px 60px -24px rgba(0,0,0,0.50)',
                }}>
                    <FormBody settings={settings} theme="light" />
                </div>
            </div>
        </section>
    );
}
