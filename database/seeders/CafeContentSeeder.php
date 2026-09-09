<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Setting;
use App\Models\Story;
use App\Models\User;
use App\Settings\SiteSettings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Idempotent demo content: create-if-missing / fill blank SiteSettings keys only.
 * Never overwrites Filament/DB edits or existing real media files on disk.
 * Intentional wipe: migrate:fresh --seed.
 */
class CafeContentSeeder extends Seeder
{
    /**
     * Curated Unsplash food photos (downloaded into storage — no hotlinks in runtime).
     *
     * @var array<string, string>
     */
    private const STOCK = [
        'zavtraki' => 'https://images.unsplash.com/photo-1533089860892-a7c6f0a88666?auto=format&fit=crop&w=900&h=1200&q=80',
        'osnovnoe-menyu' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=900&h=1200&q=80',
        'detskoe-menyu' => 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=900&h=1200&q=80',
        'deserty' => 'https://images.unsplash.com/photo-1488477181946-6428a0291777?auto=format&fit=crop&w=900&h=1200&q=80',
        'kofe-i-napitki' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=900&h=1200&q=80',
        'syrniki' => 'https://images.unsplash.com/photo-1484723091739-30a097e8f929?auto=format&fit=crop&w=1000&h=750&q=80',
        'avokado' => 'https://images.unsplash.com/photo-1525351484163-7529414344d8?auto=format&fit=crop&w=1000&h=750&q=80',
        'bowl' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=1000&h=750&q=80',
        'pasta' => 'https://images.unsplash.com/photo-1621996346565-e3dbc646d9a9?auto=format&fit=crop&w=1000&h=750&q=80',
        'nuggets' => 'https://images.unsplash.com/photo-1562967914-608f82629710?auto=format&fit=crop&w=1000&h=750&q=80',
        'cheesecake' => 'https://images.unsplash.com/photo-1533134242443-d4fd215305ad?auto=format&fit=crop&w=1000&h=750&q=80',
        'cappuccino' => 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?auto=format&fit=crop&w=1000&h=750&q=80',
        'hero-bg' => 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=1600&h=1000&q=80',
        'hero-preview' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=900&h=1200&q=80',
        'kids-1' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=1000&h=750&q=80',
        'kids-2' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=1000&h=750&q=80',
        // Stories for MAX — food / kids / social (poster placeholders; videos empty until upload)
        'story-1' => 'https://images.unsplash.com/photo-1482049016688-2d3e1b311543?auto=format&fit=crop&w=720&h=1280&q=80',
        'story-2' => 'https://images.unsplash.com/photo-1476224203421-9ac39bcb3327?auto=format&fit=crop&w=720&h=1280&q=80',
        'story-3' => 'https://images.unsplash.com/photo-1497034825429-c343d7c6a68f?auto=format&fit=crop&w=720&h=1280&q=80',
        'story-4' => 'https://images.unsplash.com/photo-1565958011703-44f9829ba187?auto=format&fit=crop&w=720&h=1280&q=80',
        'story-5' => 'https://images.unsplash.com/photo-1551024601-bec78aea704b?auto=format&fit=crop&w=720&h=1280&q=80',
        'story-6' => 'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?auto=format&fit=crop&w=720&h=1280&q=80',
        'story-7' => 'https://images.unsplash.com/photo-1476703993599-0035a21b17a9?auto=format&fit=crop&w=720&h=1280&q=80',
        'story-8' => 'https://images.unsplash.com/photo-1515488042361-ee00e0ddd4e4?auto=format&fit=crop&w=720&h=1280&q=80',
        'story-9' => 'https://images.unsplash.com/photo-1566454825481-4e48f80aa4d7?auto=format&fit=crop&w=720&h=1280&q=80',
        'story-10' => 'https://images.unsplash.com/photo-1521017432531-fbd92d768814?auto=format&fit=crop&w=720&h=1280&q=80',
        'story-11' => 'https://images.unsplash.com/photo-1559339352-11d035aa65de?auto=format&fit=crop&w=720&h=1280&q=80',
        'story-12' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=720&h=1280&q=80',
    ];

    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'admin@tetri.test'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        $categories = [
            ['title' => 'Завтраки', 'slug' => 'zavtraki', 'columns' => 3, 'sort_order' => 1, 'stock' => 'zavtraki'],
            ['title' => 'Основное меню', 'slug' => 'osnovnoe-menyu', 'columns' => 3, 'sort_order' => 2, 'stock' => 'osnovnoe-menyu'],
            ['title' => 'Детское меню', 'slug' => 'detskoe-menyu', 'columns' => 2, 'sort_order' => 3, 'stock' => 'detskoe-menyu'],
            ['title' => 'Десерты', 'slug' => 'deserty', 'columns' => 2, 'sort_order' => 4, 'stock' => 'deserty'],
            ['title' => 'Кофе и напитки', 'slug' => 'kofe-i-napitki', 'columns' => 2, 'sort_order' => 5, 'stock' => 'kofe-i-napitki'],
        ];

