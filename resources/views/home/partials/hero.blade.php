@php
    use App\Support\CssColor;
    use App\Support\PublicMedia;

    $bgUrl = PublicMedia::url($settings['hero_background_image'] ?? null);
    $videoUrl = PublicMedia::url($settings['hero_video_path'] ?? null);

    $overlayFrom = CssColor::resolve($settings['hero_overlay_from'] ?? null, 'rgba(0, 0, 0, 0.45)');
    $overlayVia = CssColor::resolve($settings['hero_overlay_via'] ?? null, 'rgba(0, 0, 0, 0.3)');
    $overlayTo = CssColor::resolve($settings['hero_overlay_to'] ?? null, 'rgba(237, 226, 207, 0.95)');
    $overlayGradient = "linear-gradient(to bottom, {$overlayFrom}, {$overlayVia}, {$overlayTo})";

    $heroTitle = $settings['hero_title'] ?? 'Т Е Т Р И';
    $heroSubtitle = trim((string) ($settings['hero_subtitle'] ?? ''));
    $subtitleWords = $heroSubtitle !== ''
        ? preg_split('/\s+/u', $heroSubtitle, -1, PREG_SPLIT_NO_EMPTY) ?: []
        : [];
    $subtitleMid = (int) ceil(count($subtitleWords) / 2);
    $subtitleLeft = array_slice($subtitleWords, 0, $subtitleMid);
    $subtitleRight = array_slice($subtitleWords, $subtitleMid);
@endphp

