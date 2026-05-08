@php
    use App\Filament\Pages\ThemeBuilder\SectionRegistry;

    $types = SectionRegistry::types();
    if ($this->currentPageId !== null) {
        // Header and Footer are homepage-only; site pages inherit them.
        unset($types['header'], $types['footer']);
    }

    $entries = array_map(
        fn ($key, $meta) => ['key' => $key] + $meta,
        array_keys($types),
        array_values($types),
    );
@endphp

{{-- Scoped styles. The admin shell does not pull in the project's Tailwind
     build (no Filament custom theme is configured), so we ship the picker
     styling inline and key everything off Filament's existing CSS variables
     (--color-primary-*, --color-gray-*) so it tracks the active brand /
     dark-mode state. --}}
<style>
    .tb-section-picker {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.75rem;
        /* Allow the hover preview bubbles to overflow the picker without
           getting clipped by an ancestor's border-radius / overflow rules. */
        overflow: visible;
    }
    @media (min-width: 640px) {
        .tb-section-picker { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }

    .tb-section-picker__card {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
        text-align: left;
        padding: 1rem;
        border-radius: 0.75rem;
        border: 1px solid var(--color-gray-200, #e5e7eb);
        background: #ffffff;
        cursor: pointer;
        transition: border-color 150ms ease, background-color 150ms ease, transform 150ms ease;
        font-family: inherit;
    }
    .tb-section-picker__card:hover {
        border-color: var(--color-primary-500, #0284c7);
        background: var(--color-primary-50, #f0f9ff);
        transform: translateY(-1px);
        z-index: 10;
    }
    .tb-section-picker__card:focus-visible {
        outline: 2px solid var(--color-primary-500, #0284c7);
        outline-offset: 2px;
    }
    .tb-section-picker__card[disabled] {
        opacity: 0.6;
        cursor: progress;
    }

    .tb-section-picker__chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.75rem;
        height: 2.75rem;
        border-radius: 0.625rem;
        background: var(--color-primary-50, #f0f9ff);
        color: var(--color-primary-600, #0284c7);
        transition: background-color 150ms ease;
    }
    .tb-section-picker__card:hover .tb-section-picker__chip {
        background: var(--color-primary-100, #e0f2fe);
    }
    .tb-section-picker__chip svg { width: 1.5rem; height: 1.5rem; }

    .tb-section-picker__label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--color-gray-950, #030712);
        line-height: 1.25;
    }
    .tb-section-picker__desc {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin-top: 0.125rem;
        font-size: 0.75rem;
        line-height: 1.35;
        color: var(--color-gray-500, #6b7280);
    }

    /* ── Hover thought bubble ──
       Hidden until the card is hovered or focus-visible. Renders an
       inline SVG mock of the section's layout so the user can scan
       the visual shape without committing to add it. */
    .tb-section-picker__bubble {
        position: absolute;
        left: 50%;
        bottom: calc(100% + 14px);
        transform: translateX(-50%) translateY(6px) scale(0.96);
        width: 240px;
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
    /* Two trailing dots — gives it that "thinking bubble" feel rather
       than a stiff tooltip. They sit between the bubble and the card. */
    .tb-section-picker__bubble::before,
    .tb-section-picker__bubble::after {
        content: '';
        position: absolute;
        background: #ffffff;
        border: 1px solid var(--color-gray-200, #e5e7eb);
        border-radius: 50%;
    }
    .tb-section-picker__bubble::before {
        width: 10px; height: 10px;
        bottom: -7px; left: 50%;
        transform: translateX(-50%);
    }
    .tb-section-picker__bubble::after {
        width: 6px; height: 6px;
        bottom: -16px; left: 50%;
        transform: translateX(-180%);
    }

    .tb-section-picker__bubble-svg {
        width: 100%;
        aspect-ratio: 240 / 140;
        background: #f8fafc;
        border-radius: 8px;
        overflow: hidden;
    }

    .tb-section-picker__card:hover .tb-section-picker__bubble,
    .tb-section-picker__card:focus-visible .tb-section-picker__bubble {
        opacity: 1;
        transform: translateX(-50%) translateY(0) scale(1);
    }

    /* First-row cards (3-col layout): not enough room above, so flip
       the bubble below the card instead. */
    @media (min-width: 640px) {
        .tb-section-picker__card:nth-child(-n+3) .tb-section-picker__bubble {
            bottom: auto;
            top: calc(100% + 14px);
            transform: translateX(-50%) translateY(-6px) scale(0.96);
        }
        .tb-section-picker__card:nth-child(-n+3) .tb-section-picker__bubble::before {
            bottom: auto; top: -7px;
        }
        .tb-section-picker__card:nth-child(-n+3) .tb-section-picker__bubble::after {
            bottom: auto; top: -16px;
        }
        .tb-section-picker__card:nth-child(-n+3):hover .tb-section-picker__bubble,
        .tb-section-picker__card:nth-child(-n+3):focus-visible .tb-section-picker__bubble {
            transform: translateX(-50%) translateY(0) scale(1);
        }
    }

    /* On narrow viewports the bubble might fall off the right edge —
       hide it instead of dealing with edge-case positioning. The card
       still works as a click target. */
    @media (max-width: 639px) {
        .tb-section-picker__bubble { display: none; }
    }

    /* Dark mode (Filament toggles `.dark` on <html>) */
    .dark .tb-section-picker__card {
        border-color: rgba(255, 255, 255, 0.1);
        background: rgba(255, 255, 255, 0.03);
    }
    .dark .tb-section-picker__card:hover {
        border-color: var(--color-primary-400, #38bdf8);
        background: rgba(2, 132, 199, 0.10);
    }
    .dark .tb-section-picker__chip {
        background: rgba(2, 132, 199, 0.15);
        color: var(--color-primary-400, #38bdf8);
    }
    .dark .tb-section-picker__card:hover .tb-section-picker__chip {
        background: rgba(2, 132, 199, 0.25);
    }
    .dark .tb-section-picker__label { color: #ffffff; }
    .dark .tb-section-picker__desc  { color: var(--color-gray-400, #9ca3af); }

    .dark .tb-section-picker__bubble {
        background: #0f172a;
        border-color: rgba(255, 255, 255, 0.10);
        box-shadow: 0 16px 40px -8px rgba(0, 0, 0, 0.5), 0 4px 10px rgba(0, 0, 0, 0.3);
    }
    .dark .tb-section-picker__bubble::before,
    .dark .tb-section-picker__bubble::after {
        background: #0f172a;
        border-color: rgba(255, 255, 255, 0.10);
    }
    .dark .tb-section-picker__bubble-svg {
        background: rgba(255, 255, 255, 0.04);
    }
</style>

<div class="tb-section-picker">
    @foreach ($entries as $entry)
        @php $key = $entry['key']; @endphp
        <button
            type="button"
            wire:click="addSectionOfType(@js($key))"
            wire:loading.attr="disabled"
            class="tb-section-picker__card"
        >
            <span class="tb-section-picker__chip">
                <x-filament::icon :icon="$entry['icon']" />
            </span>
            <span style="display: block; width: 100%;">
                <span class="tb-section-picker__label">{{ $entry['label'] }}</span>
                <span class="tb-section-picker__desc">{{ $entry['desc'] }}</span>
            </span>

            <span class="tb-section-picker__bubble" aria-hidden="true">
                <span class="tb-section-picker__bubble-svg">
                    @include('filament.pages.theme-builder.section-preview', ['key' => $key])
                </span>
            </span>
        </button>
    @endforeach
</div>
