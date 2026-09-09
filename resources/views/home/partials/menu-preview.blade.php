<section id="menu-preview" class="bg-cream px-4 py-16 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <div class="grid gap-10 lg:grid-cols-[minmax(0,0.9fr)_minmax(0,1.4fr)] lg:items-end">
            <div class="max-w-md">
                <x-site.mark
                    :text="$settings['menu_section_eyebrow'] ?? null"
                    ruled
                    class="uppercase"
                />

                <h2 class="mt-4 font-display text-4xl font-bold text-olive sm:text-5xl">
                    @typo($settings['menu_section_title'] ?? 'МЕНЮ')
                </h2>

                @if (filled($settings['menu_section_description'] ?? null))
                    <p class="mt-4 text-base leading-relaxed text-ink">
                        @typo($settings['menu_section_description'])
                    </p>
                @endif

                <a
                    href="{{ route('menu') }}"
                    class="mt-8 inline-flex w-fit rounded-full bg-plum px-6 py-3 text-sm font-semibold tracking-wide text-white transition hover:bg-plum-dark"
                >
                    @typo($settings['menu_section_cta_label'] ?? 'СМОТРЕТЬ ВСЕ')
                </a>
            </div>

            <div class="min-w-0" data-swiper-root>
                <div class="swiper" data-swiper data-space-between="20">
                    <div class="swiper-wrapper">
                        @foreach ($categories as $category)
                            <div class="swiper-slide !w-56 sm:!w-64">
                                <a href="{{ route('menu') }}" class="group block">
                                    <div class="overflow-hidden rounded-[1.75rem] bg-cream-dark shadow-sm transition group-hover:shadow-md">
                                        <x-media :path="$category->image" :alt="$category->title" class="aspect-[3/4] w-full object-cover transition duration-500 group-hover:scale-105" />
                                    </div>
                                    <p class="mt-4 text-center text-sm font-semibold uppercase tracking-wide text-ink">
                                        @typo($category->title)
                                    </p>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-4 flex gap-3">
                    <button type="button" data-swiper-prev class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-olive text-cream transition hover:bg-olive-deep" aria-label="Назад">
                        ←
                    </button>
                    <button type="button" data-swiper-next class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-olive text-cream transition hover:bg-olive-deep" aria-label="Вперёд">
                        →
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>
