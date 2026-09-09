<?php

namespace Tests\Feature;

use App\Settings\SiteSettings;
use App\Support\Typograph;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrivacyPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_privacy_page_renders_title_and_body_paragraphs_from_settings(): void
    {
        app(SiteSettings::class)->save([
            'privacy_title' => 'Политика теста',
            'privacy_body' => "Первый абзац политики.\n\nВторой абзац политики.",
        ]);

        $response = $this->get(route('privacy'));

        $response->assertOk();
        $response->assertSee(Typograph::applyWithBreaks('Политика теста'), false);
        $response->assertSee(Typograph::apply('Первый абзац политики.'), false);
        $response->assertSee(Typograph::apply('Второй абзац политики.'), false);
    }

    public function test_home_and_menu_show_cookie_notice_with_inline_privacy_link(): void
    {
        app(SiteSettings::class)->save([
            'cookie_notice' => 'Мы используем cookie на тесте. Подробнее — в {privacy}.',
            'privacy_title' => 'Политика теста',
        ]);

        $home = $this->get(route('home'));
        $menu = $this->get(route('menu'));

        $home->assertOk();
        $menu->assertOk();

        foreach ([$home, $menu] as $response) {
            $response->assertSee(Typograph::apply('Мы используем cookie на тесте. Подробнее — в '), false);
            $response->assertSee(
                '<a href="'.route('privacy').'" class="underline underline-offset-2 hover:text-cream">'.Typograph::apply('Политика теста').'</a>',
                false,
            );
            $response->assertDontSee('>Политика</a>', false);
        }
    }
}
