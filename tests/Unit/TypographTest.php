<?php

namespace Tests\Unit;

use App\Support\Typograph;
use Tests\TestCase;

class TypographTest extends TestCase
{
    public function test_binds_short_prepositions_with_non_breaking_spaces(): void
    {
        $result = Typograph::apply('в кафе на углу');

        $this->assertStringContainsString("в\u{00A0}кафе", $result);
        $this->assertStringContainsString("на\u{00A0}углу", $result);
        $this->assertStringNotContainsString('&nbsp;', $result);
    }

    public function test_returns_empty_string_for_blank_input(): void
    {
        $this->assertSame('', Typograph::apply(null));
        $this->assertSame('', Typograph::apply(''));
    }

    public function test_strips_html_tags_from_field_output(): void
    {
        $result = Typograph::apply('<b>в кафе</b>');

        $this->assertStringNotContainsString('<b>', $result);
        $this->assertStringContainsString("в\u{00A0}кафе", $result);
    }

    public function test_apply_with_breaks_keeps_br_and_newlines(): void
    {
        $fromTag = Typograph::applyWithBreaks('МЕСТО<br>ДЛЯ СЕМЬИ');
        $fromNewline = Typograph::applyWithBreaks("МЕСТО\nДЛЯ СЕМЬИ");

        $this->assertSame($fromTag, $fromNewline);
        $this->assertStringContainsString('<br>', $fromTag);
        $this->assertStringContainsString('МЕСТО', $fromTag);
        $this->assertStringContainsString('ДЛЯ', $fromTag);
    }

    public function test_apply_with_breaks_strips_non_br_markup_and_escapes_text(): void
    {
        $result = Typograph::applyWithBreaks('<b>в кафе</b><script>alert(1)</script><br>и ресторан');

        $this->assertStringNotContainsString('<b>', $result);
        $this->assertStringNotContainsString('<script>', $result);
        $this->assertStringContainsString('<br>', $result);
        $this->assertStringContainsString("в\u{00A0}кафе", $result);
    }
}
