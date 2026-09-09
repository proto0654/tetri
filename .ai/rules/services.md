---
paths:
  - 'app/Services/**'
---

# Services

## MAX booking notifications via MaxNotificationService
Booking form alerts go to MAX group chat through App\Services\MaxNotificationService (POST https://platform-api2.max.ru/messages?chat_id=). Credentials live in SiteSettings keys max_bot_token and max_chat_id (Filament ManageSiteSettings tab «Интеграция с мессенджером MAX»). Authorization header is the raw bot token (no Bearer). Do not add spatie/laravel-settings for this.
