{{--
    Live preview pane for the Edit Text Block modal — plain HTML edition.

    No iframe, no React, no separate route. Alpine reads the current
    form data straight off $wire and builds the preview HTML string
    by mirroring the production DynamicTextBlock renderer's output,
    then sets innerHTML on the target div.
--}}
<div
    class="tb-text-preview-pane"
    wire:ignore
    wire:key="tb-text-block-live-preview"
    x-data="{
        _t: null,

        init() {
            this.render();
            const modal = this.$root.closest('.fi-modal-window')
                ?? this.$root.closest('[role=&quot;dialog&quot;]');
            if (modal) {
                ['input', 'change'].forEach((evt) => {
                    modal.addEventListener(evt, () => this.scheduleRender(), true);
                });
            }
            if (window.Livewire) {
                window.Livewire.hook('commit', ({ succeed }) => {
                    succeed(() => this.scheduleRender());
                });
            }
        },

        scheduleRender() {
            clearTimeout(this._t);
            this._t = setTimeout(() => this.render(), 100);
        },

        render() {
            if (! this.$refs.target) return;
            const settings = this.readSettings();
            this.$refs.target.innerHTML = this.buildHtml(settings);
        },

        // Server-known settings (synced on Livewire commits — stale
        // for any field bound with wire:model.blur, which is Filament's
        // default for text inputs). Used as the baseline.
        readWireSettings() {
            try {
                const stack = this.$wire.mountedActions ?? [];
                const top = stack[stack.length - 1] ?? {};
                const raw = (top && top.data && top.data.settings) ? top.data.settings : {};
                return JSON.parse(JSON.stringify(raw));
            } catch (e) {
                return {};
            }
        },

        // Live form state — overlays the wire baseline with current
        // DOM input values so typing into a textarea reflects in the
        // preview without waiting for blur/server commit.
        readSettings() {
            const settings = this.readWireSettings();
            const modal = this.$root.closest('.fi-modal-window')
                ?? this.$root.closest('[role=&quot;dialog&quot;]');
            if (! modal) return settings;

            const setAt = (obj, pathParts, value) => {
                let cursor = obj;
                for (let i = 0; i < pathParts.length - 1; i++) {
                    const part = pathParts[i];
                    const idx = /^\d+$/.test(part) ? parseInt(part, 10) : null;
                    const key = idx !== null ? idx : part;
                    if (cursor[key] === undefined || cursor[key] === null) {
                        cursor[key] = idx !== null ? [] : {};
                    }
                    cursor = cursor[key];
                }
                const last = pathParts[pathParts.length - 1];
                const idx = /^\d+$/.test(last) ? parseInt(last, 10) : null;
                cursor[idx !== null ? idx : last] = value;
            };

            // Iterate every form input inside the modal that has any
            // flavour of wire:model attribute. Pull its current DOM
            // value and write it into our settings object at the path
            // after 'settings.'.
            const candidates = modal.querySelectorAll('input, textarea, select');
            candidates.forEach((el) => {
                const attrs = el.getAttributeNames();
                let path = null;
                for (const a of attrs) {
                    if (a === 'wire:model' || a.startsWith('wire:model.')) {
                        path = el.getAttribute(a);
                        break;
                    }
                }
                if (! path) return;
                const m = path.match(/^.*?settings\.(.+)$/);
                if (! m) return;
                const parts = m[1].split('.');

                let value;
                if (el.type === 'checkbox') value = el.checked;
                else if (el.type === 'radio') {
                    if (! el.checked) return;
                    value = el.value;
                } else if (el.type === 'number' || el.type === 'range') {
                    value = el.value === '' ? null : Number(el.value);
                } else {
                    value = el.value;
                }
                setAt(settings, parts, value);
            });

            return settings;
        },

        // ── Renderer ─────────────────────────────────────────────
        // Mirrors the React DynamicTextBlock component just enough to
        // give the editor a representative live preview.

        esc(s) {
            return String(s ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
        },

        fontStack(font) {
            const map = {
                system:  'system-ui, -apple-system, &quot;Segoe UI&quot;, Roboto, sans-serif',
                inter:   '&quot;Inter&quot;, system-ui, -apple-system, sans-serif',
                display: '&quot;Calistoga&quot;, Georgia, serif',
                serif:   'Georgia, &quot;Times New Roman&quot;, Cambria, serif',
                mono:    '&quot;JetBrains Mono&quot;, ui-monospace, SFMono-Regular, Menlo, monospace',
            };
            return map[font] || map.system;
        },

        luminance(hex) {
            const clean = String(hex || '').replace('#', '');
            const expand = clean.length === 3
                ? clean.split('').map((c) => c + c).join('')
                : clean;
            if (expand.length !== 6) return 0.5;
            const r = parseInt(expand.slice(0, 2), 16) / 255;
            const g = parseInt(expand.slice(2, 4), 16) / 255;
            const b = parseInt(expand.slice(4, 6), 16) / 255;
            const ch = (c) => (c <= 0.03928 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4));
            return 0.2126 * ch(r) + 0.7152 * ch(g) + 0.0722 * ch(b);
        },

        headingHtml(level, text, color) {
            const sizes = {
                h1: 'clamp(2.5rem, 6vw, 5rem); font-weight:900; letter-spacing:-0.05em; line-height:1; margin:0 0 1.5rem',
                h2: 'clamp(1.75rem, 4vw, 3rem); font-weight:800; letter-spacing:-0.025em; line-height:1.15; margin:0 0 1.25rem',
                h3: 'clamp(1.5rem, 3vw, 2rem); font-weight:700; letter-spacing:-0.02em; line-height:1.2; margin:0 0 1rem',
                h4: 'clamp(1.25rem, 2.4vw, 1.5rem); font-weight:600; letter-spacing:-0.015em; line-height:1.3; margin:0 0 0.75rem',
                h5: 'clamp(1.125rem, 1.8vw, 1.25rem); font-weight:600; line-height:1.35; margin:0 0 0.5rem',
                h6: 'clamp(1rem, 1.5vw, 1.125rem); font-weight:600; line-height:1.4; margin:0 0 0.5rem',
            };
            const tag = ['h1','h2','h3','h4','h5','h6'].includes(level) ? level : 'h2';
            const colorRule = color ? `color:${color};` : '';
            return `<${tag} style=&quot;font-size:${sizes[tag]}; ${colorRule}&quot;>${this.esc(text)}</${tag}>`;
        },

        paragraphHtml(text, color) {
            const colorRule = color ? `color:${color};` : '';
            return `<p style=&quot;font-size:clamp(1rem, 1.2vw, 1.0625rem); line-height:1.7; margin:0 0 1rem; white-space:pre-wrap; ${colorRule}&quot;>${this.esc(text)}</p>`;
        },

        blockHtml(b, headingColor, bodyColor) {
            const text = (b && b.text) || '';
            if (! text.trim()) return '';
            if (b.type === 'heading') return this.headingHtml(b.level || 'h2', text, headingColor);
            return this.paragraphHtml(text, bodyColor);
        },

        sectionStyle(s) {
            const bgColor = s.bg || '#ffffff';
            const fontFamily = this.fontStack(s.font || 'system');
            let color = s.fg || '#0f172a';
            let textShadow = '';
            let bgImage = '';
            let bgExtras = '';

            const bgImagePath = (typeof s.bgImage === 'string' ? s.bgImage.trim() : '');
            const hasImage = bgImagePath.length > 0;
            const hasGradient = !!s.bgGradient && !!s.bgGradientTo;

            if (hasImage) {
                bgImage = `background-image:url('/storage/${bgImagePath}'); background-size:cover; background-position:center; background-repeat:no-repeat;`;
                bgExtras = 'min-height:clamp(280px, 45vw, 520px);';
                textShadow = '0 1px 3px rgba(0,0,0,0.35)';
            } else if (hasGradient) {
                const dir = s.bgGradientDir || 'to right';
                bgImage = `background-image:linear-gradient(${dir}, ${bgColor}, ${s.bgGradientTo});`;
                const avg = (this.luminance(bgColor) + this.luminance(s.bgGradientTo)) / 2;
                if (avg < 0.55) { color = '#ffffff'; textShadow = '0 1px 3px rgba(0,0,0,0.35)'; }
                else { color = '#0f172a'; textShadow = '0 1px 2px rgba(255,255,255,0.55)'; }
            }

            return `background-color:${bgColor}; ${bgImage} ${bgExtras} color:${color};`
                + `padding:clamp(40px,7vw,80px) clamp(16px,4vw,32px); font-family:${fontFamily};`
                + (textShadow ? `text-shadow:${textShadow};` : '');
        },

        // ── Variants ─────────────────────────────────────────────

        renderDefault(s) {
            const align = s.align || 'left';
            const blocks = (s.blocks || []).filter((b) => ((b && b.text) || '').trim() !== '');
            let inner = '';
            if (blocks.length > 0) {
                inner = blocks.map((b) => this.blockHtml(b)).join('');
            } else {
                if (s.heading) inner += this.headingHtml('h2', s.heading);
                if (s.body) inner += this.paragraphHtml(s.body);
            }
            return `<section style=&quot;${this.sectionStyle(s)}&quot;>`
                + `<div style=&quot;max-width:760px; margin:0 auto; text-align:${align};&quot;>${inner}</div>`
                + `</section>`;
        },

        renderTwoColumn(s) {
            const blocks = (s.blocks || []).filter((b) => ((b && b.text) || '').trim() !== '');
            let leftBlocks = [], rightBlocks = [];
            if (blocks.length) {
                const idx = blocks.findIndex((b) => b.type === 'heading');
                if (idx >= 0) { leftBlocks = [blocks[idx]]; rightBlocks = blocks.filter((_, i) => i !== idx); }
                else { rightBlocks = blocks; }
            }
            const left = leftBlocks.length ? leftBlocks.map((b) => this.blockHtml(b)).join('')
                : (s.heading ? this.headingHtml('h2', s.heading) : '');
            const right = rightBlocks.length ? rightBlocks.map((b) => this.blockHtml(b)).join('')
                : (s.body ? this.paragraphHtml(s.body) : '');
            return `<section style=&quot;${this.sectionStyle(s)}&quot;>`
                + `<div class=&quot;tb-twocol&quot; style=&quot;max-width:1100px; margin:0 auto; display:grid; grid-template-columns:1fr 1.4fr; gap:clamp(24px,5vw,64px); align-items:start;&quot;>`
                + `<div>${left}</div><div>${right}</div></div></section>`;
        },

        renderCallout(s) {
            const presets = {
                info:    { bg: '#eff6ff', border: '#2563eb', fg: '#1e3a8a', headingFg: '#1e40af' },
                success: { bg: '#f0fdf4', border: '#16a34a', fg: '#14532d', headingFg: '#166534' },
                warning: { bg: '#fffbeb', border: '#d97706', fg: '#78350f', headingFg: '#92400e' },
                danger:  { bg: '#fef2f2', border: '#dc2626', fg: '#7f1d1d', headingFg: '#991b1b' },
                note:    { bg: '#f5f3ff', border: '#7c3aed', fg: '#4c1d95', headingFg: '#5b21b6' },
                neutral: { bg: '#f8fafc', border: '#475569', fg: '#1e293b', headingFg: '#0f172a' },
            };
            const key = s.calloutColor || 'info';
            const preset = presets[key];
            const calloutBg = preset ? preset.bg : 'rgba(2,132,199,0.08)';
            const calloutBorder = preset ? preset.border : '#0284c7';
            const calloutBody = preset ? preset.fg : '#0f172a';
            const calloutHead = preset ? preset.headingFg : '#0f172a';
            const align = s.align || 'left';

            const blocks = (s.blocks || []).filter((b) => ((b && b.text) || '').trim() !== '');
            const calloutImagePath = typeof s.bgImage === 'string' ? s.bgImage.trim() : '';
            let boxBg = `background:${calloutBg};`;
            let extras = '';
            if (calloutImagePath && s.bgImageOnCallout !== false) {
                const tint = `linear-gradient(${calloutBg}, ${calloutBg})`;
                boxBg = `background:${tint}, url('/storage/${calloutImagePath}') center/cover no-repeat;`;
                extras = 'min-height:clamp(220px,35vw,380px);';
            }

            const inner = blocks.length
                ? blocks.map((b) => this.blockHtml(b, calloutHead, calloutBody)).join('')
                : (s.heading ? this.headingHtml('h2', s.heading, calloutHead) : '')
                  + (s.body ? this.paragraphHtml(s.body, calloutBody) : '');

            return `<section style=&quot;${this.sectionStyle(s)}&quot;>`
                + `<div style=&quot;max-width:900px; margin:0 auto; ${boxBg} ${extras}`
                + ` border:1px solid ${calloutBorder}33; border-left:4px solid ${calloutBorder};`
                + ` border-radius:12px; padding:clamp(20px,3vw,28px) clamp(20px,3vw,32px); text-align:${align};`
                + ` color:${calloutBody}; overflow:hidden;&quot;>${inner}</div></section>`;
        },

        renderQuote(s) {
            const align = s.align || 'center';
            const paras = (s.blocks || []).filter((b) => b && b.type !== 'heading' && (b.text || '').trim() !== '').map((b) => b.text.trim());
            const quote = paras.length ? paras.join('\n\n') : (s.body || '');
            const credit = s.attribution || s.heading || '';
            const quoteLeft = align === 'center' ? '50%' : '0';
            const quoteTransform = align === 'center' ? 'translateX(-50%)' : 'none';
            return `<section style=&quot;${this.sectionStyle(s)}&quot;>`
                + `<figure style=&quot;max-width:800px; margin:0 auto; text-align:${align}; position:relative;&quot;>`
                + `<span aria-hidden=&quot;true&quot; style=&quot;position:absolute; top:-0.25em; left:${quoteLeft}; transform:${quoteTransform};`
                + ` font-size:clamp(4rem,9vw,7rem); line-height:1; font-family:Georgia,serif; color:#0284c7; opacity:0.4;&quot;>&ldquo;</span>`
                + `<blockquote style=&quot;margin:0; padding-top:clamp(28px,4vw,48px); font-size:clamp(1.125rem,2vw,1.5rem); font-style:italic;`
                + ` line-height:1.55; font-weight:500; letter-spacing:-0.005em; white-space:pre-wrap;&quot;>${this.esc(quote)}</blockquote>`
                + (credit ? `<figcaption style=&quot;margin-top:18px; font-size:0.9375rem; opacity:0.7; font-weight:600; letter-spacing:0.01em;&quot;>${this.esc(credit)}</figcaption>` : '')
                + `</figure></section>`;
        },

        buildHtml(s) {
            switch (s.variant) {
                case 'two_column': return this.renderTwoColumn(s);
                case 'callout':    return this.renderCallout(s);
                case 'quote':      return this.renderQuote(s);
                case 'default':
                default:           return this.renderDefault(s);
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
