@php
    use App\Filament\Pages\ThemeBuilder\SectionRegistry;

    $types = SectionRegistry::types();
    if ($this->currentPageId !== null) {
        // Header and Footer are homepage-only; site pages inherit them.
        unset($types['header'], $types['footer']);
    }
@endphp

<div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
    @foreach ($types as $key => $meta)
        <button
            type="button"
            wire:click="addSectionOfType(@js($key))"
            wire:loading.attr="disabled"
            class="
                group relative flex flex-col items-start gap-2 text-left
                rounded-xl border border-gray-200 dark:border-white/10
                bg-white dark:bg-white/5
                px-4 py-4
                hover:border-primary-500 hover:bg-primary-50/40
                dark:hover:bg-primary-500/10
                focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500
                transition-colors duration-150
                cursor-pointer
            "
        >
            <span class="
                inline-flex items-center justify-center
                h-11 w-11 rounded-lg
                bg-primary-50 text-primary-600
                dark:bg-primary-500/15 dark:text-primary-400
                group-hover:bg-primary-100 dark:group-hover:bg-primary-500/25
                transition-colors duration-150
            ">
                <x-filament::icon
                    :icon="$meta['icon']"
                    class="h-6 w-6"
                />
            </span>

            <span class="block w-full">
                <span class="block font-semibold text-sm text-gray-950 dark:text-white">
                    {{ $meta['label'] }}
                </span>
                <span class="block mt-0.5 text-xs leading-snug text-gray-500 dark:text-gray-400 line-clamp-2">
                    {{ $meta['desc'] }}
                </span>
            </span>
        </button>
    @endforeach
</div>
