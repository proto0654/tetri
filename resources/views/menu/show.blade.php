@extends('layouts.site')

@section('title', $item->title.' — ТЕТРИ')

@section('content')
    <section class="bg-cream px-4 pt-10 sm:px-6 lg:px-8" data-entrance data-state="pending">
        <div class="mx-auto max-w-7xl">
            <h1 class="font-display text-4xl font-normal text-olive sm:text-5xl lg:text-6xl" data-entrance-title>
                @typoBr($item->title)
            </h1>

            <x-site.breadcrumbs
                class="mt-4"
                data-entrance-fade
                :items="[
                    ['label' => 'Меню', 'url' => route('menu')],
                    ['label' => $category->title, 'url' => route('menu.category', $category)],
                    ['label' => $item->title],
                ]"
            />

            <div class="mt-10 grid gap-10 lg:grid-cols-3 lg:gap-x-12">
                <div
                    class="overflow-hidden rounded-[1.5rem] bg-cream-dark lg:col-span-2"
                    data-entrance-media
                    data-entrance-radius="24"
                    style="--entrance-radius: 1.5rem"
                >
                    <x-media
                        :path="$item->image"
                        :alt="$item->title"
                        class="aspect-video w-full object-cover"
                    />
                </div>

                <div class="flex flex-col">
                    <p class="text-2xl font-semibold text-ink" data-entrance-fade>
                        {{ number_format((float) $item->price, 0, '', ' ') }} ₽
                    </p>

                    @if ($item->description)
                        <p class="mt-4 text-base leading-relaxed text-muted" data-entrance-fade>
                            @typo($item->description)
                        </p>
                    @endif

                    <div class="mt-8" data-entrance-cta>
                        <x-site.book-button source="dish-detail" />
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if ($related->isNotEmpty())
        <section class="bg-cream px-4 pb-24 pt-20 sm:px-6 lg:px-8" data-entrance data-state="pending">
            <div class="mx-auto max-w-7xl">
                <h2 class="font-display text-4xl font-normal text-olive sm:text-5xl lg:text-6xl" data-entrance-title>
                    @typoBr('Смотри также')
                </h2>

                <div class="mt-8 grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3" data-entrance-list>
                    @foreach ($related as $relatedItem)
                        <a
                            href="{{ route('menu.show', [$category, $relatedItem]) }}"
                            class="group block"
                        >
                            <article>
                                <div class="overflow-hidden rounded-[1.5rem] bg-cream-dark">
                                    <x-media
                                        :path="$relatedItem->image"
                                        :alt="$relatedItem->title"
                                        class="aspect-video w-full object-cover transition duration-500 group-hover:scale-105"
                                    />
                                </div>
                                <div class="mt-4 flex items-start justify-between gap-4">
                                    <h3 class="text-base font-semibold text-ink group-hover:text-olive">
                                        @typo($relatedItem->title)
                                    </h3>
                                    <p class="shrink-0 text-base font-semibold text-ink">
                                        {{ number_format((float) $relatedItem->price, 0, '', ' ') }} ₽
                                    </p>
                                </div>
                                @if ($relatedItem->description)
                                    <p class="mt-2 text-sm leading-relaxed text-muted">
                                        @typo($relatedItem->description)
                                    </p>
                                @endif
                            </article>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @else
        <div class="bg-cream pb-24"></div>
    @endif
@endsection
