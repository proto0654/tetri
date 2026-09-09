@php
    $columns = (int) ($activeCategory?->columns ?? 3);
    $gridClass = match ($columns) {
        2 => 'grid grid-cols-1 gap-8 md:grid-cols-2',
        default => 'grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3',
    };
@endphp

<div class="relative">
    <div class="flex flex-wrap gap-3">
        @foreach ($categories as $category)
            <button
                type="button"
                wire:key="category-tab-{{ $category->id }}"
                wire:click="setCategory({{ $category->id }})"
                @class([
                    'rounded-full px-5 py-2.5 text-sm font-semibold tracking-wide transition',
                    'bg-plum text-white' => $activeCategory?->id === $category->id,
                    'bg-cream-dark text-ink hover:bg-plum/10' => $activeCategory?->id !== $category->id,
                ])
            >
                @typo(mb_strtoupper($category->title))
            </button>
        @endforeach
    </div>

    <div
        class="mt-12 transition"
        wire:loading.class="opacity-40 blur-[1px]"
        wire:target="setCategory"
    >
        @if ($activeCategory)
            <h2 class="font-display text-3xl font-bold text-olive sm:text-4xl">
                @typo(mb_strtoupper($activeCategory->title))
            </h2>
        @endif

        <div class="{{ $gridClass }} mt-8">
            @forelse ($items as $item)
                <article wire:key="menu-item-{{ $item->id }}" class="group">
                    <div class="overflow-hidden rounded-[1.5rem] bg-cream-dark">
                        <x-media
                            :path="$item->image"
                            :alt="$item->title"
                            class="aspect-video w-full object-cover transition duration-500 group-hover:scale-105"
                        />
                    </div>
                    <div class="mt-4 flex items-start justify-between gap-4">
                        <h3 class="text-base font-semibold text-ink">@typo($item->title)</h3>
                        <p class="shrink-0 text-base font-semibold text-ink">
                            {{ number_format((float) $item->price, 0, '', ' ') }} ₽
                        </p>
                    </div>
                    @if ($item->description)
                        <p class="mt-2 text-sm leading-relaxed text-muted">@typo($item->description)</p>
                    @endif
                </article>
            @empty
                <p class="text-muted">В этой категории пока нет блюд.</p>
            @endforelse
        </div>
    </div>
</div>
