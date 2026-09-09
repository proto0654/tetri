<?php

namespace Tests\Feature;

use App\Livewire\BookingModal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BookingModalTest extends TestCase
{
    use RefreshDatabase;

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
            ->call('submit')
            ->assertHasErrors(['name', 'phone']);
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
}
