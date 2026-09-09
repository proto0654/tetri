<section
    id="stories"
    class="overflow-x-clip bg-cream py-16"
    x-data="{
        play(event) {
            const video = event.currentTarget.querySelector('video')
            if (! video) return
            if (video.paused) {
                video.play()
            } else {
                video.pause()
            }
        }
    }"
>
    <x-site.shell>
        <div data-swiper-root>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <h2 class="font-display text-4xl font-bold text-olive sm:text-5xl lg:text-6xl">
                    @typoBr($settings['stories_section_title'] ?? 'СТОРИСЫ из MAX')
                </h2>

                <x-site.mark
                    :text="$settings['stories_section_aside'] ?? null"
                    :note="$settings['stories_section_aside_note'] ?? null"
                    class="max-w-xs sm:items-end sm:text-right"
                />
            </div>

            <div class="site-bleed-x mt-10">
                <div
                    class="swiper"
                    data-swiper
                    data-loop
                    data-space-between="16"
                    data-slides-offset-before="shell"
                >
                    <div class="swiper-wrapper">
                        @foreach ($stories as $story)
                            <div class="swiper-slide !w-56 sm:!w-64">
                                <article>
                                    <button
                                        type="button"
                                        class="w-full overflow-hidden rounded-[1.75rem] bg-cream-dark text-left shadow-sm"
                                        @click="play($event)"
                                        aria-label="{{ $story->title ? 'Смотреть '.$story->title : 'Смотреть сторис' }}"
                                    >
                                        @php
                                            $videoUrl = \App\Support\PublicMedia::url($story->video_path);
                                            $posterUrl = \App\Support\PublicMedia::url($story->preview_image);
                                        @endphp
                                        @if ($videoUrl)
                                            <video
                                                class="aspect-[3/4] w-full object-cover"
                                                muted
                                                loop
                                                playsinline
                                                preload="metadata"
                                                @if ($posterUrl) poster="{{ $posterUrl }}" @endif
                                            >
                                                <source src="{{ $videoUrl }}" type="video/mp4">
                                            </video>
                                        @else
                                            <x-media :path="$story->preview_image" :alt="$story->title ?? 'Сторис'" class="aspect-[3/4] w-full object-cover" />
                                        @endif
                                    </button>
                                </article>
                            </div>
                        @endforeach
                    </div>
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
    </x-site.shell>
</section>
