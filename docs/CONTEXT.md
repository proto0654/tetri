# Project context — Tetri

> Hub for project documentation. Branches below are deep-linked maps of meaning and functionality for the family cafe site **Тетри**.

## Snapshot

Public website and Filament admin for family cafe **Тетри**. Stack: Laravel 13 + Filament 5 + Livewire 4 + Blade/Tailwind 4. Content: categories, dishes, stories, site settings (including section accent copy and Heroicon/custom SVG icons). Local URL via Herd: `http://tetri.test`. Booking CTA opens a Livewire modal (stub submit).

## Deep links

| Branch | Path | Concern | Status |
| --- | --- | --- | --- |
| Product | [product.md](product.md) | Brand, audience, site goals | draft |
| Tech stack | [tech.md](tech.md) | Laravel, Filament, frontend tooling | active |
| Tooling | [tooling.md](tooling.md) | Cursor, Boost, MCP, skills | active |
| Setup | [setup.md](setup.md) | Bootstrap steps already done | active |
| Content | [content.md](content.md) | Pages and content model | active |
| Design | [design.md](design.md) | Visual tokens from mockup | active |
| Changelog | [changelog.md](changelog.md) | Documentation actualizations | active |

## Current decisions

- Project root: `e:\laravel\tetri` (Windows / Herd PHP 8.4); public app at `http://tetri.test`, admin `/admin`.
- Docs live in `docs/`; hub is this file (`CONTEXT.md`).
- AI agent target is **Cursor** only (`boost.json` → `cursor`).
- Boost MCP config: `.cursor/mcp.json` → `php artisan boost:mcp`.
- Dialogue → docs workflow is the Cursor skill **`actualize`**.
- UI mockup is source of truth; installed package versions beat any outdated brief.
- MVP pages: `/` + `/menu`.
- Site copy/media: `settings.key=site` JSON via `App\Settings\SiteSettings` (no Spatie Settings).
- Decorative section accents (◇ eyebrows / asides) are settings fields, rendered with `<x-site.mark>`.
- Public icons: Heroicons select with SVG preview + optional custom SVG upload (`IconFieldSchema` / `HeroiconOptions`).
- Booking: Livewire `BookingModal` via `<x-site.book-button>`; submit is a stub (alert), not a bot yet.
- Public media URLs: relative `/storage/...` via `App\Support\PublicMedia`.
- Seeders are idempotent: re-seed fills missing demo rows and blank SiteSettings keys only; `migrate:fresh --seed` for a full wipe/overwrite.
- Public carousels: local Swiper only (`npm` + Vite); markup via `data-swiper*` hooks.
- Hero overlay gradient is CMS-editable (`hero_overlay_from` / `_via` / `_to`), sanitized with `CssColor::resolve`.
- Demo MAX stories: 12 posts (food / kids / social), Unsplash posters + empty `mp4` until real video upload.
- Contacts map: Yandex embed/route from `map_latitude` / `map_longitude` / `map_marker_label` (+ optional `map_embed_url`); clip radius on bleed wrapper (`overflow-hidden` + `isolate`), not the iframe.
- `design_credit` once via `x-site.design-credit` in the site layout (fallback winbaba.ru).

## Open questions

- Production hosting and domain / DB engine.
- Wire booking stub to Telegram/MAX bot.
- Dedicated Banquet / About pages beyond home anchors.
- Replace placeholder story/hero videos with real MAX footage.

## Last actualized

- 2026-09-09 — Yandex map route/marker settings; SiteSettings merge + fill-blanks seeders; map wrapper clip; design_credit strip.
- 2026-09-09 — SiteSettings partial-save merge + seeder fill-blanks-only (no overwrite); design_credit only via layout strip; map fields documented.
- 2026-09-09 — Swiper carousels, CMS hero overlay colors, 12 themed MAX story demos; docs synced.
- 2026-09-09 — Swiper carousels, CMS hero overlay colors, 12 themed MAX story demos; docs synced.
- 2026-09-09 — Idempotent seeders (`firstOrCreate` / skip existing settings); seeding directives in setup/content/tech.
- 2026-09-09 — Accent copy fields, icon picker + custom SVG, booking modal stub; docs synced.
- 2026-09-09 — Implemented Waves 1–4 (domain, Filament, home, MenuGrid, polish); docs updated from mockup plan.
- 2026-09-09 — Initial hub + branches from project bootstrap dialogue.
