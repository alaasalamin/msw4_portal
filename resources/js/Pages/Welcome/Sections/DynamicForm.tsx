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
    heading?: string;
    paragraph?: string;
    form_id?: number | string | null;
    form?: HydratedForm;
    theme?: 'light' | 'dark' | 'muted';
    align?: 'left' | 'center';
    bg?: string;
    fg?: string;
    mutedFg?: string;
}

export default function DynamicForm({ settings }: { settings: FormSettings }) {
    const align   = settings.align ?? 'center';
    const fg      = settings.fg ?? '#0f172a';
    const mutedFg = settings.mutedFg ?? '#64748b';
    const formId  = settings.form_id ? Number(settings.form_id) : null;

    const hasContent = !!settings.heading || !!settings.paragraph;

    return (
        <section
            style={{
                background: settings.bg ?? '#ffffff',
                color: fg,
                padding: 'clamp(48px, 8vw, 96px) clamp(16px, 4vw, 32px) clamp(24px, 4vw, 48px)',
                fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
            }}
        >
            <div style={{ maxWidth: '720px', margin: '0 auto' }}>
                {hasContent && (
                    <div style={{
                        textAlign: align,
                        marginBottom: 'clamp(24px, 4vw, 36px)',
                    }}>
                        {settings.heading && (
                            <h2 style={{
                                fontSize: 'clamp(1.75rem, 3.5vw, 2.5rem)',
                                fontWeight: 700,
                                letterSpacing: '-0.025em',
                                margin: 0,
                                lineHeight: 1.15,
                                color: fg,
                            }}>{settings.heading}</h2>
                        )}
                        {settings.paragraph && (
                            <p style={{
                                fontSize: 'clamp(1rem, 1.3vw, 1.0625rem)',
                                color: mutedFg,
                                margin: '12px auto 0',
                                maxWidth: '52ch',
                                lineHeight: 1.6,
                                marginInline: align === 'center' ? 'auto' : '0',
                            }}>{settings.paragraph}</p>
                        )}
                    </div>
                )}

                {!formId ? (
                    <div style={{
                        padding: 'clamp(24px, 4vw, 40px)',
                        background: '#f8fafc',
                        borderRadius: 16,
                        textAlign: 'center',
                        color: mutedFg,
                    }}>
                        No form selected — pick one in the Website Builder.
                    </div>
                ) : !settings.form ? (
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
                ) : (
                    <FormSection
                        form_id={formId}
                        form={settings.form}
                        theme={settings.theme ?? 'light'}
                        page_slug="home"
                    />
                )}
            </div>
        </section>
    );
}
