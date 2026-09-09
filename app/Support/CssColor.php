<?php

namespace App\Support;

class CssColor
{
    /**
     * Return a safe CSS color string, or the fallback when the value is invalid.
     */
    public static function resolve(?string $value, string $fallback): string
    {
        $value = is_string($value) ? trim($value) : '';

        return self::isSafe($value) ? $value : $fallback;
    }

    public static function isSafe(string $value): bool
    {
        return (bool) preg_match(
            '/^(#(?:[0-9a-f]{3}|[0-9a-f]{4}|[0-9a-f]{6}|[0-9a-f]{8})|rgba?\(\s*\d{1,3}(?:\s*,\s*\d{1,3}){2}(?:\s*,\s*(?:0|1|0?\.\d+))?\s*\)|hsla?\(\s*\d{1,3}(?:deg)?(?:\s*,\s*[\d.]+%){2}(?:\s*,\s*(?:0|1|0?\.\d+))?\s*\))$/i',
            $value,
        );
    }
}
