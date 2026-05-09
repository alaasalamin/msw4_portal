@php
    use App\Filament\Pages\ThemeBuilder\SectionRegistry;

    // The chooseDesign action mounts with arguments[id] (the section id).
    // Pull them out of the topmost mounted action so the picker knows
    // which section's variants to render and which variant is currently
    // applied (highlighted as active).
    $args      = $this->mountedActionsArguments[count($this->mountedActions ?? []) - 1] ?? [];
    $sectionId = $args['id'] ?? null;
    $section   = $sectionId ? $this->findSection($sectionId) : null;

    $type     = $section['type'] ?? null;
    $variants = $type ? SectionRegistry::variants($type) : [];
    $current  = $section['settings']['variant'] ?? ($variants[0]['key'] ?? null);
@endphp

@if (! $section)
    <p style="text-align:center; color: var(--color-gray-500, #6b7280);">
        Section not found.
    </p>
@else
<style>
    .tb-variant-picker {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.75rem;
        overflow: visible;
    }
    @media (min-width: 640px) {
        .tb-variant-picker { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }

    .tb-variant-picker__card {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: 0.5rem;
        text-align: left;
        padding: 0.75rem;
        border-radius: 0.75rem;
        border: 1px solid var(--color-gray-200, #e5e7eb);
        background: #ffffff;
        cursor: pointer;
        transition: border-color 150ms ease, background-color 150ms ease, transform 150ms ease;
        font-family: inherit;
    }
    .tb-variant-picker__card:hover {
        border-color: var(--color-primary-500, #0284c7);
        background: var(--color-primary-50, #f0f9ff);
        transform: translateY(-1px);
        z-index: 10;
    }
    .tb-variant-picker__card:focus-visible {
        outline: 2px solid var(--color-primary-500, #0284c7);
        outline-offset: 2px;
    }
    .tb-variant-picker__card[disabled] { opacity: 0.6; cursor: progress; }

    .tb-variant-picker__card--active {
        border-color: var(--color-primary-500, #0284c7);
        background: var(--color-primary-50, #f0f9ff);
        box-shadow: 0 0 0 2px var(--color-primary-500, #0284c7);
    }
    .tb-variant-picker__active-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        font-size: 0.625rem;
        line-height: 1;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        padding: 4px 8px;
        border-radius: 9999px;
        background: var(--color-primary-600, #0284c7);
        color: #ffffff;
        z-index: 2;
    }

    .tb-variant-picker__thumb {
        width: 100%;
        aspect-ratio: 240 / 140;
        background: #f8fafc;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid var(--color-gray-100, #f3f4f6);
    }

    .tb-variant-picker__label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--color-gray-950, #030712);
        line-height: 1.25;
    }
    .tb-variant-picker__desc {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin-top: 0.125rem;
        font-size: 0.75rem;
        line-height: 1.35;
        color: var(--color-gray-500, #6b7280);
    }

    /* Hover thinking-bubble that pops a larger version of the preview. */
    .tb-variant-picker__bubble {
        position: absolute;
        left: 50%;
        bottom: calc(100% + 14px);
        transform: translateX(-50%) translateY(6px) scale(0.96);
        width: 320px;
        padding: 12px;
        background: #ffffff;
        border: 1px solid var(--color-gray-200, #e5e7eb);
        border-radius: 12px;
        box-shadow: 0 16px 40px -8px rgba(15, 23, 42, 0.18), 0 4px 10px rgba(15, 23, 42, 0.06);
        opacity: 0;
        pointer-events: none;
        transition: opacity 160ms ease, transform 160ms ease;
        z-index: 50;
    }
    .tb-variant-picker__bubble::before,
    .tb-variant-picker__bubble::after {
        content: '';
        position: absolute;
        background: #ffffff;
        border: 1px solid var(--color-gray-200, #e5e7eb);
        border-radius: 50%;
    }
    .tb-variant-picker__bubble::before {
        width: 10px; height: 10px;
        bottom: -7px; left: 50%;
        transform: translateX(-50%);
    }
    .tb-variant-picker__bubble::after {
        width: 6px; height: 6px;
        bottom: -16px; left: 50%;
        transform: translateX(-180%);
    }
    .tb-variant-picker__bubble-svg {
        width: 100%;
        aspect-ratio: 240 / 140;
        background: #f8fafc;
        border-radius: 8px;
        overflow: hidden;
    }
    .tb-variant-picker__card:hover .tb-variant-picker__bubble,
    .tb-variant-picker__card:focus-visible .tb-variant-picker__bubble {
        opacity: 1;
        transform: translateX(-50%) translateY(0) scale(1);
    }
    @media (min-width: 640px) {
        .tb-variant-picker__card:nth-child(-n+3) .tb-variant-picker__bubble {
            bottom: auto;
            top: calc(100% + 14px);
            transform: translateX(-50%) translateY(-6px) scale(0.96);
        }
        .tb-variant-picker__card:nth-child(-n+3) .tb-variant-picker__bubble::before { bottom: auto; top: -7px; }
        .tb-variant-picker__card:nth-child(-n+3) .tb-variant-picker__bubble::after  { bottom: auto; top: -16px; }
        .tb-variant-picker__card:nth-child(-n+3):hover .tb-variant-picker__bubble,
        .tb-variant-picker__card:nth-child(-n+3):focus-visible .tb-variant-picker__bubble {
            transform: translateX(-50%) translateY(0) scale(1);
        }
    }
    @media (max-width: 639px) {
        .tb-variant-picker__bubble { display: none; }
    }

    /* Dark mode */
    .dark .tb-variant-picker__card {
        border-color: rgba(255, 255, 255, 0.1);
        background: rgba(255, 255, 255, 0.03);
    }
    .dark .tb-variant-picker__card:hover {
        border-color: var(--color-primary-400, #38bdf8);
        background: rgba(2, 132, 199, 0.10);
    }
    .dark .tb-variant-picker__card--active {
        border-color: var(--color-primary-400, #38bdf8);
        background: rgba(2, 132, 199, 0.18);
        box-shadow: 0 0 0 2px var(--color-primary-400, #38bdf8);
    }
    .dark .tb-variant-picker__label { color: #ffffff; }
    .dark .tb-variant-picker__desc  { color: var(--color-gray-400, #9ca3af); }
    .dark .tb-variant-picker__thumb { background: rgba(255, 255, 255, 0.04); border-color: rgba(255, 255, 255, 0.06); }
    .dark .tb-variant-picker__bubble {
        background: #0f172a;
        border-color: rgba(255, 255, 255, 0.10);
        box-shadow: 0 16px 40px -8px rgba(0,0,0,0.5), 0 4px 10px rgba(0,0,0,0.3);
    }
    .dark .tb-variant-picker__bubble::before,
    .dark .tb-variant-picker__bubble::after {
        background: #0f172a; border-color: rgba(255, 255, 255, 0.10);
    }
    .dark .tb-variant-picker__bubble-svg { background: rgba(255,255,255,0.04); }
</style>

<div class="tb-variant-picker">
    @foreach ($variants as $v)
        @php $isActive = $v['key'] === $current; @endphp
        <button
            type="button"
            wire:click="applyDesignVariant(@js($section['id']), @js($v['key']))"
            wire:loading.attr="disabled"
            class="tb-variant-picker__card{{ $isActive ? ' tb-variant-picker__card--active' : '' }}"
        >
            @if ($isActive)
                <span class="tb-variant-picker__active-badge">Active</span>
            @endif

            {{-- Always-visible thumbnail of the variant. The hover bubble
                 below shows a larger version with a soft drop shadow. --}}
            <span class="tb-variant-picker__thumb">
                @include('filament.pages.theme-builder.variant-preview', ['type' => $type, 'variant' => $v['key']])
            </span>

            <span style="display:block; padding: 4px 4px 2px;">
                <span class="tb-variant-picker__label">{{ $v['label'] }}</span>
                <span class="tb-variant-picker__desc">{{ $v['description'] }}</span>
            </span>

            <span class="tb-variant-picker__bubble" aria-hidden="true">
                <span class="tb-variant-picker__bubble-svg">
                    @include('filament.pages.theme-builder.variant-preview', ['type' => $type, 'variant' => $v['key']])
                </span>
            </span>
        </button>
    @endforeach
</div>
@endif
