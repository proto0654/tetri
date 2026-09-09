@props([
    'flush' => false,
])

<div
    data-site-shell
    {{ $attributes->class([
        'mx-auto w-full max-w-7xl',
        'px-4 sm:px-6 lg:px-8' => ! $flush,
    ]) }}
>
    {{ $slot }}
</div>
