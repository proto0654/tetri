<?php

namespace Tests\Unit;

use App\Support\CssColor;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CssColorTest extends TestCase
{
    #[DataProvider('safeColors')]
    public function test_accepts_safe_css_colors(string $value): void
    {
        $this->assertTrue(CssColor::isSafe($value));
        $this->assertSame($value, CssColor::resolve($value, 'fallback'));
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function safeColors(): array
    {
        return [
            'hex' => ['#f5f0e6'],
            'rgba' => ['rgba(0, 0, 0, 0.45)'],
            'rgb' => ['rgb(245, 240, 230)'],
        ];
    }

    public function test_rejects_unsafe_values_and_uses_fallback(): void
    {
        $this->assertFalse(CssColor::isSafe('url(javascript:alert(1))'));
        $this->assertSame(
            'rgba(0, 0, 0, 0.45)',
            CssColor::resolve('red; background: url(x)', 'rgba(0, 0, 0, 0.45)'),
        );
        $this->assertSame(
            'rgba(0, 0, 0, 0.45)',
            CssColor::resolve(null, 'rgba(0, 0, 0, 0.45)'),
        );
    }
}
