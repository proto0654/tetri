<section id="contacts" class="bg-cream px-4 py-20 sm:px-6 lg:px-8">
    <div class="mx-auto grid max-w-7xl gap-10 lg:grid-cols-2 lg:items-center">
        <div class="overflow-hidden rounded-[2rem] bg-cream-dark shadow-sm">
            @if (! empty($settings['map_embed_url']))
                <iframe
                    src="{{ $settings['map_embed_url'] }}"
                    class="aspect-square w-full border-0 lg:aspect-[4/3]"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    title="Карта ТЕТРИ"
                ></iframe>
            @else
                <div class="flex aspect-square items-center justify-center text-muted lg:aspect-[4/3]">Карта</div>
            @endif
        </div>

        <div>
            <h2 class="font-display text-3xl font-bold text-olive sm:text-4xl lg:text-5xl">
                @typo($settings['contacts_title'] ?? 'МЫ В СИМФЕРОПОЛЕ')
            </h2>
            <dl class="mt-8 space-y-4 text-base text-ink">
                <div>
                    <dt class="text-sm uppercase tracking-wide text-muted">Адрес</dt>
                    <dd class="mt-1">@typo($settings['address'] ?? '')</dd>
                </div>
                <div>
                    <dt class="text-sm uppercase tracking-wide text-muted">Телефон</dt>
                    <dd class="mt-1 space-y-1">
                        @foreach ($settings['phones'] ?? [] as $phone)
                            <a href="tel:{{ preg_replace('/[^\d+]/', '', $phone['number'] ?? '') }}" class="block hover:text-plum">
                                {{ $phone['number'] ?? '' }}
                            </a>
                        @endforeach
                    </dd>
                </div>
                <div>
                    <dt class="text-sm uppercase tracking-wide text-muted">Часы работы</dt>
                    <dd class="mt-1">@typo($settings['working_hours'] ?? '')</dd>
                </div>
            </dl>
        </div>
    </div>
</section>
