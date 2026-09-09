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

    public function test_menu_page_renders_livewire_grid(): void
    {
        $category = Category::factory()->create([
            'title' => 'Завтраки',
            'is_active' => true,
        ]);

        MenuItem::factory()->create([
            'category_id' => $category->id,
            'title' => 'Сырники',
            'is_active' => true,
        ]);

        $this->get(route('menu'))
            ->assertOk()
            ->assertSeeLivewire(MenuGrid::class)
            ->assertSee('Сырники', false);
    }

    public function test_set_category_swaps_visible_menu_items(): void
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
            ->assertSet('activeCategoryId', $breakfast->id)
            ->assertSee('Сырники')
            ->assertDontSee('Чизкейк')
            ->call('setCategory', $desserts->id)
            ->assertSet('activeCategoryId', $desserts->id)
            ->assertSee('Чизкейк')
            ->assertDontSee('Сырники');
    }
}
