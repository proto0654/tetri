<!DOCTYPE html>
<html lang="ru">
<head>
    @php($settings = $settings ?? app(\App\Settings\SiteSettings::class)->all())
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @if (! empty($settings['block_search_indexing']))
        <meta name="robots" content="noindex, nofollow">
    @endif
    <title>@yield('title', 'ТЕТРИ — семейное кафе')</title>
    <x-site.favicon :path="$settings['favicon'] ?? null" />
    @php($ogImageUrl = \App\Support\PublicMedia::absoluteUrl($settings['og_image'] ?? null))
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', 'ТЕТРИ — семейное кафе')">
    <meta property="og:url" content="{{ url()->current() }}">
    @if (filled($ogImageUrl))
        <meta property="og:image" content="{{ $ogImageUrl }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:image" content="{{ $ogImageUrl }}">
    @endif
    @fonts('display')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>[x-cloak]{display:none !important}</style>
</head>
<body id="top" class="min-h-screen font-sans">
    <x-site.header :settings="$settings" :transparent="Request::routeIs('home')" />

    <main>
        @yield('content')
    </main>

    <x-site.contacts :settings="$settings" />

    <x-site.design-credit :settings="$settings" />

    <livewire:booking-modal />

    @livewireScripts
</body>
</html>
