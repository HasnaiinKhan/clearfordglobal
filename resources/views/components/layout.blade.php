<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO --}}
    <title>{{ config('clearford.seo.title') }}</title>
    <meta name="description" content="{{ config('clearford.seo.description') }}">
    <link rel="canonical" href="{{ config('clearford.company.url') }}">

    {{-- Open Graph --}}
    <meta property="og:type"        content="website">
    <meta property="og:url"         content="{{ config('clearford.company.url') }}">
    <meta property="og:title"       content="{{ config('clearford.seo.title') }}">
    <meta property="og:description" content="{{ config('clearford.seo.description') }}">
    <meta property="og:image"       content="{{ config('clearford.company.url') . config('clearford.seo.og_image') }}">

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    {{-- Hide Alpine x-cloak elements immediately --}}
    <style>[x-cloak]{display:none!important}</style>

    {{-- Font --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap">

    {{-- Styles --}}
    <link rel="stylesheet" href="/css/app.css">

    {{-- Alpine.js --}}
    @vite(['resources/js/app.js'])

    {{-- JSON-LD --}}
    <script type="application/ld+json">{!! json_encode([
        '@context'     => 'https://schema.org',
        '@type'        => 'Organization',
        'name'         => config('clearford.company.name'),
        'url'          => config('clearford.company.url'),
        'logo'         => config('clearford.company.url').'/logo/Logowithoutbg.png',
        'contactPoint' => [
            '@type'       => 'ContactPoint',
            'contactType' => 'sales',
            'email'       => config('clearford.company.email'),
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}</script>
</head>
<body>
    <a href="#main-content" class="skip-link">Skip to main content</a>

    {{ $slot }}

    <div id="sr-announcer" role="status" aria-live="polite" aria-atomic="true" class="sr-only"></div>

    {{-- Scroll animations --}}
    <script>
    (function () {
        function revealAll() {
            document.querySelectorAll('.animate-on-scroll').forEach(function (el) {
                el.classList.add('is-visible');
            });
        }
        if (!('IntersectionObserver' in window)) { revealAll(); return; }
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.05, rootMargin: '0px 0px 80px 0px' });
        document.querySelectorAll('.animate-on-scroll').forEach(function (el) {
            var rect = el.getBoundingClientRect();
            if (rect.top < window.innerHeight + 80) {
                el.classList.add('is-visible');
            } else {
                io.observe(el);
            }
        });
    })();
    </script>
</body>
</html>