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
        $this->assertSame('ул. Севастопольская', $loaded->get('address'));
        $this->assertSame('+7 (978) 111-22-33', $loaded->get('phones.0.number'));
        $this->assertSame('МЕНЮ', $loaded->get('menu_section_title'));
        $this->assertSame('ОБЕДЫ, УЖИНЫ И АВТОРСКАЯ КУХНЯ.', $loaded->get('menu_section_eyebrow'));
        $this->assertSame('СВОЯ КУХНЯ И ПЕКАРНЯ', $loaded->get('concept_eyebrow'));
        $this->assertSame('ДЛЯ ВСЕЙ СЕМЬИ', $loaded->get('kids_eyebrow'));
        $this->assertInstanceOf(Setting::class, Setting::query()->where('key', SiteSettings::KEY)->first());
    }
}
