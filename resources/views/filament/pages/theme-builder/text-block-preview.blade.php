{{--
    Live preview pane for the Edit Text Block modal.

    The iframe loads /admin/theme-builder/text-preview which mounts the
    actual DynamicTextBlock React component. We never reload the iframe;
    instead, the parent (this Alpine block) listens to every input/change
    event inside the modal and pushes the current `settings` over
    postMessage so the React component re-renders in place.
--}}
<div
    class="tb-text-preview-pane"
    x-data="textBlockPreview()"
    x-init="init()"
>
    <div class="tb-text-preview-toolbar">
        <span class="tb-text-preview-dot"></span>
        Live preview
    </div>
    <div class="tb-text-preview-stage">
        <iframe
            x-ref="frame"
            src="/admin/theme-builder/text-preview"
            title="Text block preview"
        ></iframe>
    </div>
</div>

<script>
    if (! window.themeBuilderTextBlockPreview) {
        window.themeBuilderTextBlockPreview = function () {
            return {
                ready: false,
                _debounceTimer: null,

                init() {
                    console.debug('[tb-preview-pane] init');

                    // Seed the iframe URL with the current settings so
                    // the first paint already shows the right preview.
                    // Subsequent updates flow through postMessage and
                    // never reload the iframe.
                    try {
                        const initial = this.readSettings();
                        const url = new URL('/admin/theme-builder/text-preview', window.location.origin);
                        url.searchParams.set('settings', JSON.stringify(initial));
                        if (this.$refs.frame) {
                            this.$refs.frame.src = url.pathname + url.search;
                        }
                        console.debug('[tb-preview-pane] iframe seeded', initial);
                    } catch (e) {
                        console.warn('[tb-preview-pane] seed failed', e);
                    }

                    // The iframe page posts 'tb-text-preview:ready' as
                    // soon as it mounts. We respond by pushing the
                    // current settings — covers the case where the
                    // iframe finishes loading after this handler runs.
                    window.addEventListener('message', (event) => {
                        if (event?.data?.type === 'tb-text-preview:ready') {
                            console.debug('[tb-preview-pane] iframe ready');
                            this.ready = true;
                            this.push();
                        }
                    });

                    // Listen for ANY form input/change inside the modal
                    // and debounce a push. This catches every Filament
                    // field type (text, color picker, toggle buttons,
                    // file upload, repeater, …) without us having to
                    // wire them up individually.
                    const modal = this.$root.closest('.fi-modal-window')
                        ?? this.$root.closest('[role="dialog"]');
                    if (modal) {
                        ['input', 'change'].forEach((evt) => {
                            modal.addEventListener(evt, () => this.schedulePush(), true);
                        });
                    }

                    // Belt-and-braces: also push on Livewire updates,
                    // since some field changes (toggle buttons, image
                    // uploads) round-trip through the server before the
                    // DOM input event fires reliably.
                    if (window.Livewire) {
                        window.Livewire.hook('commit', ({ succeed }) => {
                            succeed(() => this.schedulePush());
                        });
                    }
                },

                schedulePush() {
                    clearTimeout(this._debounceTimer);
                    this._debounceTimer = setTimeout(() => this.push(), 150);
                },

                push() {
                    if (! this.ready || ! this.$refs.frame?.contentWindow) return;
                    const settings = this.readSettings();
                    console.debug('[tb-preview-pane] push', settings);
                    this.$refs.frame.contentWindow.postMessage({
                        type: 'tb-text-preview:settings',
                        settings,
                    }, '*');
                },

                // Pull the latest settings out of the currently-mounted
                // Filament action data. The mountedActions stack grows
                // when Filament opens a modal action, so the live edit
                // form's data is at the last entry.
                readSettings() {
                    try {
                        const stack = this.$wire.mountedActions ?? [];
                        const top = stack[stack.length - 1] ?? {};
                        return (top.data && top.data.settings) ? top.data.settings : {};
                    } catch (e) {
                        return {};
                    }
                },
            };
        };
    }

    // Alpine wants the data function in scope at evaluation time.
    function textBlockPreview() { return window.themeBuilderTextBlockPreview(); }
</script>
