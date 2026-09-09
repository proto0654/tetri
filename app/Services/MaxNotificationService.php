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

        try {
            // MAX (platform-api2) serves a Минцифры TLS cert missing from many trust stores.
            // Prefer installing the Russian Trusted CA on production instead of disabling verify.
            $response = Http::withHeaders(['Authorization' => $token])
                ->acceptJson()
                ->asJson()
                ->connectTimeout(3)
                ->timeout(10)
                ->withoutVerifying()
                ->post(self::API_BASE.'/messages?chat_id='.urlencode((string) $chatId), [
                    'text' => $text,
                ]);

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
}
