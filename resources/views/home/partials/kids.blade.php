<section id="kids" class="bg-cream py-20">
    <x-site.shell>
        <div class="site-text-shift-scope">
            <x-site.mark
                :text="$settings['kids_eyebrow'] ?? null"
                ruled
                class="uppercase"
            />

            <h2 class="site-text-shift mt-4 font-display text-4xl font-bold leading-tight text-olive sm:text-5xl lg:text-6xl">
                @typoBr($settings['kids_title'] ?? '')
            </h2>
        </div>

        <div class="mt-12 grid gap-10 lg:grid-cols-3 lg:gap-x-10 lg:gap-y-8">
            <div class="flex flex-col lg:col-span-1">
                <ul class="space-y-4">
                    @foreach ($settings['kids_benefits'] ?? [] as $benefit)
                        <li class="flex items-start gap-3 text-ink">
                            @php($benefitUrl = $benefit['url'] ?? null)
                            <span class="mt-0.5 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-cream-dark text-olive">
                                @if (filled($benefitUrl))
                                    <a href="{{ $benefitUrl }}" class="inline-flex text-olive">
                                        <x-site.icon :name="$benefit['icon'] ?? null" :custom="$benefit['custom_icon'] ?? null" class="h-4 w-4" />
                                    </a>
                                @else
                                    <x-site.icon :name="$benefit['icon'] ?? null" :custom="$benefit['custom_icon'] ?? null" class="h-4 w-4" />
                                @endif
                            </span>
                            @if (filled($benefitUrl))
                                <a href="{{ $benefitUrl }}" class="pt-1.5 text-base hover:text-plum">@typo($benefit['text'] ?? '')</a>
                            @else
                                <span class="pt-1.5 text-base">@typo($benefit['text'] ?? '')</span>
                            @endif
                        </li>
                    @endforeach
                </ul>

                <x-site.mark
                    :text="$settings['kids_location_note'] ?? null"
                    class="mt-10 text-sm text-ink lg:mt-auto lg:pt-10"
                />
            </div>

            <div class="lg:col-span-2">
                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach ($settings['kids_images'] ?? [] as $image)
                        <x-media :path="$image" alt="" class="aspect-[4/3] w-full rounded-3xl object-cover" />
                    @endforeach
                </div>

                @if (filled($settings['kids_description'] ?? null))
                    <p class="mt-6 max-w-2xl text-sm uppercase leading-relaxed tracking-wide text-ink">
                        @typo($settings['kids_description'])
                    </p>
                @endif

                @if (filled($settings['kids_description_secondary'] ?? null))
                    <p class="mt-4 max-w-2xl text-sm uppercase leading-relaxed tracking-wide text-ink">
                        @typo($settings['kids_description_secondary'])
                    </p>
                @endif

                <x-site.book-button
                    source="kids"
                    :label="$settings['kids_cta_label'] ?? ($settings['booking_cta_label'] ?? 'БРОНИРОВАНИЕ')"
                    class="mt-8 inline-flex rounded-full bg-plum px-6 py-3 text-sm font-semibold tracking-wide text-white transition hover:bg-plum-dark"
                />
            </div>
        </div>
    </x-site.shell>
</section>
