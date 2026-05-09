{{--
    Hero visual editor — opens at the top of the Edit Hero modal.
    Renders the hero with click-to-edit text, color swatches, an image
    drop zone, and an image-overlay slider for the bg_image variant.

    The form data path:
      Filament v5.5 mounts each open action with its form state at
      $wire.mountedActions[N].data — there's no separate
      mountedActionsData array (that's an older Filament shape and
      throws PublicPropertyNotFoundException here). N is the topmost
      action's index, derived on the fly from mountedActions.length so
      we don't go stale after a nested action gets pushed on top.
--}}

<style>
    /* Tooltip-style edit hint that fades in over each click-to-edit
       region on hover, telling the user the area is editable. */
    .tb-hero-pad        { position: relative; }
    .tb-hero-pad::after {
        content: attr(data-edit-hint);
        position: absolute;
        top: -10px; right: -8px;
        font-size: 10px; font-weight: 600;
        background: #0284c7; color: #ffffff;
        padding: 2px 6px; border-radius: 9999px;
        opacity: 0; transform: translateY(-4px) scale(0.94);
        transition: opacity 140ms ease, transform 140ms ease;
        pointer-events: none;
        white-space: nowrap;
    }
    .tb-hero-pad:hover::after,
    .tb-hero-pad:focus-within::after {
        opacity: 1; transform: translateY(0) scale(1);
    }
    .tb-hero-pad[contenteditable="true"]:hover {
        outline: 1.5px dashed rgba(2, 132, 199, 0.5);
        outline-offset: 4px;
        border-radius: 4px;
    }
    .tb-hero-pad[contenteditable="true"]:focus {
        outline: 2px solid #0284c7;
        outline-offset: 4px;
        border-radius: 4px;
    }

    /* Color swatch toolbar at the top of the editor. Each swatch is a
       round chip that wraps a native color input. The native picker is
       hidden (zero-size) but still receives clicks via the chip label. */
    .tb-hero-swatches {
        display: flex; align-items: center; gap: 12px;
        padding: 10px 14px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 10px 10px 0 0;
        border-bottom: 0;
        font-size: 11px; font-weight: 500; color: #475569;
    }
    .dark .tb-hero-swatches {
        background: rgba(255,255,255,0.04);
        border-color: rgba(255,255,255,0.08);
        color: #cbd5e1;
    }
    .tb-hero-swatch {
        position: relative; cursor: pointer;
        display: inline-flex; align-items: center; gap: 6px;
    }
    .tb-hero-swatch__chip {
        width: 24px; height: 24px; border-radius: 9999px;
        border: 2px solid #ffffff;
        box-shadow: 0 0 0 1px rgba(15, 23, 42, 0.18), 0 1px 2px rgba(15, 23, 42, 0.12);
        transition: transform 120ms ease;
    }
    .tb-hero-swatch:hover .tb-hero-swatch__chip { transform: scale(1.06); }
    .tb-hero-swatch__input {
        position: absolute; inset: 0;
        width: 24px; height: 24px;
        opacity: 0; cursor: pointer;
        padding: 0; border: 0;
    }

    /* Image drop zone — empty state shows a dashed area with an
       upload icon, populated state shows the image with a hover
       overlay that says "click to change". */
    .tb-hero-imgwrap {
        position: relative;
        cursor: pointer;
        border-radius: 10px;
        overflow: hidden;
    }
    .tb-hero-imgwrap--empty {
        border: 2px dashed rgba(2,132,199,0.4);
        background: rgba(2,132,199,0.05);
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        gap: 6px;
        color: #0284c7; font-size: 12px; font-weight: 500;
        min-height: 180px;
    }
    .tb-hero-imgwrap__overlay {
        position: absolute; inset: 0;
        background: rgba(15, 23, 42, 0.0);
        display: flex; align-items: center; justify-content: center;
        color: #ffffff; font-size: 12px; font-weight: 600;
        opacity: 0; transition: opacity 160ms ease, background-color 160ms ease;
        pointer-events: none;
    }
    .tb-hero-imgwrap:hover .tb-hero-imgwrap__overlay {
        opacity: 1; background: rgba(15, 23, 42, 0.55);
    }

    /* Variant chip toolbar — quick switch among the 5 hero variants
       without scrolling down to the Design fieldset. */
    .tb-hero-variants {
        display: flex; flex-wrap: wrap; gap: 6px;
        padding: 8px 14px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-top: 0;
        border-bottom: 0;
        font-size: 11px;
    }
    .dark .tb-hero-variants {
        background: rgba(255,255,255,0.04);
        border-color: rgba(255,255,255,0.08);
    }
    .tb-hero-variant-btn {
        padding: 4px 10px;
        border-radius: 9999px;
        border: 1px solid #e5e7eb;
        background: #ffffff;
        color: #475569;
        font-weight: 500;
        cursor: pointer;
        transition: all 140ms ease;
    }
    .dark .tb-hero-variant-btn {
        border-color: rgba(255,255,255,0.10);
        background: rgba(255,255,255,0.04);
        color: #cbd5e1;
    }
    .tb-hero-variant-btn:hover {
        border-color: #0284c7;
        color: #0284c7;
    }
    .tb-hero-variant-btn--active {
        background: #0284c7;
        border-color: #0284c7;
        color: #ffffff;
    }
    .dark .tb-hero-variant-btn--active {
        background: #0284c7;
        border-color: #0284c7;
        color: #ffffff;
    }

    /* The preview canvas itself — frames the hero so the editable
       area is visually distinct from Filament's surrounding form. */
    .tb-hero-canvas {
        border: 1px solid #e5e7eb;
        border-top: 0;
        border-radius: 0 0 10px 10px;
        overflow: hidden;
    }
    .dark .tb-hero-canvas { border-color: rgba(255,255,255,0.08); }
