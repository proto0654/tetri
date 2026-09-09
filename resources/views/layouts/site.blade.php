<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'ТЕТРИ — семейное кафе')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Literata:opsz,wght@7..72,500;7..72,700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>[x-cloak]{display:none !important}</style>
</head>
<body class="min-h-screen font-sans">
    @php($settings = $settings ?? app(\App\Settings\SiteSettings::class)->all())

    <x-site.header :settings="$settings" :transparent="Request::routeIs('home')" />

    <main>
        @yield('content')
    </main>

    <x-site.footer :settings="$settings" />

    <livewire:booking-modal />

    @livewireScripts
</body>
</html>
