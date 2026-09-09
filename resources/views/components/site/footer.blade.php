@props(['settings'])

<footer class="bg-olive-deep text-cream">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 lg:grid-cols-[1fr_1fr_1.4fr] lg:px-8">
        <div>
            <p class="font-display text-2xl font-bold tracking-[0.3em]">ТЕТРИ</p>
            <nav class="mt-6 flex flex-col gap-2 text-sm text-cream/85">
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
            <p class="text-sm font-semibold uppercase tracking-wider text-cream/70">Соцсети</p>
            <div class="mt-4 flex flex-wrap gap-3">
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
                        class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-cream/30 text-cream transition hover:bg-cream/10"
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="{{ $label }}"
                        title="{{ $label }}"
                    >
                        <x-site.icon :name="$icon" :custom="$link['custom_icon'] ?? null" class="h-5 w-5" />
                    </a>
                @endforeach
            </div>
        </div>

        <div>
            <p class="max-w-md text-sm leading-relaxed text-cream/85">
                {{ $settings['footer_about'] ?? '' }}
            </p>
        </div>
    </div>

    <div class="border-t border-cream/15">
        <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-4 text-xs text-cream/60 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
            <span>{{ $settings['copyright'] ?? '© ТЕТРИ' }}</span>
            @if (! empty($settings['design_credit']))
                <span>{{ $settings['design_credit'] }}</span>
            @endif
        </div>
    </div>
</footer>
