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
}
