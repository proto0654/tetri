<?php

namespace App\Settings;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SiteSettings
{
    public const KEY = 'site';

    /**
     * @var list<string>
     */
    public const LIST_KEYS = ['hero_icons', 'kids_benefits', 'kids_images', 'phones', 'social_links'];

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        $stored = Cache::rememberForever(self::cacheKey(), function (): array {
            $setting = Setting::query()->where('key', self::KEY)->first();
            $value = $setting?->value ?? [];

            return is_array($value) ? $value : [];
        });

        return array_replace_recursive(self::defaults(), $stored);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return data_get($this->all(), $key, $default);
    }

    /**
     * Persist settings. Merges defaults → existing row → $data so partial updates
     * do not wipe unrelated keys. List fields in $data replace wholesale.
     *
     * @param  array<string, mixed>  $data
     */
    public function save(array $data): void
    {
        $existing = Setting::query()->where('key', self::KEY)->value('value') ?? [];
        if (! is_array($existing)) {
            $existing = [];
        }

        $merged = array_replace_recursive(self::defaults(), $existing, $data);

        foreach (self::LIST_KEYS as $listKey) {
            if (array_key_exists($listKey, $data)) {
                $merged[$listKey] = $data[$listKey];
            } elseif (array_key_exists($listKey, $existing)) {
                $merged[$listKey] = $existing[$listKey];
            }
        }

        Setting::query()->updateOrCreate(
            ['key' => self::KEY],
            ['value' => $merged],
        );

        Cache::forget(self::cacheKey());
    }

    /**
     * Write only blank keys from $data. Never overwrites non-blank stored values.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed> keys that were filled
     */
    public function fillMissing(array $data): array
    {
        $existing = Setting::query()->where('key', self::KEY)->value('value') ?? [];
        if (! is_array($existing)) {
            $existing = [];
        }

        $toFill = [];

        foreach ($data as $key => $value) {
            if (self::isBlank($existing[$key] ?? null)) {
                $toFill[$key] = $value;
            }
        }

        if ($toFill !== []) {
            $this->save($toFill);
        }

        return $toFill;
    }

    public static function isBlank(mixed $value): bool
    {
        return $value === null || $value === '' || $value === [];
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
            'hero_title' => 'Т Е Т Р И',
            'hero_subtitle' => 'СЕМЕЙНЫЙ РЕСТОРАН',
            'hero_background_image' => null,
            'hero_video_path' => null,
            'hero_video_preview' => null,
            'hero_overlay_from' => 'rgba(0, 0, 0, 0.45)',
            'hero_overlay_via' => 'rgba(0, 0, 0, 0.3)',
            'hero_overlay_to' => 'rgba(237, 226, 207, 0.95)',
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
            'map_latitude' => null,
            'map_longitude' => null,
            'map_marker_label' => null,
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
            'cookie_notice' => 'Мы используем файлы cookie для улучшения работы сайта. Подробнее — в {privacy}.',
            'privacy_title' => 'Политика конфиденциальности',
            'privacy_body' => "Настоящая политика описывает, как семейное кафе ТЕТРИ обрабатывает персональные данные и использует файлы cookie.\n\nМы можем собирать данные, которые вы оставляете при бронировании стола (имя, телефон и пожелания), а также технические данные, необходимые для работы сайта.\n\nФайлы cookie помогают корректно отображать страницы и улучшать работу сервиса. Продолжая пользоваться сайтом, вы соглашаетесь с использованием cookie в соответствии с этой политикой.\n\nПо вопросам обработки данных свяжитесь с нами по контактам, указанным на сайте.",
            'design_credit' => null,
            'favicon' => null,
            'logo' => null,
            'og_image' => null,
            'seo_title' => 'ТЕТРИ — семейное кафе',
            'seo_description' => 'Семейное кафе ТЕТРИ в Симферополе — место, где хорошо и детям, и родителям. Меню, детская игровая, бронирование стола.',
            'seo_title_suffix' => 'ТЕТРИ',
            'home_seo_title' => 'ТЕТРИ — семейное кафе в Симферополе',
            'home_seo_description' => 'Семейное кафе ТЕТРИ в Симферополе: детская игровая, авторское меню и уютный зал. Забронируйте стол онлайн.',
            'menu_seo_title' => 'Меню',
            'max_bot_token' => null,
            'max_chat_id' => null,
            'block_search_indexing' => true,
        ];
    }

    /**
     * Build a document title: "Part — Part — {seo_title_suffix}".
     * Empty parts are skipped. With no parts, returns seo_title.
     *
     * @param  array<string, mixed>  $settings
     */
    public static function documentTitle(array $settings, ?string ...$parts): string
    {
        $segments = array_values(array_filter(
            $parts,
            static fn (?string $part): bool => filled($part),
        ));

        if ($segments === []) {
            return (string) ($settings['seo_title'] ?? 'ТЕТРИ — семейное кафе');
        }

        $suffix = (string) ($settings['seo_title_suffix'] ?? 'ТЕТРИ');

        return implode(' — ', [...$segments, $suffix]);
    }

    protected static function cacheKey(): string
    {
        return 'site_settings.'.self::KEY;
    }
}
