<?php

namespace Tests\Feature;

use App\Livewire\MenuGrid;
use App\Models\Category;
use App\Models\MenuItem;
use App\Settings\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MenuItemShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_dish_page_renders_title_price_description_and_breadcrumbs(): void
    {
        $category = Category::factory()->create([
            'title' => 'Завтраки',
            'slug' => 'zavtraki',
            'is_active' => true,
        ]);

        $item = MenuItem::factory()->create([
            'category_id' => $category->id,
            'title' => 'Сырники со сметаной',
            'slug' => 'syrniki',
            'description' => 'Творожные сырники',
            'price' => 390,
            'is_active' => true,
        ]);

        $this->get(route('menu.show', [$category, $item]))
            ->assertOk()
            ->assertSee('Сырники со сметаной', false)
            ->assertSee('Творожные сырники', false)
            ->assertSee('390', false)
            ->assertSee('Меню', false)
            ->assertSee('Завтраки', false)
            ->assertSee(route('menu'), false)
            ->assertSee(route('menu.category', $category), false);
    }

    public function test_inactive_item_returns_not_found(): void
    {
        $category = Category::factory()->create([
            'slug' => 'zavtraki',
            'is_active' => true,
        ]);

        $item = MenuItem::factory()->inactive()->create([
            'category_id' => $category->id,
            'slug' => 'syrniki',
        ]);

        $this->get(route('menu.show', [$category, $item]))
            ->assertNotFound();
    }

    public function test_inactive_category_returns_not_found(): void
    {
        $category = Category::factory()->inactive()->create([
            'slug' => 'zavtraki',
        ]);

        $item = MenuItem::factory()->create([
            'category_id' => $category->id,
            'slug' => 'syrniki',
            'is_active' => true,
        ]);

        $this->get(route('menu.show', [$category, $item]))
            ->assertNotFound();
    }

    public function test_item_from_another_category_returns_not_found(): void
    {
        $breakfast = Category::factory()->create([
            'slug' => 'zavtraki',
            'is_active' => true,
        ]);

        $desserts = Category::factory()->create([
            'slug' => 'deserty',
            'is_active' => true,
        ]);

        $item = MenuItem::factory()->create([
            'category_id' => $desserts->id,
            'slug' => 'cheesecake',
            'is_active' => true,
        ]);

        $this->get("/menu/{$breakfast->slug}/{$item->slug}")
            ->assertNotFound();
    }

    public function test_related_items_are_from_same_category_excluding_current(): void
    {
        $category = Category::factory()->create([
            'slug' => 'zavtraki',
            'is_active' => true,
        ]);

        $otherCategory = Category::factory()->create([
            'slug' => 'deserty',
            'is_active' => true,
        ]);

        $current = MenuItem::factory()->create([
            'category_id' => $category->id,
            'title' => 'Текущее блюдо',
            'slug' => 'current',
            'is_active' => true,
        ]);

        $sameA = MenuItem::factory()->create([
            'category_id' => $category->id,
            'title' => 'Сосед А',
            'slug' => 'same-a',
            'is_active' => true,
        ]);

        $sameB = MenuItem::factory()->create([
            'category_id' => $category->id,
            'title' => 'Сосед Б',
            'slug' => 'same-b',
            'is_active' => true,
        ]);

        MenuItem::factory()->create([
            'category_id' => $otherCategory->id,
            'title' => 'Чужое блюдо',
            'slug' => 'other',
            'is_active' => true,
        ]);

        $response = $this->get(route('menu.show', [$category, $current]))
            ->assertOk()
            ->assertSee('Смотри также', false)
            ->assertDontSee('Чужое блюдо', false);

        $html = $response->getContent();

        $this->assertStringNotContainsString(
            'href="'.route('menu.show', [$category, $current]).'"',
            $html,
        );

        $relatedLinks = substr_count($html, 'href="'.route('menu.show', [$category, $sameA]).'"')
            + substr_count($html, 'href="'.route('menu.show', [$category, $sameB]).'"');

        $this->assertGreaterThanOrEqual(1, $relatedLinks);
        $this->assertLessThanOrEqual(2, $relatedLinks);
    }

    public function test_menu_grid_links_to_dish_page(): void
    {
        $category = Category::factory()->create([
            'title' => 'Завтраки',
            'slug' => 'zavtraki',
            'is_active' => true,
        ]);

        $item = MenuItem::factory()->create([
            'category_id' => $category->id,
            'title' => 'Сырники',
            'slug' => 'syrniki',
            'is_active' => true,
        ]);

        $this->get(route('menu'))
            ->assertOk()
            ->assertSee(route('menu.show', [$category, $item]), false);
    }

    public function test_menu_grid_opens_category_from_slug_prop(): void
    {
        $breakfast = Category::factory()->create([
            'title' => 'Завтраки',
            'slug' => 'zavtraki',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $desserts = Category::factory()->create([
            'title' => 'Десерты',
            'slug' => 'deserty',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        MenuItem::factory()->create([
            'category_id' => $breakfast->id,
            'title' => 'Сырники',
            'is_active' => true,
        ]);

        MenuItem::factory()->create([
            'category_id' => $desserts->id,
            'title' => 'Чизкейк',
            'is_active' => true,
        ]);

        Livewire::test(MenuGrid::class, ['categorySlug' => 'deserty'])
            ->assertSet('activeCategoryId', $desserts->id)
            ->assertSee('Чизкейк')
            ->assertDontSee('Сырники');
    }

    public function test_dish_page_uses_item_image_for_og_and_description_for_meta(): void
    {
        $category = Category::factory()->create([
            'slug' => 'zavtraki',
            'is_active' => true,
        ]);

        $item = MenuItem::factory()->create([
            'category_id' => $category->id,
            'title' => 'Сырники',
            'slug' => 'syrniki',
            'description' => 'Творожные сырники со сметаной',
            'image' => 'menu-items/syrniki.jpg',
            'is_active' => true,
        ]);

        app(SiteSettings::class)->save([
            'og_image' => 'site/og/share.jpg',
            'menu_og_image' => 'site/og/menu.jpg',
            'seo_description' => 'Общий description сайта',
        ]);

        $response = $this->get(route('menu.show', [$category, $item]));

        $response->assertOk();
        $response->assertSee('property="og:image"', false);
        $response->assertSee('/storage/menu-items/syrniki.jpg', false);
        $response->assertDontSee('/storage/site/og/menu.jpg', false);
        $response->assertDontSee('/storage/site/og/share.jpg', false);
        $response->assertSee('name="description"', false);
        $response->assertSee('Творожные сырники со сметаной', false);
        $response->assertSee('property="og:description"', false);
    }

    public function test_dish_page_falls_back_to_menu_og_image_when_item_has_no_image(): void
    {
        $category = Category::factory()->create([
            'slug' => 'zavtraki',
            'is_active' => true,
        ]);

        $item = MenuItem::factory()->create([
            'category_id' => $category->id,
            'slug' => 'syrniki',
            'image' => null,
            'description' => null,
            'is_active' => true,
        ]);

        app(SiteSettings::class)->save([
            'og_image' => 'site/og/share.jpg',
            'menu_og_image' => 'site/og/menu.jpg',
            'seo_description' => 'Общий description сайта',
        ]);

        $response = $this->get(route('menu.show', [$category, $item]));

        $response->assertOk();
        $response->assertSee('/storage/site/og/menu.jpg', false);
        $response->assertDontSee('/storage/site/og/share.jpg', false);
        $response->assertSee('Общий description сайта', false);
    }
}
