<?php

namespace Tests\Feature;

use App\Livewire\BookingModal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
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

    public function test_submit_accepts_valid_payload_and_closes(): void
    {
        Livewire::test(BookingModal::class)
            ->dispatch('booking-open', source: 'kids')
            ->set('name', 'Анна')
            ->set('phone', '+7 978 000-00-00')
            ->set('date', '2026-09-20')
            ->set('guests', 4)
            ->set('comment', 'Окно')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('show', false)
            ->assertSet('name', '');
    }

    public function test_submit_allows_empty_comment(): void
    {
        Livewire::test(BookingModal::class)
            ->dispatch('booking-open')
            ->set('name', 'Анна')
            ->set('phone', '+7 978 000-00-00')
            ->set('date', '2026-09-20')
            ->set('guests', 2)
            ->set('comment', '')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('show', false);
    }
}
