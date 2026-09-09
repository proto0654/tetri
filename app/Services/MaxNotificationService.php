<?php

namespace App\Services;

use App\Settings\SiteSettings;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class MaxNotificationService
{
    private const API_BASE = 'https://platform-api2.max.ru';

    public function __construct(private SiteSettings $settings) {}

    /**
     * @param  array<string, mixed>  $formData
     */
    public function sendFormNotification(array $formData): bool
    {
        $token = $this->settings->get('max_bot_token');
        $chatId = $this->settings->get('max_chat_id');

        if (! is_string($token) || $token === '' || $chatId === null || $chatId === '') {
            Log::error('MAX notification skipped: missing bot token or chat id.');

            return false;
        }

        $text = $this->formatMessage($formData);
        $payload = ['text' => $text];

        $keyboard = $this->phoneActionKeyboard($formData['phone'] ?? null);
        if ($keyboard !== null) {
            $payload['attachments'] = [$keyboard];
        }

        try {
            // MAX (platform-api2) serves a Минцифры TLS cert missing from many trust stores.
            // Prefer installing the Russian Trusted CA on production instead of disabling verify.
            $response = Http::withHeaders(['Authorization' => $token])
                ->acceptJson()
                ->asJson()
                ->connectTimeout(3)
                ->timeout(10)
                ->withoutVerifying()
                ->post(self::API_BASE.'/messages?chat_id='.urlencode((string) $chatId), $payload);

            if ($response->successful()) {
                return true;
            }

            Log::error('MAX API returned a non-2xx response.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        } catch (ConnectionException|RequestException $exception) {
            Log::error('MAX API request failed.', [
                'message' => $exception->getMessage(),
                'response' => $exception instanceof RequestException
                    ? $exception->response?->body()
                    : null,
            ]);

            return false;
        } catch (Throwable $exception) {
            Log::error('MAX notification failed unexpectedly.', [
                'message' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * @param  array<string, mixed>  $formData
     */
    protected function formatMessage(array $formData): string
    {
        $comment = trim((string) ($formData['comment'] ?? ''));

        return implode("\n", [
            'Новая заявка на бронь',
            'Имя: '.($formData['name'] ?? '—'),
            'Телефон: '.($formData['phone'] ?? '—'),
            'Дата: '.($formData['date'] ?? '—'),
            'Гостей: '.($formData['guests'] ?? '—'),
            'Комментарий: '.($comment !== '' ? $comment : '—'),
            'Источник: '.($formData['source'] ?? '—'),
        ]);
    }

    /**
     * Inline keyboard with call + copy actions when the phone can be dialed.
     *
     * @return array{type: string, payload: array{buttons: list<list<array<string, string>>>}}|null
     */
    protected function phoneActionKeyboard(mixed $phone): ?array
    {
        $dialable = $this->normalizePhoneForDial($phone);

        if ($dialable === null) {
            return null;
        }

        return [
            'type' => 'inline_keyboard',
            'payload' => [
                'buttons' => [[
                    [
                        'type' => 'link',
                        'text' => 'Позвонить',
                        'url' => 'tel:'.$dialable,
                    ],
                    [
                        'type' => 'clipboard',
                        'text' => 'Скопировать',
                        'payload' => $dialable,
                    ],
                ]],
            ],
        ];
    }

    /**
     * Normalize a display phone into +E.164-ish form for tel: / clipboard.
     */
    protected function normalizePhoneForDial(mixed $phone): ?string
    {
        if (! is_string($phone)) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if ($digits === '') {
            return null;
        }

        // Local Russian numbers often start with 8XXXXXXXXXX.
        if (strlen($digits) === 11 && str_starts_with($digits, '8')) {
            $digits = '7'.substr($digits, 1);
        }

        if (strlen($digits) < 10 || strlen($digits) > 15) {
            return null;
        }

        return '+'.$digits;
    }
}
