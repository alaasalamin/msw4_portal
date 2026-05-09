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
        </button>
    @endforeach
</div>
