<x-filament-panels::page>
    <div
        x-data="themeBuilder()"
        x-init="init()"
        x-on:beforeunload.window="onUnload($event)"
        x-on:theme-builder:section-saved.window="reloadPreview()"
        style="display:grid; grid-template-columns: 360px 1fr; gap:16px; align-items:start;"
    >
        {{-- ── Section list ────────────────────────────────────────── --}}
        <aside style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:16px; box-shadow:0 1px 2px rgba(0,0,0,.04); position:sticky; top:16px;">
            {{-- Save / Discard bar --}}
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:14px; padding-bottom:14px; border-bottom:1px solid #f3f4f6;">
                <button
                    type="button"
                    x-bind:disabled="pendingCount === 0 || saving"
                    x-on:click="saveAll()"
                    x-bind:style="(pendingCount === 0 || saving)
                        ? 'background:#e5e7eb; color:#9ca3af; cursor:not-allowed; border:1px solid #e5e7eb;'
                        : 'background:#0284c7; color:#fff; cursor:pointer; border:1px solid #0284c7;'"
                    style="flex:1; display:inline-flex; align-items:center; justify-content:center; gap:6px;
                           font-size:13px; font-weight:600; border-radius:8px; padding:8px 12px; transition:background .12s;"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px; height:14px;" x-show="!saving">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="width:14px; height:14px; animation:spin 1s linear infinite;" x-show="saving" x-cloak>
                        <circle style="opacity:.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path style="opacity:.75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    <span x-text="saving ? 'Saving…' : (pendingCount === 0 ? 'Save' : 'Save ' + pendingCount + ' change' + (pendingCount === 1 ? '' : 's'))"></span>
                </button>
                <button
                    type="button"
                    x-bind:disabled="pendingCount === 0 || saving"
                    x-on:click="discardAll()"
                    x-bind:style="(pendingCount === 0 || saving)
                        ? 'background:#fff; color:#9ca3af; cursor:not-allowed; border:1px solid #e5e7eb;'
                        : 'background:#fff; color:#374151; cursor:pointer; border:1px solid #e5e7eb;'"
                    style="display:inline-flex; align-items:center; justify-content:center; gap:4px;
                           font-size:12px; font-weight:500; border-radius:8px; padding:8px 10px;"
                    title="Discard pending changes"
                >
                    Discard
                </button>
            </div>

            <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:8px; margin-bottom:12px;">
                <div>
                    <h2 style="font-size:14px; font-weight:600; color:#111827; margin:0 0 2px;">Sections</h2>
                    <p style="font-size:11px; color:#6b7280; margin:0;">Click text in the preview, or use <em>Edit</em> below.</p>
                </div>
                <button
                    type="button"
                    x-on:click="reloadPreview()"
                    style="display:inline-flex; align-items:center; gap:4px; font-size:11px; font-weight:500; color:#374151;
                           background:#fff; border:1px solid #e5e7eb; border-radius:6px; padding:4px 8px; cursor:pointer;"
                    title="Refresh preview (drops pending changes)"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px; height:14px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    Refresh
                </button>
            </div>

            <ul style="list-style:none; margin:0; padding:0; display:flex; flex-direction:column; gap:8px;">
                @foreach ($this->getSections() as $section)
                    <li style="display:flex; align-items:center; gap:12px; padding:10px 12px;
                               background:#f9fafb; border:1px solid #f3f4f6; border-radius:10px;">
                        <div style="flex:0 0 36px; height:36px; display:flex; align-items:center; justify-content:center;
                                    background:#fff; border:1px solid #e5e7eb; border-radius:8px; color:#6b7280;">
                            <x-filament::icon :icon="$section['icon']" style="width:16px; height:16px;" />
                        </div>
                        <div style="min-width:0; flex:1;">
                            <div style="font-size:13px; font-weight:500; color:#111827; line-height:1.2;">
                                {{ $section['label'] }}
                            </div>
                            <div style="font-size:11px; color:#6b7280; line-height:1.35; margin-top:2px;
                                        overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                {{ $section['desc'] }}
                            </div>
                        </div>
                        <div style="flex:0 0 auto;">
                            {{ $this->actionFor($section['key']) }}
                        </div>
                    </li>
                @endforeach
            </ul>

            <div style="margin-top:14px; padding:10px 12px; background:#eff6ff;
                        border:1px solid #bfdbfe; border-radius:8px;
                        font-size:11px; line-height:1.45; color:#1e40af;">
                <strong>Tip:</strong> click any text in the preview, edit it, then press <kbd style="background:#dbeafe; padding:0 4px; border-radius:3px;">Enter</kbd> or click outside. Changes are pending until you press <strong>Save</strong>. <kbd style="background:#dbeafe; padding:0 4px; border-radius:3px;">Esc</kbd> cancels the current edit.
            </div>
        </aside>

        {{-- ── Live preview ────────────────────────────────────────── --}}
        <section style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:8px; box-shadow:0 1px 2px rgba(0,0,0,.04);">
            <div style="display:flex; align-items:center; justify-content:space-between; padding:6px 8px 8px;">
                <div style="display:flex; align-items:center; gap:8px;">
                    <span x-bind:style="pendingCount > 0
                        ? 'display:inline-block; width:8px; height:8px; border-radius:9999px; background:#f59e0b;'
                        : 'display:inline-block; width:8px; height:8px; border-radius:9999px; background:#10b981;'"
                    ></span>
                    <span style="font-size:11px; font-weight:500; color:#374151;"
                          x-text="pendingCount > 0
                              ? pendingCount + ' unsaved change' + (pendingCount === 1 ? '' : 's')
                              : 'Live preview · click any text to edit'"></span>
                </div>
                <a
                    href="/"
                    target="_blank"
                    rel="noopener"
                    style="display:inline-flex; align-items:center; gap:4px; font-size:11px; font-weight:500;
                           color:#0284c7; text-decoration:none;"
                >
                    Open in new tab
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:12px; height:12px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                    </svg>
                </a>
            </div>
            <div style="height:calc(100vh - 220px); min-height:600px; overflow:hidden;
                        border:1px solid #e5e7eb; border-radius:8px; background:#fff;">
                <iframe
                    x-ref="preview"
                    src="/?themePreview=1"
                    x-on:load="onIframeLoad()"
                    style="width:100%; height:100%; border:0; display:block; background:#fff;"
                    title="Homepage preview"
                ></iframe>
            </div>
        </section>
    </div>

    <x-filament-actions::modals />

    <style>@keyframes spin { to { transform: rotate(360deg); } }</style>

    <script>
        function themeBuilder() {
            return {
                /** Map of dedupeKey → { kind, key, index, field, value, original, el }. */
                pending: {},
                pendingCount: 0,
                saving: false,

                init() {
                    this.$nextTick(() => this.onIframeLoad());
                },

                reloadPreview() {
                    if (this.pendingCount > 0 && !confirm('Refresh will drop your unsaved changes. Continue?')) return;
                    this.pending = {};
                    this.pendingCount = 0;
                    const iframe = this.$refs.preview;
                    if (!iframe) return;
                    iframe.src = '/?themePreview=1&t=' + Date.now();
                },

                onUnload(e) {
                    if (this.pendingCount > 0) {
                        e.preventDefault();
                        e.returnValue = '';
                    }
                },

                /**
                 * On iframe load, inject styles + a delegated click handler.
                 * The welcome page is rendered by React AFTER the iframe load
                 * event, so delegating on the document is the only reliable
                 * way to catch clicks regardless of when elements appear.
                 */
                onIframeLoad() {
                    const iframe = this.$refs.preview;
                    let doc;
                    try { doc = iframe.contentDocument; } catch (e) { return; }
                    if (!doc) return;

                    if (!doc.getElementById('tb-styles')) {
                        const style = doc.createElement('style');
                        style.id = 'tb-styles';
                        style.textContent = `
                            [data-tb], [data-tb-list] {
                                outline: 1px dashed transparent;
                                outline-offset: 3px;
                                transition: outline-color .12s ease, background-color .12s ease;
                                cursor: pointer !important;
                                border-radius: 3px;
                            }
                            [data-tb]:hover, [data-tb-list]:hover {
                                outline-color: #3b82f6;
                                background-color: rgba(59,130,246,.08);
                            }
                            [data-tb][contenteditable="true"], [data-tb-list][contenteditable="true"] {
                                outline: 2px solid #3b82f6 !important;
                                outline-offset: 2px;
                                background-color: rgba(59,130,246,.12) !important;
                                cursor: text !important;
                            }
                            [data-tb-pending="1"], [data-tb-list][data-tb-pending="1"] {
                                outline-color: #f59e0b !important;
                                background-color: rgba(245,158,11,.10) !important;
                            }
                            [data-tb] a, [data-tb-list] a { pointer-events: none; }
                        `;
                        doc.head.appendChild(style);
                    }

                    if (!doc.__tbDelegated) {
                        doc.__tbDelegated = true;
                        doc.addEventListener('click', (e) => {
                            const tbEl = e.target.closest('[data-tb], [data-tb-list]');
                            if (!tbEl) return;
                            e.preventDefault();
                            e.stopPropagation();
                            if (tbEl.isContentEditable) return;
                            this.beginEdit(tbEl);
                        }, true);
                    }
                },

                /** Returns the unique pending-edit key for an element. */
                dedupeKey(el) {
                    if (el.dataset.tb) return 'str:' + el.dataset.tb;
                    return 'lst:' + el.dataset.tbList + ':' + (el.dataset.tbI ?? '0') + ':' + (el.dataset.tbF ?? '');
                },

                beginEdit(el) {
                    const dk = this.dedupeKey(el);
                    // The "true" original is the one before any pending edit.
                    const original = (this.pending[dk]?.original) ?? el.textContent;
                    el.dataset.tbOriginal = original;
                    el.setAttribute('contenteditable', 'plaintext-only');
                    el.focus();

                    const doc = el.ownerDocument;
                    const range = doc.createRange();
                    range.selectNodeContents(el);
                    const sel = doc.defaultView.getSelection();
                    sel.removeAllRanges();
                    sel.addRange(range);

                    const finish = (commit) => {
                        el.removeEventListener('keydown', onKey);
                        el.removeEventListener('blur', onBlur);
                        el.removeAttribute('contenteditable');
                        const next = el.textContent;
                        if (commit) {
                            if (next !== original) {
                                this.markPending(el, original, next);
                            }
                            // unchanged: drop any prior pending entry for this key
                            else if (this.pending[dk]) {
                                this.unmarkPending(el);
                            }
                        } else {
                            // Cancelled — restore previous shown value (could be a pending value).
                            const shown = this.pending[dk] ? this.pending[dk].value : original;
                            el.textContent = shown;
                        }
                    };
                    const onKey = (ev) => {
                        if (ev.key === 'Enter') { ev.preventDefault(); finish(true); }
                        else if (ev.key === 'Escape') { ev.preventDefault(); finish(false); }
                    };
                    const onBlur = () => finish(true);
                    el.addEventListener('keydown', onKey);
                    el.addEventListener('blur', onBlur);
                },

                markPending(el, original, value) {
                    const dk = this.dedupeKey(el);
                    const isList = !!el.dataset.tbList;
                    this.pending[dk] = {
                        kind: isList ? 'list' : 'str',
                        key: isList ? el.dataset.tbList : el.dataset.tb,
                        index: isList ? parseInt(el.dataset.tbI ?? '0', 10) : null,
                        field: isList ? (el.dataset.tbF || null) : null,
                        value,
                        original,
                        el,
                    };
                    el.dataset.tbPending = '1';
                    this.pendingCount = Object.keys(this.pending).length;
                },

                unmarkPending(el) {
                    const dk = this.dedupeKey(el);
                    delete this.pending[dk];
                    el.removeAttribute('data-tb-pending');
                    this.pendingCount = Object.keys(this.pending).length;
                },

                async saveAll() {
                    if (this.pendingCount === 0 || this.saving) return;
                    this.saving = true;
                    const changes = Object.values(this.pending).map(p => ({
                        kind: p.kind,
                        key: p.key,
                        index: p.index,
                        field: p.field,
                        value: p.value,
                    }));
                    try {
                        await this.$wire.call('saveBatch', changes);
                        // Clear pending state on success.
                        Object.values(this.pending).forEach(p => {
                            if (p.el && p.el.isConnected) p.el.removeAttribute('data-tb-pending');
                        });
                        this.pending = {};
                        this.pendingCount = 0;
                    } catch (err) {
                        console.error('[ThemeBuilder] save failed', err);
                    } finally {
                        this.saving = false;
                    }
                },

                discardAll() {
                    if (this.pendingCount === 0) return;
                    if (!confirm('Discard ' + this.pendingCount + ' pending change' + (this.pendingCount === 1 ? '' : 's') + '?')) return;
                    Object.values(this.pending).forEach(p => {
                        if (p.el && p.el.isConnected) {
                            p.el.textContent = p.original;
                            p.el.removeAttribute('data-tb-pending');
                        }
                    });
                    this.pending = {};
                    this.pendingCount = 0;
                },
            };
        }
    </script>
</x-filament-panels::page>
