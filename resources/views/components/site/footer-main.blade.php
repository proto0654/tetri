@props(['settings'])

<div {{ $attributes->class(['grid gap-10 sm:grid-cols-2 lg:grid-cols-3 lg:gap-x-8']) }}>
    <div>
        <p class="text-xs uppercase tracking-wide text-cream/70" data-entrance-fade>
            <span aria-hidden="true">◇</span> НАВИГАЦИЯ
        </p>
        <nav class="mt-5 flex flex-col gap-2 text-sm text-cream/85" data-entrance-list>
            @foreach ($settings['nav_links'] ?? [] as $item)
                <x-site.nav-item :item="$item" variant="footer" />
            @endforeach
        </nav>
    </div>

    <div>
        <p class="text-xs uppercase tracking-wide text-cream/70" data-entrance-fade>
            <span aria-hidden="true">◇</span> МЫ В СЕТИ
        </p>
        <div class="mt-5 flex flex-wrap gap-5" data-entrance-list>
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
                    <span class="inline-flex h-11 w-11 items-center justify-center">
                        <x-site.icon :name="$icon" :custom="$link['custom_icon'] ?? null" class="h-5 w-5" />
                    </span>
                    <span class="text-[0.65rem] uppercase tracking-wide text-cream/70">{{ $label }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <div class="sm:col-span-2 lg:col-span-1">
        <div data-entrance-fade>
            <x-site.logo
                :path="$settings['logo'] ?? null"
                class="h-10 w-auto object-contain sm:h-12"
                text-class="font-display text-3xl font-normal tracking-[0.2em]"
            />
        </div>
        <p class="mt-4 max-w-sm text-sm leading-relaxed text-cream/85" data-entrance-fade>
            @typo($settings['footer_about'] ?? '')
        </p>
    </div>
</div>
