@props(['path', 'alt' => '', 'class' => ''])

@php
    $url = \App\Support\PublicMedia::url($path);
@endphp

@if ($url)
    <img src="{{ $url }}" alt="{{ $alt }}" {{ $attributes->merge(['class' => $class, 'loading' => 'lazy']) }}>
@else
    <div {{ $attributes->merge(['class' => 'bg-cream-dark '.$class]) }} aria-hidden="true"></div>
@endif
