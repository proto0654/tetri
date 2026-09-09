@php
    $mapLatitude = $settings['map_latitude'] ?? null;
    $mapLongitude = $settings['map_longitude'] ?? null;
    $mapMarkerLabel = $settings['map_marker_label'] ?? null;
    $mapEmbedUrl = $settings['map_embed_url'] ?? null;
    $mapSrc = filled($mapEmbedUrl)
        ? $mapEmbedUrl
        : \App\Support\YandexMap::widgetUrl($mapLatitude, $mapLongitude, $mapMarkerLabel);
    $mapRouteUrl = \App\Support\YandexMap::routeUrl($mapLatitude, $mapLongitude);
    $mapTitle = filled($mapMarkerLabel) ? $mapMarkerLabel : 'Карта ТЕТРИ';
@endphp

<section id="contacts" class="overflow-x-clip">
    <x-site.shell>
        <div class="grid lg:grid-cols-3 lg:items-stretch">
            <div class="site-contacts-map-olive-half relative min-h-[16rem] max-lg:mb-8 lg:min-h-0">
                <div class="site-bleed-left z-10 isolate overflow-hidden rounded-[2rem] bg-cream-dark shadow-sm max-lg:relative max-lg:min-h-[16rem] lg:absolute lg:inset-y-0 lg:left-0 lg:rounded-l-none lg:rounded-r-[2rem]">
                    @if (! empty($mapSrc))
                        <iframe
                            src="{{ $mapSrc }}"
                            class="absolute inset-0 h-full w-full border-0 max-lg:min-h-[16rem]"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            title="{{ $mapTitle }}"
                        ></iframe>
                    @else
                        <div class="flex h-full min-h-[16rem] items-center justify-center text-muted lg:absolute lg:inset-0">Карта</div>
                    @endif
                </div>
            </div>

            <div class="relative z-10 flex min-w-0 flex-col lg:col-span-2">
                <div class="site-bleed-right bg-cream">
                    <div class="py-16 sm:py-20 lg:pl-10 lg:pr-[var(--site-shell-pad,2rem)]">
                        <h2 class="font-display text-4xl font-bold text-olive sm:text-5xl lg:text-6xl">
                            @typo($settings['contacts_title'] ?? 'МЫ В СИМФЕРОПОЛЕ')
                        </h2>
                        <div class="mt-8 space-y-2 text-base text-ink">
                            @if (filled($settings['address'] ?? null))
                                <p>@typo($settings['address'])</p>
                            @endif
                            @foreach ($settings['phones'] ?? [] as $phone)
                                <p>
                                    <a href="tel:{{ preg_replace('/[^\d+]/', '', $phone['number'] ?? '') }}" class="hover:text-plum">
                                        {{ $phone['number'] ?? '' }}
                                    </a>
                                </p>
                            @endforeach
                            @if (filled($settings['working_hours'] ?? null))
                                <p>@typo($settings['working_hours'])</p>
                            @endif
                        </div>
                        @if ($mapRouteUrl)
                            <a
                                href="{{ $mapRouteUrl }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="mt-6 inline-flex rounded-full bg-plum px-6 py-3 text-sm font-semibold tracking-wide text-white transition hover:bg-plum-dark"
                            >
                                Проложить маршрут на карте
                            </a>
                        @endif
                    </div>
                </div>

                <div class="flex flex-1 flex-col bg-olive-deep text-cream">
                    <div class="py-14 lg:pl-10">
                        <x-site.footer-main :settings="$settings" />
                    </div>

                    <div class="mt-auto border-t border-cream/15 py-4 text-xs text-cream/60 sm:flex sm:items-center sm:justify-between lg:pl-10">
                        <span>@typo($settings['copyright'] ?? '© ТЕТРИ')</span>
                        <a href="#top" class="mt-2 inline-block hover:text-cream sm:mt-0">Наверх ↑</a>
                    </div>
                </div>
            </div>
        </div>
    </x-site.shell>
</section>
