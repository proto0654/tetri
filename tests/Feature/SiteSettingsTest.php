<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Settings\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SiteSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_site_settings_save_and_read_merged_defaults(): void
    {
        Cache::flush();

        $settings = app(SiteSettings::class);

        $settings->save([
            'hero_title' => 'ТЕТРИ',
            'address' => 'ул. Севастопольская',
            'phones' => [
                ['number' => '+7 (978) 111-22-33'],
            ],
        ]);

        $this->assertDatabaseHas('settings', [
            'key' => SiteSettings::KEY,
        ]);

        Cache::flush();

        $loaded = app(SiteSettings::class);

        $this->assertSame('ТЕТРИ', $loaded->get('hero_title'));
        $this->assertSame('rgba(0, 0, 0, 0.45)', $loaded->get('hero_overlay_from'));
        $this->assertSame('rgba(0, 0, 0, 0.3)', $loaded->get('hero_overlay_via'));
        $this->assertSame('rgba(237, 226, 207, 0.95)', $loaded->get('hero_overlay_to'));
        $this->assertSame('ул. Севастопольская', $loaded->get('address'));
        $this->assertSame('+7 (978) 111-22-33', $loaded->get('phones.0.number'));
        $this->assertSame('МЕНЮ', $loaded->get('menu_section_title'));
        $this->assertSame('ОБЕДЫ, УЖИНЫ И АВТОРСКАЯ КУХНЯ.', $loaded->get('menu_section_eyebrow'));
        $this->assertSame('СВОЯ КУХНЯ И ПЕКАРНЯ', $loaded->get('concept_eyebrow'));
        $this->assertSame('ДЛЯ ВСЕЙ СЕМЬИ', $loaded->get('kids_eyebrow'));
        $this->assertInstanceOf(Setting::class, Setting::query()->where('key', SiteSettings::KEY)->first());
    }

    public function test_partial_save_preserves_existing_keys(): void
    {
        $settings = app(SiteSettings::class);

        $settings->save([
            'hero_title' => 'Custom Hero',
            'hero_background_image' => 'site/hero/custom.jpg',
            'address' => 'ул. Севастопольская',
        ]);

        $settings->save([
            'map_latitude' => '44.950000',
            'map_longitude' => '34.100000',
        ]);

        Cache::flush();

        $loaded = app(SiteSettings::class);

        $this->assertSame('Custom Hero', $loaded->get('hero_title'));
        $this->assertSame('site/hero/custom.jpg', $loaded->get('hero_background_image'));
        $this->assertSame('ул. Севастопольская', $loaded->get('address'));
        $this->assertSame('44.950000', $loaded->get('map_latitude'));
        $this->assertSame('34.100000', $loaded->get('map_longitude'));
    }

    public function test_fill_missing_only_writes_blank_keys(): void
    {
        $settings = app(SiteSettings::class);

        $settings->save([
            'hero_title' => 'Keep Me',
            'address' => 'ул. Севастопольская',
        ]);

        $filled = $settings->fillMissing([
            'hero_title' => 'Should Not Overwrite',
            'map_latitude' => '44.950000',
            'map_marker_label' => 'ТЕТРИ',
        ]);

        $this->assertSame(['map_latitude', 'map_marker_label'], array_keys($filled));
        $this->assertSame('Keep Me', $settings->get('hero_title'));
        $this->assertSame('ул. Севастопольская', $settings->get('address'));
        $this->assertSame('44.950000', $settings->get('map_latitude'));
        $this->assertSame('ТЕТРИ', $settings->get('map_marker_label'));
    }
}
