@props(['settings'])

<div {{ $attributes->class(['grid gap-10 sm:grid-cols-2 lg:grid-cols-3 lg:gap-x-8']) }}>
    <div>
        <p class="text-xs uppercase tracking-wide text-cream/70">
            <span aria-hidden="true">◇</span> НАВИГАЦИЯ
        </p>
        <nav class="mt-5 flex flex-col gap-2 text-sm text-cream/85">
            <a href="{{ route('home') }}#concept" class="hover:text-cream">О нас</a>
            <a href="{{ route('menu') }}" class="hover:text-cream">Меню</a>
            <a href="{{ route('home') }}#kids" class="hover:text-cream">Детская</a>
            <x-site.book-button
                source="footer-banket"
                :label="'Банкет'"
                class="bg-transparent p-0 text-left text-sm text-cream/85 shadow-none hover:bg-transparent hover:text-cream"
            />
            <a href="{{ route('home') }}#contacts" class="hover:text-cream">Контакты</a>
        </nav>
    </div>

    <div>
        <p class="text-xs uppercase tracking-wide text-cream/70">
            <span aria-hidden="true">◇</span> МЫ В СЕТИ
        </p>
        <div class="mt-5 flex flex-wrap gap-5">
            @foreach ($settings['social_links'] ?? [] as $link)
                @php
                    $icon = $link['icon']
                        ?? match ($link['network'] ?? '') {
                            'instagram' => 'heroicon-o-camera',
                            'telegram' => 'heroicon-o-paper-airplane',
                            'vk' => 'heroicon-o-chat-bubble-left-right',
                            'max' => 'heroicon-o-chat-bubble-oval-left-ellipsis',
                            default => 'heroicon-o-link',
                        };
                    $label = $link['text'] ?? ($link['network'] ?? 'Соцсеть');
                @endphp
                <a
                    href="{{ $link['url'] ?? '#' }}"
                    class="inline-flex flex-col items-center gap-2 text-cream transition hover:text-cream"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="{{ $label }}"
                >
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-cream/30">
                        <x-site.icon :name="$icon" :custom="$link['custom_icon'] ?? null" class="h-5 w-5" />
                    </span>
                    <span class="text-[0.65rem] uppercase tracking-wide text-cream/70">{{ $label }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <div class="sm:col-span-2 lg:col-span-1">
        <p class="font-display text-3xl font-bold tracking-[0.2em]">ТЕТРИ</p>
        <p class="mt-4 max-w-sm text-sm leading-relaxed text-cream/85">
            @typo($settings['footer_about'] ?? '')
        </p>
    </div>
</div>
