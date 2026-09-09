<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class BookingModal extends Component
{
    public bool $show = false;

    #[Validate('required|string|min:2|max:120')]
    public string $name = '';

    #[Validate('required|string|min:5|max:40')]
    public string $phone = '';

    #[Validate('nullable|date')]
    public ?string $date = null;

    #[Validate('nullable|integer|min:1|max:50')]
    public ?int $guests = null;

    #[Validate('nullable|string|max:1000')]
    public string $comment = '';

    public string $source = 'site';

    #[On('booking-open')]
    public function open(string $source = 'site'): void
    {
        $this->resetValidation();
        $this->source = $source !== '' ? $source : 'site';
        $this->show = true;
    }

    public function close(): void
    {
        $this->show = false;
    }

    public function submit(): void
    {
        $data = $this->validate();

        $payload = [
            'name' => $data['name'],
            'phone' => $data['phone'],
            'date' => $data['date'] ?? null,
            'guests' => $data['guests'] ?? null,
            'comment' => $data['comment'] ?? '',
            'source' => $this->source,
        ];

        // TODO: send to Telegram / MAX bot.
        $this->js('alert('.json_encode(
            "Заявка на бронь (заглушка):\n".
            'Имя: '.$payload['name']."\n".
            'Телефон: '.$payload['phone']."\n".
            'Дата: '.($payload['date'] ?: '—')."\n".
            'Гостей: '.($payload['guests'] ?? '—')."\n".
            'Комментарий: '.($payload['comment'] !== '' ? $payload['comment'] : '—')."\n".
            'Источник: '.$payload['source'],
            JSON_UNESCAPED_UNICODE
        ).')');

        $this->reset(['name', 'phone', 'date', 'guests', 'comment', 'source']);
        $this->show = false;
    }

    public function render(): View
    {
        return view('livewire.booking-modal');
    }
}
