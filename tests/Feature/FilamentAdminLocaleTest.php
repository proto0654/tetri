<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentAdminLocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_page_uses_russian_locale(): void
    {
        $response = $this->get('/admin/login');

        $response->assertOk();
        $response->assertSee('lang="ru"', false);
        $response->assertSee('Войдите в свой аккаунт');
        $response->assertSee('Адрес электронной почты');
        $response->assertSee('Пароль');
        $response->assertSee('Войти');
        $this->assertSame('ru', app()->getLocale());
        $this->assertSame('Предпросмотр', __('filament-peek::ui.preview-action-label'));
    }

    public function test_public_home_keeps_default_app_locale(): void
    {
        $this->get('/')->assertOk();

        $this->assertSame('en', app()->getLocale());
    }
}
