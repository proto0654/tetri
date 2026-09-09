---
paths:
  - 'app/Services/**'
---

# Services

## MAX booking notifications via MaxNotificationService
Booking form alerts go to MAX group chat through App\Services\MaxNotificationService (POST https://platform-api2.max.ru/messages?chat_id=). Credentials live in SiteSettings keys max_bot_token and max_chat_id (Filament ManageSiteSettings tab «Интеграция с мессенджером MAX»). Authorization header is the raw bot token (no Bearer). Do not add spatie/laravel-settings for this.

## MAX booking messages include call/copy inline buttons
When the guest phone normalizes to dialable digits, attach an `inline_keyboard` with `link` (`tel:+…`) «Позвонить» and `clipboard` «Скопировать». MAX has no dedicated call button type. Omit attachments if the phone cannot be normalized (empty / too short / non-digits).
