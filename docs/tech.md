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
- Seeders are idempotent (`firstOrCreate` / create-if-missing); `updateOrCreate` must not be used for demo cafe content. Details: [setup.md](setup.md#seeding-idempotent).
- Unsafe CSS color strings from CMS: `App\Support\CssColor::resolve` (rgba/hex allowlist + fallback).

## Open questions

- Switch local/prod DB from SQLite to MySQL/Postgres.
- Production hosting and domain.
- Booking modal → Telegram/MAX bot.

## Links

- Setup history: [setup.md](setup.md)
- AI tooling: [tooling.md](tooling.md)
- Product: [product.md](product.md)
- Content: [content.md](content.md)
