<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @php
            use App\Models\Setting;
            $siteName    = Setting::get('site_name', config('app.name', 'MSW4'));
            $seoTitle    = Setting::get('seo_title', $siteName);
            $seoDesc     = Setting::get('seo_description', Setting::get('site_description'));
            $seoKeywords = Setting::get('seo_keywords');
            $ogTitle     = Setting::get('og_title', $seoTitle);
            $ogDesc      = Setting::get('og_description', $seoDesc);
            $ogImage     = Setting::get('og_image') ? asset('storage/' . Setting::get('og_image')) : null;
            $favicon     = Setting::get('favicon') ? asset('storage/' . Setting::get('favicon')) : null;
            $ga          = Setting::get('google_analytics');

            // ── Theme primary color: pulled from the Theme Builder header section.
            // If the user explicitly set "Primary brand color" we use it; otherwise
            // we auto-track the header's background so the legacy orange accents
            // disappear without needing a second config step.
            $themeSections   = json_decode(Setting::get('home_sections') ?? '', true) ?: [];
            $headerSettings  = (collect($themeSections)->firstWhere('type', 'header')['settings'] ?? []);
            $primaryHex      = $headerSettings['primary']
                              ?? $headerSettings['bg']
                              ?? '#0f172a';
        @endphp

        <title inertia>%s | {{ $siteName }}</title>

        {{-- Core SEO --}}
        @if($seoDesc)
        <meta name="description" content="{{ $seoDesc }}">
        @endif
        @if($seoKeywords)
        <meta name="keywords" content="{{ $seoKeywords }}">
        @endif
        <meta name="robots" content="index, follow">

        {{-- Open Graph --}}
        <meta property="og:type"        content="website">
        <meta property="og:site_name"   content="{{ $siteName }}">
        <meta property="og:title"       content="{{ $ogTitle }}">
        @if($ogDesc)
        <meta property="og:description" content="{{ $ogDesc }}">
        @endif
        @if($ogImage)
        <meta property="og:image"       content="{{ $ogImage }}">
        @endif

        {{-- Twitter Card --}}
        <meta name="twitter:card"        content="summary_large_image">
        <meta name="twitter:title"       content="{{ $ogTitle }}">
        @if($ogDesc)
        <meta name="twitter:description" content="{{ $ogDesc }}">
        @endif
        @if($ogImage)
        <meta name="twitter:image"       content="{{ $ogImage }}">
        @endif

        {{-- Favicon --}}
        @if($favicon)
        <link rel="icon"             href="{{ $favicon }}" type="image/png">
        <link rel="apple-touch-icon" href="{{ $favicon }}">
        @else
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="apple-touch-icon" href="/favicon.svg">
        @endif

        {{-- Google Analytics --}}
        @if($ga)
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $ga }}');
        </script>
        @endif

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        {{-- Theme: derive a full palette from the header's primary color and
             remap every orange Tailwind utility used in the legacy blog/page
             templates so the brand color is theme-driven. --}}
        <style>
            :root {
                --primary:     {{ $primaryHex }};
                --primary-50:  color-mix(in srgb, {{ $primaryHex }}  8%, white);
                --primary-100: color-mix(in srgb, {{ $primaryHex }} 16%, white);
                --primary-200: color-mix(in srgb, {{ $primaryHex }} 32%, white);
                --primary-300: color-mix(in srgb, {{ $primaryHex }} 50%, white);
                --primary-400: color-mix(in srgb, {{ $primaryHex }} 70%, white);
                --primary-500: color-mix(in srgb, {{ $primaryHex }} 88%, white);
                --primary-600: {{ $primaryHex }};
                --primary-700: color-mix(in srgb, {{ $primaryHex }} 85%, black);
                --primary-800: color-mix(in srgb, {{ $primaryHex }} 70%, black);
                --primary-900: color-mix(in srgb, {{ $primaryHex }} 55%, black);
            }
            /* color */
            .text-orange-100              { color: var(--primary-100) !important; }
            .text-orange-300              { color: var(--primary-300) !important; }
            .text-orange-400              { color: var(--primary-400) !important; }
            .text-orange-500              { color: var(--primary-500) !important; }
            .text-orange-600              { color: var(--primary-600) !important; }
            .hover\:text-orange-500:hover { color: var(--primary-500) !important; }
            .hover\:text-orange-600:hover { color: var(--primary-600) !important; }
            .group:hover .group-hover\:text-orange-500 { color: var(--primary-500) !important; }
            .group:hover .group-hover\:text-orange-600 { color: var(--primary-600) !important; }
            /* background */
            .bg-orange-50                 { background-color: var(--primary-50)  !important; }
            .bg-orange-400                { background-color: var(--primary-400) !important; }
            .bg-orange-500                { background-color: var(--primary-500) !important; }
            .bg-orange-600                { background-color: var(--primary-600) !important; }
            .hover\:bg-orange-50:hover    { background-color: var(--primary-50)  !important; }
            .hover\:bg-orange-100:hover   { background-color: var(--primary-100) !important; }
            .hover\:bg-orange-500:hover   { background-color: var(--primary-500) !important; }
            .hover\:bg-orange-600:hover   { background-color: var(--primary-600) !important; }
            /* border */
            .border-orange-100            { border-color: var(--primary-100) !important; }
            .border-orange-200            { border-color: var(--primary-200) !important; }
            .border-orange-400            { border-color: var(--primary-400) !important; }
            .border-orange-500            { border-color: var(--primary-500) !important; }
            .hover\:border-orange-200:hover { border-color: var(--primary-200) !important; }
            /* ring (focus rings) */
            .ring-orange-400              { --tw-ring-color: var(--primary-400) !important; }
            .focus-visible\:ring-orange-400:focus-visible { --tw-ring-color: var(--primary-400) !important; }
        </style>

        <!-- Scripts -->
        @routes
        @viteReactRefresh
        @vite(['resources/js/app.tsx', "resources/js/Pages/{$page['component']}.tsx"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
