<section id="menu-preview" class="overflow-x-clip bg-cream py-16" data-entrance data-state="pending">
    <x-site.shell>
        <div class="grid gap-10 lg:grid-cols-3 lg:items-stretch lg:gap-x-10" data-swiper-root>
            <div class="max-w-md lg:col-span-1 lg:flex lg:h-full lg:flex-col">
                <div>
                    <x-site.mark
                        :text="$settings['menu_section_eyebrow'] ?? null"
                        ruled
                        class="uppercase"
                        data-entrance-fade
                    />

                    <h2 class="mt-4 font-display text-4xl font-normal text-olive sm:text-5xl lg:text-6xl" data-entrance-title>
                        @typoBr($settings['menu_section_title'] ?? 'МЕНЮ')
                    </h2>

                    @if (filled($settings['menu_section_description'] ?? null))
                        <p class="mt-4 text-base leading-relaxed text-ink" data-entrance-fade>
                            @typo($settings['menu_section_description'])
                        </p>
                    @endif
                </div>

                <a
                    href="{{ route('menu') }}"
                    class="mt-8 inline-flex w-fit rounded-full bg-plum px-6 py-3 text-sm font-semibold tracking-wide text-white transition hover:bg-plum-dark lg:mt-auto"
                    data-entrance-cta
                >
                    @typo($settings['menu_section_cta_label'] ?? 'СМОТРЕТЬ ВСЕ')
                </a>
            </div>

            <div class="min-w-0 lg:col-span-2">
                <div class="site-bleed-right">
                    <div class="swiper" data-swiper data-loop data-space-between="20">
                        <div class="swiper-wrapper">
                            @foreach ($categories as $category)
                                <div class="swiper-slide !w-56 sm:!w-64">
                                    <a
                                        href="{{ route('menu.category', $category) }}"
                                        class="group relative block overflow-hidden rounded-[1.75rem] bg-cream-dark shadow-sm transition-shadow group-hover:shadow-md"
                                        data-entrance-media="fade"
                                    >
                                        <x-media :path="$category->image" :alt="$category->title" class="aspect-[3/4] w-full object-cover transition-transform duration-500 group-hover:scale-105" />
                                        <div class="pointer-events-none absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/60 to-transparent px-4 pb-4 pt-12">
                                            <p class="text-sm font-semibold uppercase tracking-wide text-white">
                                                @typo($category->title)
                                            </p>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex gap-3" data-entrance-cta>
                    <button type="button" data-swiper-prev class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-olive text-cream transition hover:bg-olive-deep" aria-label="Назад">
                        ←
                    </button>
                    <button type="button" data-swiper-next class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-olive text-cream transition hover:bg-olive-deep" aria-label="Вперёд">
                        →
                    </button>
                </div>
            </div>
        </div>
    </x-site.shell>
</section>