</style>

<div
    x-data="{
        // Form-state path resolver — works no matter how deep the
        // mounted-action stack is. The editSection modal is normally
        // index 0, but a follow-up modal could push a second entry.
        get idx() { return ($wire.mountedActions || []).length - 1; },
        get s() {
            const i = this.idx; if (i < 0) return {};
            const actions = $wire.mountedActions || [];
            return (actions[i] && actions[i].data && actions[i].data.settings) || {};
        },
        // Read with a default fallback so empty state still renders
        // sensible placeholder copy.
        get(key, fallback) {
            const v = this.s[key];
            return (v === null || v === undefined || v === '') ? fallback : v;
        },
        set(key, value) {
            const i = this.idx; if (i < 0) return;
            $wire.set(`mountedActions.${i}.data.settings.${key}`, value);
        },
        commitText(key, fallback, event) {
            // Trim leading/trailing newlines that contenteditable sometimes
            // injects, but preserve intentional internal line breaks the
            // user typed.
            const raw  = (event.target.innerText || '').replace(/^\n+|\n+$/g, '');
            const next = raw.length === 0 ? fallback : raw;
            this.set(key, next);
            // Reflect the cleaned-up value back so the rendered text
            // matches what we just persisted.
            event.target.innerText = next;
        },
        imageSrc(value) {
            if (!value) return '';
            if (typeof value !== 'string') return '';
            if (value.startsWith('http')) return value;
            return '/storage/' + value;
        },
        // Forward an image click in the preview to Filament's underlying
        // FileUpload field so the native picker opens — much cheaper than
        // re-implementing upload/preview state here. Scope to the open
        // modal so we don't accidentally fire a different page's input.
        triggerImageUpload() {
            const modal = document.querySelector('.fi-modal-window') || document;
            const input = modal.querySelector('input[type=file]');
            if (input) input.click();
            else modal.querySelector('.fi-section')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        },
        // Single source of truth for the variant chips at the top.
        setVariant(v) { this.set('variant', v); },
        get variant() { return this.get('variant', 'centered'); },
    }"
    style="font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;"
