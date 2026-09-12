@php
    /** @var \App\Models\Story|null $record */
    $record = $getRecord();
    $imageUrl = \App\Support\PublicMedia::url($record?->preview_image);
    $videoUrl = \App\Support\PublicMedia::url($record?->video_path);
    $mediaStyle = 'height: 5rem; width: 3.75rem; border-radius: 0.5rem; object-fit: cover; display: block;';
@endphp

@if ($imageUrl)
    <img
        src="{{ $imageUrl }}"
        alt=""
        style="{{ $mediaStyle }}"
    >
@elseif ($videoUrl)
    <video
        src="{{ $videoUrl }}#t=0.1"
        style="{{ $mediaStyle }}"
        muted
        playsinline
        preload="metadata"
        tabindex="-1"
    ></video>
@else
    <span class="fi-ta-placeholder">—</span>
@endif
