@props([
    'text' => null,
    'note' => null,
    'ruled' => false,
])

@if (filled($text) || filled($note))
    <p {{ $attributes->merge(['class' => 'flex flex-col gap-1 text-xs leading-relaxed tracking-wide text-muted']) }}>
        @if (filled($text))
            <span>
                <span aria-hidden="true">◇</span>
                @if ($ruled)
                    <span aria-hidden="true"> —— </span>
                @else
                    <span aria-hidden="true"> </span>
                @endif
                {{ $text }}
            </span>
        @endif
        @if (filled($note))
            <span>
                <span aria-hidden="true">— </span>{{ $note }}
            </span>
        @endif
    </p>
@endif
