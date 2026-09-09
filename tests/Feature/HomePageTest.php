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
        $response->assertSee('linear-gradient(to bottom, rgba(10, 20, 30, 0.5), rgba(40, 50, 60, 0.4), rgba(245, 240, 230, 0.95))', false);
        $response->assertSee(Typograph::apply('МЕСТО ДЛЯ СЕМЬИ'), false);
        $response->assertSee(Typograph::apply('ДЛЯ ВСЕЙ СЕМЬИ'), false);
        $response->assertSee(Typograph::apply('пр-т. Кирова, 31А'), false);
        $response->assertSee(Typograph::apply('ОБЕДЫ, УЖИНЫ И АВТОРСКАЯ КУХНЯ.'), false);
        $response->assertSee(Typograph::apply('От завтраков до десертов'), false);
        $response->assertSee(Typograph::apply('ЕДА С ХАРАКТЕРОМ'), false);
        $response->assertSee(Typograph::apply('СВОЯ КУХНЯ И ПЕКАРНЯ'), false);
        $response->assertSee(Typograph::apply('Кофе - выпечка — 10:00–23:00'), false);
        $response->assertSee(Typograph::apply('Новинки и атмосфера зала'), false);
        $response->assertSee(Typograph::apply('Смотрите в MAX'), false);
        $response->assertSee(Typograph::apply('МЫ В СИМФЕРОПОЛЕ'), false);
        $response->assertSee(Typograph::apply('Завтраки'), false);
        $response->assertSee('СТОРИСЫ', false);
        $response->assertSee(route('menu'), false);
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
}
