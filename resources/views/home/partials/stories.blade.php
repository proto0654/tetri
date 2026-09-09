<section id="stories" class="bg-cream px-4 py-16 sm:px-6 lg:px-8">
    <div
        class="mx-auto max-w-7xl"
        x-data="{
            scrollBy(dir) {
                this.$refs.track.scrollBy({ left: dir * 200, behavior: 'smooth' })
            },
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
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <h2 class="font-display text-3xl font-bold text-olive sm:text-4xl">
                @typo($settings['stories_section_title'] ?? 'СТОРИСЫ из MAX')
            </h2>

            <x-site.mark
                :text="$settings['stories_section_aside'] ?? null"
                :note="$settings['stories_section_aside_note'] ?? null"
                class="max-w-xs sm:items-end sm:text-right"
            />
        </div>

        <div
            x-ref="track"
            class="mt-10 flex gap-4 overflow-x-auto scroll-smooth pb-4 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
        >
            @foreach ($stories as $story)
                <article class="w-40 shrink-0 sm:w-48">
                    <button
                        type="button"
                        class="w-full overflow-hidden rounded-[1.5rem] bg-cream-dark text-left shadow-sm"
                        @click="play($event)"
                        aria-label="{{ $story->title ? 'Смотреть '.$story->title : 'Смотреть сторис' }}"
                    >
                        @php
                            $videoUrl = \App\Support\PublicMedia::url($story->video_path);
                            $posterUrl = \App\Support\PublicMedia::url($story->preview_image);
                        @endphp
                        @if ($videoUrl)
                            <video
                                class="aspect-[9/16] w-full object-cover"
                                muted
                                loop
                                playsinline
                                preload="metadata"
                                @if ($posterUrl) poster="{{ $posterUrl }}" @endif
                            >
                                <source src="{{ $videoUrl }}" type="video/mp4">
                            </video>
                        @else
                            <x-media :path="$story->preview_image" :alt="$story->title ?? 'Сторис'" class="aspect-[9/16] w-full object-cover" />
                        @endif
                    </button>
                </article>
            @endforeach
        </div>

        <div class="mt-4 flex gap-3">
            <button type="button" @click="scrollBy(-1)" class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-olive text-cream transition hover:bg-olive-deep" aria-label="Назад">
                ←
            </button>
            <button type="button" @click="scrollBy(1)" class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-olive text-cream transition hover:bg-olive-deep" aria-label="Вперёд">
                →
            </button>
        </div>
    </div>
</section>
