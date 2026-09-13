@props(['settings', 'transparent' => false])

@php
    $bookingLabel = $settings['booking_cta_label'] ?? 'ЗАБРОНИРОВАТЬ';
    $linkClass = 'text-ink/90 transition hover:text-plum';
    $navLinks = $settings['nav_links'] ?? [];
@endphp

<header
    x-data="{ open: false }"
    data-header-entrance
    data-header-variant="{{ $transparent ? 'pill' : 'bar' }}"
    data-state="pending"
    @class([
        'z-40',
        'absolute inset-x-0 top-0' => $transparent,
        'sticky top-0 bg-cream' => ! $transparent,
    ])
>
    <div
        @class([
            'px-4 sm:px-6 lg:px-8',
            'mx-auto max-w-7xl pt-4' => $transparent,
        ])
    >
        <div
            data-header-shell
            @class([
                'flex items-center justify-between gap-4 py-2.5 sm:gap-6',
                'mx-auto max-w-7xl' => ! $transparent,
                'rounded-full bg-cream px-4 sm:px-6' => $transparent,
            ])
        >
            <a href="{{ route('home') }}" class="inline-flex items-center shrink-0" data-header-logo>
                <x-site.logo
                    :path="$settings['logo'] ?? null"
                    class="h-8 w-auto object-contain"
                    text-class="font-display text-xl font-normal tracking-[0.35em] text-olive"
                />
            </a>

            <nav class="hidden items-center gap-7 text-sm font-medium md:flex" data-header-nav>
                @foreach ($navLinks as $item)
                    <x-site.nav-item :item="$item" variant="header" :link-class="$linkClass" />
                @endforeach
            </nav>

            <div class="flex items-center gap-3" data-header-cta>
                <x-site.book-button
                    source="header"
                    :label="$bookingLabel"
                    class="rounded-full bg-olive px-5 py-2.5 text-xs font-semibold tracking-wide text-cream transition hover:bg-olive-deep sm:text-sm"
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
            @class([
                'mt-2 px-4 py-4 md:hidden',
                'mx-auto max-w-7xl' => ! $transparent,
                'rounded-3xl bg-cream' => $transparent,
                'rounded-2xl border border-ink/10 bg-cream-dark' => ! $transparent,
            ])
        >
            <nav class="flex flex-col gap-3 text-sm font-medium text-ink">
                @foreach ($navLinks as $item)
                    <x-site.nav-item :item="$item" variant="header-mobile" />
                @endforeach
            </nav>
        </div>
    </div>
</header>
