import React, { useState } from 'react';
import axios from 'axios';

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

interface Props {
    form_id: number;
    title?: string;
    description?: string;
    theme?: 'light' | 'dark' | 'muted';
    // Injected at render time by PageController
    form?: {
        id: number;
        name: string;
        success_message: string | null;
        redirect_url: string | null;
        fields: FormField[];
    };
    page_slug?: string;
    /** Optional submit button colors. Empty / undefined falls back to the site primary. */
    submitBtnBg?: string;
    submitBtnFg?: string;
}

// ── Polished form styles ────────────────────────────────────────────────────
// Scoped to .tb-form so the look is consistent everywhere DynamicForm
// renders without leaking into other parts of the page. Brand color comes
// from --primary (set globally by app.blade.php from the header section's
// primary color), with a sensible fallback.
const FORM_CSS = `
.tb-form {
    --tbf-radius: 14px;
    --tbf-radius-sm: 10px;
    --tbf-ease: cubic-bezier(0.22, 0.61, 0.36, 1);
    --tbf-bg-input: #f8fafc;
    --tbf-bg-input-hover: #ffffff;
    --tbf-border: #e2e8f0;
    --tbf-border-hover: #cbd5e1;
    --tbf-fg: #0f172a;
    --tbf-muted: #64748b;
    --tbf-placeholder: #94a3b8;
    --tbf-label: #334155;
    --tbf-error-fg: #b91c1c;
    --tbf-error-bg: #fef2f2;
    --tbf-error-border: #fecaca;
    --tbf-primary: var(--primary, #ea580c);
    --tbf-primary-soft: color-mix(in srgb, var(--primary, #ea580c) 14%, transparent);
}
.tb-form[data-theme="dark"] {
    --tbf-bg-input: rgba(255,255,255,0.05);
    --tbf-bg-input-hover: rgba(255,255,255,0.08);
    --tbf-border: rgba(255,255,255,0.10);
    --tbf-border-hover: rgba(255,255,255,0.18);
    --tbf-fg: #f4f4f5;
    --tbf-muted: #a1a1aa;
    --tbf-placeholder: #71717a;
    --tbf-label: #d4d4d8;
}
.tb-form[data-theme="muted"] {
    --tbf-bg-input: #ffffff;
    --tbf-border: #e5e7eb;
}

/* Field labels: uppercase eyebrow style — small, bold, slight tracking. */
.tb-form__label {
    display: block;
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: var(--tbf-label);
    margin-bottom: 8px;
}
.tb-form__label-asterisk {
    margin-left: 4px;
    color: var(--tbf-primary);
    font-weight: 700;
}

/* Inputs share a common pill-rounded filled look. Resting bg is a hair off
   white; focus lifts to white (or hair-darker on dark) with a primary ring
   + soft halo so the affordance feels strong without being noisy. */
.tb-form__input,
.tb-form__textarea,
.tb-form__select {
    width: 100%;
    background: var(--tbf-bg-input);
    color: var(--tbf-fg);
    border: 1.5px solid transparent;
    border-radius: var(--tbf-radius);
    padding: 13px 16px;
    font-size: 0.9375rem;
    font-family: inherit;
    line-height: 1.4;
    box-shadow: inset 0 0 0 1px var(--tbf-border);
    transition: background-color 180ms var(--tbf-ease),
                border-color 180ms var(--tbf-ease),
                box-shadow 180ms var(--tbf-ease);
    appearance: none;
    -webkit-appearance: none;
}
.tb-form__input:hover:not(:focus),
.tb-form__textarea:hover:not(:focus),
.tb-form__select:hover:not(:focus) {
    background: var(--tbf-bg-input-hover);
    box-shadow: inset 0 0 0 1px var(--tbf-border-hover);
}
.tb-form__input:focus,
.tb-form__textarea:focus,
.tb-form__select:focus {
    outline: none;
    background: var(--tbf-bg-input-hover);
    border-color: var(--tbf-primary);
    box-shadow: 0 0 0 4px var(--tbf-primary-soft);
}
.tb-form__input::placeholder,
.tb-form__textarea::placeholder {
    color: var(--tbf-placeholder);
    opacity: 1;
}
.tb-form__textarea {
    resize: vertical;
    min-height: 112px;
}

/* Custom select chevron — keeps the native dropdown but swaps the
   browser's default arrow for a subtle one tinted in our muted color. */
.tb-form__select {
    background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.4' stroke-linecap='round' stroke-linejoin='round'><polyline points='6 9 12 15 18 9'/></svg>");
    background-repeat: no-repeat;
    background-position: right 18px center;
    background-size: 14px 14px;
    padding-right: 44px;
}
.tb-form[data-theme="dark"] .tb-form__select {
    background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%23a1a1aa' stroke-width='2.4' stroke-linecap='round' stroke-linejoin='round'><polyline points='6 9 12 15 18 9'/></svg>");
}

/* Error state — subtle red wash + ring, never alarmist. */
.tb-form__input--error,
.tb-form__textarea--error,
.tb-form__select--error {
    background: var(--tbf-error-bg);
    box-shadow: inset 0 0 0 1.5px var(--tbf-error-border);
}
.tb-form__error {
    margin-top: 6px;
    font-size: 0.75rem;
    color: var(--tbf-error-fg);
    display: flex;
    align-items: center;
    gap: 4px;
}
.tb-form__error::before {
    content: "";
    display: inline-block;
    width: 4px; height: 4px; border-radius: 50%;
    background: currentColor;
    flex-shrink: 0;
}

/* Custom checkbox — bigger, smoother, primary-tinted check. */
.tb-form__checkrow {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 4px 0;
    cursor: pointer;
}
.tb-form__checkbox {
    appearance: none;
    -webkit-appearance: none;
    flex-shrink: 0;
    width: 20px; height: 20px;
    border-radius: 6px;
    border: 1.5px solid var(--tbf-border-hover);
    background: var(--tbf-bg-input);
    cursor: pointer;
    position: relative;
    transition: background-color 160ms var(--tbf-ease),
                border-color 160ms var(--tbf-ease),
                box-shadow 160ms var(--tbf-ease);
    margin-top: 1px;
}
.tb-form__checkbox:hover { border-color: var(--tbf-primary); }
.tb-form__checkbox:focus-visible {
    outline: none;
    box-shadow: 0 0 0 3px var(--tbf-primary-soft);
}
.tb-form__checkbox:checked {
    background: var(--tbf-primary);
    border-color: var(--tbf-primary);
}
.tb-form__checkbox:checked::after {
    content: "";
    position: absolute;
    top: 50%; left: 50%;
    width: 10px; height: 6px;
    border-left: 2px solid #ffffff;
    border-bottom: 2px solid #ffffff;
    transform: translate(-50%, -65%) rotate(-45deg);
}
.tb-form__check-label {
    font-size: 0.9375rem;
    line-height: 1.5;
    color: var(--tbf-fg);
}

/* Subheading + spacer — typographic dividers between groups of fields. */
.tb-form__subheading {
    font-size: 1rem;
    font-weight: 700;
    color: var(--tbf-fg);
    letter-spacing: -0.01em;
    margin: 4px 0 0;
}
.tb-form__subheading-desc {
    font-size: 0.875rem;
    color: var(--tbf-muted);
    margin: 4px 0 0;
}
.tb-form__spacer {
    height: 1px;
    background: var(--tbf-border);
    border: 0;
    margin: 4px 0;
}

/* Submit button — full-width pill with a subtle hover lift. */
.tb-form__submit {
    width: 100%;
    background: var(--tbf-primary);
    color: #ffffff;
    border: 0;
    border-radius: var(--tbf-radius);
    padding: 15px 24px;
    font-size: 0.9375rem;
    font-weight: 700;
    letter-spacing: 0.01em;
    cursor: pointer;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.08),
                0 4px 12px -2px color-mix(in srgb, var(--tbf-primary) 30%, transparent);
    transition: transform 160ms var(--tbf-ease),
                box-shadow 200ms var(--tbf-ease),
                opacity 160ms ease;
}
.tb-form__submit:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(15, 23, 42, 0.10),
                0 10px 24px -4px color-mix(in srgb, var(--tbf-primary) 38%, transparent);
}
.tb-form__submit:active:not(:disabled) { transform: translateY(0); }
.tb-form__submit:disabled { opacity: 0.6; cursor: progress; }

/* Success card — primary-tinted, not green; ties to the brand. */
.tb-form__success {
    border-radius: var(--tbf-radius);
    background: var(--tbf-primary-soft);
    border: 1px solid color-mix(in srgb, var(--tbf-primary) 30%, transparent);
    padding: 28px 24px;
    text-align: center;
    color: var(--tbf-fg);
}
.tb-form__success-icon {
    width: 48px; height: 48px;
    border-radius: 50%;
    margin: 0 auto 12px;
    display: flex; align-items: center; justify-content: center;
    background: var(--tbf-primary);
    color: #ffffff;
}
`;