        foreach ($categories as $index => $data) {
            $stock = $data['stock'];
            unset($data['stock']);

            $category = Category::query()->where('slug', $data['slug'])->first();
            $demoImagePath = "categories/{$data['slug']}.jpg";

            if ($category === null) {
                $category = Category::query()->create([
                    ...$data,
                    'image' => $this->storeStockImage($stock, $demoImagePath),
                    'is_active' => true,
                ]);
            } elseif (blank($category->image)) {
                $category->update([
                    'image' => $this->storeStockImage($stock, $demoImagePath),
                ]);
            } elseif ($category->image === $demoImagePath) {
                $this->storeStockImage($stock, $demoImagePath);
            }

            $this->seedMenuItems($category, $index);
        }

        // Placeholder MAX stories: food / kids / social. Real videos replace empty mp4 later.
        $stories = [
            ['stock' => 'story-1', 'title' => 'Завтрак дня', 'sort_order' => 1],
            ['stock' => 'story-2', 'title' => 'Горячее с кухни', 'sort_order' => 2],
            ['stock' => 'story-3', 'title' => 'Кофе и десерт', 'sort_order' => 3],
            ['stock' => 'story-4', 'title' => 'Ягодный чизкейк', 'sort_order' => 4],
            ['stock' => 'story-5', 'title' => 'Сладкий день', 'sort_order' => 5],
            ['stock' => 'story-6', 'title' => 'Детская игровая', 'sort_order' => 6],
            ['stock' => 'story-7', 'title' => 'Семейный обед', 'sort_order' => 7],
            ['stock' => 'story-8', 'title' => 'Малыши в Тетри', 'sort_order' => 8],
            ['stock' => 'story-9', 'title' => 'Детский праздник', 'sort_order' => 9],
            ['stock' => 'story-10', 'title' => 'За чашкой кофе', 'sort_order' => 10],
            ['stock' => 'story-11', 'title' => 'Друзья за столом', 'sort_order' => 11],
            ['stock' => 'story-12', 'title' => 'Вечер в зале', 'sort_order' => 12],
        ];

        foreach ($stories as $story) {
            $n = $story['sort_order'];
            $videoPath = "stories/story-{$n}.mp4";
            $previewPath = "stories/previews/story-{$n}.jpg";
            $existing = Story::query()->where('video_path', $videoPath)->first();

            // Stable demo key is video_path (not title — titles are edited in Filament).
            if ($existing !== null) {
                if (blank($existing->preview_image) || $existing->preview_image === $previewPath) {
                    $preview = $this->storeStockImage($story['stock'], $previewPath);

                    if (blank($existing->preview_image)) {
                        $existing->update(['preview_image' => $preview]);
                    }
                }

                $this->storePlaceholderVideo($videoPath);

                continue;
            }

            Story::query()->create([
                'title' => $story['title'],
                'video_path' => $this->storePlaceholderVideo($videoPath),
                'preview_image' => $this->storeStockImage($story['stock'], $previewPath),
                'sort_order' => $n,
                'is_active' => true,
            ]);
        }

