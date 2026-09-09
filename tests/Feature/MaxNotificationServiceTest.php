<?php

namespace Tests\Feature;

use App\Services\MaxNotificationService;
use App\Settings\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class MaxNotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_sends_booking_notification_to_max_api(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'platform-api2.max.ru/*' => Http::response(['ok' => true], 200),
        ]);

        app(SiteSettings::class)->save([
            'max_bot_token' => 'test-token',
            'max_chat_id' => '12345',
        ]);

        $sent = app(MaxNotificationService::class)->sendFormNotification([
            'name' => 'Анна',
            'phone' => '+7 978 000-00-00',
            'date' => '2026-09-20',
            'guests' => 4,
            'comment' => 'Окно',
            'source' => 'kids',
        ]);

        $this->assertTrue($sent);

        Http::assertSent(function (Request $request): bool {
            return $request->url() === 'https://platform-api2.max.ru/messages?chat_id=12345'
                && $request->hasHeader('Authorization', 'test-token')
                && $request['text'] === implode("\n", [
                    'Новая заявка на бронь',
                    'Имя: Анна',
                    'Телефон: +7 978 000-00-00',
                    'Дата: 2026-09-20',
                    'Гостей: 4',
                    'Комментарий: Окно',
                    'Источник: kids',
                ]);
        });
    }

    public function test_returns_false_and_logs_when_settings_are_missing(): void
    {
        Http::preventStrayRequests();
        Log::spy();

        $sent = app(MaxNotificationService::class)->sendFormNotification([
            'name' => 'Анна',
            'phone' => '+7 978 000-00-00',
            'date' => '2026-09-20',
            'guests' => 2,
            'comment' => '',
            'source' => 'site',
        ]);

        $this->assertFalse($sent);
        Http::assertNothingSent();
        Log::shouldHaveReceived('error')
            ->once()
            ->withArgs(fn (string $message): bool => str_contains($message, 'missing bot token or chat id'));
    }

    public function test_returns_false_and_logs_when_api_responds_with_error(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'platform-api2.max.ru/*' => Http::response(['error' => 'unauthorized'], 401),
        ]);
        Log::spy();

        app(SiteSettings::class)->save([
            'max_bot_token' => 'bad-token',
            'max_chat_id' => '99',
        ]);

        $sent = app(MaxNotificationService::class)->sendFormNotification([
            'name' => 'Анна',
            'phone' => '+7 978 000-00-00',
            'date' => '2026-09-20',
            'guests' => 2,
            'comment' => '',
            'source' => 'site',
        ]);

        $this->assertFalse($sent);
        Log::shouldHaveReceived('error')
            ->once()
            ->withArgs(fn (string $message): bool => str_contains($message, 'non-2xx'));
    }
}
