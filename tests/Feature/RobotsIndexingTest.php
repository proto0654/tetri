<?php

namespace Tests\Feature;

use App\Settings\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RobotsIndexingTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_blocks_search_indexing_via_meta_and_robots_txt(): void
    {
        $this->assertTrue((bool) app(SiteSettings::class)->get('block_search_indexing'));

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex, nofollow">', false);

        $this->get(route('robots'))
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee("User-agent: *\nDisallow: /\n", false);
    }

    public function test_allowing_indexing_removes_noindex_and_opens_robots_txt(): void
    {
        app(SiteSettings::class)->save([
            'block_search_indexing' => false,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('<meta name="robots" content="noindex, nofollow">', false);

        $this->get(route('robots'))
            ->assertOk()
            ->assertSee("User-agent: *\nDisallow:\n", false);
    }
}
