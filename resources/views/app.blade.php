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
