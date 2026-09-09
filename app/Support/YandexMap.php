<?php

namespace App\Support;

class YandexMap
{
    public static function widgetUrl(mixed $latitude, mixed $longitude, ?string $label = null, int $zoom = 16): ?string
    {
        if (! self::hasCoordinates($latitude, $longitude)) {
            return null;
        }

        $lat = self::normalizeCoordinate($latitude);
        $lng = self::normalizeCoordinate($longitude);
        $point = "{$lng},{$lat},pm2rdm";

        if (filled($label)) {
            $point .= '~'.trim($label);
        }

        return 'https://yandex.ru/map-widget/v1/?'.http_build_query([
            'll' => "{$lng},{$lat}",
            'z' => $zoom,
            'pt' => $point,
        ], '', '&', PHP_QUERY_RFC3986);
    }

    public static function routeUrl(mixed $latitude, mixed $longitude): ?string
    {
        if (! self::hasCoordinates($latitude, $longitude)) {
            return null;
        }

        $lat = self::normalizeCoordinate($latitude);
        $lng = self::normalizeCoordinate($longitude);

        return 'https://yandex.ru/maps/?'.http_build_query([
            'rtext' => "~{$lat},{$lng}",
        ], '', '&', PHP_QUERY_RFC3986);
    }

    public static function hasCoordinates(mixed $latitude, mixed $longitude): bool
    {
        return self::isCoordinate($latitude) && self::isCoordinate($longitude);
    }

    protected static function isCoordinate(mixed $value): bool
    {
        if (is_int($value) || is_float($value)) {
            return true;
        }

        if (! is_string($value) || trim($value) === '') {
            return false;
        }

        return is_numeric(trim($value));
    }

    protected static function normalizeCoordinate(mixed $value): string
    {
        return is_string($value) ? trim($value) : (string) $value;
    }
}
