# Tech stack

Back to [project context](CONTEXT.md)

## Purpose

Record settled technical choices for the Tetri Laravel application.

## Facts

| Layer | Choice | Version / note |
| --- | --- | --- |
| Runtime | PHP | 8.4 (Herd) |
| Local URL | Herd | `http://tetri.test` (not localhost/:8000) |
| Framework | Laravel | 13.31 |
| Admin | Filament | 5.8 (`AdminPanelProvider`) |
| Public UI | Blade + Livewire + Alpine | Livewire 4.4 (via Filament) |
| Tests | PHPUnit | 12.x |
| Frontend tooling | Vite + Tailwind CSS | Vite 8 / Tailwind 4 |
| Sliders | Swiper (local npm) | bundled via Vite; no CDN |
| DB (local) | SQLite | |
| AI boost | laravel/boost | 2.8 (dev) |

## Decisions

- Use Filament panel scaffolding (`php artisan filament:install --panels`).
- Prefer Laravel Boost MCP tools over ad-hoc exploration when available.
- Keep project conventions via Boost guidelines in `AGENTS.md`, `.ai/rules`, and Cursor skills under `.cursor/skills/`.
- Site copy/media settings live in `settings` JSON (`App\Settings\SiteSettings`) — no Spatie Settings package unless approved.
- Public frontend is Blade + Livewire (`MenuGrid`, `BookingModal`), not Inertia.
- All public carousels/sliders use local Swiper (`npm` + `resources/js/app.js`); no Alpine overflow scroll or CDN.
- Mockup is the source of truth over external entity summaries; stack versions in old briefs are ignored in favor of installed packages.
- Category needs `image` + `columns` for homepage previews and menu grid density.
- Media URLs for the public site: `App\Support\PublicMedia` → relative `/storage/...`.
- Icon fields: `HeroiconOptions` + `IconFieldSchema` (Filament `Select::allowHtml()` previews must use fixed inline SVG size, not Tailwind `h-*`/`w-*`).
- Livewire temp uploads raised for hero video (`config/livewire.php`); Herd PHP upload limits may need matching.
- Seeders are idempotent (`firstOrCreate` / create-if-missing for models; SiteSettings fill blank keys only). `updateOrCreate` must not be used for demo cafe content. Overwrite only via `migrate:fresh --seed`. Details: [setup.md](setup.md#seeding-idempotent--fill-blanks-only).
- Unsafe CSS color strings from CMS: `App\Support\CssColor::resolve` (rgba/hex allowlist + fallback).
- Russian typography: `akh/typograf` via `App\Support\Typograph`; Blade `@typo` (strip all HTML) and `@typoBr` (keep newlines/`<br>` for section titles).
- Booking → MAX: `App\Services\MaxNotificationService` posts to `https://platform-api2.max.ru/messages?chat_id=`; `Authorization` is the raw bot token (no Bearer). Credentials: SiteSettings `max_bot_token` / `max_chat_id`. Temporary `withoutVerifying()` for Минцифры TLS — prefer installing the CA in production.

## Open questions

- Switch local/prod DB from SQLite to MySQL/Postgres.
- Production hosting and domain.
- Production TLS trust store for MAX API (drop `withoutVerifying()` when CA is installed).

## Links

- Setup history: [setup.md](setup.md)
- AI tooling: [tooling.md](tooling.md)
- Product: [product.md](product.md)
- Content: [content.md](content.md)
