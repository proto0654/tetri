<section
    id="stories"
    class="overflow-x-clip bg-cream py-16 lg:relative lg:z-[3]"
    data-entrance
    data-state="pending"
>
    <x-site.shell>
        <div data-swiper-root>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <h2 class="font-display text-4xl font-normal text-olive sm:text-5xl lg:text-6xl" data-entrance-title>
                    @typoBr($settings['stories_section_title'] ?? 'СТОРИСЫ из MAX')
                </h2>

                <x-site.mark
                    :text="$settings['stories_section_aside'] ?? null"
                    :note="$settings['stories_section_aside_note'] ?? null"
                    class="max-w-xs sm:items-end sm:text-right"
                    data-entrance-fade
                />
            </div>

            <div class="site-bleed-x mt-10">
                <div
                    class="swiper"
                    data-swiper
                    data-space-between="16"
                    data-slides-offset-before="shell"
                >
                    <div class="swiper-wrapper">
                        @foreach ($stories as $story)
                            <div class="swiper-slide !w-56 sm:!w-64">
                                <article>
                                    <div
                                        role="button"
                                        tabindex="0"
                                        class="w-full cursor-pointer overflow-hidden rounded-[1.75rem] bg-cream-dark text-left shadow-sm select-none"
                                        data-entrance-media="fade"
                                        data-story-open
                                        data-story-id="{{ $story->id }}"
                                        data-story-index="{{ $loop->index }}"
                                        aria-label="{{ $story->title ? 'Смотреть '.$story->title : 'Смотреть сторис' }}"
                                    >
                                        @php
                                            $videoUrl = \App\Support\PublicMedia::url($story->video_path);
                                            $posterUrl = \App\Support\PublicMedia::url($story->preview_image);
                                        @endphp
                                        @if ($videoUrl)
                                            <video
                                                class="pointer-events-none aspect-[3/4] w-full object-cover"
                                                muted
                                                loop
                                                playsinline
                                                preload="metadata"
                                                tabindex="-1"
                                                draggable="false"
                                                @if ($posterUrl) poster="{{ $posterUrl }}" @endif
                                            >
                                                <source src="{{ $videoUrl }}" type="video/mp4">
                                            </video>
                                        @else
                                            <x-media :path="$story->preview_image" :alt="$story->title ?? 'Сторис'" class="pointer-events-none aspect-[3/4] w-full object-cover" />
                                        @endif
                                    </div>
                                </article>
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
    </x-site.shell>
</section>

@if ($stories->isNotEmpty())
    <div
        id="story-viewer"
        class="story-viewer"
        data-story-viewer
        hidden
        aria-hidden="true"
    >
        <div class="story-viewer__scrim" data-story-viewer-scrim></div>

        <div class="story-viewer__frame" data-story-viewer-frame>
            <button
                type="button"
                class="story-viewer__back"
                data-story-viewer-close
                aria-label="Назад"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M15 18l-6-6 6-6" />
                </svg>
            </button>

            <div
                class="story-viewer__stage"
                data-story-viewer-stage
                role="dialog"
                aria-modal="true"
                aria-label="Сторисы"
            >
                <div class="swiper story-viewer__swiper" data-story-viewer-swiper>
                    <div class="swiper-wrapper">
                        @foreach ($stories as $story)
                            @php
                                $videoUrl = \App\Support\PublicMedia::url($story->video_path);
                                $posterUrl = \App\Support\PublicMedia::url($story->preview_image);
                            @endphp
                            <div
                                class="swiper-slide story-viewer__slide"
                                data-story-slide
                                data-story-id="{{ $story->id }}"
                            >
                                @if ($videoUrl)
                                    <video
                                        class="story-viewer__media"
                                        playsinline
                                        loop
                                        preload="none"
                                        tabindex="-1"
                                        draggable="false"
                                        @if ($posterUrl) poster="{{ $posterUrl }}" @endif
                                    >
                                        <source data-src="{{ $videoUrl }}" type="video/mp4">
                                    </video>
                                @elseif ($posterUrl)
                                    <img
                                        src="{{ $posterUrl }}"
                                        alt="{{ $story->title ?? 'Сторис' }}"
                                        class="story-viewer__media"
                                        draggable="false"
                                    >
                                @else
                                    <div class="story-viewer__media story-viewer__media--empty bg-cream-dark"></div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="story-viewer__hint" data-story-viewer-hint hidden>
                    <div class="story-viewer__hint-chevrons" aria-hidden="true">
                        <span>↑</span>
                        <span>↓</span>
                    </div>
                    <p class="story-viewer__hint-text" data-story-viewer-hint-text></p>
                </div>
            </div>

            <div class="story-viewer__rail" data-story-viewer-rail>
                <button
                    type="button"
                    class="story-viewer__close"
                    data-story-viewer-close
                    aria-label="Свернуть"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M8 3H5a2 2 0 0 0-2 2v3M16 3h3a2 2 0 0 1 2 2v3M8 21H5a2 2 0 0 1-2-2v-3M16 21h3a2 2 0 0 0 2-2v-3" />
                    </svg>
                </button>

                <div class="story-viewer__nav" data-story-viewer-nav>
                    <button
                        type="button"
                        class="story-viewer__nav-btn"
                        data-story-viewer-prev
                        aria-label="Предыдущий сторис"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M18 15l-6-6-6 6" />
                        </svg>
                    </button>

                    <button
                        type="button"
                        class="story-viewer__nav-btn"
                        data-story-viewer-next
                        aria-label="Следующий сторис"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M6 9l6 6 6-6" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
