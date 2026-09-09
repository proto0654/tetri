<section id="concept" class="bg-surface px-4 py-20 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-5xl">
        <x-site.mark
            :text="$settings['concept_eyebrow'] ?? null"
            ruled
            class="uppercase"
        />

        <h2 class="mt-5 max-w-4xl font-display text-3xl font-bold leading-tight text-olive sm:text-4xl lg:text-5xl">
            {{ $settings['concept_title'] ?? '' }}
        </h2>

        <div class="mt-10 grid gap-8 sm:grid-cols-[minmax(10rem,0.35fr)_minmax(0,1fr)] sm:items-start">
            <x-site.mark
                :text="$settings['concept_aside'] ?? null"
                class="text-sm text-ink sm:pt-1"
            />

            <div>
                <p class="text-sm uppercase leading-relaxed tracking-wide text-ink sm:text-base">
                    {{ $settings['concept_description'] ?? '' }}
                </p>

                <x-site.book-button
                    source="concept"
                    :label="$settings['concept_cta_label'] ?? ($settings['booking_cta_label'] ?? 'БРОНИРОВАНИЕ')"
                    class="mt-8 inline-flex rounded-full bg-plum px-6 py-3 text-sm font-semibold tracking-wide text-white transition hover:bg-plum-dark"
                />
            </div>
        </div>
    </div>
</section>
