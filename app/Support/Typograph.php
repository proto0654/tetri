<?php

namespace App\Support;

use Akh\Typograf\Typograf;

class Typograph
{
    public static function apply(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }

        $plain = strip_tags($text);
        $result = app(Typograf::class)->apply($plain);
        $result = html_entity_decode($result, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return strip_tags($result);
    }

    /**
     * Typograph CMS titles while allowing intentional line breaks.
     * Accepts real newlines and &lt;br&gt; tags; strips every other HTML tag
     * and HTML-escapes each line so the result is safe to echo raw.
     */
    public static function applyWithBreaks(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }

        $normalized = preg_replace('/<br\s*\/?>/i', "\n", $text) ?? $text;
        $normalized = strip_tags($normalized);
        $lines = preg_split("/\r\n|\r|\n/", $normalized);

        if ($lines === false) {
            return e(static::apply($text));
        }

        $processed = array_map(
            static fn (string $line): string => e(static::apply($line)),
            $lines,
        );

        return implode('<br>', $processed);
    }
}