const THEMES = {
    light: 'bg-white',
    dark:  'bg-zinc-900 text-white',
    muted: 'bg-zinc-50',
};

export default function FormSection({
    form_id,
    title,
    description,
    theme = 'light',
    form,
    page_slug = '',
    submitBtnBg,
    submitBtnFg,
}: Props) {
    const [values, setValues] = useState<Record<string, string>>({});
    const [errors, setErrors] = useState<Record<string, string[]>>({});
    const [submitting, setSubmitting] = useState(false);
    const [success, setSuccess] = useState<string | null>(null);

    if (!form) return null;

    const bg     = THEMES[theme] ?? THEMES.light;
    const isDark = theme === 'dark';
    const fields = form.fields ?? [];

    const handleChange = (fieldId: number, value: string) => {
        setValues(v => ({ ...v, [`field_${fieldId}`]: value }));
        setErrors(e => { const next = { ...e }; delete next[`field_${fieldId}`]; return next; });
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setSubmitting(true);
        setErrors({});

        try {
            const payload: Record<string, string> = { _page_slug: page_slug };
            fields.forEach(f => {
                payload[`field_${f.id}`] = values[`field_${f.id}`] ?? '';
            });

            const res = await axios.post(`/forms/${form.id}/submit`, payload);
            const msg = res.data.message ?? 'Thank you!';
            setSuccess(msg);
            setValues({});

            if (res.data.redirect) {
                setTimeout(() => { window.location.href = res.data.redirect; }, 1500);
            }
        } catch (err: any) {
            if (err.response?.status === 422) {
                setErrors(err.response.data.errors ?? {});
            }
        } finally {
            setSubmitting(false);
        }
    };

    const fieldHasError = (id: number) => !!errors[`field_${id}`]?.length;

    return (
        <section className={`tb-form ${bg} py-16 px-4`} data-theme={theme}>
            <style>{FORM_CSS}</style>
            <div className="mx-auto max-w-2xl">
                {title && (
                    <h2 className={`text-2xl font-bold mb-2 ${isDark ? 'text-white' : 'text-zinc-900'}`}>
                        {title}
                    </h2>
                )}
                {description && (
                    <p className={`text-sm mb-8 ${isDark ? 'text-zinc-400' : 'text-zinc-500'}`}>
                        {description}
                    </p>
                )}

                {success ? (
                    <div className="tb-form__success">
                        <div className="tb-form__success-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2.4} strokeLinecap="round" strokeLinejoin="round">
                                <polyline points="20 6 9 17 4 12" />
                            </svg>
                        </div>
                        <p className="text-base font-semibold">{success}</p>
                    </div>
                ) : (
                    <form onSubmit={handleSubmit} className="grid grid-cols-2 gap-5">
                        {fields.map(field => {
                            if (field.type === 'spacer') {
                                return (
                                    <div key={field.id} className="col-span-2">
                                        <hr className="tb-form__spacer" />
                                    </div>
                                );
                            }

                            if (field.type === 'subheading') {
                                return (
                                    <div key={field.id} className="col-span-2">
                                        <h3 className="tb-form__subheading">{field.label}</h3>
                                        {field.placeholder && (
                                            <p className="tb-form__subheading-desc">{field.placeholder}</p>
                                        )}
                                    </div>
                                );
                            }

                            const hasErr = fieldHasError(field.id);

                            return (
                                <div key={field.id} className={field.col_span === 'half' ? 'col-span-1' : 'col-span-2'}>
                                    {field.type !== 'checkbox' && (
                                        <label className="tb-form__label">
                                            {field.label}
                                            {field.is_required && <span className="tb-form__label-asterisk">*</span>}
                                        </label>
                                    )}

                                    {field.type === 'textarea' ? (
                                        <textarea
                                            className={`tb-form__textarea${hasErr ? ' tb-form__textarea--error' : ''}`}
                                            rows={4}
                                            placeholder={field.placeholder ?? ''}
                                            value={values[`field_${field.id}`] ?? ''}
                                            onChange={e => handleChange(field.id, e.target.value)}
                                        />
                                    ) : field.type === 'select' ? (
                                        <select
                                            className={`tb-form__select${hasErr ? ' tb-form__select--error' : ''}`}
                                            value={values[`field_${field.id}`] ?? ''}
                                            onChange={e => handleChange(field.id, e.target.value)}
                                        >
                                            <option value="">— select —</option>
                                            {(field.options ?? []).map(opt => (
                                                <option key={opt.value} value={opt.value}>{opt.label}</option>
                                            ))}
                                        </select>
                                    ) : field.type === 'checkbox' ? (
                                        <label className="tb-form__checkrow">
                                            <input
                                                type="checkbox"
                                                className="tb-form__checkbox"
                                                checked={values[`field_${field.id}`] === '1'}
                                                onChange={e => handleChange(field.id, e.target.checked ? '1' : '')}
                                            />
                                            <span className="tb-form__check-label">
                                                {field.placeholder ?? field.label}
                                                {field.is_required && <span className="tb-form__label-asterisk">*</span>}
                                            </span>
                                        </label>
                                    ) : (
                                        <input
                                            type={field.type}
                                            className={`tb-form__input${hasErr ? ' tb-form__input--error' : ''}`}
                                            placeholder={field.placeholder ?? ''}
                                            value={values[`field_${field.id}`] ?? ''}
                                            onChange={e => handleChange(field.id, e.target.value)}
                                        />
                                    )}

                                    {errors[`field_${field.id}`]?.map((err, i) => (
                                        <p key={i} className="tb-form__error">{err}</p>
                                    ))}
                                </div>
                            );
                        })}

                        <button
                            type="submit"
                            disabled={submitting}
                            className="tb-form__submit col-span-2"
                            style={submitBtnBg ? {
                                background: submitBtnBg,
                                color: submitBtnFg ?? '#ffffff',
                            } : undefined}
                        >
                            {submitting ? 'Sending…' : 'Send'}
                        </button>
                    </form>
                )}
            </div>
        </section>
    );
}
