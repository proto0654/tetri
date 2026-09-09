<section id="kids" class="bg-cream px-4 py-20 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <x-site.mark
            :text="$settings['kids_eyebrow'] ?? null"
            ruled
            class="uppercase"
        />

        <h2 class="mt-4 max-w-4xl font-display text-3xl font-bold leading-tight text-olive sm:text-4xl lg:text-5xl">
            @typo($settings['kids_title'] ?? '')
        </h2>

        <div class="mt-12 grid gap-12 lg:grid-cols-[0.85fr_1.15fr] lg:items-start">
            <div class="flex h-full flex-col">
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
                    class="mt-auto pt-10 text-sm text-ink"
                />
            </div>

            <div>
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
            </div>
        </div>

        <div class="mt-12 flex justify-center">
            <x-site.book-button
                source="kids"
                :label="$settings['kids_cta_label'] ?? ($settings['booking_cta_label'] ?? 'БРОНИРОВАНИЕ')"
                class="inline-flex rounded-full bg-plum px-6 py-3 text-sm font-semibold tracking-wide text-white transition hover:bg-plum-dark"
            />
        </div>
    </div>
</section>
