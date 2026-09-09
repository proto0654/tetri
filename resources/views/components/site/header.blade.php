@props(['settings', 'transparent' => false])

@php
    $bookingLabel = $settings['booking_cta_label'] ?? 'ЗАБРОНИРОВАТЬ';
    $linkClass = 'text-ink/90 transition hover:text-plum';
@endphp

<header
    x-data="{ open: false }"
    @class([
        'z-40',
        'absolute inset-x-0 top-0' => $transparent,
        'sticky top-0' => ! $transparent,
    ])
>
    <div class="mx-auto max-w-7xl px-4 pt-4 sm:px-6 lg:px-8" @class(['pb-4' => ! $transparent])>
        <div class="flex items-center justify-between gap-4 rounded-full bg-cream px-4 py-2.5 shadow-sm sm:gap-6 sm:px-6">
            <a href="{{ route('home') }}" class="font-display text-xl font-bold tracking-[0.35em] text-olive">
                ТЕТРИ
            </a>

            <nav class="hidden items-center gap-7 text-sm font-medium md:flex">
                <a href="{{ route('home') }}#concept" class="{{ $linkClass }}">О нас</a>
                <a href="{{ route('home') }}#kids" class="{{ $linkClass }}">Детская</a>
                <a href="{{ route('menu') }}" class="{{ $linkClass }}">Меню</a>
                <x-site.book-button
                    source="nav-banket"
                    :label="'Банкет'"
                    :class="'bg-transparent p-0 text-sm font-medium shadow-none hover:bg-transparent '.$linkClass"
                />
                <a href="{{ route('home') }}#contacts" class="{{ $linkClass }}">Контакты</a>
            </nav>

            <div class="flex items-center gap-3">
                <x-site.book-button
                    source="header"
                    :label="$bookingLabel"
                    class="rounded-full bg-plum px-5 py-2.5 text-xs font-semibold tracking-wide text-white transition hover:bg-plum-dark sm:text-sm"
                />

                <button
                    type="button"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-ink/20 text-ink md:hidden"
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
            class="mt-2 rounded-3xl bg-cream px-4 py-4 shadow-sm md:hidden"
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
    </div>
</header>
