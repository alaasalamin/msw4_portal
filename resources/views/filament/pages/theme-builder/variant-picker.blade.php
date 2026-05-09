@php
    use App\Filament\Pages\ThemeBuilder\SectionRegistry;

    // The chooseDesign action mounts with arguments[id] (the section id).
    // Filament v5.5 stores them as mountedActions[last]['arguments'] —
    // there is no separate mountedActionsArguments property in this
    // version. getMountedAction()->getArguments() works too but is
    // slightly more expensive (resolves the action object); the array
    // path is enough for a render-time read.
    $mounted   = $this->mountedActions ?? [];
    $args      = end($mounted)['arguments'] ?? [];
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
        </button>
    @endforeach
</div>
@endif
