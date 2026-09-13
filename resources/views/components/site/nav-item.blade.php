@props([
    'item',
    'variant' => 'header',
    'linkClass' => '',
])

@php
    $label = (string) ($item['label'] ?? '');
    $type = ($item['type'] ?? 'link') === 'booking' ? 'booking' : 'link';
    $url = (string) ($item['url'] ?? '#');
    $sourceSlug = filled($item['booking_source'] ?? null)
        ? (string) $item['booking_source']
        : \Illuminate\Support\Str::slug($label);
    $bookingSource = match ($variant) {
        'header-mobile' => 'nav-'.$sourceSlug.'-mobile',
        'footer' => 'footer-'.$sourceSlug,
        default => 'nav-'.$sourceSlug,
    };

    $bookingClass = match ($variant) {
        'header' => 'bg-transparent p-0 text-sm font-medium shadow-none hover:bg-transparent '.$linkClass,
        'header-mobile' => 'bg-transparent p-0 text-left text-sm font-medium text-ink shadow-none hover:bg-transparent',
        'footer' => 'bg-transparent p-0 text-left text-sm text-cream/85 shadow-none hover:bg-transparent hover:text-cream',
        default => $linkClass,
    };

    $linkClasses = match ($variant) {
        'header' => $linkClass,
        'footer' => 'hover:text-cream',
        default => '',
    };
@endphp

@if ($type === 'booking')
    @if ($variant === 'header')
        <x-site.book-button
            :source="$bookingSource"
            :label="$label"
            :class="$bookingClass"
            data-header-nav-item
        />
    @elseif ($variant === 'header-mobile')
        <x-site.book-button
            :source="$bookingSource"
            :label="$label"
            :class="$bookingClass"
            @click="open = false"
        />
    @else
        <x-site.book-button
            :source="$bookingSource"
            :label="$label"
            :class="$bookingClass"
        />
    @endif
@elseif ($variant === 'header')
    <a href="{{ $url }}" class="{{ $linkClasses }}" data-header-nav-item>{{ $label }}</a>
@elseif ($variant === 'header-mobile')
    <a href="{{ $url }}" @click="open = false">{{ $label }}</a>
@else
    <a href="{{ $url }}" @class([$linkClasses => filled($linkClasses)])>{{ $label }}</a>
@endif
