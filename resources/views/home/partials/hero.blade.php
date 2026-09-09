@php
    use App\Support\CssColor;
    use App\Support\PublicMedia;

    $bgUrl = PublicMedia::url($settings['hero_background_image'] ?? null);
    $videoUrl = PublicMedia::url($settings['hero_video_path'] ?? null);
    $posterUrl = PublicMedia::url($settings['hero_video_preview'] ?? null);

    $overlayFrom = CssColor::resolve($settings['hero_overlay_from'] ?? null, 'rgba(0, 0, 0, 0.45)');
    $overlayVia = CssColor::resolve($settings['hero_overlay_via'] ?? null, 'rgba(0, 0, 0, 0.3)');
    $overlayTo = CssColor::resolve($settings['hero_overlay_to'] ?? null, 'rgba(237, 226, 207, 0.95)');
    $overlayGradient = "linear-gradient(to bottom, {$overlayFrom}, {$overlayVia}, {$overlayTo})";
@endphp

<section class="relative flex min-h-[92vh] items-center justify-center overflow-x-clip bg-olive-deep">
    @if ($bgUrl)
        <img src="{{ $bgUrl }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-70" loading="eager">
    @endif
    <div class="absolute inset-0" style="background: {{ $overlayGradient }}"></div>

    <div class="relative z-10 mx-auto flex w-full max-w-6xl flex-col items-center px-4 pb-16 pt-32 text-center sm:px-6">
        <div class="relative flex w-full flex-col items-center">
            <div class="w-full max-w-xs overflow-hidden rounded-[2rem] border border-white/20 bg-black/20 shadow-2xl backdrop-blur-sm sm:max-w-sm">
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

            <h1 class="pointer-events-none absolute bottom-0 left-1/2 z-10 w-full -translate-x-1/2 translate-y-[35%] font-display text-[clamp(2.75rem,11vw,7.5rem)] font-bold leading-none tracking-[0.35em] whitespace-nowrap text-white drop-shadow">
                {{ $settings['hero_title'] ?? 'Т Е Т Р И' }}
            </h1>
        </div>
    </div>

    @if (! empty($settings['hero_icons']))
        <div class="absolute inset-x-0 bottom-0 z-20 flex translate-y-1/2 items-center justify-center gap-5">
            @foreach ($settings['hero_icons'] as $icon)
                @php($href = $icon['url'] ?? '#')
                <a
                    href="{{ $href }}"
                    class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-cream text-plum transition hover:bg-cream-dark"
                    @if (\Illuminate\Support\Str::startsWith($href, ['http://', 'https://'])) target="_blank" rel="noopener noreferrer" @endif
                >
                    <x-site.icon :name="$icon['icon'] ?? null" :custom="$icon['custom_icon'] ?? null" class="h-7 w-7" />
                </a>
            @endforeach
        </div>
    @endif
</section>
