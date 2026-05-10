import { useEffect, useState } from 'react';
import { Head } from '@inertiajs/react';
import DynamicTextBlock from '../Welcome/Sections/DynamicTextBlock';

interface PreviewProps {
    settings?: Record<string, unknown> | unknown[];
}

function hasContent(s: Record<string, unknown>): boolean {
    if (!s || typeof s !== 'object') return false;
    const blocks = (s.blocks as Array<{ text?: string }> | undefined) ?? [];
    if (blocks.some((b) => (b?.text ?? '').trim() !== '')) return true;
    if ((s.heading as string | undefined)?.trim()) return true;
    if ((s.body as string | undefined)?.trim()) return true;
    return false;
}

export default function TextBlockPreview({ settings: initialSettings }: PreviewProps) {
    const seed = (initialSettings && !Array.isArray(initialSettings) ? initialSettings : {}) as Record<string, unknown>;
    const [settings, setSettings] = useState<Record<string, unknown>>(seed);

    useEffect(() => {
        function handleMessage(event: MessageEvent) {
            const data = event.data;
            if (data && typeof data === 'object' && data.type === 'tb-text-preview:settings') {
                // eslint-disable-next-line no-console
                console.debug('[tb-preview] settings received', data.settings);
                setSettings((data.settings ?? {}) as Record<string, unknown>);
            }
        }
        window.addEventListener('message', handleMessage);

        // Tell the parent window we're mounted and ready to receive
        // settings updates — the parent will respond with the current
        // form state immediately, before the user touches anything.
        try {
            window.parent?.postMessage({ type: 'tb-text-preview:ready' }, '*');
            // eslint-disable-next-line no-console
            console.debug('[tb-preview] ready ping sent');
        } catch (_) {
            // No-op — parent may be cross-origin in dev.
        }

        return () => window.removeEventListener('message', handleMessage);
    }, []);

    return (
        <>
            <Head title="Text block preview" />
            {hasContent(settings) ? (
                <DynamicTextBlock settings={settings as never} />
            ) : (
                <div style={{
                    minHeight: '100vh',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    padding: '48px 24px',
                    color: '#64748b',
                    fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
                    fontSize: '0.9375rem',
                    textAlign: 'center',
                    background: '#f8fafc',
                }}>
                    Add a heading or paragraph on the left to see your text block here.
                </div>
            )}
        </>
    );
}