>
    {{-- ── Color swatch toolbar ─────────────────────────────────── --}}
    <div class="tb-hero-swatches">
        <span style="margin-right: 4px;">Colors:</span>
        @foreach ([
            ['key' => 'bg',       'label' => 'Background', 'default' => '#ffffff'],
            ['key' => 'fg',       'label' => 'Text',       'default' => '#000000'],
            ['key' => 'buttonBg', 'label' => 'Button',     'default' => '#000000'],
            ['key' => 'buttonFg', 'label' => 'Button text','default' => '#ffffff'],
        ] as $sw)
            <label class="tb-hero-swatch" title="{{ $sw['label'] }}">
                <span
                    class="tb-hero-swatch__chip"
                    x-bind:style="`background: ${get('{{ $sw['key'] }}', '{{ $sw['default'] }}')};`"
                ></span>
                <span>{{ $sw['label'] }}</span>
                <input
                    class="tb-hero-swatch__input"
                    type="color"
                    x-bind:value="get('{{ $sw['key'] }}', '{{ $sw['default'] }}')"
                    x-on:input.debounce.150ms="set('{{ $sw['key'] }}', $event.target.value)"
                />
            </label>
        @endforeach
    </div>

    {{-- ── Variant chips ────────────────────────────────────────── --}}
    <div class="tb-hero-variants">
        <span style="margin-right: 4px; align-self: center; color:#94a3b8;">Layout:</span>
        @foreach ([
            'centered'   => 'Centered',
            'split'      => 'Split image',
            'bg_image'   => 'Background image',
            'minimal'    => 'Minimal',
            'with_stats' => 'With stats',
        ] as $key => $label)
            <button
                type="button"
                class="tb-hero-variant-btn"
                x-bind:class="variant === '{{ $key }}' ? 'tb-hero-variant-btn--active' : ''"
                x-on:click="setVariant('{{ $key }}')"
            >{{ $label }}</button>
        @endforeach
    </div>

    {{-- ── Preview canvas ───────────────────────────────────────── --}}
    <div
        class="tb-hero-canvas"
        x-bind:style="`background: ${get('bg', '#ffffff')}; color: ${get('fg', '#000000')};`"
    >
        {{-- Layout-agnostic: we always render the centered structure
             below. Variant-specific styling differences (split image,
             full-bleed bg_image, etc.) live in the live iframe preview
             at the right of the Theme Builder; the editor's job here
             is to give a faithful sense of copy + colors, not pixel
             parity across all 5 variants. --}}
        <div style="padding: clamp(40px, 6vw, 72px) clamp(20px, 4vw, 56px); text-align: center; min-height: 240px;
                    display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 14px;">

            {{-- Eyebrow --}}
            <span
                class="tb-hero-pad"
                contenteditable="plaintext-only"
                spellcheck="false"
                data-edit-hint="Eyebrow"
                x-init="$el.innerText = get('eyebrow', 'Welcome')"
                x-on:blur="commitText('eyebrow', 'Welcome', $event)"
                style="font-size: 13px; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; opacity: 0.78; min-width: 60px; outline: none;"
            ></span>

            {{-- Headline --}}
            <h1
                class="tb-hero-pad"
                contenteditable="plaintext-only"
                spellcheck="false"
                data-edit-hint="Headline"
                x-init="$el.innerText = get('headline', 'A great big headline goes here')"
                x-on:blur="commitText('headline', 'A great big headline goes here', $event)"
                style="margin: 0; font-size: clamp(1.75rem, 4vw, 2.75rem); font-weight: 700; letter-spacing: -0.02em; line-height: 1.1; min-width: 200px; outline: none;"
            ></h1>

            {{-- Subtitle --}}
            <p
                class="tb-hero-pad"
                contenteditable="plaintext-only"
                spellcheck="false"
                data-edit-hint="Subtitle"
                x-init="$el.innerText = get('subtitle', 'A short paragraph that explains what your product does and why people should care.')"
                x-on:blur="commitText('subtitle', 'A short paragraph that explains what your product does and why people should care.', $event)"
                style="margin: 0; font-size: clamp(0.9375rem, 1.4vw, 1.0625rem); line-height: 1.55; max-width: 56ch; opacity: 0.85; min-width: 200px; outline: none;"
            ></p>

            {{-- CTA button --}}
            <span style="margin-top: 8px;">
                <span
                    class="tb-hero-pad"
                    contenteditable="plaintext-only"
                    spellcheck="false"
                    data-edit-hint="Button"
                    x-init="$el.innerText = get('buttonText', 'Get started')"
                    x-on:blur="commitText('buttonText', 'Get started', $event)"
                    x-bind:style="`background: ${get('buttonBg', '#000000')}; color: ${get('buttonFg', '#ffffff')}; padding: 10px 20px; border-radius: 8px; font-weight: 600; font-size: 14px; display: inline-block; outline: none;`"
                ></span>
            </span>

            {{-- Image hint when an image-using variant is selected --}}
            <template x-if="['split', 'bg_image'].includes(variant)">
                <div
                    class="tb-hero-imgwrap"
                    x-bind:class="get('image', '') ? '' : 'tb-hero-imgwrap--empty'"
                    style="margin-top: 18px; width: 100%; max-width: 520px;"
                    x-on:click="triggerImageUpload()"
                >
                    <template x-if="get('image', '')">
                        <img
                            x-bind:src="imageSrc(get('image', ''))"
                            alt=""
                            style="width: 100%; height: auto; display: block; object-fit: cover; aspect-ratio: 16/9;"
                        />
                    </template>
                    <template x-if="!get('image', '')">
                        <div style="display: contents;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 28px; height: 28px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                            </svg>
                            <span>Click to upload an image</span>
                        </div>
                    </template>
                    <div class="tb-hero-imgwrap__overlay">Click to change image</div>
                </div>
            </template>
        </div>
    </div>

    <p style="font-size: 11px; color: #94a3b8; margin: 8px 4px 0; line-height: 1.45;">
        <strong style="color: #475569;">Tip:</strong>
        Click any text above to edit it inline. Use the swatches at the top to change colors.
        For finer control (alignment, hero stats, the CTA URL, fine-tuned image upload), expand the sections below.
    </p>
</div>
