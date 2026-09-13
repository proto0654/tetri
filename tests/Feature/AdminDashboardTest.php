<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageSiteSettings;
use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\MenuItems\MenuItemResource;
use App\Filament\Resources\Stories\StoryResource;
use App\Filament\Widgets\AdminQuickSettings;
use App\Models\User;
use App\Settings\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_quick_links_and_hides_filament_docs(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin');

        $response->assertOk();
        $response->assertSee('Выйти');
        $response->assertSee('Быстрый переход');
        $response->assertSee('Быстрые настройки');
        $response->assertSee('SEO');
        $response->assertSee('Навигация');
        $response->assertSee('Категории');
        $response->assertSee('Блюда');
        $response->assertSee('Сторисы');
        $response->assertSee('Настройки сайта');
        $response->assertSee(CategoryResource::getUrl('index'), false);
        $response->assertSee(MenuItemResource::getUrl('index'), false);
        $response->assertSee(StoryResource::getUrl('index'), false);
        $response->assertSee(ManageSiteSettings::getUrl(), false);
        $response->assertDontSee('https://filamentphp.com');
        $response->assertDontSee('Documentation');
    }

    public function test_quick_settings_widget_saves_contacts_and_hero_without_wiping_other_keys(): void
    {
        $user = User::factory()->create();
        $settings = app(SiteSettings::class);

        $settings->save([
            'hero_title' => 'Старый hero',
            'address' => 'Старый адрес',
            'working_hours' => '10:00–20:00',
            'phones' => [
                ['number' => '+7 (900) 000-00-00'],
            ],
            'nav_links' => [
                ['label' => 'О нас', 'type' => 'link', 'url' => '/#concept'],
            ],
            'footer_about' => 'Не трогать',
            'map_latitude' => '44.95',
        ]);

        Livewire::actingAs($user)
            ->test(AdminQuickSettings::class)
            ->assertOk()
            ->fillForm([
                'hero_title' => 'Новый hero',
                'address' => 'ул. Новая, 1',
                'working_hours' => '09:00–22:00',
                'phones' => [
                    ['number' => '+7 (978) 111-22-33'],
                ],
                'nav_links' => [
                    ['label' => 'Меню', 'type' => 'link', 'url' => '/menu'],
                    ['label' => 'Банкет', 'type' => 'booking', 'booking_source' => 'banket'],
                ],
                'seo_title' => 'Новый SEO title',
                'seo_description' => 'Новый SEO description',
                'seo_title_suffix' => 'ТЕТРИ',
                'home_seo_title' => 'Главная SEO title',
                'home_seo_description' => 'Главная SEO description',
                'menu_seo_title' => 'Меню',
                'menu_seo_description' => 'Меню SEO description',
            ])
            ->call('save')
            ->assertNotified();

        Cache::flush();

        $loaded = app(SiteSettings::class);

        $this->assertSame('Новый hero', $loaded->get('hero_title'));
        $this->assertSame('ул. Новая, 1', $loaded->get('address'));
        $this->assertSame('09:00–22:00', $loaded->get('working_hours'));
        $this->assertSame('+7 (978) 111-22-33', $loaded->get('phones.0.number'));
        $this->assertSame('Новый SEO title', $loaded->get('seo_title'));
        $this->assertSame('Главная SEO title', $loaded->get('home_seo_title'));
        $this->assertSame('Меню SEO description', $loaded->get('menu_seo_description'));
        $this->assertSame('Не трогать', $loaded->get('footer_about'));
        $this->assertSame('44.95', $loaded->get('map_latitude'));
        $this->assertSame('Меню', $loaded->get('nav_links.0.label'));
        $this->assertSame('/menu', $loaded->get('nav_links.0.url'));
        $this->assertSame('booking', $loaded->get('nav_links.1.type'));
        $this->assertCount(2, $loaded->get('nav_links'));
    }

    public function test_guest_is_redirected_from_dashboard_to_login(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/admin/login');
    }
}
