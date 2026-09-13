<?php

namespace Tests\Feature;

use App\Support\HeroiconOptions;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HeroiconOptionsTest extends TestCase
{
    public function test_custom_svg_html_injects_class_when_svg_has_no_class(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('icons/plain.svg', '<svg viewBox="0 0 24 24"><path d="M0 0h24v24H0z"/></svg>');

        $html = HeroiconOptions::customSvgHtml('icons/plain.svg', 'h-5 w-5');

        $this->assertNotNull($html);
        $this->assertStringContainsString('class="h-5 w-5"', $html);
        $this->assertStringContainsString('fill="currentColor"', $html);
    }

    public function test_custom_svg_html_prepends_class_when_svg_already_has_class(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put(
            'icons/with-class.svg',
            '<svg class="existing" width="24" height="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0z"/></svg>',
        );

        $html = HeroiconOptions::customSvgHtml('icons/with-class.svg', 'h-5 w-5');

        $this->assertNotNull($html);
        $this->assertStringContainsString('class="h-5 w-5 existing"', $html);
        $this->assertStringNotContainsString('width=', $html);
        $this->assertStringNotContainsString('height=', $html);
    }
}