<section class="relative flex min-h-[92vh] items-center justify-center overflow-x-clip bg-olive-deep" data-hero-entrance data-state="pending">
    @if ($bgUrl)
        <div class="hero-bg absolute inset-0" data-hero-bg data-state="pending">
            <img
                src="{{ $bgUrl }}"
                alt=""
                class="hero-bg__image absolute inset-0 h-full w-full object-cover opacity-70"
                loading="eager"
            >
            {{-- Overlay reveals with the image under the cover bands --}}
            <div class="hero-bg__overlay absolute inset-0" style="background: {{ $overlayGradient }}"></div>
            <div class="hero-bg__cover" aria-hidden="true">
                @php
                    // 14 bands, taller at top → finer toward bottom; overlap & Y scaled to local size.
                    $bandCount = 14;
                    $bandJitters = [0, 22, -14, 36, -10, 28, -24, 16, 40, -18, 12, -30, 24, 6];
                    $bandDurs = [0.66, 0.74, 0.62, 0.78, 0.7, 0.64, 0.76, 0.68, 0.72, 0.8, 0.65, 0.73, 0.69, 0.71];
                    // Authored Y direction/strength −1..1; clamped into each band's overlap room.
                    $bandYFactors = [0, 0.55, -0.4, 0.7, -0.6, 0.45, -0.75, 0.35, 0.65, -0.5, 0.55, -0.7, 0.4, 0];

                    $rawWeights = [];
                    for ($i = 0; $i < $bandCount; $i++) {
                        // Mild taper so bottom bands stay visible, still clearly smaller.
                        $rawWeights[] = ($bandCount - $i) ** 1.15;
                    }
                    $weightSum = array_sum($rawWeights);
                    $weights = array_map(fn (float $w): float => $w / $weightSum, $rawWeights);

                    $baseTops = [];
                    $baseHeights = [];
                    $cursor = 0.0;
                    foreach ($weights as $weight) {
                        $baseTops[] = $cursor * 100;
                        $baseHeights[] = $weight * 100;
                        $cursor += $weight;
                    }

                    $overlapFactor = 0.42;
                    // Leave ≥50% of each seam's overlap budget after Y jitter (avoids olive hairlines).
                    $yRoomScale = 0.5;
                    $bands = [];
                    for ($i = 0; $i < $bandCount; $i++) {
                        $overlapAbove = $i === 0
                            ? 0.0
                            : min($baseHeights[$i - 1], $baseHeights[$i]) * $overlapFactor;
                        $overlapBelow = $i === $bandCount - 1
                            ? 0.0
                            : min($baseHeights[$i], $baseHeights[$i + 1]) * $overlapFactor;

                        // Y stays inside a reduced half-overlap room so seams never go near-zero.
                        $yMin = -($overlapBelow / 2) * $yRoomScale;
                        $yMax = ($overlapAbove / 2) * $yRoomScale;
                        $ySpan = (($overlapAbove + $overlapBelow) / 2) * $yRoomScale;
                        $y = max($yMin, min($yMax, $bandYFactors[$i] * $ySpan));

                        if ($i === 0) {
                            $top = 0.0;
                            $height = $baseHeights[0] + ($overlapBelow / 2);
                        } elseif ($i === $bandCount - 1) {
                            $top = $baseTops[$i] - ($overlapAbove / 2) + $y;
                            $height = 100 - $top;
                        } else {
                            $top = $baseTops[$i] - ($overlapAbove / 2) + $y;
                            $height = $baseHeights[$i] + ($overlapAbove / 2) + ($overlapBelow / 2);
                        }

                        $bands[] = [
                            'i' => $i,
                            'top' => round($top, 3),
                            'height' => round($height, 3),
                            'jitter' => $bandJitters[$i],
                            'dur' => $bandDurs[$i],
                        ];
                    }
                @endphp
                @foreach ($bands as $band)
                    <span
                        class="hero-bg__band"
                        style="--i: {{ $band['i'] }}; --top: {{ $band['top'] }}%; --h: {{ $band['height'] }}%; --jitter: {{ $band['jitter'] }}ms; --dur: {{ $band['dur'] }}s;"
                    ></span>
                @endforeach
            </div>
        </div>
    @else
        <div class="absolute inset-0" style="background: {{ $overlayGradient }}"></div>
    @endif

    <div
        class="relative z-10 mx-auto flex w-full max-w-6xl flex-col items-center px-4 text-center sm:px-6"
        data-hero-content
    >
        <div class="relative flex w-full flex-col items-center">
            <div class="relative flex w-full items-center justify-center">
                @if ($subtitleWords !== [])
                    <p
                        class="site-info pointer-events-none absolute inset-x-0 top-1/2 z-10 hidden -translate-y-1/2 justify-between !text-white drop-shadow hyphens-none lg:flex [text-align:inherit]"
                        data-hero-subtitle
                    >
                        <span class="tracking-[0.35em] !text-white">@typo(implode(' ', $subtitleLeft))</span>
                        <span class="tracking-[0.35em] !text-white">@typo(implode(' ', $subtitleRight))</span>
                    </p>
                @endif

                <div
                    class="relative z-0 aspect-[3/4] overflow-hidden rounded-[2rem] bg-black/20 shadow-2xl backdrop-blur-sm"
                    data-hero-story
                >
                    @if ($videoUrl)
                        {{-- Video wins over preview image; #t=0.1 paints first frame from metadata while paused (no poster flash). --}}
                        <video
                            class="absolute inset-[-3px] h-[calc(100%+6px)] w-[calc(100%+6px)] max-w-none object-cover"
                            src="{{ $videoUrl }}#t=0.1"
                            muted
                            loop
                            playsinline
                            preload="metadata"
                        ></video>
                    @else
                        <x-media :path="$settings['hero_video_preview'] ?? null" alt="ТЕТРИ" class="absolute inset-[-3px] h-[calc(100%+6px)] w-[calc(100%+6px)] max-w-none object-cover" />
                    @endif
                </div>
            </div>

            @php
                $titleGlyphs = array_values(array_filter(
                    mb_str_split($heroTitle),
                    fn (string $char): bool => $char !== ' ',
                ));
                $glyphCount = count($titleGlyphs);
                $pivotGlyph = $glyphCount > 0 ? (int) floor(($glyphCount - 1) / 2) : 0;
            @endphp

            <h1
                class="pointer-events-none absolute bottom-0 left-1/2 z-10 translate-y-[35%] font-display font-normal leading-none text-white drop-shadow"
                aria-label="{{ $heroTitle }}"
                data-hero-title
                style="--hero-title-step: 1.2em"
            >
                <span class="hero-title__track" aria-hidden="true">
                    @foreach ($titleGlyphs as $charIndex => $char)
                        <span class="hero-title__slot" style="--n: {{ $charIndex - $pivotGlyph }}">
                            <span
                                class="hero-title__char"
                                data-hero-char
                                @if ($charIndex === $pivotGlyph) data-hero-char-pivot @endif
                                style="--i: {{ $charIndex }}"
                            >{{ $char }}</span>
                        </span>
                    @endforeach
                </span>
            </h1>

            @if ($subtitleWords !== [])
                <p
                    class="site-info pointer-events-none absolute bottom-0 left-1/2 z-10 max-w-none -translate-x-1/2 translate-y-[calc(0.35*var(--hero-title-size)+4.25em)] whitespace-nowrap !w-max !text-center !text-white drop-shadow hyphens-none lg:hidden"
                    data-hero-subtitle
                >
                    @typo($heroSubtitle)
                </p>
            @endif
        </div>
    </div>

    @if (! empty($settings['hero_icons']))
        <div class="absolute inset-x-0 bottom-0 z-20 flex translate-y-1/2 items-center justify-center" data-hero-icons>
            @foreach ($settings['hero_icons'] as $iconIndex => $icon)
                @php($href = $icon['url'] ?? '#')
                <a
                    href="{{ $href }}"
                    class="inline-flex items-center justify-center rounded-full bg-cream text-plum transition hover:bg-cream-dark"
                    data-hero-icon
                    style="--i: {{ $iconIndex }}"
                    @if (\Illuminate\Support\Str::startsWith($href, ['http://', 'https://'])) target="_blank" rel="noopener noreferrer" @endif
                >
                    <span data-hero-icon-glyph>
                        <x-site.icon :name="$icon['icon'] ?? null" :custom="$icon['custom_icon'] ?? null" class="h-full w-full" />
                    </span>
                </a>
            @endforeach
        </div>
    @endif
</section>
