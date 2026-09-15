<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Story;
use App\Settings\SiteSettings;
use App\Support\Typograph;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders_key_sections_from_settings_and_models(): void
    {
        app(SiteSettings::class)->save([
            'hero_title' => 'Т Е Т Р И',
            'hero_subtitle' => 'СЕМЕЙНЫЙ РЕСТОРАН',
            'hero_overlay_from' => 'rgba(10, 20, 30, 0.5)',
            'hero_overlay_via' => 'rgba(40, 50, 60, 0.4)',
            'hero_overlay_to' => 'rgba(245, 240, 230, 0.95)',
            'kids_title' => 'МЕСТО ДЛЯ СЕМЬИ',
            'kids_eyebrow' => 'ДЛЯ ВСЕЙ СЕМЬИ',
            'kids_location_note' => 'пр-т. Кирова, 31А',
            'menu_section_eyebrow' => 'ОБЕДЫ, УЖИНЫ И АВТОРСКАЯ КУХНЯ.',
            'menu_section_description' => 'От завтраков до десертов',
            'concept_title' => 'ЕДА С ХАРАКТЕРОМ',
            'concept_eyebrow' => 'СВОЯ КУХНЯ И ПЕКАРНЯ',
            'concept_aside' => 'Кофе - выпечка — 10:00–23:00',
            'stories_section_aside' => 'Новинки и атмосфера зала',
            'stories_section_aside_note' => 'Смотрите в MAX',
            'contacts_title' => 'МЫ В СИМФЕРОПОЛЕ',
            'address' => 'ул. Севастопольская',
            'nav_links' => [
                ['label' => 'О нас', 'type' => 'link', 'url' => '/#concept'],
                ['label' => 'Детская', 'type' => 'link', 'url' => '/#kids'],
                ['label' => 'Меню', 'type' => 'link', 'url' => '/menu'],
                ['label' => 'Банкет', 'type' => 'booking', 'booking_source' => 'banket'],
                ['label' => 'Контакты', 'type' => 'link', 'url' => '#contacts'],
            ],
        ]);

        Category::factory()->create([
            'title' => 'Завтраки',
            'slug' => 'zavtraki',
            'is_active' => true,
        ]);

        Story::factory()->create([
            'title' => 'Сторис тест',
            'is_active' => true,
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Т Е Т Р И', false);
        $response->assertSee(Typograph::apply('СЕМЕЙНЫЙ РЕСТОРАН'), false);
        $response->assertSee(Typograph::apply('СЕМЕЙНЫЙ'), false);
        $response->assertSee(Typograph::apply('РЕСТОРАН'), false);
        $response->assertSee('data-hero-subtitle', false);
        $response->assertSee('linear-gradient(to bottom, rgba(10, 20, 30, 0.5), rgba(40, 50, 60, 0.4), rgba(245, 240, 230, 0.95))', false);
        $response->assertSee(Typograph::applyWithBreaks('МЕСТО ДЛЯ СЕМЬИ'), false);
        $response->assertSee(Typograph::apply('ДЛЯ ВСЕЙ СЕМЬИ'), false);
        $response->assertSee(Typograph::apply('пр-т. Кирова, 31А'), false);
        $response->assertSee(Typograph::apply('ОБЕДЫ, УЖИНЫ И АВТОРСКАЯ КУХНЯ.'), false);
        $response->assertSee(Typograph::apply('От завтраков до десертов'), false);
        $response->assertSee(Typograph::applyWithBreaks('ЕДА С ХАРАКТЕРОМ'), false);
        $response->assertSee(Typograph::apply('СВОЯ КУХНЯ И ПЕКАРНЯ'), false);
        $response->assertSee(Typograph::apply('Кофе - выпечка — 10:00–23:00'), false);
        $response->assertSee(Typograph::apply('Новинки и атмосфера зала'), false);
        $response->assertSee(Typograph::apply('Смотрите в MAX'), false);
        $response->assertSee(Typograph::applyWithBreaks('МЫ В СИМФЕРОПОЛЕ'), false);
        $response->assertSee(Typograph::apply('Завтраки'), false);
        $response->assertSee('СТОРИСЫ', false);
        $response->assertSee('data-story-open', false);
        $response->assertSee('id="story-viewer"', false);
        $response->assertSee('data-story-viewer', false);
        $response->assertSee('aria-label="Сторисы"', false);
        $response->assertSee('aria-label="Назад"', false);
        $response->assertSee('aria-label="Свернуть"', false);
        $response->assertSee('data-story-viewer-prev', false);
        $response->assertSee('data-story-viewer-next', false);
        $response->assertSee(route('menu'), false);
        $response->assertSee(route('menu.category', 'zavtraki'), false);
        $response->assertSee('О нас', false);
        $response->assertSee('Детская', false);
        $response->assertSee('Банкет', false);
        $response->assertSee('/#concept', false);
        $response->assertSee('nav-banket', false);
    }

    public function test_home_page_hero_story_uses_video_metadata_frame_not_preview_poster(): void
    {
        app(SiteSettings::class)->save([
            'hero_video_path' => 'site/hero/story.mp4',
            'hero_video_preview' => 'site/hero/preview.jpg',
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('data-hero-story', false);
        $response->assertSee('/storage/site/hero/story.mp4#t=0.1', false);
        $response->assertDontSee('poster="/storage/site/hero/preview.jpg"', false);
        $response->assertDontSee('src="/storage/site/hero/preview.jpg"', false);
    }

    public function test_home_page_hero_story_falls_back_to_preview_image_without_video(): void
    {
        app(SiteSettings::class)->save([
            'hero_video_path' => null,
            'hero_video_preview' => 'site/hero/preview.jpg',
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('data-hero-story', false);
        $response->assertSee('/storage/site/hero/preview.jpg', false);
        $response->assertDontSee('site/hero/story.mp4', false);
        $response->assertDontSee('#t=0.1', false);
    }

    public function test_home_page_renders_custom_nav_links_from_settings(): void
    {
        app(SiteSettings::class)->save([
            'nav_links' => [
                ['label' => 'Галерея', 'type' => 'link', 'url' => '/#stories'],
                ['label' => 'Зал', 'type' => 'booking', 'booking_source' => 'hall'],
            ],
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Галерея', false);
        $response->assertSee('/#stories', false);
        $response->assertSee('Зал', false);
        $response->assertSee('nav-hall', false);
        $response->assertSee('footer-hall', false);
        $response->assertDontSee('О нас', false);
    }

    public function test_home_page_renders_line_breaks_in_section_titles(): void
    {
        app(SiteSettings::class)->save([
            'kids_title' => 'МЕСТО<br>ДЛЯ СЕМЬИ',
            'concept_title' => "ЕДА\nС ХАРАКТЕРОМ",
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee(Typograph::applyWithBreaks('МЕСТО<br>ДЛЯ СЕМЬИ'), false);
        $response->assertSee(Typograph::applyWithBreaks("ЕДА\nС ХАРАКТЕРОМ"), false);
    }

    public function test_home_page_shows_yandex_route_link_when_map_coordinates_are_set(): void
    {
        config(['services.yandex_maps.key' => 'test-yandex-maps-key']);

        app(SiteSettings::class)->save([
            'map_latitude' => '44.950000',
            'map_longitude' => '34.100000',
            'map_marker_label' => 'ТЕТРИ',
            'map_embed_url' => null,
            'logo' => 'site/logo/mark.png',
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Проложить маршрут на карте', false);
        $response->assertSee('https://yandex.ru/maps/?rtext=', false);
        $response->assertSee('data-yandex-map', false);
        $response->assertSee('data-lat="44.950000"', false);
        $response->assertSee('data-lng="34.100000"', false);
        $response->assertSee('data-label="ТЕТРИ"', false);
        $response->assertSee('data-logo=', false);
        $response->assertSee('site/logo/mark.png', false);
        $response->assertSee('data-apikey="test-yandex-maps-key"', false);
        $response->assertDontSee('map-widget/v1', false);
    }

    public function test_home_page_falls_back_to_iframe_map_without_api_key(): void
    {
        config(['services.yandex_maps.key' => null]);

        app(SiteSettings::class)->save([
            'map_latitude' => '44.950000',
            'map_longitude' => '34.100000',
            'map_marker_label' => 'ТЕТРИ',
            'map_embed_url' => null,
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('map-widget/v1', false);
        $response->assertDontSee('data-yandex-map', false);
    }

    public function test_home_page_prefers_map_embed_url_over_js_map(): void
    {
        config(['services.yandex_maps.key' => 'test-yandex-maps-key']);

        app(SiteSettings::class)->save([
            'map_latitude' => '44.950000',
            'map_longitude' => '34.100000',
            'map_marker_label' => 'ТЕТРИ',
            'map_embed_url' => 'https://yandex.ru/map-widget/v1/?um=constructor%3Acustom',
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('um=constructor%3Acustom', false);
        $response->assertDontSee('data-yandex-map', false);
    }

    public function test_home_page_hides_yandex_route_link_without_map_coordinates(): void
    {
        app(SiteSettings::class)->save([
            'map_latitude' => null,
            'map_longitude' => null,
            'map_embed_url' => null,
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertDontSee('Проложить маршрут на карте', false);
    }

    public function test_menu_route_is_available(): void
    {
        app(SiteSettings::class)->save([
            'menu_section_eyebrow' => 'ОБЕДЫ, УЖИНЫ И АВТОРСКАЯ КУХНЯ.',
            'menu_page_meta' => 'Обеды · ужины · детское меню',
            'menu_page_meta_note' => 'Обновляем сезонно',
        ]);

        $response = $this->get(route('menu'));

        $response->assertOk();
        $response->assertSee(Typograph::apply('ОБЕДЫ, УЖИНЫ И АВТОРСКАЯ КУХНЯ.'), false);
        $response->assertSee(Typograph::apply('Обеды · ужины · детское меню'), false);
        $response->assertSee(Typograph::apply('Обновляем сезонно'), false);
    }

    public function test_home_and_menu_reuse_the_same_contacts_footer_block(): void
    {
        app(SiteSettings::class)->save([
            'contacts_title' => 'МЫ В ТЕСТЕ',
            'address' => 'ул. Тестовая, 1',
            'copyright' => '© ТЕТРИ тест',
            'footer_about' => 'Семейное кафе в футере',
            'cookie_notice' => 'Cookie-уведомление в футере. Подробнее — в {privacy}.',
            'privacy_title' => 'Политика в футере',
            'map_latitude' => '44.950000',
            'map_longitude' => '34.100000',
        ]);

        $home = $this->get(route('home'));
        $menu = $this->get(route('menu'));

        $home->assertOk();
        $menu->assertOk();

        foreach ([$home, $menu] as $response) {
            $response->assertSee(Typograph::applyWithBreaks('МЫ В ТЕСТЕ'), false);
            $response->assertSee(Typograph::apply('ул. Тестовая, 1'), false);
            $response->assertSee('Проложить маршрут на карте', false);
            $response->assertSee('id="contacts"', false);
            $response->assertSee('НАВИГАЦИЯ', false);
            $response->assertSee('МЫ В СЕТИ', false);
            $response->assertDontSee(Typograph::apply('© ТЕТРИ тест'), false);
            $response->assertSee(Typograph::apply('Семейное кафе в футере'), false);
            $response->assertSee(Typograph::apply('Cookie-уведомление в футере. Подробнее — в '), false);
            $response->assertSee(
                '<a href="'.route('privacy').'" class="underline underline-offset-2 hover:text-cream">'.Typograph::apply('Политика в футере').'</a>',
                false,
            );
            $response->assertSee('Наверх ↑', false);
        }
    }

    public function test_home_page_renders_favicon_links_when_set(): void
    {
        app(SiteSettings::class)->save([
            'favicon' => 'site/favicon/icon.png',
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('rel="icon"', false);
        $response->assertSee('/storage/site/favicon/icon.png', false);
        $response->assertSee('rel="apple-touch-icon"', false);
    }

    public function test_home_page_omits_favicon_links_when_unset(): void
    {
        app(SiteSettings::class)->save([
            'favicon' => null,
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertDontSee('rel="icon"', false);
        $response->assertDontSee('rel="apple-touch-icon"', false);
    }

    public function test_home_page_renders_logo_image_when_set(): void
    {
        app(SiteSettings::class)->save([
            'logo' => 'site/logo/mark.png',
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('/storage/site/logo/mark.png', false);
        $response->assertSee('alt="ТЕТРИ"', false);
    }

    public function test_home_page_falls_back_to_text_logo_when_unset(): void
    {
        app(SiteSettings::class)->save([
            'logo' => null,
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertDontSee('/storage/site/logo/', false);
        $response->assertSee('ТЕТРИ', false);
    }

    public function test_home_page_renders_seo_title_and_description_from_settings(): void
    {
        app(SiteSettings::class)->save([
            'home_seo_title' => 'Кастомный title главной',
            'home_seo_description' => 'Кастомный description главной для поиска.',
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('<title>Кастомный title главной</title>', false);
        $response->assertSee('name="description"', false);
        $response->assertSee('Кастомный description главной для поиска.', false);
        $response->assertSee('property="og:description"', false);
    }

    public function test_home_page_renders_og_image_when_set(): void
    {
        app(SiteSettings::class)->save([
            'og_image' => 'site/og/share.jpg',
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('property="og:image"', false);
        $response->assertSee('/storage/site/og/share.jpg', false);
        $response->assertSee('name="twitter:card"', false);
        $response->assertSee('summary_large_image', false);
    }

    public function test_home_page_omits_og_image_when_unset(): void
    {
        app(SiteSettings::class)->save([
            'og_image' => null,
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertDontSee('property="og:image"', false);
        $response->assertDontSee('name="twitter:image"', false);
        $response->assertDontSee('summary_large_image', false);
    }
}
