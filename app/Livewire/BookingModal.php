<?php

namespace App\Livewire;

use App\Services\MaxNotificationService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class BookingModal extends Component
{
    public bool $show = false;

    public string $name = '';

    public string $phone = '';

    public ?string $date = null;

    public ?int $guests = null;

    public string $comment = '';

    public string $source = 'site';

    /**
     * @return array<string, list<string>>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'phone' => ['required', 'string', 'min:10', 'max:40', 'regex:/^[\d\s+\-()]+$/'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'guests' => ['required', 'integer', 'min:1', 'max:50'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'name.required' => 'Укажите имя.',
            'name.min' => 'Имя должно содержать не менее :min символов.',
            'phone.required' => 'Укажите телефон.',
            'phone.min' => 'Введите телефон полностью.',
            'phone.regex' => 'Телефон может содержать только цифры и символы + - ( ).',
            'date.required' => 'Укажите дату.',
            'date.after_or_equal' => 'Дата не может быть в прошлом.',
            'guests.required' => 'Укажите число гостей.',
            'guests.min' => 'Нужен хотя бы один гость.',
            'guests.max' => 'Максимум :max гостей.',
            'comment.max' => 'Комментарий слишком длинный.',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'name' => 'имя',
            'phone' => 'телефон',
            'date' => 'дата',
            'guests' => 'гостей',
            'comment' => 'комментарий',
        ];
    }

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

    public function submit(MaxNotificationService $max): void
    {
        $data = $this->validate();

        $payload = [
            'name' => $data['name'],
            'phone' => $data['phone'],
            'date' => $data['date'],
            'guests' => $data['guests'],
            'comment' => $data['comment'] ?? '',
            'source' => $this->source,
        ];

        if (! $max->sendFormNotification($payload)) {
            $this->addError('form', 'Не удалось отправить заявку. Попробуйте позже или позвоните нам.');

            return;
        }

        $this->reset(['name', 'phone', 'date', 'guests', 'comment', 'source']);
        $this->show = false;
    }

    public function render(): View
    {
        return view('livewire.booking-modal');
    }
}
