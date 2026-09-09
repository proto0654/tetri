<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Setting;
use App\Models\Story;
use App\Models\User;
use App\Settings\SiteSettings;
use Database\Seeders\CafeContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CafeContentSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_demo_content_once_and_preserves_edits_on_rerun(): void
    {
        Http::fake();

        $this->seed(CafeContentSeeder::class);

        $this->assertDatabaseHas('users', ['email' => 'admin@tetri.test']);
        $this->assertSame(5, Category::query()->count());
        $this->assertSame(5, Story::query()->count());
        $this->assertTrue(Setting::query()->where('key', SiteSettings::KEY)->exists());

        $menuItemCount = MenuItem::query()->count();
        $this->assertGreaterThan(0, $menuItemCount);

        Category::query()->where('slug', 'zavtraki')->update(['title' => 'Завтраки (edited)']);
        Story::query()->where('title', 'Сторис 1')->update(['title' => 'Сторис edited']);
        MenuItem::query()->where('title', 'Сырники со сметаной')->firstOrFail()->update([
            'description' => 'Custom description',
            'price' => 999,
        ]);
        app(SiteSettings::class)->save(['hero_title' => 'Custom Hero', 'address' => 'Custom Address']);

        User::query()->where('email', 'admin@tetri.test')->update(['name' => 'Custom Admin']);

        $this->seed(CafeContentSeeder::class);

        $this->assertSame(5, Category::query()->count());
        $this->assertSame(5, Story::query()->count());
        $this->assertSame($menuItemCount, MenuItem::query()->count());
        $this->assertSame(1, Setting::query()->where('key', SiteSettings::KEY)->count());

        $this->assertDatabaseHas('categories', ['slug' => 'zavtraki', 'title' => 'Завтраки (edited)']);
        $this->assertDatabaseHas('stories', ['title' => 'Сторис edited']);
        $this->assertDatabaseHas('menu_items', [
            'title' => 'Сырники со сметаной',
            'description' => 'Custom description',
            'price' => 999,
        ]);
        $this->assertDatabaseHas('users', ['email' => 'admin@tetri.test', 'name' => 'Custom Admin']);

        $settings = app(SiteSettings::class);
        $this->assertSame('Custom Hero', $settings->get('hero_title'));
        $this->assertSame('Custom Address', $settings->get('address'));
    }
}
