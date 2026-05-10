{{--
    Live preview pane for the Edit Text Block modal — inline div edition.

    No iframe. The pane mounts the actual DynamicTextBlock React
    component via window.tbMountTextPreview (registered by the
    text-block-preview Vite entry, loaded once on the Theme Builder
    page) and re-renders it whenever the editor changes anything in
    the modal.
--}}
{{-- wire:ignore on the OUTER pane: Livewire never re-morphs anything
     here on commits, so Alpine state, the React root, and the React-
     rendered DOM all survive across repeater adds, image uploads, and
     other server round-trips. --}}
<div
    class="tb-text-preview-pane"
    wire:ignore
    wire:key="tb-text-block-live-preview"
    x-data="{
        _debounceTimer: null,

        init() {
            console.debug('[tb-preview] init');
            this.render();

            // Re-render on every input/change inside the modal —
            // catches every Filament field type without per-field
            // wiring.
            const modal = this.$root.closest('.fi-modal-window')
                ?? this.$root.closest('[role=&quot;dialog&quot;]');
            if (modal) {
                ['input', 'change'].forEach((evt) => {
                    modal.addEventListener(evt, () => this.scheduleRender(), true);
                });
            }

            // Livewire commits cover server round-tripping field
            // changes (image uploads, repeater add/remove, etc.)
            // before any DOM event fires reliably.
            if (window.Livewire) {
                window.Livewire.hook('commit', ({ succeed }) => {
                    succeed(() => {
                        console.debug('[tb-preview] livewire commit succeeded → schedule render');
                        this.scheduleRender();
                    });
                });
            }
        },

        destroy() {
            if (window.tbUnmountTextPreview && this.$refs.target) {
                window.tbUnmountTextPreview(this.$refs.target);
            }
        },

        scheduleRender() {
            clearTimeout(this._debounceTimer);
            this._debounceTimer = setTimeout(() => this.render(), 120);
        },

        render() {
            if (! window.tbMountTextPreview) {
                console.warn('[tb-preview] tbMountTextPreview missing');
                return;
            }
            if (! this.$refs.target) {
                console.warn('[tb-preview] target ref missing');
                return;
            }
            const settings = this.readSettings();
            console.debug('[tb-preview] render', {
                blocks: Array.isArray(settings.blocks) ? settings.blocks.length : 0,
                variant: settings.variant,
                connected: this.$refs.target.isConnected,
            });
            window.tbMountTextPreview(this.$refs.target, settings);
        },

        // Strip the Livewire proxy so React gets a plain object.
        readSettings() {
            try {
                const stack = this.$wire.mountedActions ?? [];
                const top = stack[stack.length - 1] ?? {};
                const raw = (top && top.data && top.data.settings) ? top.data.settings : {};
                return JSON.parse(JSON.stringify(raw));
            } catch (e) {
                return {};
            }
        },
    }"
>
    <div class="tb-text-preview-toolbar">
        <span class="tb-text-preview-dot"></span>
        Live preview
    </div>
    <div class="tb-text-preview-stage">
        <div x-ref="target" class="tb-text-preview-target"></div>
    </div>
</div>
