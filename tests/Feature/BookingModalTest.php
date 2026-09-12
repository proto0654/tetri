<?php

namespace Tests\Feature;

use App\Livewire\BookingModal;
use App\Settings\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class BookingModalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-09-09 12:00:00');

        app(SiteSettings::class)->save([
            'max_bot_token' => 'test-token',
            'max_chat_id' => '12345',
        ]);
    }

    public function test_home_page_includes_booking_modal(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSeeLivewire(BookingModal::class);
    }

    public function test_booking_open_event_shows_modal(): void
    {
        Livewire::test(BookingModal::class)
            ->assertSet('show', false)
            ->dispatch('booking-open', source: 'header')
            ->assertSet('show', true)
            ->assertSet('source', 'header');
    }

    public function test_submit_validates_required_fields(): void
    {
        Livewire::test(BookingModal::class)
            ->dispatch('booking-open')
            ->set('name', '')
            ->set('phone', '')
            ->set('date', null)
            ->set('guests', null)
            ->call('submit')
            ->assertHasErrors([
                'name' => 'required',
                'phone' => 'required',
                'date' => 'required',
                'guests' => 'required',
            ])
            ->assertSee('Укажите имя.')
            ->assertSee('Укажите телефон.')
            ->assertSee('Укажите дату.')
            ->assertSee('Укажите число гостей.');
    }

    #[DataProvider('invalidPayloadProvider')]
    public function test_submit_rejects_invalid_values(
        string $field,
        mixed $value,
        string $rule,
    ): void {
        $component = Livewire::test(BookingModal::class)
            ->dispatch('booking-open')
            ->set('name', 'Анна')
            ->set('phone', '+7 978 000-00-00')
            ->set('date', '2026-09-20')
            ->set('guests', 4)
            ->set($field, $value)
            ->call('submit');

        $component->assertHasErrors([$field => $rule]);
    }

    /**
     * @return array<string, array{0: string, 1: mixed, 2: string}>
     */
    public static function invalidPayloadProvider(): array
    {
        return [
            'name too short' => ['name', 'А', 'min'],
            'phone too short' => ['phone', '12345', 'min'],
            'phone invalid chars' => ['phone', 'abc-def-ghij', 'regex'],
            'date in the past' => ['date', '2026-09-01', 'after_or_equal'],
            'guests below minimum' => ['guests', 0, 'min'],
            'guests above maximum' => ['guests', 51, 'max'],
        ];
    }

    public function test_submit_sends_max_notification_and_shows_thanks(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'platform-api2.max.ru/*' => Http::response(['ok' => true], 200),
        ]);

        Livewire::test(BookingModal::class)
            ->dispatch('booking-open', source: 'kids')
            ->set('name', 'Анна')
            ->set('phone', '+7 978 000-00-00')
            ->set('date', '2026-09-20')
            ->set('guests', 4)
            ->set('comment', 'Окно')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('show', true)
            ->assertSet('sent', true)
            ->assertSet('failed', false)
            ->assertSet('name', '')
            ->assertSee('Спасибо за обращение!')
            ->assertSee('Мы скоро свяжемся с вами, чтобы подтвердить бронь.')
            ->assertSee('Бронь считается подтверждённой после связи с менеджером.');

        Http::assertSent(function (Request $request): bool {
            return str_contains($request->url(), 'chat_id=12345')
                && str_contains((string) $request['text'], 'Анна')
                && str_contains((string) $request['text'], 'Источник: Раздел «Детская»');
        });
    }

    public function test_submit_allows_empty_comment(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'platform-api2.max.ru/*' => Http::response(['ok' => true], 200),
        ]);

        Livewire::test(BookingModal::class)
            ->dispatch('booking-open')
            ->set('name', 'Анна')
            ->set('phone', '+7 978 000-00-00')
            ->set('date', '2026-09-20')
            ->set('guests', 2)
            ->set('comment', '')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('show', true)
            ->assertSet('sent', true);
    }

    public function test_submit_shows_error_state_when_max_api_fails(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'platform-api2.max.ru/*' => Http::response(['error' => 'fail'], 500),
        ]);

        Livewire::test(BookingModal::class)
            ->dispatch('booking-open')
            ->set('name', 'Анна')
            ->set('phone', '+7 978 000-00-00')
            ->set('date', '2026-09-20')
            ->set('guests', 2)
            ->call('submit')
            ->assertSet('show', true)
            ->assertSet('sent', false)
            ->assertSet('failed', true)
            ->assertSet('name', 'Анна')
            ->assertSee('Не удалось отправить')
            ->assertSee('Попробовать снова')
            ->call('retry')
            ->assertSet('failed', false)
            ->assertSee('Забронировать стол')
            ->assertSet('name', 'Анна');
    }

    public function test_reopening_after_success_shows_form_again(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'platform-api2.max.ru/*' => Http::response(['ok' => true], 200),
        ]);

        Livewire::test(BookingModal::class)
            ->dispatch('booking-open')
            ->set('name', 'Анна')
            ->set('phone', '+7 978 000-00-00')
            ->set('date', '2026-09-20')
            ->set('guests', 2)
            ->call('submit')
            ->assertSet('sent', true)
            ->dispatch('booking-open', source: 'header')
            ->assertSet('sent', false)
            ->assertSet('failed', false)
            ->assertSet('show', true)
            ->assertSee('Забронировать стол');
    }
}
