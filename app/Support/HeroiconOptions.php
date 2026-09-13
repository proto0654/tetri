<?php

namespace App\Support;

use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HeroiconOptions
{
    /** @var array<string, string>|null */
    private static ?array $outlinedCache = null;

    /** @var array<string, string>|null */
    private static ?array $previewCache = null;

    /**
     * Searchable outlined Heroicons for Filament selects.
     * Values are Blade Icons names, e.g. heroicon-o-cake.
     *
     * @return array<string, string>
     */
    public static function outlined(): array
    {
        if (self::$outlinedCache !== null) {
            return self::$outlinedCache;
        }

        $options = [];

        foreach (Heroicon::cases() as $icon) {
            if (! str_starts_with($icon->value, 'o-')) {
                continue;
            }

            $label = Str::of($icon->name)
                ->after('Outlined')
                ->headline()
                ->toString();

            $options['heroicon-'.$icon->value] = $label;
        }

        asort($options, SORT_NATURAL | SORT_FLAG_CASE);

        return self::$outlinedCache = $options;
    }

    /**
     * Same options with inline SVG preview for Filament Select::allowHtml().
     *
     * @return array<string, string>
     */
    public static function outlinedWithPreview(): array
    {
        if (self::$previewCache !== null) {
            return self::$previewCache;
        }

        $options = [];

        foreach (self::outlined() as $name => $label) {
            $svg = self::previewSvgHtml($name);

            $options[$name] = '<span style="display:inline-flex;align-items:center;gap:0.5rem;line-height:1.25;max-height:1.5rem;overflow:hidden;">'
                .$svg
                .'<span>'.e($label).'</span>'
                .'</span>';
        }

        return self::$previewCache = $options;
    }

    public static function previewLabel(?string $name): ?string
    {
        if (blank($name)) {
            return null;
        }

        return self::outlinedWithPreview()[$name]
            ?? e(self::outlined()[$name] ?? $name);
    }

    /**
     * Compact SVG for Filament Select HTML labels (no Tailwind dependency).
     */
    public static function previewSvgHtml(string $name): string
    {
        try {
            return svg($name, '', [
                'width' => '20',
                'height' => '20',
                'style' => 'width:20px;height:20px;min-width:20px;min-height:20px;flex-shrink:0;display:block;',
            ])->toHtml();
        } catch (\Throwable) {
            return '';
        }
    }

    /**
     * Map legacy custom icon keys to Heroicons.
     */
    public static function normalize(?string $name): string
    {
        if (blank($name)) {
            return 'heroicon-o-star';
        }

        $legacy = [
            'cutlery' => 'heroicon-o-cake',
            'wine' => 'heroicon-o-beaker',
            'car' => 'heroicon-o-truck',
            'playground' => 'heroicon-o-puzzle-piece',
            'animation' => 'heroicon-o-sparkles',
            'parking' => 'heroicon-o-truck',
            'party' => 'heroicon-o-gift',
        ];

        if (isset($legacy[$name])) {
            return $legacy[$name];
        }

        if (str_starts_with($name, 'heroicon-')) {
            return $name;
        }

        if (str_starts_with($name, 'o-')) {
            return 'heroicon-'.$name;
        }

        return 'heroicon-o-'.ltrim($name, '-');
    }

    public static function sanitizeSvg(string $svg): string
    {
        $svg = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $svg) ?? $svg;
        $svg = preg_replace('/\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $svg) ?? $svg;
        $svg = preg_replace('/javascript\s*:/i', '', $svg) ?? $svg;

        return $svg;
    }

    public static function customSvgHtml(?string $path, string $class = 'h-5 w-5'): ?string
    {
        if (blank($path) || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        $raw = Storage::disk('public')->get($path);

        if (! is_string($raw) || $raw === '') {
            return null;
        }

        $svg = self::sanitizeSvg($raw);

        if (! str_contains(strtolower($svg), '<svg')) {
            return null;
        }

        // Force currentColor-friendly sizing via wrapper classes on the SVG root.
        if (preg_match('/<svg\b([^>]*)>/i', $svg, $matches)) {
            $attrs = $matches[1];
            $attrs = preg_replace('/\s(width|height)=("[^"]*"|\'[^\']*\')/i', '', $attrs) ?? $attrs;

            if (preg_match('/\sclass=(["\'])/i', $attrs)) {
                $attrs = preg_replace('/\sclass=(["\'])/i', ' class=$1'.e($class).' ', $attrs, 1) ?? $attrs;
            } else {
                $attrs .= ' class="'.e($class).'"';
            }

            if (! str_contains($attrs, 'fill=')) {
                $attrs .= ' fill="currentColor"';
            }

            $svg = preg_replace('/<svg\b[^>]*>/i', '<svg'.$attrs.'>', $svg, 1) ?? $svg;
        }

        return $svg;
    }
}
