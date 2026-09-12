<section id="concept" class="bg-surface py-20" data-entrance data-state="pending">
    <x-site.shell>
        <div class="site-text-shift-scope">
            <x-site.mark
                :text="$settings['concept_eyebrow'] ?? null"
                ruled
                class="uppercase"
                data-entrance-fade
            />

            <h2 class="site-text-shift mt-4 font-display text-4xl font-normal leading-tight text-olive sm:text-5xl lg:text-6xl" data-entrance-title>
                @typoBr($settings['concept_title'] ?? '')
            </h2>
        </div>

        <div class="mt-10 grid gap-8 lg:grid-cols-3 lg:gap-x-10">
            <div class="flex flex-col lg:col-span-1 lg:h-full">
                <x-site.mark
                    :text="$settings['concept_aside'] ?? null"
                    class="text-sm text-ink lg:mt-auto"
                    data-entrance-fade
                />
            </div>

            <div class="lg:col-span-2">
                <p class="site-info" data-entrance-info>
                    @typo($settings['concept_description'] ?? '')
                </p>

                <x-site.book-button
                    source="concept"
                    :label="$settings['concept_cta_label'] ?? ($settings['booking_cta_label'] ?? 'БРОНИРОВАНИЕ')"
                    class="mt-8 inline-flex rounded-full bg-plum px-6 py-3 text-sm font-semibold tracking-wide text-white transition hover:bg-plum-dark"
                    data-entrance-cta
                />
            </div>
        </div>
    </x-site.shell>
</section>
