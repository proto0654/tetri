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
}
