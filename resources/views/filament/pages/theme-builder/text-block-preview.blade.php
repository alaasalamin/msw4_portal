{{--
    Live preview pane for the Edit Text Block modal — inline div edition.

    No iframe. The pane mounts the actual DynamicTextBlock React
    component via window.tbMountTextPreview (registered by the
    text-block-preview Vite entry, loaded once on the Theme Builder
    page) and re-renders it whenever the editor changes anything in
    the modal.
--}}
<div
    class="tb-text-preview-pane"
    x-data="{
        _debounceTimer: null,

        init() {
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
            // changes (image uploads, etc.) before any DOM event
            // fires reliably.
            if (window.Livewire) {
                window.Livewire.hook('commit', ({ succeed }) => {
                    succeed(() => this.scheduleRender());
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
            if (! window.tbMountTextPreview || ! this.$refs.target) return;
            window.tbMountTextPreview(this.$refs.target, this.readSettings());
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
    {{-- wire:ignore tells Livewire to leave the React mount point
         alone during partial re-renders, otherwise it morphs away
         the rendered output and leaves React holding a stale Root. --}}
    <div class="tb-text-preview-stage" wire:ignore>
        <div x-ref="target" class="tb-text-preview-target"></div>
    </div>
</div>
