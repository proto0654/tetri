<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryMenuItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_has_many_active_ordered_menu_items(): void
    {
        $category = Category::factory()->create([
            'title' => 'Завтраки',
            'slug' => 'zavtraki',
            'columns' => 3,
        ]);

        MenuItem::factory()->create([
            'category_id' => $category->id,
            'title' => 'Сырники',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        MenuItem::factory()->create([
            'category_id' => $category->id,
            'title' => 'Тост',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        MenuItem::factory()->inactive()->create([
            'category_id' => $category->id,
            'title' => 'Скрытое блюдо',
        ]);

        $activeTitles = $category->menuItems()->active()->ordered()->pluck('title')->all();

        $this->assertSame(['Тост', 'Сырники'], $activeTitles);
        $this->assertSame(3, $category->fresh()->columns);
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'slug' => 'zavtraki',
        ]);
    }

    public function test_menu_item_belongs_to_category(): void
    {
        $category = Category::factory()->create();
        $item = MenuItem::factory()->create([
            'category_id' => $category->id,
            'price' => 390.50,
        ]);

        $this->assertTrue($item->category->is($category));
        $this->assertSame('390.50', $item->price);
    }
}
