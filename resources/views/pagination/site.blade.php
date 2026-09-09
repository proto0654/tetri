@php
    $pageName = $paginator->getPageName();
@endphp

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Навигация по страницам" class="flex flex-wrap items-center justify-center gap-3 lg:justify-end">
        @if ($paginator->onFirstPage())
            <span
                class="inline-flex h-11 w-11 cursor-default items-center justify-center rounded-full bg-cream-dark text-muted"
                aria-disabled="true"
                aria-label="Назад"
            >
                ←
            </span>
        @else
            <button
                type="button"
                wire:click="previousPage('{{ $pageName }}')"
                wire:loading.attr="disabled"
                class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-olive text-cream transition hover:bg-olive-deep disabled:opacity-50"
                aria-label="Назад"
                rel="prev"
            >
                ←
            </button>
        @endif

        <div class="flex flex-wrap items-center justify-center gap-2">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-2 text-sm font-semibold text-muted" aria-hidden="true">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <span wire:key="paginator-{{ $pageName }}-page{{ $page }}">
                            @if ($page == $paginator->currentPage())
                                <span
                                    aria-current="page"
                                    class="inline-flex min-w-11 items-center justify-center rounded-full bg-plum px-4 py-2.5 text-sm font-semibold tracking-wide text-white"
                                >
                                    {{ $page }}
                                </span>
                            @else
                                <button
                                    type="button"
                                    wire:click="gotoPage({{ $page }}, '{{ $pageName }}')"
                                    wire:loading.attr="disabled"
                                    class="inline-flex min-w-11 items-center justify-center rounded-full bg-cream-dark px-4 py-2.5 text-sm font-semibold tracking-wide text-ink transition hover:bg-plum/10 disabled:opacity-50"
                                    aria-label="Страница {{ $page }}"
                                >
                                    {{ $page }}
                                </button>
                            @endif
                        </span>
                    @endforeach
                @endif
            @endforeach
        </div>

        @if ($paginator->hasMorePages())
            <button
                type="button"
                wire:click="nextPage('{{ $pageName }}')"
                wire:loading.attr="disabled"
                class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-olive text-cream transition hover:bg-olive-deep disabled:opacity-50"
                aria-label="Вперёд"
                rel="next"
            >
                →
            </button>
        @else
            <span
                class="inline-flex h-11 w-11 cursor-default items-center justify-center rounded-full bg-cream-dark text-muted"
                aria-disabled="true"
                aria-label="Вперёд"
            >
                →
            </span>
        @endif
    </nav>
@endif
