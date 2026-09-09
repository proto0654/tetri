@extends('layouts.site')

@section('title', $item->title.' — ТЕТРИ')

@section('content')
    <section class="bg-cream px-4 pb-24 pt-10 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <h1 class="font-display text-4xl font-bold text-olive sm:text-5xl lg:text-6xl">
                @typoBr($item->title)
            </h1>

            <x-site.breadcrumbs
                class="mt-4"
                :items="[
                    ['label' => 'Меню', 'url' => route('menu')],
                    ['label' => $category->title, 'url' => route('menu.category', $category)],
                    ['label' => $item->title],
                ]"
            />

            <div class="mt-10 grid gap-10 lg:grid-cols-3 lg:gap-x-12">
                <div class="overflow-hidden rounded-[1.5rem] bg-cream-dark lg:col-span-2">
                    <x-media
                        :path="$item->image"
                        :alt="$item->title"
                        class="aspect-video w-full object-cover"
                    />
                </div>

                <div class="flex flex-col">
                    <p class="text-2xl font-semibold text-ink">
                        {{ number_format((float) $item->price, 0, '', ' ') }} ₽
                    </p>

                    @if ($item->description)
                        <p class="mt-4 text-base leading-relaxed text-muted">
                            @typo($item->description)
                        </p>
                    @endif

                    <div class="mt-8">
                        <x-site.book-button source="dish-detail" />
                    </div>
                </div>
            </div>

            @if ($related->isNotEmpty())
                <div class="mt-20">
                    <h2 class="font-display text-4xl font-bold text-olive sm:text-5xl lg:text-6xl">
                        @typoBr('Смотри также')
                    </h2>

                    <div class="mt-8 grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
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
            @endif
        </div>
    </section>
@endsection
