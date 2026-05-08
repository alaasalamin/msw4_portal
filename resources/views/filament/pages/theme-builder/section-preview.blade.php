{{-- Stylized 240×140 SVG mock of each section type, used by the picker
     hover bubble. Neutral palette; the active variant inside the section
     section card is hinted with the primary brand color. --}}
@php
    /** @var string $key */
@endphp

<svg viewBox="0 0 240 140" xmlns="http://www.w3.org/2000/svg" style="width: 100%; height: 100%; display: block;">
    @switch($key)
        @case('header')
            <rect x="14" y="50" width="212" height="40" rx="6" fill="#0f172a"/>
            <rect x="26" y="64" width="40" height="12" rx="2" fill="#cbd5e1"/>
            <circle cx="170" cy="70" r="3" fill="#cbd5e1"/>
            <circle cx="186" cy="70" r="3" fill="#cbd5e1"/>
            <circle cx="202" cy="70" r="3" fill="#cbd5e1"/>
            @break

        @case('hero')
            <rect x="14" y="14" width="212" height="112" rx="8" fill="#f3f4f6"/>
            <rect x="60" y="48" width="120" height="14" rx="3" fill="#374151"/>
            <rect x="80" y="70" width="80" height="6" rx="2" fill="#d1d5db"/>
            <rect x="80" y="80" width="80" height="6" rx="2" fill="#d1d5db"/>
            <rect x="92" y="98" width="56" height="14" rx="3" fill="#0284c7"/>
            @break

        @case('text')
            <rect x="20" y="38" width="120" height="12" rx="2" fill="#1f2937"/>
            <rect x="20" y="60" width="200" height="6" rx="2" fill="#d1d5db"/>
            <rect x="20" y="72" width="200" height="6" rx="2" fill="#d1d5db"/>
            <rect x="20" y="84" width="160" height="6" rx="2" fill="#d1d5db"/>
            <rect x="20" y="96" width="180" height="6" rx="2" fill="#d1d5db"/>
            @break

        @case('team')
            @foreach ([60, 120, 180] as $cx)
                <circle cx="{{ $cx }}" cy="50" r="14" fill="#9ca3af"/>
                <rect x="{{ $cx - 16 }}" y="74" width="32" height="6" rx="2" fill="#1f2937"/>
                <rect x="{{ $cx - 10 }}" y="86" width="20" height="4" rx="2" fill="#d1d5db"/>
            @endforeach
            @break

        @case('blog_posts')
            @foreach ([14, 88, 162] as $x)
                <rect x="{{ $x }}" y="30" width="64" height="80" rx="6" fill="#ffffff" stroke="#e5e7eb"/>
                <rect x="{{ $x + 6 }}" y="36" width="52" height="32" rx="3" fill="#9ca3af"/>
                <rect x="{{ $x + 6 }}" y="74" width="40" height="6" rx="2" fill="#1f2937"/>
                <rect x="{{ $x + 6 }}" y="84" width="48" height="4" rx="2" fill="#d1d5db"/>
                <rect x="{{ $x + 6 }}" y="92" width="36" height="4" rx="2" fill="#d1d5db"/>
            @endforeach
            @break

        @case('blog_carousel')
            <rect x="14" y="20" width="212" height="84" rx="8" fill="#374151"/>
            <rect x="32" y="60" width="100" height="10" rx="3" fill="#ffffff" opacity="0.92"/>
            <rect x="32" y="76" width="64" height="6" rx="2" fill="#ffffff" opacity="0.7"/>
            <rect x="32" y="86" width="44" height="8" rx="3" fill="#0284c7"/>
            <rect x="106" y="118" width="22" height="5" rx="2.5" fill="#0284c7"/>
            <circle cx="138" cy="120" r="2.5" fill="#cbd5e1"/>
            <circle cx="148" cy="120" r="2.5" fill="#cbd5e1"/>
            @break

        @case('map')
            <rect x="14" y="14" width="212" height="112" rx="8" fill="#e5e7eb"/>
            <path d="M75 40 L150 30 L210 50 L160 90 L80 110 Z" fill="#d1d5db" opacity="0.6"/>
            <path d="M120 50c-9 0-16 7-16 16 0 12 16 28 16 28s16-16 16-28c0-9-7-16-16-16z" fill="#0284c7"/>
            <circle cx="120" cy="66" r="6" fill="#ffffff"/>
            @break

        @case('reviews')
            @foreach ([14, 88, 162] as $x)
                <rect x="{{ $x }}" y="30" width="64" height="80" rx="6" fill="#ffffff" stroke="#e5e7eb"/>
                @foreach ([22, 30, 38, 46, 54] as $sx)
                    <circle cx="{{ $x + $sx - 14 }}" cy="42" r="2" fill="#facc15"/>
                @endforeach
                <rect x="{{ $x + 6 }}" y="56" width="52" height="4" rx="2" fill="#d1d5db"/>
                <rect x="{{ $x + 6 }}" y="64" width="48" height="4" rx="2" fill="#d1d5db"/>
                <rect x="{{ $x + 6 }}" y="72" width="40" height="4" rx="2" fill="#d1d5db"/>
                <rect x="{{ $x + 6 }}" y="92" width="28" height="6" rx="2" fill="#1f2937"/>
            @endforeach
            @break

        @case('form')
            <rect x="40" y="20" width="160" height="14" rx="3" fill="#ffffff" stroke="#d1d5db"/>
            <rect x="40" y="42" width="160" height="14" rx="3" fill="#ffffff" stroke="#d1d5db"/>
            <rect x="40" y="64" width="160" height="36" rx="3" fill="#ffffff" stroke="#d1d5db"/>
            <rect x="40" y="108" width="64" height="14" rx="3" fill="#0284c7"/>
            @break

        @case('pricing')
            @foreach ([[14, false], [88, true], [162, false]] as $col)
                @php [$x, $featured] = $col; @endphp
                <rect x="{{ $x }}" y="20" width="64" height="100" rx="6" fill="{{ $featured ? '#0f172a' : '#ffffff' }}" stroke="#e5e7eb"/>
                <rect x="{{ $x + 8 }}" y="32" width="48" height="8" rx="2" fill="{{ $featured ? '#ffffff' : '#1f2937' }}"/>
                <rect x="{{ $x + 14 }}" y="46" width="36" height="14" rx="3" fill="{{ $featured ? '#0284c7' : '#9ca3af' }}"/>
                <rect x="{{ $x + 8 }}" y="68" width="48" height="4" rx="2" fill="{{ $featured ? '#475569' : '#e5e7eb' }}"/>
                <rect x="{{ $x + 8 }}" y="78" width="48" height="4" rx="2" fill="{{ $featured ? '#475569' : '#e5e7eb' }}"/>
                <rect x="{{ $x + 8 }}" y="88" width="48" height="4" rx="2" fill="{{ $featured ? '#475569' : '#e5e7eb' }}"/>
                <rect x="{{ $x + 8 }}" y="100" width="48" height="10" rx="3" fill="{{ $featured ? '#ffffff' : '#0284c7' }}"/>
            @endforeach
            @break

        @case('faq')
            @foreach ([[28, 80], [56, 100], [84, 68]] as $row)
                @php [$y, $w] = $row; @endphp
                <rect x="20" y="{{ $y }}" width="200" height="20" rx="4" fill="#ffffff" stroke="#e5e7eb"/>
                <rect x="32" y="{{ $y + 8 }}" width="{{ $w }}" height="6" rx="2" fill="#1f2937"/>
                <path d="M204 {{ $y + 8 }} l4 4 -4 4" stroke="#9ca3af" fill="none" stroke-width="1.5" stroke-linecap="round"/>
            @endforeach
            @break

        @case('cta')
            <rect x="14" y="20" width="212" height="100" rx="8" fill="#0284c7"/>
            <rect x="60" y="46" width="120" height="14" rx="3" fill="#ffffff" opacity="0.95"/>
            <rect x="80" y="68" width="80" height="6" rx="2" fill="#ffffff" opacity="0.7"/>
            <rect x="92" y="88" width="56" height="14" rx="3" fill="#ffffff"/>
            @break

        @case('stats')
            @foreach ([42, 120, 198] as $cx)
                <rect x="{{ $cx - 24 }}" y="46" width="48" height="22" rx="3" fill="#0f172a"/>
                <rect x="{{ $cx - 18 }}" y="76" width="36" height="4" rx="2" fill="#d1d5db"/>
            @endforeach
            @break

        @case('steps')
            <line x1="60" y1="60" x2="180" y2="60" stroke="#cbd5e1" stroke-width="2" stroke-dasharray="4 3"/>
            @foreach ([[60, '#0284c7'], [120, '#9ca3af'], [180, '#9ca3af']] as $i => $step)
                @php [$cx, $color] = $step; @endphp
                <circle cx="{{ $cx }}" cy="60" r="14" fill="{{ $color }}"/>
                <text x="{{ $cx }}" y="64" text-anchor="middle" fill="#ffffff" font-size="10" font-weight="bold" font-family="system-ui, sans-serif">{{ $i + 1 }}</text>
                <rect x="{{ $cx - 14 }}" y="84" width="28" height="4" rx="2" fill="#1f2937"/>
                <rect x="{{ $cx - 18 }}" y="94" width="36" height="4" rx="2" fill="#d1d5db"/>
            @endforeach
            @break

        @case('gallery')
            @foreach ([20, 56, 92] as $row => $y)
                @foreach ([14, 88, 162] as $x)
                    <rect x="{{ $x }}" y="{{ $y }}" width="64" height="32" rx="3" fill="{{ ($row + (int)($x / 80)) % 2 === 0 ? '#9ca3af' : '#cbd5e1' }}"/>
                @endforeach
            @endforeach
            @break

        @case('table')
            <rect x="14" y="32" width="212" height="14" rx="3" fill="#0f172a"/>
            <rect x="14" y="50" width="212" height="14" fill="#ffffff" stroke="#e5e7eb"/>
            <rect x="14" y="66" width="212" height="14" fill="#f9fafb" stroke="#e5e7eb"/>
            <rect x="14" y="82" width="212" height="14" fill="#ffffff" stroke="#e5e7eb"/>
            <rect x="14" y="98" width="212" height="14" fill="#f9fafb" stroke="#e5e7eb"/>
            <line x1="80" y1="46" x2="80" y2="112" stroke="#e5e7eb" stroke-width="1"/>
            <line x1="160" y1="46" x2="160" y2="112" stroke="#e5e7eb" stroke-width="1"/>
            @break

        @case('footer')
            <rect x="14" y="46" width="212" height="80" rx="6" fill="#0f172a"/>
            <rect x="26" y="58" width="40" height="6" rx="2" fill="#cbd5e1"/>
            <rect x="26" y="70" width="60" height="4" rx="2" fill="#64748b"/>
            <rect x="26" y="80" width="48" height="4" rx="2" fill="#64748b"/>
            <rect x="26" y="106" width="80" height="4" rx="2" fill="#475569"/>
            <circle cx="186" cy="64" r="3" fill="#cbd5e1"/>
            <circle cx="200" cy="64" r="3" fill="#cbd5e1"/>
            <circle cx="214" cy="64" r="3" fill="#cbd5e1"/>
            @break

        @default
            <rect x="20" y="20" width="200" height="100" rx="8" fill="#e5e7eb"/>
    @endswitch
</svg>
