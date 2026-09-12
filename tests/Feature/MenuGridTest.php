<?php

namespace Tests\Feature;

use App\Livewire\MenuGrid;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MenuGridTest extends TestCase
{
    use RefreshDatabase;

    public function test_menu_page_defaults_to_all_menu_with_category_previews(): void
    {
        $breakfast = Category::factory()->create([
            'title' => 'Завтраки',
            'slug' => 'zavtraki',
            'sort_order' => 1,
            'columns' => 2,
            'is_active' => true,
        ]);

        $desserts = Category::factory()->create([
            'title' => 'Десерты',
            'slug' => 'deserty',
            'sort_order' => 2,
            'columns' => 3,
            'is_active' => true,
        ]);

        MenuItem::factory()->count(3)->sequence(
            ['title' => 'Сырники', 'sort_order' => 1],
            ['title' => 'Омлет', 'sort_order' => 2],
            ['title' => 'Каша', 'sort_order' => 3],
        )->create([
            'category_id' => $breakfast->id,
            'is_active' => true,
        ]);

        MenuItem::factory()->create([
            'category_id' => $desserts->id,
            'title' => 'Чизкейк',
            'is_active' => true,
        ]);

        $this->get(route('menu'))
            ->assertOk()
            ->assertSeeLivewire(MenuGrid::class)
            ->assertSee('ВСЕ МЕНЮ', false)
            ->assertSee('Сырники', false)
            ->assertSee('Омлет', false)
            ->assertDontSee('Каша', false)
            ->assertSee('Чизкейк', false);
    }

    public function test_set_category_and_show_all_swap_visible_menu_items(): void
    {
        $breakfast = Category::factory()->create([
            'title' => 'Завтраки',
            'sort_order' => 1,
            'columns' => 3,
            'is_active' => true,
        ]);

        $desserts = Category::factory()->create([
            'title' => 'Десерты',
            'sort_order' => 2,
            'columns' => 2,
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

        Livewire::test(MenuGrid::class)
            ->assertSet('activeCategoryId', null)
            ->assertSee('Сырники')
            ->assertSee('Чизкейк')
            ->call('setCategory', $desserts->id)
            ->assertSet('activeCategoryId', $desserts->id)
            ->assertSee('Чизкейк')
            ->assertDontSee('Сырники')
            ->call('showAll')
            ->assertSet('activeCategoryId', null)
            ->assertSee('Сырники')
            ->assertSee('Чизкейк');
    }

    public function test_category_mode_paginates_twelve_items_per_page(): void
    {
        $category = Category::factory()->create([
            'title' => 'Завтраки',
            'slug' => 'zavtraki',
            'is_active' => true,
        ]);

        MenuItem::factory()->count(13)->sequence(fn ($sequence) => [
            'title' => sprintf('Dish%02d', $sequence->index + 1),
            'sort_order' => $sequence->index + 1,
        ])->create([
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        Livewire::test(MenuGrid::class, ['categorySlug' => 'zavtraki'])
            ->assertSet('activeCategoryId', $category->id)
            ->assertSee('Dish01')
            ->assertSee('Dish12')
            ->assertDontSee('Dish13')
            ->call('gotoPage', 2)
            ->assertSee('Dish13')
            ->assertDontSee('Dish01')
            ->assertSee('bg-plum', false)
            ->assertSee('bg-olive', false)
            ->assertDontSee('border-gray-300', false);
    }

    public function test_pagination_scrolls_to_heading_but_category_tabs_do_not(): void
    {
        $breakfast = Category::factory()->create([
            'title' => 'Завтраки',
            'slug' => 'zavtraki',
            'is_active' => true,
        ]);

        $desserts = Category::factory()->create([
            'title' => 'Десерты',
            'slug' => 'deserty',
            'is_active' => true,
        ]);

        MenuItem::factory()->count(13)->create([
            'category_id' => $breakfast->id,
            'is_active' => true,
        ]);

        MenuItem::factory()->create([
            'category_id' => $desserts->id,
            'title' => 'Чизкейк',
            'is_active' => true,
        ]);

        $scrollJs = <<<'JS'
            document.getElementById('menu-grid-heading')?.scrollIntoView({ behavior: 'smooth', block: 'start' })
        JS;

        $component = Livewire::test(MenuGrid::class, ['categorySlug' => 'zavtraki'])
            ->call('gotoPage', 2)
            ->assertJs($scrollJs);

        $component
            ->call('setCategory', $desserts->id)
            ->assertSet('activeCategoryId', $desserts->id)
            ->assertJs('window.history.pushState({}, "", '.json_encode(route('menu.category', 'deserty')).')');

        $evaluatedJs = $component->effects['xjs'] ?? [];

        $this->assertFalse(
            collect($evaluatedJs)->contains(
                fn (array $item): bool => str_contains($item['expression'] ?? '', 'scrollIntoView')
            ),
            'Category tab changes must not scroll to the menu heading.'
        );
    }

    public function test_menu_cards_link_to_dish_show_page(): void
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

        Livewire::test(MenuGrid::class)
            ->assertSee(route('menu.show', [$category, $item]), false);
    }

    public function test_category_route_opens_category_tab(): void
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

        $this->get(route('menu.category', $desserts))
            ->assertOk()
            ->assertSee('Чизкейк', false)
            ->assertDontSee('Сырники', false)
            ->assertSee('Десерты — Меню — ТЕТРИ', false);

        Livewire::test(MenuGrid::class, ['categorySlug' => 'deserty'])
            ->assertSet('activeCategoryId', $desserts->id)
            ->assertSee('Чизкейк')
            ->assertDontSee('Сырники');
    }

    public function test_inactive_category_route_returns_not_found(): void
    {
        $category = Category::factory()->inactive()->create([
            'slug' => 'hidden',
        ]);

        $this->get(route('menu.category', $category))
            ->assertNotFound();
    }

    public function test_legacy_category_query_redirects_to_category_path(): void
    {
        $category = Category::factory()->create([
            'slug' => 'deserty',
            'is_active' => true,
        ]);

        $this->get(route('menu', ['category' => 'deserty']))
            ->assertRedirect(route('menu.category', $category));
    }
}
