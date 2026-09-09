@php
    use App\Support\PublicMedia;

    $bgUrl = PublicMedia::url($settings['hero_background_image'] ?? null);
    $videoUrl = PublicMedia::url($settings['hero_video_path'] ?? null);
    $posterUrl = PublicMedia::url($settings['hero_video_preview'] ?? null);
@endphp

<section class="relative flex min-h-[92vh] items-center justify-center overflow-hidden bg-olive-deep">
    @if ($bgUrl)
        <img src="{{ $bgUrl }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-70" loading="eager">
    @endif
    <div class="absolute inset-0 bg-gradient-to-b from-black/45 via-black/30 to-cream/95"></div>

    <div class="relative z-10 mx-auto flex w-full max-w-6xl flex-col items-center px-4 pb-24 pt-32 text-center sm:px-6">
        <h1 class="font-display text-5xl font-bold tracking-[0.45em] text-white drop-shadow sm:text-6xl lg:text-7xl">
            {{ $settings['hero_title'] ?? 'Т Е Т Р И' }}
        </h1>

        <div class="mt-10 w-full max-w-xs overflow-hidden rounded-[2rem] border border-white/20 bg-black/20 shadow-2xl backdrop-blur-sm sm:max-w-sm">
            @if ($videoUrl)
                <video
                    class="aspect-[3/4] w-full object-cover"
                    autoplay
                    muted
                    loop
                    playsinline
                    @if ($posterUrl) poster="{{ $posterUrl }}" @endif
                >
                    <source src="{{ $videoUrl }}" type="video/mp4">
                </video>
            @else
                <x-media :path="$settings['hero_video_preview'] ?? null" alt="ТЕТРИ" class="aspect-[3/4] w-full object-cover" />
            @endif
        </div>

        <div class="mt-10 flex items-center gap-5">
            @foreach ($settings['hero_icons'] ?? [] as $icon)
                @php($href = $icon['url'] ?? '#')
                <a
                    href="{{ $href }}"
                    class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-white/90 text-ink shadow transition hover:bg-white"
                    @if (\Illuminate\Support\Str::startsWith($href, ['http://', 'https://'])) target="_blank" rel="noopener noreferrer" @endif
                >
                    <x-site.icon :name="$icon['icon'] ?? null" :custom="$icon['custom_icon'] ?? null" class="h-5 w-5" />
                </a>
            @endforeach
        </div>
    </div>
</section>
