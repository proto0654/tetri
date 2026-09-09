<?php

namespace App\Settings;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SiteSettings
{
    public const KEY = 'site';

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return Cache::rememberForever(self::cacheKey(), function (): array {
            $setting = Setting::query()->where('key', self::KEY)->first();

            return array_replace_recursive(self::defaults(), $setting?->value ?? []);
        });
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return data_get($this->all(), $key, $default);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function save(array $data): void
    {
        $merged = array_replace_recursive(self::defaults(), $data);

        // List fields must be replaced wholesale, not merged by index.
        foreach (['hero_icons', 'kids_benefits', 'kids_images', 'phones', 'social_links'] as $listKey) {
            if (array_key_exists($listKey, $data)) {
                $merged[$listKey] = $data[$listKey];
            }
        }

        Setting::query()->updateOrCreate(
            ['key' => self::KEY],
            ['value' => $merged],
        );

        Cache::forget(self::cacheKey());
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
            'hero_title' => 'Т Е Т Р И',
            'hero_background_image' => null,
            'hero_video_path' => null,
            'hero_video_preview' => null,
            'hero_icons' => [
                ['icon' => 'heroicon-o-cake', 'url' => '#menu-preview'],
                ['icon' => 'heroicon-o-beaker', 'url' => '#menu-preview'],
                ['icon' => 'heroicon-o-truck', 'url' => '#contacts'],
            ],
            'kids_eyebrow' => 'ДЛЯ ВСЕЙ СЕМЬИ',
            'kids_title' => 'МЕСТО, ГДЕ ХОРОШО И ДЕТЯМ, И РОДИТЕЛЯМ',
            'kids_benefits' => [
                ['icon' => 'heroicon-o-puzzle-piece', 'text' => 'стильная детская игровая', 'url' => null],
                ['icon' => 'heroicon-o-sparkles', 'text' => 'анимация', 'url' => null],
                ['icon' => 'heroicon-o-shield-check', 'text' => 'безопасная площадка', 'url' => null],
                ['icon' => 'heroicon-o-heart', 'text' => 'уютно для родителей', 'url' => null],
            ],
            'kids_images' => [],
            'kids_description' => 'В ресторане есть просторная детская игровая комната, где дети могут играть и веселиться, пока родители спокойно отдыхают за столом.',
            'kids_description_secondary' => 'Игровая оборудована качественной шумоизоляцией, поэтому детский смех и игры не мешают атмосфере основного зала.',
            'kids_location_note' => 'Семейный ресторан в центре Симферополя — пр-т. Кирова, 31А',
            'kids_cta_label' => 'БРОНИРОВАНИЕ',
            'menu_section_eyebrow' => 'ОБЕДЫ, УЖИНЫ И АВТОРСКАЯ КУХНЯ.',
            'menu_section_title' => 'МЕНЮ',
            'menu_section_description' => 'От завтраков до десертов — готовим из свежих продуктов каждый день.',
            'menu_section_cta_label' => 'СМОТРЕТЬ ВСЕ',
            'menu_page_meta' => 'Обеды · ужины · детское меню',
            'menu_page_meta_note' => 'Обновляем сезонно',
            'concept_eyebrow' => 'СВОЯ КУХНЯ И ПЕКАРНЯ',
            'concept_title' => 'ЕДА С ХАРАКТЕРОМ — ВЕЧЕРА БЕЗ СУЕТЫ',
            'concept_aside' => 'Кофе - выпечка — 10:00–23:00',
            'concept_description' => 'Авторские блюда, свежая выпечка и кофейня под одной крышей. Приходите на бизнес-ланч, семейный ужин или тихий вечер — в Тетри всегда своя атмосфера.',
            'concept_cta_label' => 'БРОНИРОВАНИЕ',
            'stories_section_title' => 'СТОРИСЫ из MAX',
            'stories_section_aside' => 'Новинки и атмосфера зала',
            'stories_section_aside_note' => 'Смотрите в MAX',
            'contacts_title' => 'МЫ В СИМФЕРОПОЛЕ',
            'address' => 'пр-т. Кирова, 31А',
            'phones' => [
                ['number' => '+7 (978) 000-00-00'],
            ],
            'working_hours' => 'Ежедневно 10:00–23:00',
            'map_embed_url' => null,
            'footer_about' => 'Семейное кафе ТЕТРИ — место, где хорошо и детям, и родителям.',
            'social_links' => [
                ['icon' => 'heroicon-o-camera', 'text' => 'Instagram', 'url' => 'https://instagram.com'],
                ['icon' => 'heroicon-o-paper-airplane', 'text' => 'Telegram', 'url' => 'https://t.me'],
                ['icon' => 'heroicon-o-chat-bubble-left-right', 'text' => 'VK', 'url' => 'https://vk.com'],
            ],
            'booking_cta_label' => 'БРОНИРОВАНИЕ',
            'booking_cta_url' => '#contacts',
            'copyright' => '© ТЕТРИ',
            'design_credit' => '',
        ];
    }

    protected static function cacheKey(): string
    {
        return 'site_settings.'.self::KEY;
    }
}
