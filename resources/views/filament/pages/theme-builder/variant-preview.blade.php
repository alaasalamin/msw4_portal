{{-- 240×140 structural mock per (section type, variant) pair.
     Compact rect/circle/line primitives — the goal is for users to
     read the *layout* difference at a glance, not pixel-perfect
     fidelity. A neutral palette is used everywhere so brand color
     (#0284c7) only appears where it actively pulls the eye. --}}
@php
    /** @var string $type */
    /** @var string $variant */
@endphp

<svg viewBox="0 0 240 140" xmlns="http://www.w3.org/2000/svg" style="width:100%; height:100%; display:block;">
    @switch($type . ':' . $variant)

        {{-- ── Header ───────────────────────────────────────────── --}}
        @case('header:classic')
            <rect x="14" y="50" width="212" height="40" rx="6" fill="#0f172a"/>
            <rect x="26" y="64" width="40" height="12" rx="2" fill="#cbd5e1"/>
            <circle cx="170" cy="70" r="3" fill="#cbd5e1"/>
            <circle cx="186" cy="70" r="3" fill="#cbd5e1"/>
            <circle cx="202" cy="70" r="3" fill="#cbd5e1"/>
            @break
        @case('header:centered')
            <rect x="14" y="36" width="212" height="68" rx="6" fill="#0f172a"/>
            <rect x="100" y="46" width="40" height="12" rx="2" fill="#cbd5e1"/>
            <circle cx="98"  cy="84" r="3" fill="#cbd5e1"/>
            <circle cx="120" cy="84" r="3" fill="#cbd5e1"/>
            <circle cx="142" cy="84" r="3" fill="#cbd5e1"/>
            @break
        @case('header:split')
            <rect x="14" y="50" width="212" height="40" rx="6" fill="#0f172a"/>
            <rect x="26" y="64" width="32" height="12" rx="2" fill="#cbd5e1"/>
            <circle cx="110" cy="70" r="3" fill="#cbd5e1"/>
            <circle cx="124" cy="70" r="3" fill="#cbd5e1"/>
            <circle cx="138" cy="70" r="3" fill="#cbd5e1"/>
            <rect x="172" y="62" width="42" height="16" rx="4" fill="#0284c7"/>
            @break
        @case('header:minimal')
            <rect x="14" y="50" width="212" height="40" rx="6" fill="#0f172a"/>
            <rect x="26" y="64" width="40" height="12" rx="2" fill="#cbd5e1"/>
            <line x1="200" y1="68" x2="212" y2="68" stroke="#cbd5e1" stroke-width="1.5"/>
            <line x1="200" y1="72" x2="212" y2="72" stroke="#cbd5e1" stroke-width="1.5"/>
            <line x1="200" y1="76" x2="212" y2="76" stroke="#cbd5e1" stroke-width="1.5"/>
            @break

        {{-- ── Hero ─────────────────────────────────────────────── --}}
        @case('hero:centered')
            <rect x="14" y="14" width="212" height="112" rx="8" fill="#f3f4f6"/>
            <rect x="60" y="48" width="120" height="14" rx="3" fill="#374151"/>
            <rect x="80" y="70" width="80" height="6" rx="2" fill="#d1d5db"/>
            <rect x="92" y="92" width="56" height="14" rx="3" fill="#0284c7"/>
            @break
        @case('hero:split')
            <rect x="14" y="14" width="212" height="112" rx="8" fill="#f3f4f6"/>
            <rect x="120" y="14" width="106" height="112" fill="#9ca3af"/>
            <rect x="28" y="50" width="80" height="14" rx="3" fill="#374151"/>
            <rect x="28" y="72" width="60" height="6" rx="2" fill="#d1d5db"/>
            <rect x="28" y="94" width="48" height="14" rx="3" fill="#0284c7"/>
            @break
        @case('hero:bg_image')
            <rect x="14" y="14" width="212" height="112" rx="8" fill="#9ca3af"/>
            <rect x="14" y="14" width="212" height="112" rx="8" fill="#0f172a" opacity="0.45"/>
            <rect x="60" y="50" width="120" height="14" rx="3" fill="#ffffff" opacity="0.95"/>
            <rect x="80" y="72" width="80" height="6" rx="2" fill="#ffffff" opacity="0.7"/>
            <rect x="92" y="94" width="56" height="14" rx="3" fill="#ffffff"/>
            @break
        @case('hero:minimal')
            <rect x="14" y="14" width="212" height="112" rx="8" fill="#f3f4f6"/>
            <rect x="28" y="68" width="100" height="14" rx="3" fill="#374151"/>
            <rect x="28" y="92" width="48" height="14" rx="3" fill="#0284c7"/>
            @break
        @case('hero:with_stats')
            <rect x="14" y="14" width="212" height="112" rx="8" fill="#f3f4f6"/>
            <rect x="60" y="36" width="120" height="14" rx="3" fill="#374151"/>
            <rect x="80" y="58" width="80" height="6" rx="2" fill="#d1d5db"/>
            <rect x="34"  y="86" width="50" height="24" rx="3" fill="#ffffff" stroke="#e5e7eb"/>
            <rect x="42" y="92" width="34" height="6" rx="2" fill="#0284c7"/>
            <rect x="42" y="102" width="26" height="3" rx="1.5" fill="#9ca3af"/>
            <rect x="95"  y="86" width="50" height="24" rx="3" fill="#ffffff" stroke="#e5e7eb"/>
            <rect x="103" y="92" width="34" height="6" rx="2" fill="#0284c7"/>
            <rect x="103" y="102" width="26" height="3" rx="1.5" fill="#9ca3af"/>
            <rect x="156" y="86" width="50" height="24" rx="3" fill="#ffffff" stroke="#e5e7eb"/>
            <rect x="164" y="92" width="34" height="6" rx="2" fill="#0284c7"/>
            <rect x="164" y="102" width="26" height="3" rx="1.5" fill="#9ca3af"/>
            @break

        {{-- ── Text ─────────────────────────────────────────────── --}}
        @case('text:default')
            <rect x="20" y="38" width="120" height="12" rx="2" fill="#1f2937"/>
            <rect x="20" y="60" width="200" height="6" rx="2" fill="#d1d5db"/>
            <rect x="20" y="72" width="200" height="6" rx="2" fill="#d1d5db"/>
            <rect x="20" y="84" width="160" height="6" rx="2" fill="#d1d5db"/>
            @break
        @case('text:two_column')
            <rect x="20" y="38" width="80" height="12" rx="2" fill="#1f2937"/>
            <rect x="20" y="58" width="80" height="5" rx="2" fill="#d1d5db"/>
            <rect x="120" y="38" width="100" height="6" rx="2" fill="#d1d5db"/>
            <rect x="120" y="50" width="100" height="6" rx="2" fill="#d1d5db"/>
            <rect x="120" y="62" width="100" height="6" rx="2" fill="#d1d5db"/>
            <rect x="120" y="74" width="80"  height="6" rx="2" fill="#d1d5db"/>
            @break
        @case('text:callout')
            <rect x="14" y="36" width="212" height="68" rx="8" fill="#dbeafe"/>
            <rect x="14" y="36" width="4" height="68" fill="#0284c7"/>
            <rect x="32" y="46" width="80" height="10" rx="2" fill="#1e40af"/>
            <rect x="32" y="62" width="180" height="5" rx="2" fill="#3b82f6"/>
            <rect x="32" y="72" width="160" height="5" rx="2" fill="#3b82f6"/>
            <rect x="32" y="82" width="120" height="5" rx="2" fill="#3b82f6"/>
            @break
        @case('text:quote')
            <text x="40" y="60" fill="#cbd5e1" font-size="40" font-family="Georgia, serif" font-weight="bold">"</text>
            <rect x="40" y="58" width="170" height="8" rx="2" fill="#1f2937"/>
            <rect x="40" y="72" width="150" height="8" rx="2" fill="#1f2937"/>
            <rect x="40" y="86" width="120" height="8" rx="2" fill="#1f2937"/>
            <rect x="40" y="106" width="60" height="4" rx="2" fill="#9ca3af"/>
            @break

        {{-- ── Reviews ──────────────────────────────────────────── --}}
        @case('reviews:cards')
            @foreach ([14, 88, 162] as $x)
                <rect x="{{ $x }}" y="30" width="64" height="80" rx="6" fill="#ffffff" stroke="#e5e7eb"/>
                @foreach ([8, 16, 24, 32, 40] as $sx)
                    <circle cx="{{ $x + $sx }}" cy="44" r="2" fill="#facc15"/>
                @endforeach
                <rect x="{{ $x + 6 }}" y="58" width="52" height="4" rx="2" fill="#d1d5db"/>
                <rect x="{{ $x + 6 }}" y="66" width="40" height="4" rx="2" fill="#d1d5db"/>
                <rect x="{{ $x + 6 }}" y="92" width="28" height="6" rx="2" fill="#1f2937"/>
            @endforeach
            @break
        @case('reviews:single_featured')
            <text x="40" y="56" fill="#cbd5e1" font-size="44" font-family="Georgia, serif" font-weight="bold">"</text>
            <rect x="40" y="56" width="160" height="8" rx="2" fill="#1f2937"/>
            <rect x="40" y="70" width="140" height="8" rx="2" fill="#1f2937"/>
            <rect x="40" y="84" width="120" height="8" rx="2" fill="#1f2937"/>
            <rect x="100" y="106" width="40" height="6" rx="2" fill="#9ca3af"/>
            <circle cx="50" cy="120" r="6" fill="#9ca3af"/>
            <circle cx="64" cy="120" r="6" fill="#cbd5e1"/>
            @break
        @case('reviews:bubbles')
            @foreach ([14, 88, 162] as $x)
                <path d="M{{ $x }} 36 q0 -8 8 -8 h48 q8 0 8 8 v32 q0 8 -8 8 h-32 l-8 8 v-8 h-8 q-8 0 -8 -8 z" fill="#ffffff" stroke="#e5e7eb"/>
                <rect x="{{ $x + 8 }}" y="42" width="48" height="4" rx="2" fill="#facc15"/>
                <rect x="{{ $x + 8 }}" y="52" width="44" height="4" rx="2" fill="#d1d5db"/>
                <rect x="{{ $x + 8 }}" y="60" width="36" height="4" rx="2" fill="#d1d5db"/>
                <rect x="{{ $x + 12 }}" y="98" width="24" height="6" rx="2" fill="#1f2937"/>
            @endforeach
            @break
        @case('reviews:list')
            @foreach ([28, 60, 92] as $y)
                @foreach ([20, 28, 36, 44, 52] as $sx)
                    <circle cx="{{ $sx + 4 }}" cy="{{ $y + 4 }}" r="2" fill="#facc15"/>
                @endforeach
                <rect x="80" y="{{ $y }}" width="140" height="5" rx="2" fill="#1f2937"/>
                <rect x="80" y="{{ $y + 8 }}" width="120" height="4" rx="2" fill="#d1d5db"/>
                <line x1="20" y1="{{ $y + 22 }}" x2="220" y2="{{ $y + 22 }}" stroke="#e5e7eb"/>
            @endforeach
            @break

        {{-- ── Pricing ──────────────────────────────────────────── --}}
        @case('pricing:cards')
            @foreach ([[14,false], [88,true], [162,false]] as [$x, $featured])
                <rect x="{{ $x }}" y="20" width="64" height="100" rx="6" fill="{{ $featured ? '#0f172a' : '#ffffff' }}" stroke="#e5e7eb"/>
                <rect x="{{ $x + 8 }}" y="32" width="48" height="8" rx="2" fill="{{ $featured ? '#ffffff' : '#1f2937' }}"/>
                <rect x="{{ $x + 14 }}" y="46" width="36" height="14" rx="3" fill="{{ $featured ? '#0284c7' : '#9ca3af' }}"/>
                <rect x="{{ $x + 8 }}" y="68" width="48" height="4" rx="2" fill="{{ $featured ? '#475569' : '#e5e7eb' }}"/>
                <rect x="{{ $x + 8 }}" y="78" width="48" height="4" rx="2" fill="{{ $featured ? '#475569' : '#e5e7eb' }}"/>
                <rect x="{{ $x + 8 }}" y="100" width="48" height="10" rx="3" fill="{{ $featured ? '#ffffff' : '#0284c7' }}"/>
            @endforeach
            @break
        @case('pricing:table')
            <rect x="14" y="20" width="212" height="100" rx="6" fill="#ffffff" stroke="#e5e7eb"/>
            <rect x="14" y="20" width="212" height="22" fill="#0f172a"/>
            <rect x="22" y="28" width="40" height="6" rx="2" fill="#cbd5e1"/>
            @foreach ([74, 138, 196] as $cx)
                <rect x="{{ $cx - 12 }}" y="28" width="24" height="6" rx="2" fill="#cbd5e1"/>
            @endforeach
            @foreach ([54, 70, 86, 102] as $y)
                <line x1="14" y1="{{ $y }}" x2="226" y2="{{ $y }}" stroke="#e5e7eb"/>
                <rect x="22" y="{{ $y - 6 }}" width="36" height="4" rx="2" fill="#1f2937"/>
                @foreach ([74, 138, 196] as $cx)
                    <circle cx="{{ $cx }}" cy="{{ $y - 4 }}" r="2.5" fill="#9ca3af"/>
                @endforeach
            @endforeach
            @break
        @case('pricing:tabs')
            <rect x="40" y="22" width="50" height="14" rx="3" fill="#0284c7"/>
            <rect x="98" y="22" width="50" height="14" rx="3" fill="#e5e7eb"/>
            <rect x="156" y="22" width="50" height="14" rx="3" fill="#e5e7eb"/>
            <rect x="60" y="48" width="120" height="76" rx="6" fill="#ffffff" stroke="#e5e7eb"/>
            <rect x="80" y="56" width="80" height="8" rx="2" fill="#1f2937"/>
            <rect x="92" y="70" width="56" height="14" rx="3" fill="#0284c7"/>
            <rect x="80" y="92" width="80" height="4" rx="2" fill="#e5e7eb"/>
            <rect x="80" y="100" width="60" height="4" rx="2" fill="#e5e7eb"/>
            <rect x="92" y="112" width="56" height="8" rx="2" fill="#0284c7"/>
            @break
        @case('pricing:minimal')
            @foreach ([28, 56, 84] as $y)
                <line x1="14" y1="{{ $y + 14 }}" x2="226" y2="{{ $y + 14 }}" stroke="#e5e7eb"/>
                <rect x="22" y="{{ $y }}" width="50" height="6" rx="2" fill="#1f2937"/>
                <rect x="120" y="{{ $y }}" width="40" height="6" rx="2" fill="#9ca3af"/>
                <rect x="180" y="{{ $y - 2 }}" width="40" height="10" rx="3" fill="#0284c7"/>
            @endforeach
            @break

        {{-- ── CTA ──────────────────────────────────────────────── --}}
        @case('cta:centered')
            <rect x="14" y="20" width="212" height="100" rx="8" fill="#f3f4f6"/>
            <rect x="60" y="46" width="120" height="14" rx="3" fill="#1f2937"/>
            <rect x="92" y="80" width="56" height="14" rx="3" fill="#0284c7"/>
            @break
        @case('cta:split')
            <rect x="14" y="40" width="212" height="60" rx="8" fill="#f3f4f6"/>
            <rect x="28" y="58" width="120" height="10" rx="2" fill="#1f2937"/>
            <rect x="28" y="74" width="80" height="5" rx="2" fill="#9ca3af"/>
            <rect x="160" y="60" width="56" height="20" rx="4" fill="#0284c7"/>
            @break
        @case('cta:gradient')
            <defs>
                <linearGradient id="ctaGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="#0284c7"/>
                    <stop offset="100%" stop-color="#7c3aed"/>
                </linearGradient>
            </defs>
            <rect x="14" y="20" width="212" height="100" rx="8" fill="url(#ctaGrad)"/>
            <rect x="60" y="46" width="120" height="14" rx="3" fill="#ffffff" opacity="0.95"/>
            <rect x="92" y="80" width="56" height="14" rx="3" fill="#ffffff"/>
            @break
        @case('cta:boxed')
            <rect x="14" y="14" width="212" height="112" rx="8" fill="#f3f4f6"/>
            <rect x="34" y="34" width="172" height="72" rx="8" fill="#ffffff" stroke="#e5e7eb"/>
            <rect x="80" y="52" width="80" height="12" rx="3" fill="#1f2937"/>
            <rect x="100" y="76" width="40" height="12" rx="3" fill="#0284c7"/>
            @break
        @case('cta:inset')
            <rect x="14" y="20" width="212" height="100" rx="8" fill="#f8fafc"/>
            <rect x="56" y="48" width="128" height="44" rx="10" fill="#ffffff" stroke="#e5e7eb"/>
            <rect x="74" y="58" width="92" height="8" rx="2" fill="#1f2937"/>
            <rect x="92" y="74" width="56" height="10" rx="3" fill="#0284c7"/>
            @break

        {{-- ── Steps ────────────────────────────────────────────── --}}
        @case('steps:horizontal')
            <line x1="60" y1="60" x2="180" y2="60" stroke="#cbd5e1" stroke-width="2" stroke-dasharray="4 3"/>
            @foreach ([60, 120, 180] as $i => $cx)
                <circle cx="{{ $cx }}" cy="60" r="14" fill="{{ $i === 0 ? '#0284c7' : '#9ca3af' }}"/>
                <text x="{{ $cx }}" y="64" text-anchor="middle" fill="#fff" font-size="10" font-weight="bold" font-family="system-ui, sans-serif">{{ $i + 1 }}</text>
                <rect x="{{ $cx - 14 }}" y="84" width="28" height="4" rx="2" fill="#1f2937"/>
                <rect x="{{ $cx - 18 }}" y="94" width="36" height="4" rx="2" fill="#d1d5db"/>
            @endforeach
            @break
        @case('steps:vertical')
            <line x1="40" y1="30" x2="40" y2="118" stroke="#cbd5e1" stroke-width="2" stroke-dasharray="4 3"/>
            @foreach ([30, 70, 110] as $i => $cy)
                <circle cx="40" cy="{{ $cy }}" r="10" fill="{{ $i === 0 ? '#0284c7' : '#9ca3af' }}"/>
                <text x="40" y="{{ $cy + 3 }}" text-anchor="middle" fill="#fff" font-size="9" font-weight="bold" font-family="system-ui, sans-serif">{{ $i + 1 }}</text>
                <rect x="60" y="{{ $cy - 5 }}" width="120" height="5" rx="2" fill="#1f2937"/>
                <rect x="60" y="{{ $cy + 3 }}" width="160" height="4" rx="2" fill="#d1d5db"/>
            @endforeach
            @break
        @case('steps:cards')
            @foreach ([14, 88, 162] as $i => $x)
                <rect x="{{ $x }}" y="30" width="64" height="80" rx="6" fill="#ffffff" stroke="#e5e7eb"/>
                <circle cx="{{ $x + 16 }}" cy="48" r="10" fill="{{ $i === 0 ? '#0284c7' : '#9ca3af' }}"/>
                <text x="{{ $x + 16 }}" y="51" text-anchor="middle" fill="#fff" font-size="9" font-weight="bold" font-family="system-ui, sans-serif">{{ $i + 1 }}</text>
                <rect x="{{ $x + 8 }}" y="68" width="48" height="6" rx="2" fill="#1f2937"/>
                <rect x="{{ $x + 8 }}" y="80" width="48" height="4" rx="2" fill="#d1d5db"/>
                <rect x="{{ $x + 8 }}" y="88" width="36" height="4" rx="2" fill="#d1d5db"/>
            @endforeach
            @break
        @case('steps:arrows')
            @foreach ([60, 120, 180] as $i => $cx)
                <circle cx="{{ $cx }}" cy="60" r="14" fill="{{ $i === 0 ? '#0284c7' : '#9ca3af' }}"/>
                <text x="{{ $cx }}" y="64" text-anchor="middle" fill="#fff" font-size="10" font-weight="bold" font-family="system-ui, sans-serif">{{ $i + 1 }}</text>
                <rect x="{{ $cx - 14 }}" y="84" width="28" height="4" rx="2" fill="#1f2937"/>
                @if ($i < 2)
                    <path d="M{{ $cx + 18 }} 60 l16 0 m-4 -4 4 4 -4 4" stroke="#9ca3af" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                @endif
            @endforeach
            @break
        @case('steps:timeline')
            <rect x="38" y="22" width="4" height="100" rx="2" fill="#0284c7"/>
            @foreach ([34, 68, 102] as $i => $cy)
                <circle cx="40" cy="{{ $cy }}" r="8" fill="#0284c7"/>
                <circle cx="40" cy="{{ $cy }}" r="3" fill="#ffffff"/>
                <rect x="60" y="{{ $cy - 6 }}" width="120" height="6" rx="2" fill="#1f2937"/>
                <rect x="60" y="{{ $cy + 4 }}" width="160" height="4" rx="2" fill="#d1d5db"/>
            @endforeach
            @break

        {{-- ── Form ─────────────────────────────────────────────── --}}
        @case('form:default')
            <rect x="40" y="20" width="160" height="14" rx="3" fill="#ffffff" stroke="#d1d5db"/>
            <rect x="40" y="42" width="160" height="14" rx="3" fill="#ffffff" stroke="#d1d5db"/>
            <rect x="40" y="64" width="160" height="36" rx="3" fill="#ffffff" stroke="#d1d5db"/>
            <rect x="40" y="108" width="64" height="14" rx="3" fill="#0284c7"/>
            @break
        @case('form:split')
            <rect x="14" y="22" width="100" height="10" rx="2" fill="#1f2937"/>
            <rect x="14" y="40" width="100" height="5" rx="2" fill="#d1d5db"/>
            <rect x="14" y="50" width="80" height="5" rx="2" fill="#d1d5db"/>
            <rect x="130" y="22" width="96" height="14" rx="3" fill="#fff" stroke="#d1d5db"/>
            <rect x="130" y="42" width="96" height="14" rx="3" fill="#fff" stroke="#d1d5db"/>
            <rect x="130" y="62" width="96" height="32" rx="3" fill="#fff" stroke="#d1d5db"/>
            <rect x="130" y="100" width="48" height="14" rx="3" fill="#0284c7"/>
            @break
        @case('form:boxed')
            <rect x="14" y="14" width="212" height="112" rx="8" fill="#f8fafc"/>
            <rect x="34" y="28" width="172" height="84" rx="8" fill="#fff" stroke="#e5e7eb"/>
            <rect x="50" y="40" width="140" height="10" rx="2" fill="#fff" stroke="#d1d5db"/>
            <rect x="50" y="56" width="140" height="10" rx="2" fill="#fff" stroke="#d1d5db"/>
            <rect x="50" y="72" width="140" height="22" rx="2" fill="#fff" stroke="#d1d5db"/>
            <rect x="50" y="98" width="50" height="10" rx="2" fill="#0284c7"/>
            @break
        @case('form:bg_image')
            <rect x="14" y="14" width="212" height="112" rx="8" fill="#9ca3af"/>
            <rect x="14" y="14" width="212" height="112" rx="8" fill="#0f172a" opacity="0.5"/>
            <rect x="58" y="32" width="124" height="76" rx="8" fill="#fff"/>
            <rect x="70" y="42" width="100" height="10" rx="2" fill="#fff" stroke="#d1d5db"/>
            <rect x="70" y="58" width="100" height="10" rx="2" fill="#fff" stroke="#d1d5db"/>
            <rect x="70" y="74" width="100" height="20" rx="2" fill="#fff" stroke="#d1d5db"/>
            <rect x="70" y="98" width="40" height="8" rx="2" fill="#0284c7"/>
            @break

        {{-- ── Team ─────────────────────────────────────────────── --}}
        @case('team:cards')
            @foreach ([14, 88, 162] as $x)
                <rect x="{{ $x }}" y="20" width="64" height="100" rx="6" fill="#ffffff" stroke="#e5e7eb"/>
                <circle cx="{{ $x + 32 }}" cy="48" r="14" fill="#9ca3af"/>
                <rect x="{{ $x + 14 }}" y="74" width="36" height="6" rx="2" fill="#1f2937"/>
                <rect x="{{ $x + 18 }}" y="86" width="28" height="4" rx="2" fill="#0284c7"/>
                <rect x="{{ $x + 8 }}" y="98" width="48" height="4" rx="2" fill="#d1d5db"/>
                <rect x="{{ $x + 8 }}" y="106" width="36" height="4" rx="2" fill="#d1d5db"/>
            @endforeach
            @break
        @case('team:list')
            @foreach ([28, 60, 92] as $y)
                <circle cx="32" cy="{{ $y + 4 }}" r="10" fill="#9ca3af"/>
                <rect x="56" y="{{ $y - 2 }}" width="60" height="5" rx="2" fill="#1f2937"/>
                <rect x="56" y="{{ $y + 6 }}" width="40" height="4" rx="2" fill="#0284c7"/>
                <rect x="130" y="{{ $y - 2 }}" width="90" height="4" rx="2" fill="#d1d5db"/>
                <rect x="130" y="{{ $y + 6 }}" width="80" height="4" rx="2" fill="#d1d5db"/>
            @endforeach
            @break
        @case('team:photo_focus')
            @foreach ([14, 88, 162] as $x)
                <rect x="{{ $x }}" y="20" width="64" height="100" rx="6" fill="#9ca3af"/>
                <rect x="{{ $x }}" y="92" width="64" height="28" rx="0" fill="#0f172a" opacity="0.7"/>
                <rect x="{{ $x + 8 }}" y="98" width="40" height="5" rx="2" fill="#fff"/>
                <rect x="{{ $x + 8 }}" y="108" width="28" height="4" rx="2" fill="#cbd5e1"/>
            @endforeach
            @break
        @case('team:compact')
            @foreach ([22, 58, 94, 130, 166, 202] as $cx)
                <circle cx="{{ $cx }}" cy="50" r="12" fill="#9ca3af"/>
                <rect x="{{ $cx - 14 }}" y="74" width="28" height="4" rx="2" fill="#1f2937"/>
                <rect x="{{ $cx - 10 }}" y="84" width="20" height="4" rx="2" fill="#9ca3af"/>
            @endforeach
            @break

        {{-- ── Gallery ──────────────────────────────────────────── --}}
        @case('gallery:grid')
            @foreach ([20, 56, 92] as $i => $y)
                @foreach ([14, 88, 162] as $x)
                    <rect x="{{ $x }}" y="{{ $y }}" width="64" height="32" rx="3" fill="#9ca3af"/>
                @endforeach
            @endforeach
            @break
        @case('gallery:masonry')
            <rect x="14" y="20" width="64" height="48" rx="3" fill="#9ca3af"/>
            <rect x="14" y="74" width="64" height="32" rx="3" fill="#cbd5e1"/>
            <rect x="14" y="112" width="64" height="14" rx="3" fill="#9ca3af"/>
            <rect x="88" y="20" width="64" height="32" rx="3" fill="#cbd5e1"/>
            <rect x="88" y="58" width="64" height="48" rx="3" fill="#9ca3af"/>
            <rect x="88" y="112" width="64" height="14" rx="3" fill="#cbd5e1"/>
            <rect x="162" y="20" width="64" height="14" rx="3" fill="#9ca3af"/>
            <rect x="162" y="40" width="64" height="48" rx="3" fill="#cbd5e1"/>
            <rect x="162" y="94" width="64" height="32" rx="3" fill="#9ca3af"/>
            @break
        @case('gallery:carousel')
            <rect x="-20" y="40" width="80" height="60" rx="6" fill="#cbd5e1"/>
            <rect x="68" y="36" width="104" height="68" rx="6" fill="#9ca3af"/>
            <rect x="180" y="40" width="80" height="60" rx="6" fill="#cbd5e1"/>
            <circle cx="22" cy="70" r="14" fill="#fff" opacity="0.9"/>
            <circle cx="218" cy="70" r="14" fill="#fff" opacity="0.9"/>
            @break
        @case('gallery:collage')
            <rect x="14" y="20" width="106" height="80" rx="3" fill="#9ca3af"/>
            <rect x="130" y="20" width="48" height="38" rx="3" fill="#cbd5e1"/>
            <rect x="186" y="20" width="40" height="38" rx="3" fill="#9ca3af"/>
            <rect x="130" y="62" width="40" height="38" rx="3" fill="#9ca3af"/>
            <rect x="178" y="62" width="48" height="38" rx="3" fill="#cbd5e1"/>
            <rect x="14" y="108" width="60" height="14" rx="3" fill="#cbd5e1"/>
            <rect x="82" y="108" width="40" height="14" rx="3" fill="#9ca3af"/>
            @break

        {{-- ── FAQ ──────────────────────────────────────────────── --}}
        @case('faq:accordion')
            @foreach ([[28, 80, false], [56, 100, true], [110, 68, false]] as [$y, $w, $open])
                <rect x="20" y="{{ $y }}" width="200" height="20" rx="4" fill="#ffffff" stroke="#e5e7eb"/>
                <rect x="32" y="{{ $y + 8 }}" width="{{ $w }}" height="6" rx="2" fill="#1f2937"/>
                @if ($open)
                    <path d="M204 {{ $y + 6 }} l-4 4 4 4" stroke="#0284c7" fill="none" stroke-width="1.5" stroke-linecap="round"/>
                    <rect x="32" y="{{ $y + 22 }}" width="180" height="4" rx="2" fill="#9ca3af"/>
                    <rect x="32" y="{{ $y + 30 }}" width="160" height="4" rx="2" fill="#9ca3af"/>
                @else
                    <path d="M204 {{ $y + 8 }} l4 4 -4 4" stroke="#9ca3af" fill="none" stroke-width="1.5" stroke-linecap="round"/>
                @endif
            @endforeach
            @break
        @case('faq:open')
            @foreach ([24, 62, 100] as $y)
                <rect x="20" y="{{ $y }}" width="120" height="6" rx="2" fill="#1f2937"/>
                <rect x="20" y="{{ $y + 12 }}" width="200" height="4" rx="2" fill="#9ca3af"/>
                <rect x="20" y="{{ $y + 22 }}" width="170" height="4" rx="2" fill="#9ca3af"/>
            @endforeach
            @break
        @case('faq:two-col')
            @foreach ([14, 124] as $col)
                @foreach ([24, 70] as $y)
                    <rect x="{{ $col }}" y="{{ $y }}" width="100" height="6" rx="2" fill="#1f2937"/>
                    <rect x="{{ $col }}" y="{{ $y + 12 }}" width="100" height="4" rx="2" fill="#9ca3af"/>
                    <rect x="{{ $col }}" y="{{ $y + 20 }}" width="80" height="4" rx="2" fill="#9ca3af"/>
                @endforeach
            @endforeach
            @break
        @case('faq:cards')
            @foreach ([14, 88, 162] as $x)
                <rect x="{{ $x }}" y="20" width="64" height="100" rx="6" fill="#ffffff" stroke="#e5e7eb"/>
                <rect x="{{ $x + 8 }}" y="36" width="48" height="6" rx="2" fill="#1f2937"/>
                <rect x="{{ $x + 8 }}" y="56" width="48" height="4" rx="2" fill="#9ca3af"/>
                <rect x="{{ $x + 8 }}" y="66" width="44" height="4" rx="2" fill="#9ca3af"/>
                <rect x="{{ $x + 8 }}" y="76" width="36" height="4" rx="2" fill="#9ca3af"/>
            @endforeach
            @break
        @case('faq:bordered')
            @foreach ([24, 56, 88] as $y)
                <rect x="20" y="{{ $y }}" width="200" height="24" rx="3" fill="#ffffff" stroke="#e5e7eb"/>
                <rect x="20" y="{{ $y }}" width="4" height="24" fill="#0284c7"/>
                <rect x="32" y="{{ $y + 8 }}" width="120" height="6" rx="2" fill="#1f2937"/>
                <path d="M204 {{ $y + 8 }} l4 4 -4 4" stroke="#9ca3af" fill="none" stroke-width="1.5" stroke-linecap="round"/>
            @endforeach
            @break

        {{-- ── Stats ────────────────────────────────────────────── --}}
        @case('stats:row')
            @foreach ([42, 120, 198] as $cx)
                <rect x="{{ $cx - 24 }}" y="46" width="48" height="22" rx="3" fill="#0f172a"/>
                <rect x="{{ $cx - 18 }}" y="76" width="36" height="4" rx="2" fill="#d1d5db"/>
            @endforeach
            @break
        @case('stats:cards')
            @foreach ([14, 88, 162] as $x)
                <rect x="{{ $x }}" y="32" width="64" height="76" rx="6" fill="#ffffff" stroke="#e5e7eb"/>
                <rect x="{{ $x + 12 }}" y="48" width="40" height="20" rx="3" fill="#0f172a"/>
                <rect x="{{ $x + 14 }}" y="74" width="36" height="4" rx="2" fill="#d1d5db"/>
                <rect x="{{ $x + 16 }}" y="84" width="32" height="4" rx="2" fill="#d1d5db"/>
            @endforeach
            @break
        @case('stats:dividers')
            <line x1="80" y1="40" x2="80" y2="100" stroke="#e5e7eb"/>
            <line x1="160" y1="40" x2="160" y2="100" stroke="#e5e7eb"/>
            @foreach ([42, 120, 198] as $cx)
                <rect x="{{ $cx - 24 }}" y="46" width="48" height="22" rx="3" fill="#0f172a"/>
                <rect x="{{ $cx - 18 }}" y="76" width="36" height="4" rx="2" fill="#d1d5db"/>
            @endforeach
            @break
        @case('stats:gradient')
            <defs>
                <linearGradient id="statsGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="#0284c7"/>
                    <stop offset="100%" stop-color="#7c3aed"/>
                </linearGradient>
            </defs>
            <rect x="14" y="20" width="212" height="100" rx="8" fill="url(#statsGrad)"/>
            @foreach ([42, 120, 198] as $cx)
                <rect x="{{ $cx - 24 }}" y="50" width="48" height="22" rx="3" fill="#ffffff" opacity="0.95"/>
                <rect x="{{ $cx - 18 }}" y="80" width="36" height="4" rx="2" fill="#ffffff" opacity="0.7"/>
            @endforeach
            @break

        {{-- ── Map ──────────────────────────────────────────────── --}}
        @case('map:split')
            <rect x="14" y="20" width="120" height="100" rx="6" fill="#e5e7eb"/>
            <path d="M70 50c-9 0-16 7-16 16 0 12 16 28 16 28s16-16 16-28c0-9-7-16-16-16z" fill="#0284c7"/>
            <circle cx="70" cy="66" r="6" fill="#fff"/>
            <rect x="146" y="32" width="80" height="76" rx="6" fill="#fff" stroke="#e5e7eb"/>
            <rect x="158" y="46" width="56" height="6" rx="2" fill="#1f2937"/>
            <rect x="158" y="60" width="48" height="4" rx="2" fill="#9ca3af"/>
            <rect x="158" y="70" width="56" height="4" rx="2" fill="#9ca3af"/>
            <rect x="158" y="86" width="40" height="10" rx="3" fill="#0284c7"/>
            @break
        @case('map:full')
            <rect x="14" y="14" width="212" height="84" rx="6" fill="#e5e7eb"/>
            <path d="M120 36c-9 0-16 7-16 16 0 12 16 28 16 28s16-16 16-28c0-9-7-16-16-16z" fill="#0284c7"/>
            <circle cx="120" cy="52" r="6" fill="#fff"/>
            <rect x="60" y="108" width="120" height="6" rx="2" fill="#1f2937"/>
            <rect x="76" y="120" width="88" height="4" rx="2" fill="#9ca3af"/>
            @break
        @case('map:card')
            <rect x="14" y="14" width="212" height="112" rx="8" fill="#f8fafc"/>
            <rect x="34" y="32" width="172" height="76" rx="8" fill="#e5e7eb"/>
            <path d="M120 50c-9 0-16 7-16 16 0 12 16 28 16 28s16-16 16-28c0-9-7-16-16-16z" fill="#0284c7"/>
            <circle cx="120" cy="66" r="6" fill="#fff"/>
            @break
        @case('map:bordered')
            <rect x="14" y="14" width="212" height="112" rx="8" fill="#0284c7"/>
            <rect x="22" y="22" width="196" height="96" rx="6" fill="#e5e7eb"/>
            <path d="M120 50c-9 0-16 7-16 16 0 12 16 28 16 28s16-16 16-28c0-9-7-16-16-16z" fill="#0284c7"/>
            <circle cx="120" cy="66" r="6" fill="#fff"/>
            @break

        {{-- ── Blog Posts ───────────────────────────────────────── --}}
        @case('blog_posts:cards')
            @foreach ([14, 88, 162] as $x)
                <rect x="{{ $x }}" y="20" width="64" height="100" rx="6" fill="#fff" stroke="#e5e7eb"/>
                <rect x="{{ $x + 6 }}" y="26" width="52" height="34" rx="3" fill="#9ca3af"/>
                <rect x="{{ $x + 6 }}" y="68" width="40" height="6" rx="2" fill="#1f2937"/>
                <rect x="{{ $x + 6 }}" y="80" width="48" height="4" rx="2" fill="#d1d5db"/>
                <rect x="{{ $x + 6 }}" y="88" width="36" height="4" rx="2" fill="#d1d5db"/>
                <rect x="{{ $x + 6 }}" y="102" width="22" height="6" rx="2" fill="#0284c7"/>
            @endforeach
            @break
        @case('blog_posts:list')
            @foreach ([22, 60, 98] as $y)
                <rect x="14" y="{{ $y }}" width="56" height="32" rx="3" fill="#9ca3af"/>
                <rect x="80" y="{{ $y }}" width="120" height="6" rx="2" fill="#1f2937"/>
                <rect x="80" y="{{ $y + 12 }}" width="140" height="4" rx="2" fill="#d1d5db"/>
                <rect x="80" y="{{ $y + 20 }}" width="100" height="4" rx="2" fill="#d1d5db"/>
            @endforeach
            @break
        @case('blog_posts:featured')
            <rect x="14" y="14" width="212" height="60" rx="6" fill="#9ca3af"/>
            <rect x="14" y="14" width="212" height="60" rx="6" fill="#0f172a" opacity="0.4"/>
            <rect x="30" y="36" width="120" height="10" rx="2" fill="#fff"/>
            <rect x="30" y="50" width="80" height="6" rx="2" fill="#fff" opacity="0.8"/>
            @foreach ([14, 88, 162] as $x)
                <rect x="{{ $x }}" y="84" width="64" height="40" rx="6" fill="#fff" stroke="#e5e7eb"/>
                <rect x="{{ $x + 6 }}" y="90" width="52" height="14" rx="2" fill="#9ca3af"/>
                <rect x="{{ $x + 6 }}" y="110" width="40" height="6" rx="2" fill="#1f2937"/>
            @endforeach
            @break
        @case('blog_posts:minimal')
            @foreach ([28, 56, 84, 112] as $y)
                <line x1="14" y1="{{ $y + 12 }}" x2="226" y2="{{ $y + 12 }}" stroke="#e5e7eb"/>
                <rect x="22" y="{{ $y }}" width="40" height="4" rx="2" fill="#0284c7"/>
                <rect x="80" y="{{ $y }}" width="120" height="6" rx="2" fill="#1f2937"/>
                <rect x="200" y="{{ $y + 1 }}" width="20" height="4" rx="2" fill="#9ca3af"/>
            @endforeach
            @break

        {{-- ── Blog Carousel ────────────────────────────────────── --}}
        @case('blog_carousel:cinematic')
            <rect x="14" y="14" width="212" height="112" rx="8" fill="#9ca3af"/>
            <rect x="14" y="14" width="212" height="112" rx="8" fill="#0f172a" opacity="0.4"/>
            <rect x="34" y="74" width="40" height="6" rx="2" fill="#0284c7"/>
            <rect x="34" y="86" width="120" height="10" rx="3" fill="#fff" opacity="0.95"/>
            <rect x="34" y="102" width="60" height="8" rx="3" fill="#fff"/>
            @break
        @case('blog_carousel:split')
            <rect x="14" y="20" width="106" height="100" rx="8" fill="#9ca3af"/>
            <rect x="120" y="20" width="106" height="100" rx="8" fill="#fff" stroke="#e5e7eb"/>
            <rect x="132" y="36" width="36" height="6" rx="2" fill="#0284c7"/>
            <rect x="132" y="50" width="80" height="10" rx="3" fill="#1f2937"/>
            <rect x="132" y="68" width="80" height="4" rx="2" fill="#d1d5db"/>
            <rect x="132" y="76" width="60" height="4" rx="2" fill="#d1d5db"/>
            <rect x="132" y="92" width="50" height="10" rx="3" fill="#0284c7"/>
            @break
        @case('blog_carousel:card')
            <rect x="34" y="20" width="172" height="100" rx="10" fill="#fff" stroke="#e5e7eb"/>
            <rect x="42" y="28" width="156" height="48" rx="6" fill="#9ca3af"/>
            <rect x="50" y="84" width="40" height="5" rx="2" fill="#0284c7"/>
            <rect x="50" y="94" width="120" height="8" rx="2" fill="#1f2937"/>
            <rect x="50" y="106" width="80" height="4" rx="2" fill="#d1d5db"/>
            @break
        @case('blog_carousel:editorial')
            <rect x="14" y="14" width="212" height="112" rx="8" fill="#fff" stroke="#e5e7eb"/>
            <rect x="34" y="34" width="36" height="5" rx="2" fill="#0284c7"/>
            <text x="34" y="60" fill="#1f2937" font-size="20" font-family="Georgia, serif" font-weight="bold">Aa</text>
            <rect x="34" y="68" width="100" height="8" rx="2" fill="#1f2937"/>
            <rect x="34" y="80" width="100" height="4" rx="2" fill="#9ca3af"/>
            <rect x="34" y="100" width="50" height="3" rx="1.5" fill="#0284c7"/>
            <rect x="160" y="30" width="50" height="80" rx="6" fill="#9ca3af"/>
            @break

        {{-- ── Footer ───────────────────────────────────────────── --}}
        @case('footer:columns')
            <rect x="14" y="20" width="212" height="106" rx="6" fill="#0f172a"/>
            <rect x="26" y="32" width="36" height="6" rx="2" fill="#cbd5e1"/>
            <rect x="26" y="44" width="56" height="4" rx="2" fill="#64748b"/>
            <rect x="26" y="52" width="48" height="4" rx="2" fill="#64748b"/>
            @foreach ([90, 140, 190] as $col)
                <rect x="{{ $col }}" y="32" width="36" height="5" rx="2" fill="#cbd5e1"/>
                <rect x="{{ $col }}" y="46" width="40" height="3" rx="1.5" fill="#64748b"/>
                <rect x="{{ $col }}" y="54" width="30" height="3" rx="1.5" fill="#64748b"/>
                <rect x="{{ $col }}" y="62" width="36" height="3" rx="1.5" fill="#64748b"/>
            @endforeach
            <line x1="26" y1="98" x2="214" y2="98" stroke="#475569"/>
            <rect x="26" y="106" width="60" height="4" rx="2" fill="#475569"/>
            @break
        @case('footer:minimal')
            <rect x="14" y="58" width="212" height="36" rx="6" fill="#0f172a"/>
            <rect x="26" y="72" width="40" height="6" rx="2" fill="#cbd5e1"/>
            <circle cx="186" cy="76" r="3" fill="#cbd5e1"/>
            <circle cx="200" cy="76" r="3" fill="#cbd5e1"/>
            <circle cx="214" cy="76" r="3" fill="#cbd5e1"/>
            <rect x="14" y="100" width="212" height="14" rx="0" fill="#1f2937"/>
            <rect x="80" y="106" width="80" height="4" rx="2" fill="#475569"/>
            @break
        @case('footer:centered')
            <rect x="14" y="14" width="212" height="112" rx="6" fill="#0f172a"/>
            <rect x="92" y="34" width="56" height="8" rx="2" fill="#cbd5e1"/>
            <rect x="60" y="50" width="120" height="4" rx="2" fill="#64748b"/>
            <circle cx="100" cy="74" r="3" fill="#cbd5e1"/>
            <circle cx="114" cy="74" r="3" fill="#cbd5e1"/>
            <circle cx="128" cy="74" r="3" fill="#cbd5e1"/>
            <circle cx="142" cy="74" r="3" fill="#cbd5e1"/>
            <rect x="56" y="92" width="20" height="4" rx="2" fill="#cbd5e1"/>
            <rect x="84" y="92" width="20" height="4" rx="2" fill="#cbd5e1"/>
            <rect x="112" y="92" width="20" height="4" rx="2" fill="#cbd5e1"/>
            <rect x="140" y="92" width="20" height="4" rx="2" fill="#cbd5e1"/>
            <rect x="168" y="92" width="20" height="4" rx="2" fill="#cbd5e1"/>
            <line x1="40" y1="108" x2="200" y2="108" stroke="#475569"/>
            <rect x="92" y="116" width="56" height="3" rx="1.5" fill="#475569"/>
            @break

        {{-- ── Table ────────────────────────────────────────────── --}}
        @case('table:simple')
            <rect x="14" y="32" width="212" height="14" rx="3" fill="#0f172a"/>
            <rect x="14" y="50" width="212" height="14" fill="#fff" stroke="#e5e7eb"/>
            <rect x="14" y="66" width="212" height="14" fill="#fff" stroke="#e5e7eb"/>
            <rect x="14" y="82" width="212" height="14" fill="#fff" stroke="#e5e7eb"/>
            <rect x="14" y="98" width="212" height="14" fill="#f1f5f9" stroke="#e5e7eb"/>
            @break
        @case('table:striped')
            <rect x="14" y="32" width="212" height="14" rx="3" fill="#0f172a"/>
            <rect x="14" y="50" width="212" height="14" fill="#ffffff"/>
            <rect x="14" y="66" width="212" height="14" fill="#f9fafb"/>
            <rect x="14" y="82" width="212" height="14" fill="#ffffff"/>
            <rect x="14" y="98" width="212" height="14" fill="#f9fafb"/>
            @break
        @case('table:bordered')
            <rect x="14" y="32" width="212" height="14" fill="#0f172a"/>
            @foreach ([50, 66, 82, 98] as $y)
                <rect x="14" y="{{ $y }}" width="212" height="14" fill="#fff" stroke="#9ca3af"/>
            @endforeach
            <line x1="80"  y1="32" x2="80"  y2="112" stroke="#9ca3af"/>
            <line x1="160" y1="32" x2="160" y2="112" stroke="#9ca3af"/>
            @break
        @case('table:minimal')
            <rect x="14" y="36" width="60" height="6" rx="2" fill="#1f2937"/>
            <rect x="100" y="36" width="60" height="6" rx="2" fill="#1f2937"/>
            <rect x="184" y="36" width="36" height="6" rx="2" fill="#1f2937"/>
            @foreach ([56, 76, 96, 116] as $y)
                <line x1="14" y1="{{ $y }}" x2="226" y2="{{ $y }}" stroke="#e5e7eb"/>
                <rect x="14" y="{{ $y - 8 }}" width="50" height="4" rx="2" fill="#9ca3af"/>
                <rect x="100" y="{{ $y - 8 }}" width="40" height="4" rx="2" fill="#9ca3af"/>
                <rect x="184" y="{{ $y - 8 }}" width="36" height="4" rx="2" fill="#9ca3af"/>
            @endforeach
            @break

        {{-- ── Fallback ─────────────────────────────────────────── --}}
        @default
            <rect x="20" y="20" width="200" height="100" rx="8" fill="#e5e7eb"/>
            <rect x="60" y="58" width="120" height="6" rx="2" fill="#9ca3af"/>
            <rect x="84" y="72" width="72" height="4" rx="2" fill="#cbd5e1"/>
    @endswitch
</svg>
