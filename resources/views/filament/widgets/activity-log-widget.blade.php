<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Activity Log</x-slot>
        <x-slot name="description">All admin actions across the platform</x-slot>

        @php $activities = $this->getActivities(); @endphp

        @if($activities->isEmpty())
            <div class="py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                No activity recorded yet.
            </div>
        @else
            <div class="divide-y divide-gray-100 dark:divide-white/5">
                @foreach($activities as $entry)
                    @php
                        $color = match($entry['event']) {
                            'created' => 'text-success-600 bg-success-50 dark:bg-success-500/10 dark:text-success-400',
                            'deleted' => 'text-danger-600 bg-danger-50 dark:bg-danger-500/10 dark:text-danger-400',
                            default   => 'text-warning-600 bg-warning-50 dark:bg-warning-500/10 dark:text-warning-400',
                        };
                        $icon = match($entry['event']) {
                            'created' => '+',
                            'deleted' => 'x',
                            default   => '~',
                        };
                    @endphp

                    <div x-data="{ open: false }" class="py-3">
                        <div class="flex items-start gap-3">
                            {{-- Icon --}}
                            <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-xs font-bold {{ $color }}">
                                {{ $icon }}
                            </span>

                            {{-- Content --}}
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5">
                                    <span class="font-semibold text-sm text-gray-900 dark:text-white">
                                        {{ $entry['causer'] }}
                                    </span>
                                    <span class="text-sm text-gray-600 dark:text-gray-300">
                                        {!! $entry['description'] !!}
                                    </span>
                                </div>
                                <div class="mt-0.5 flex items-center gap-2">
                                    <span class="text-xs text-gray-400 dark:text-gray-500">
                                        {{ $entry['time']->diffForHumans() }}
                                        &middot;
                                        {{ $entry['time']->format('d M Y, H:i') }}
                                    </span>

                                    @if(!empty($entry['changes']))
                                        <button
                                            @click="open = !open"
                                            class="text-xs text-primary-600 hover:underline dark:text-primary-400"
                                        >
                                            <span x-text="open ? 'Hide details' : 'Show details'"></span>
                                        </button>
                                    @endif
                                </div>

                                {{-- Changed values --}}
                                @if(!empty($entry['changes']))
                                    <div x-show="open" x-cloak class="mt-2 rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 p-3 text-xs space-y-1">
                                        @foreach($entry['changes'] as $field => $newVal)
                                            @php
                                                // Flat list (e.g. permissions log: ['changes' => ['Enabled — x', ...]])
                                                $isFlat   = is_int($field);
                                                $display  = is_array($newVal) ? implode(', ', array_map('strval', $newVal)) : (string) $newVal;
                                                $oldVal   = (!$isFlat && !empty($entry['old'][$field]))
                                                    ? (is_array($entry['old'][$field]) ? implode(', ', array_map('strval', $entry['old'][$field])) : (string) $entry['old'][$field])
                                                    : null;
                                            @endphp
                                            <div class="flex gap-2 flex-wrap">
                                                @if(!$isFlat)
                                                    <span class="font-mono text-gray-500 dark:text-gray-400 shrink-0">{{ $field }}</span>
                                                    <span class="text-gray-400 dark:text-gray-500 shrink-0">&rarr;</span>
                                                @endif
                                                @if($oldVal)
                                                    <span class="line-through text-danger-500 shrink-0">{{ Str::limit($oldVal, 60) }}</span>
                                                    <span class="text-gray-400 dark:text-gray-500 shrink-0">&rarr;</span>
                                                @endif
                                                <span class="text-success-600 dark:text-success-400 break-all">{{ Str::limit($display, 80) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
