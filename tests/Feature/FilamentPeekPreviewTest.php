<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageSiteSettings;
use App\Filament\Resources\Categories\Pages\EditCategory;
use App\Filament\Resources\MenuItems\Pages\EditMenuItem;
use App\Filament\Resources\Stories\Pages\EditStory;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Story;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FilamentPeekPreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_menu_item_page_exposes_preview_action(): void
    {
        $user = User::factory()->create();
        $item = MenuItem::factory()->create();

        Livewire::actingAs($user)
            ->test(EditMenuItem::class, ['record' => $item->getRouteKey()])
            ->assertOk()
            ->assertActionExists('preview');
    }

    public function test_edit_category_page_exposes_preview_action(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        Livewire::actingAs($user)
            ->test(EditCategory::class, ['record' => $category->getRouteKey()])
            ->assertOk()
            ->assertActionExists('preview');
    }

    public function test_edit_story_page_exposes_preview_action(): void
    {
        $user = User::factory()->create();
        $story = Story::factory()->create();

        Livewire::actingAs($user)
            ->test(EditStory::class, ['record' => $story->getRouteKey()])
            ->assertOk()
            ->assertActionExists('preview');
    }

    public function test_manage_site_settings_page_exposes_preview_action(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ManageSiteSettings::class)
            ->assertOk()
            ->assertActionExists('preview');
    }
}
