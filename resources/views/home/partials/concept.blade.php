<section id="concept" class="bg-surface py-20">
    <x-site.shell>
        <div class="site-text-shift-scope">
            <x-site.mark
                :text="$settings['concept_eyebrow'] ?? null"
                ruled
                class="uppercase"
            />

            <h2 class="site-text-shift mt-4 font-display text-4xl font-bold leading-tight text-olive sm:text-5xl lg:text-6xl">
                @typo($settings['concept_title'] ?? '')
            </h2>
        </div>

        <div class="mt-10 grid gap-8 lg:grid-cols-3 lg:gap-x-10">
            <div class="flex flex-col lg:col-span-1 lg:h-full">
                <x-site.mark
                    :text="$settings['concept_aside'] ?? null"
                    class="text-sm text-ink lg:mt-auto"
                />
            </div>

            <div class="lg:col-span-2">
                <p class="max-w-2xl text-sm uppercase leading-relaxed tracking-wide text-ink sm:text-base">
                    @typo($settings['concept_description'] ?? '')
                </p>

                <x-site.book-button
                    source="concept"
                    :label="$settings['concept_cta_label'] ?? ($settings['booking_cta_label'] ?? 'БРОНИРОВАНИЕ')"
                    class="mt-8 inline-flex rounded-full bg-plum px-6 py-3 text-sm font-semibold tracking-wide text-white transition hover:bg-plum-dark"
                />
            </div>
        </div>
    </x-site.shell>
</section>
