@props([
    'path' => null,
    'textClass' => 'font-display text-xl font-normal tracking-[0.35em] text-olive',
])

@php
    use App\Support\PublicMedia;

    $url = PublicMedia::url($path);
@endphp

@if (filled($url))
    <img
        src="{{ $url }}"
        alt="ТЕТРИ"
        {{ $attributes->class(['w-auto object-contain']) }}
    >
@else
    <span class="{{ $textClass }}">ТЕТРИ</span>
@endif
