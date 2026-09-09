@props(['settings', 'transparent' => false])

@php
    $bookingLabel = $settings['booking_cta_label'] ?? 'ЗАБРОНИРОВАТЬ';
    $linkClass = $transparent
        ? 'text-white/95 hover:text-white'
        : 'text-ink/90 hover:text-plum';
    $brandClass = $transparent
        ? 'text-white drop-shadow'
        : 'text-olive';
@endphp

<header
    x-data="{ open: false }"
    @class([
        'z-40',
        'absolute inset-x-0 top-0' => $transparent,
        'sticky top-0 border-b border-cream-dark/80 bg-cream/95 backdrop-blur' => ! $transparent,
    ])
>
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-4 py-5 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" @class(['font-display text-xl font-bold tracking-[0.35em]', $brandClass])>
            ТЕТРИ
        </a>

        <nav class="hidden items-center gap-7 text-sm font-medium md:flex">
            <a href="{{ route('home') }}#concept" @class(['transition', $linkClass])>О нас</a>
            <a href="{{ route('home') }}#kids" @class(['transition', $linkClass])>Детская</a>
            <a href="{{ route('menu') }}" @class(['transition', $linkClass])>Меню</a>
            <x-site.book-button
                source="nav-banket"
                :label="'Банкет'"
                :class="'bg-transparent p-0 text-sm font-medium shadow-none hover:bg-transparent '.$linkClass"
            />
            <a href="{{ route('home') }}#contacts" @class(['transition', $linkClass])>Контакты</a>
        </nav>

        <div class="flex items-center gap-3">
            <x-site.book-button
                source="header"
                :label="$bookingLabel"
                class="rounded-full bg-plum px-5 py-2.5 text-xs font-semibold tracking-wide text-white transition hover:bg-plum-dark sm:text-sm"
            />

            <button
                type="button"
                class="inline-flex h-10 w-10 items-center justify-center rounded-full border md:hidden"
                @class([
                    'border-white/40 text-white' => $transparent,
                    'border-ink/20 text-ink' => ! $transparent,
                ])
                @click="open = !open"
                :aria-expanded="open.toString()"
                aria-label="Меню"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path x-show="!open" d="M4 7h16M4 12h16M4 17h16" />
                    <path x-cloak x-show="open" d="M6 6l12 12M18 6L6 18" />
                </svg>
            </button>
        </div>
    </div>

    <div
        x-cloak
        x-show="open"
        x-transition
        class="border-t border-cream-dark/60 bg-cream px-4 py-4 md:hidden"
    >
        <nav class="flex flex-col gap-3 text-sm font-medium text-ink">
            <a href="{{ route('home') }}#concept" @click="open = false">О нас</a>
            <a href="{{ route('home') }}#kids" @click="open = false">Детская</a>
            <a href="{{ route('menu') }}" @click="open = false">Меню</a>
            <x-site.book-button
                source="nav-banket-mobile"
                :label="'Банкет'"
                class="bg-transparent p-0 text-left text-sm font-medium text-ink shadow-none hover:bg-transparent"
                @click="open = false"
            />
            <a href="{{ route('home') }}#contacts" @click="open = false">Контакты</a>
        </nav>
    </div>
</header>
