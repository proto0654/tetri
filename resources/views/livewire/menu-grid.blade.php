@php
    $categoryGridClass = 'grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-4';
    $menuPanePage = $items instanceof \Illuminate\Contracts\Pagination\Paginator
        ? $items->currentPage()
        : 1;
    $tabClass = 'min-w-0 flex-1 basis-[calc(50%-0.25rem)] rounded-full px-3 py-2.5 text-center text-xs font-semibold tracking-wide transition data-loading:pointer-events-none data-loading:opacity-60 sm:basis-0 sm:px-4 sm:text-sm';
@endphp

<div class="relative">
    <div class="flex w-full flex-wrap gap-2 sm:gap-3 lg:flex-nowrap" data-entrance-fade>
        <button
            type="button"
            wire:key="category-tab-all"
            wire:click="showAll"
            @class([
                $tabClass,
                'bg-plum text-white' => $activeCategory === null,
                'bg-cream-dark text-ink hover:bg-plum/10' => $activeCategory !== null,
            ])
        >
            @typo('ВСЕ МЕНЮ')
        </button>

        @foreach ($categories as $category)
            <button
                type="button"
                wire:key="category-tab-{{ $category->id }}"
                wire:click="setCategory({{ $category->id }})"
                @class([
                    $tabClass,
                    'bg-plum text-white' => $activeCategory?->id === $category->id,
                    'bg-cream-dark text-ink hover:bg-plum/10' => $activeCategory?->id !== $category->id,
                ])
            >
                @typo(mb_strtoupper($category->title))
            </button>
        @endforeach
    </div>

    <div
        class="mt-12"
        wire:key="menu-pane-{{ $activeCategoryId ?? 'all' }}-{{ $menuPanePage }}"
    >
        @if ($activeCategory === null)
            <div class="space-y-16">
                @forelse ($categories as $category)
                    @php
                        $previewCount = (int) ($category->columns ?: 3);
                        $previewItems = $category->menuItems->take($previewCount);
                        $blockGridClass = match ($previewCount) {
                            2 => 'grid grid-cols-1 gap-8 md:grid-cols-2',
                            default => 'grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3',
                        };
                        $blockItemOffset = $loop->index * max($previewCount, 1);
                    @endphp

                    <section
                        wire:key="menu-block-{{ $category->id }}"
                        class="menu-grid-block"
                        style="--menu-i: {{ $loop->index }}"
                    >
                        <h2 class="font-display text-4xl font-normal text-olive sm:text-5xl lg:text-6xl">
                            @typo(mb_strtoupper($category->title))
                        </h2>

                        <div class="{{ $blockGridClass }} mt-8">
                            @forelse ($previewItems as $item)
                                <a
                                    wire:key="menu-item-{{ $item->id }}"
                                    href="{{ route('menu.show', [$category, $item]) }}"
                                    class="menu-grid-item group block"
                                    style="--menu-i: {{ $blockItemOffset + $loop->index }}"
                                >
                                    <article>
                                        <div class="overflow-hidden rounded-[1.5rem] bg-cream-dark">
                                            <x-media
                                                :path="$item->image"
                                                :alt="$item->title"
                                                class="aspect-video w-full object-cover transition duration-500 group-hover:scale-105"
                                            />
                                        </div>
                                        <div class="mt-4 flex items-start justify-between gap-4">
                                            <h3 class="text-base font-semibold text-ink group-hover:text-olive">@typo($item->title)</h3>
                                            <p class="shrink-0 text-base font-semibold text-ink">
                                                {{ number_format((float) $item->price, 0, '', ' ') }} ₽
                                            </p>
                                        </div>
                                        @if ($item->description)
                                            <p class="mt-2 text-sm leading-relaxed text-muted">@typo($item->description)</p>
                                        @endif
                                    </article>
                                </a>
                            @empty
                                <p class="text-muted">В этой категории пока нет блюд.</p>
                            @endforelse
                        </div>
                    </section>
                @empty
                    <p class="text-muted">Категории меню пока не добавлены.</p>
                @endforelse
            </div>
        @else
            <h2
                id="menu-grid-heading"
                class="menu-grid-heading scroll-mt-28 font-display text-4xl font-normal text-olive sm:text-5xl lg:text-6xl"
                style="--menu-i: 0"
            >
                @typo(mb_strtoupper($activeCategory->title))
            </h2>

            <div class="{{ $categoryGridClass }} mt-8">
                @forelse ($items as $item)
                    <a
                        wire:key="menu-item-{{ $item->id }}"
                        href="{{ route('menu.show', [$activeCategory, $item]) }}"
                        class="menu-grid-item group block"
                        style="--menu-i: {{ $loop->index }}"
                    >
                        <article>
                            <div class="overflow-hidden rounded-[1.5rem] bg-cream-dark">
                                <x-media
                                    :path="$item->image"
                                    :alt="$item->title"
                                    class="aspect-video w-full object-cover transition duration-500 group-hover:scale-105"
                                />
                            </div>
                            <div class="mt-4 flex items-start justify-between gap-4">
                                <h3 class="text-base font-semibold text-ink group-hover:text-olive">@typo($item->title)</h3>
                                <p class="shrink-0 text-base font-semibold text-ink">
                                    {{ number_format((float) $item->price, 0, '', ' ') }} ₽
                                </p>
                            </div>
                            @if ($item->description)
                                <p class="mt-2 text-sm leading-relaxed text-muted">@typo($item->description)</p>
                            @endif
                        </article>
                    </a>
                @empty
                    <p class="text-muted">В этой категории пока нет блюд.</p>
                @endforelse
            </div>

            @if ($items->hasPages())
                <div class="mt-12">
                    {{ $items->links('pagination.site') }}
                </div>
            @endif
        @endif
    </div>
</div>
