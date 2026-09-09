@props([
    'path' => null,
])

@php
    use App\Support\PublicMedia;

    $url = PublicMedia::url($path);
    $extension = strtolower(pathinfo((string) $path, PATHINFO_EXTENSION));
    $type = match ($extension) {
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        'webp' => 'image/webp',
        'jpg', 'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        default => 'image/png',
    };
    $isSvg = $extension === 'svg';
@endphp

@if (filled($url))
    <link rel="icon" href="{{ $url }}" type="{{ $type }}" sizes="any">
    @unless ($isSvg)
        <link rel="apple-touch-icon" href="{{ $url }}">
    @endunless
@endif
