@props(['name' => null, 'custom' => null, 'class' => 'h-5 w-5'])

@php
    $customHtml = filled($custom)
        ? \App\Support\HeroiconOptions::customSvgHtml($custom, $class)
        : null;
@endphp

@if ($customHtml)
    <span {{ $attributes->merge(['class' => 'inline-flex items-center justify-center [&>svg]:h-full [&>svg]:w-full']) }} aria-hidden="true">
        {!! $customHtml !!}
    </span>
@elseif (filled($name))
    @php($icon = \App\Support\HeroiconOptions::normalize($name))
    {!! svg($icon, $class, ['aria-hidden' => 'true'])->toHtml() !!}
@endif
