// Standalone Vite entry that mounts the real DynamicTextBlock React
// component inside a plain DOM element. The Theme Builder's Edit Text
// Block modal loads this entry and calls window.tbMountTextPreview
// from Alpine to render the live preview pane on the right side of
// the modal — no iframe, no separate page.

import '../css/text-block-preview.css';
import { createRoot, type Root } from 'react-dom/client';
import DynamicTextBlock from './Pages/Welcome/Sections/DynamicTextBlock';

const roots = new WeakMap<Element, Root>();

function mount(el: HTMLElement, settings: Record<string, unknown>): void {
    let root = roots.get(el);
    // If the element was disconnected from the DOM (e.g. Livewire
    // tore it down despite wire:ignore), unmount the orphan root and
    // create a fresh one — otherwise React warns or throws.
    if (root && ! el.isConnected) {
        try { root.unmount(); } catch (_) { /* already unmounted */ }
        root = undefined;
    }
    if (! root) {
        root = createRoot(el);
        roots.set(el, root);
    }
    try {
        // eslint-disable-next-line @typescript-eslint/no-explicit-any
        root.render(<DynamicTextBlock settings={settings as any} />);
    } catch (e) {
        // If render fails (rare — typically a stale root), drop the
        // root and rebuild on the next call.
        // eslint-disable-next-line no-console
        console.warn('[tb-text-preview] render failed, dropping root', e);
        try { root.unmount(); } catch (_) { /* noop */ }
        roots.delete(el);
    }
}

function unmount(el: HTMLElement): void {
    const root = roots.get(el);
    if (root) {
        root.unmount();
        roots.delete(el);
    }
}

declare global {
    interface Window {
        tbMountTextPreview?: (el: HTMLElement, settings: Record<string, unknown>) => void;
        tbUnmountTextPreview?: (el: HTMLElement) => void;
    }
}

window.tbMountTextPreview = mount;
window.tbUnmountTextPreview = unmount;
