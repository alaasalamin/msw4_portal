import { useEffect, useState } from 'react';
import { Head } from '@inertiajs/react';
import DynamicTextBlock from '../Welcome/Sections/DynamicTextBlock';

interface PreviewProps {
    settings?: Record<string, unknown>;
}

export default function TextBlockPreview({ settings: initialSettings }: PreviewProps) {
    const [settings, setSettings] = useState<Record<string, unknown>>(initialSettings ?? {});

    useEffect(() => {
        function handleMessage(event: MessageEvent) {
            const data = event.data;
            if (data && typeof data === 'object' && data.type === 'tb-text-preview:settings') {
                setSettings(data.settings ?? {});
            }
        }
        window.addEventListener('message', handleMessage);

        // Tell the parent window we're mounted and ready to receive
        // settings updates — the parent will respond with the current
        // form state immediately, before the user touches anything.
        try {
            window.parent?.postMessage({ type: 'tb-text-preview:ready' }, '*');
        } catch (_) {
            // No-op — parent may be cross-origin in dev.
        }

        return () => window.removeEventListener('message', handleMessage);
    }, []);

    return (
        <>
            <Head title="Text block preview" />
            <DynamicTextBlock settings={settings as never} />
        </>
    );
}
