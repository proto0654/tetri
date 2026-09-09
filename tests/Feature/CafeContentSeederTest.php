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
use Illuminate\Support\Facades\Storage;
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
        $this->assertSame(12, Story::query()->count());
        $this->assertTrue(Setting::query()->where('key', SiteSettings::KEY)->exists());

        $menuItemCount = MenuItem::query()->count();
        $this->assertGreaterThan(0, $menuItemCount);

        $mainCategory = Category::query()->where('slug', 'osnovnoe-menyu')->firstOrFail();
        $this->assertGreaterThan(12, $mainCategory->menuItems()->count());

        Category::query()->where('slug', 'zavtraki')->update(['title' => 'Завтраки (edited)']);
        Story::query()->where('title', 'Завтрак дня')->update(['title' => 'Сторис edited']);
        MenuItem::query()->where('title', 'Сырники со сметаной')->firstOrFail()->update([
            'description' => 'Custom description',
            'price' => 999,
        ]);
        app(SiteSettings::class)->save(['hero_title' => 'Custom Hero', 'address' => 'Custom Address']);

        User::query()->where('email', 'admin@tetri.test')->update(['name' => 'Custom Admin']);

        $this->seed(CafeContentSeeder::class);

        $this->assertSame(5, Category::query()->count());
        $this->assertSame(12, Story::query()->count());
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

    public function test_seeder_fills_blank_site_settings_keys_without_overwriting(): void
    {
        Http::fake();

        app(SiteSettings::class)->save([
            'hero_title' => 'Custom Hero',
            'address' => 'Custom Address',
            'hero_background_image' => null,
            'map_latitude' => null,
            'map_longitude' => null,
            'map_marker_label' => null,
        ]);

        $this->seed(CafeContentSeeder::class);

        $settings = app(SiteSettings::class);

        $this->assertSame('Custom Hero', $settings->get('hero_title'));
        $this->assertSame('Custom Address', $settings->get('address'));
        $this->assertNotBlank($settings->get('hero_background_image'));
        $this->assertSame('44.950000', $settings->get('map_latitude'));
        $this->assertSame('34.100000', $settings->get('map_longitude'));
        $this->assertSame('ТЕТРИ', $settings->get('map_marker_label'));
    }

    public function test_seeder_does_not_overwrite_existing_real_media_files(): void
    {
        Http::fake([
            'images.unsplash.com/*' => Http::response(str_repeat('x', 20_000), 200),
        ]);

        $path = 'categories/zavtraki.jpg';
        $original = str_repeat('KEEP', 15_000); // > 50KB real-media threshold
        Storage::disk('public')->put($path, $original);

        $this->seed(CafeContentSeeder::class);

        $this->assertSame($original, Storage::disk('public')->get($path));
    }

    protected function assertNotBlank(mixed $value): void
    {
        $this->assertFalse(SiteSettings::isBlank($value), 'Failed asserting that value is not blank.');
    }
}
