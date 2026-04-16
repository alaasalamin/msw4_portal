import React, { useState } from 'react';
import axios from 'axios';

interface FormField {
    id: number;
    label: string;
    type: string;
    placeholder: string | null;
    is_required: boolean;
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
}

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
}: Props) {
    const [values, setValues] = useState<Record<string, string>>({});
    const [errors, setErrors] = useState<Record<string, string[]>>({});
    const [submitting, setSubmitting] = useState(false);
    const [success, setSuccess] = useState<string | null>(null);

    if (!form) return null;

    const bg      = THEMES[theme] ?? THEMES.light;
    const isDark  = theme === 'dark';
    const fields  = form.fields ?? [];

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

    const inputBase = `w-full px-4 py-2.5 rounded-lg border text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-orange-400 ${
        isDark
            ? 'bg-zinc-800 border-zinc-700 text-white placeholder-zinc-500'
            : 'bg-white border-zinc-200 text-zinc-900 placeholder-zinc-400'
    }`;

    const labelBase = `block text-sm font-medium mb-1 ${isDark ? 'text-zinc-300' : 'text-zinc-700'}`;
    const errorCls  = 'text-red-400 text-xs mt-1';

    return (
        <section className={`${bg} py-16 px-4`}>
            <div className="mx-auto max-w-xl">
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
                    <div className="rounded-xl border border-green-500/30 bg-green-500/10 px-6 py-8 text-center">
                        <svg className="mx-auto mb-3 h-10 w-10 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={1.5}>
                            <path strokeLinecap="round" strokeLinejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p className={`text-base font-semibold ${isDark ? 'text-white' : 'text-zinc-800'}`}>{success}</p>
                    </div>
                ) : (
                    <form onSubmit={handleSubmit} className="space-y-5">
                        {fields.map(field => (
                            <div key={field.id}>
                                <label className={labelBase}>
                                    {field.label}
                                    {field.is_required && <span className="ml-1 text-red-400">*</span>}
                                </label>

                                {field.type === 'textarea' ? (
                                    <textarea
                                        className={`${inputBase} resize-none`}
                                        rows={4}
                                        placeholder={field.placeholder ?? ''}
                                        value={values[`field_${field.id}`] ?? ''}
                                        onChange={e => handleChange(field.id, e.target.value)}
                                    />
                                ) : field.type === 'select' ? (
                                    <select
                                        className={inputBase}
                                        value={values[`field_${field.id}`] ?? ''}
                                        onChange={e => handleChange(field.id, e.target.value)}
                                    >
                                        <option value="">— select —</option>
                                        {(field.options ?? []).map(opt => (
                                            <option key={opt.value} value={opt.value}>{opt.label}</option>
                                        ))}
                                    </select>
                                ) : field.type === 'checkbox' ? (
                                    <label className="flex items-center gap-3 cursor-pointer">
                                        <input
                                            type="checkbox"
                                            className="h-4 w-4 rounded border-zinc-300 accent-orange-500"
                                            checked={values[`field_${field.id}`] === '1'}
                                            onChange={e => handleChange(field.id, e.target.checked ? '1' : '')}
                                        />
                                        <span className={`text-sm ${isDark ? 'text-zinc-400' : 'text-zinc-600'}`}>
                                            {field.placeholder ?? field.label}
                                        </span>
                                    </label>
                                ) : (
                                    <input
                                        type={field.type}
                                        className={inputBase}
                                        placeholder={field.placeholder ?? ''}
                                        value={values[`field_${field.id}`] ?? ''}
                                        onChange={e => handleChange(field.id, e.target.value)}
                                    />
                                )}

                                {errors[`field_${field.id}`]?.map((err, i) => (
                                    <p key={i} className={errorCls}>{err}</p>
                                ))}
                            </div>
                        ))}

                        <button
                            type="submit"
                            disabled={submitting}
                            className="w-full rounded-lg bg-orange-500 px-6 py-3 text-sm font-bold text-white transition hover:bg-orange-600 disabled:opacity-60"
                        >
                            {submitting ? 'Sending…' : 'Send'}
                        </button>
                    </form>
                )}
            </div>
        </section>
    );
}
