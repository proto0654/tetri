@props([
    'items' => [],
])

@if (count($items) > 0)
    <nav {{ $attributes->merge(['class' => 'text-sm text-muted']) }} aria-label="Хлебные крошки">
        <ol class="flex flex-wrap items-center gap-x-2 gap-y-1">
            @foreach ($items as $index => $crumb)
                @php
                    $label = $crumb['label'] ?? '';
                    $url = $crumb['url'] ?? null;
                    $isLast = $index === array_key_last($items);
                @endphp

                <li class="inline-flex items-center gap-x-2">
                    @if ($index > 0)
                        <span aria-hidden="true" class="text-muted/60">→</span>
                    @endif

                    @if (filled($url) && ! $isLast)
                        <a href="{{ $url }}" class="text-ink transition hover:text-olive">
                            @typo($label)
                        </a>
                    @else
                        <span @if ($isLast) aria-current="page" @endif class="text-muted">
                            @typo($label)
                        </span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