        $this->seedSiteSettingsFillMissing();
        $this->ensureSiteSettingsDemoMedia();
    }

    /**
     * Fill blank SiteSettings keys only. Media downloaded lazily for keys that need filling.
     */
    protected function seedSiteSettingsFillMissing(): void
    {
        $existing = Setting::query()->where('key', SiteSettings::KEY)->value('value') ?? [];
        if (! is_array($existing)) {
            $existing = [];
        }

        /** @var array<string, mixed|\Closure(): mixed> $candidates */
        $candidates = [
            'hero_title' => 'Т Е Т Р И',
            'hero_background_image' => fn (): string => $this->storeStockImage('hero-bg', 'site/hero/background.jpg'),
            'hero_video_path' => fn (): string => $this->storePlaceholderVideo('site/hero/hero.mp4'),
            'hero_video_preview' => fn (): string => $this->storeStockImage('hero-preview', 'site/hero/preview.jpg'),
            'kids_eyebrow' => 'ДЛЯ ВСЕЙ СЕМЬИ',
            'kids_images' => fn (): array => [
                $this->storeStockImage('kids-1', 'site/kids/exterior.jpg'),
                $this->storeStockImage('kids-2', 'site/kids/veranda.jpg'),
            ],
            'kids_description' => 'В ресторане есть просторная детская игровая комната, где дети могут играть и веселиться, пока родители спокойно отдыхают за столом.',
            'kids_description_secondary' => 'Игровая оборудована качественной шумоизоляцией, поэтому детский смех и игры не мешают атмосфере основного зала.',
            'kids_location_note' => 'Семейный ресторан в центре Симферополя — пр-т. Кирова, 31А',
            'kids_benefits' => [
                ['icon' => 'heroicon-o-puzzle-piece', 'text' => 'стильная детская игровая', 'url' => null],
                ['icon' => 'heroicon-o-sparkles', 'text' => 'анимация', 'url' => null],
                ['icon' => 'heroicon-o-shield-check', 'text' => 'безопасная площадка', 'url' => null],
                ['icon' => 'heroicon-o-heart', 'text' => 'уютно для родителей', 'url' => null],
            ],
            'menu_section_eyebrow' => 'ОБЕДЫ, УЖИНЫ И АВТОРСКАЯ КУХНЯ.',
            'menu_section_description' => 'От завтраков до десертов — готовим из свежих продуктов каждый день.',
            'menu_section_cta_label' => 'СМОТРЕТЬ ВСЕ',
            'menu_page_meta' => 'Обеды · ужины · детское меню',
            'menu_page_meta_note' => 'Обновляем сезонно',
            'concept_eyebrow' => 'СВОЯ КУХНЯ И ПЕКАРНЯ',
            'concept_aside' => 'Кофе - выпечка — 10:00–23:00',
            'concept_description' => 'Авторские блюда, свежая выпечка и кофейня под одной крышей. Приходите на бизнес-ланч, семейный ужин или тихий вечер — в Тетри всегда своя атмосфера.',
            'stories_section_aside' => 'Новинки и атмосфера зала',
            'stories_section_aside_note' => 'Смотрите в MAX',
            'address' => 'пр-т. Кирова, 31А',
            'booking_cta_url' => '#contacts',
            'social_links' => [
                ['icon' => 'heroicon-o-camera', 'text' => 'Instagram', 'url' => 'https://instagram.com'],
                ['icon' => 'heroicon-o-paper-airplane', 'text' => 'Telegram', 'url' => 'https://t.me'],
                ['icon' => 'heroicon-o-chat-bubble-left-right', 'text' => 'VK', 'url' => 'https://vk.com'],
            ],
            'map_latitude' => '44.950000',
            'map_longitude' => '34.100000',
            'map_marker_label' => 'ТЕТРИ',
        ];

        $toFill = [];

        foreach ($candidates as $key => $value) {
            if (! SiteSettings::isBlank($existing[$key] ?? null)) {
                continue;
            }

            $toFill[$key] = $value instanceof \Closure ? $value() : $value;
        }

        if ($toFill !== []) {
            app(SiteSettings::class)->save($toFill);
        }
    }

    /**
     * Re-download missing/tiny demo media for SiteSettings paths already stored in DB.
     */
    protected function ensureSiteSettingsDemoMedia(): void
    {
        $settings = app(SiteSettings::class)->all();

        $heroBg = $settings['hero_background_image'] ?? null;
        if (is_string($heroBg) && $heroBg === 'site/hero/background.jpg') {
            $this->storeStockImage('hero-bg', $heroBg);
        }

        $heroPreview = $settings['hero_video_preview'] ?? null;
        if (is_string($heroPreview) && $heroPreview === 'site/hero/preview.jpg') {
            $this->storeStockImage('hero-preview', $heroPreview);
        }

        $heroVideo = $settings['hero_video_path'] ?? null;
        if (is_string($heroVideo) && $heroVideo === 'site/hero/hero.mp4') {
            $this->storePlaceholderVideo($heroVideo);
        }

        $kidsImages = $settings['kids_images'] ?? null;
        if (is_array($kidsImages)) {
            $demoKids = [
                'site/kids/exterior.jpg' => 'kids-1',
                'site/kids/veranda.jpg' => 'kids-2',
            ];

            foreach ($kidsImages as $path) {
                if (is_string($path) && isset($demoKids[$path])) {
                    $this->storeStockImage($demoKids[$path], $path);
                }
            }
        }
    }

    protected function seedMenuItems(Category $category, int $categoryIndex): void
    {
        $samples = [
            ['Сырники со сметаной', 'Творожные сырники, сметана, ягодный соус', 390, 'syrniki'],
            ['Авокадо-тост', 'Зерновой тост, авокадо, яйцо пашот', 520, 'avokado'],
            ['Боул с лососем', 'Рис, лосось, овощи, соус юдзу', 890, 'bowl'],
            ['Паста с креветками', 'Лингвини, креветки, томаты черри', 790, 'pasta'],
            ['Детские наггетсы', 'Куриные наггетсы, картофель фри', 420, 'nuggets'],
            ['Чизкейк', 'Классический чизкейк Нью-Йорк', 450, 'cheesecake'],
            ['Капучино', 'Эспрессо, молоко, плотная пенка', 250, 'cappuccino'],
        ];

        foreach (array_slice($samples, 0, 4 + ($categoryIndex % 3)) as $itemIndex => [$title, $description, $price, $stock]) {
            $imagePath = 'menu-items/'.Str::slug($title).'-'.$category->slug.'.jpg';
            $legacyImagePath = 'menu-items/'.Str::slug($title).'.jpg';

            $item = MenuItem::query()
                ->where('category_id', $category->id)
                ->where(function ($query) use ($title, $imagePath, $legacyImagePath): void {
                    $query->where('title', $title)
                        ->orWhere('image', $imagePath)
                        ->orWhere('image', $legacyImagePath);
                })
                ->first();

            if ($item !== null) {
                if (blank($item->image)) {
                    $item->update(['image' => $this->storeStockImage($stock, $imagePath)]);
                } elseif (in_array($item->image, [$imagePath, $legacyImagePath], true)) {
                    $this->storeStockImage($stock, $item->image);
                }

                continue;
            }

            MenuItem::query()->create([
                'category_id' => $category->id,
                'title' => $title,
                'slug' => Str::slug($title),
                'description' => $description,
                'price' => $price,
                'image' => $this->storeStockImage($stock, $imagePath),
                'sort_order' => $itemIndex + 1,
                'is_active' => true,
            ]);
        }
    }

    protected function storeStockImage(string $stockKey, string $path): string
    {
        if ($this->publicFileLooksReal($path)) {
            return $path;
        }

        $url = self::STOCK[$stockKey] ?? null;

        if ($url) {
            try {
                $response = Http::timeout(20)
                    ->withHeaders(['User-Agent' => 'TetriCafeSeeder/1.0'])
                    ->get($url);

                if ($response->successful() && strlen($response->body()) > 50_000) {
                    Storage::disk('public')->put($path, $response->body());

                    return $path;
                }
            } catch (\Throwable) {
                // Fall through to solid-color JPEG fallback.
            }
        }

        if (! Storage::disk('public')->exists($path)) {
            Storage::disk('public')->put($path, $this->fallbackJpeg());
        }

        return $path;
    }

    protected function storePlaceholderVideo(string $path): string
    {
        // Never clobber an uploaded or previously written video — even an empty demo stub.
        if (Storage::disk('public')->exists($path)) {
            return $path;
        }

        Storage::disk('public')->put($path, '');

        return $path;
    }

    /**
     * Treat existing public files as real media (Filament uploads or prior Unsplash stock).
     * Solid-color JPEG fallback is ~13KB — keep threshold above that so re-seed can replace stubs.
     */
    protected function publicFileLooksReal(string $path): bool
    {
        if (! Storage::disk('public')->exists($path)) {
            return false;
        }

        return Storage::disk('public')->size($path) > 50_000;
    }

    /**
     * Visible colored JPEG fallback if Unsplash is unreachable.
     */
    protected function fallbackJpeg(): string
    {
        if (function_exists('imagecreatetruecolor')) {
            $img = imagecreatetruecolor(800, 1000);
            $bg = imagecolorallocate($img, 92, 61, 74);
            imagefilledrectangle($img, 0, 0, 800, 1000, $bg);
            ob_start();
            imagejpeg($img, null, 85);
            $binary = ob_get_clean();
            imagedestroy($img);

            return $binary ?: '';
        }

        return base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==') ?: '';
    }
}
