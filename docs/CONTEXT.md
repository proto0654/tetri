# Project context — Tetri

> Hub for project documentation. Branches below are deep-linked maps of meaning and functionality for the family cafe site **Тетри**.

## Snapshot

Public website and Filament admin for family cafe **Тетри**. Stack: Laravel 13 + Filament 5 + Livewire 4 + Blade/Tailwind 4. Content: categories, dishes, stories, site settings (section accents, Heroicon/custom SVG icons, favicon, MAX bot credentials). Local URL via Herd: `http://tetri.test`. Booking CTA opens Livewire `BookingModal`; submit notifies a MAX group chat via `MaxNotificationService`.

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
- MVP pages: `/` + `/menu` (+ `/menu/{category}`, dish show, `/privacy`).
- Site copy/media: `settings.key=site` JSON via `App\Settings\SiteSettings` (no Spatie Settings).
- Decorative section accents (◇ eyebrows / asides) are settings fields, rendered with `<x-site.mark>`.
- Public icons: Heroicons select with SVG preview + optional custom SVG upload (`IconFieldSchema` / `HeroiconOptions`).
- Favicon: `SiteSettings.favicon` upload («Подвал и CTA»); rendered once via `<x-site.favicon>` in the site layout head (`rel=icon` + `apple-touch-icon` for raster).
- Booking: Livewire `BookingModal` via `<x-site.book-button>`; submit → `MaxNotificationService` → MAX group (`max_bot_token` / `max_chat_id` in SiteSettings). Failures keep the modal open with a form error.
- Public media URLs: relative `/storage/...` via `App\Support\PublicMedia`.
- CMS typography: `@typo` / `Typograph::apply()` for plain copy; section titles use `@typoBr` / `applyWithBreaks()` (newlines/`<br>` only).
- Seeders are idempotent: re-seed fills missing demo rows and blank SiteSettings keys only; never overwrites real on-disk media (>50KB images; any existing video). `migrate:fresh --seed` for a full wipe/overwrite.
- Menu page: default tab «Все меню» (blocks with `columns` preview 2|3); category pills Livewire-load a 4-col grid paginated by 12; SEO paths `/menu/{category}`; home menu-preview cards deep-link there.
- Dish detail: `/menu/{category}/{item}` with breadcrumbs + related items.
- Privacy: `/privacy` + footer cookie notice link.
- Public carousels: local Swiper only (`npm` + Vite); markup via `data-swiper*` hooks.
- Hero overlay gradient is CMS-editable (`hero_overlay_from` / `_via` / `_to`), sanitized with `CssColor::resolve`.
- Demo MAX stories: 12 posts (food / kids / social), Unsplash posters + empty `mp4` until real video upload.
- Contacts map: Yandex embed/route from `map_latitude` / `map_longitude` / `map_marker_label` (+ optional `map_embed_url`); clip radius on bleed wrapper (`overflow-hidden` + `isolate`), not the iframe. Map column `.site-contacts-map-olive-half` paints a full-viewport olive band on the bottom 50% (`::before`, z-index -1); right column is `relative z-10`.
- Home olive footer: no right-bleed; content stays in the 2/3 shell column (cream contacts may still bleed). Shared `x-site.contacts` + `x-site.footer` / `footer-bar`.
- Section titles: shared scale `text-4xl sm:text-5xl lg:text-6xl`; kids/concept use `.site-text-shift` + `cqw` hanging indent on lg (not `%`).
- Header: cream rounded-full pill (logo + nav + CTA); home absolute over hero, other pages sticky full-bleed cream bar; link/brand colors always ink/olive (never white on cream).
- Hero brand: fluid `clamp(vw)` at bottom of stack with slight frame overlap; amenity icons half-out via `translate-y-1/2`; section `overflow-x-clip` only.
- Cream token: `#ede2cf` (`--color-cream`).
- Menu-preview: arrows under swiper column; CTA bottom of col1; category labels overlay images; cards → `route('menu.category')`.
- `design_credit` once via `x-site.design-credit` in the site layout (fallback winbaba.ru).

## Open questions

- Production hosting and domain / DB engine.
- Dedicated Banquet / About pages beyond home anchors.
- Replace placeholder story/hero videos with real MAX footage.
- Production TLS trust for MAX `platform-api2` (prefer Минцифры CA over `withoutVerifying()`).

## Last actualized

- 2026-09-10 — Menu «Все меню» + category SEO URLs / dish+privacy pages; seeder media protect (>50KB); contacts/footer components; docs synced from open diffs.
- 2026-09-10 — MAX booking notifications, favicon CMS, `@typoBr`, cream pill header/hero polish, cream `#ede2cf`; docs synced from all open diffs.
- 2026-09-09 — Homepage desktop UI: title text-shift (cqw), shared section type scale, menu overlay/arrows, olive footer no right-bleed; docs synced.
- 2026-09-09 — Contacts map olive half-band (`site-contacts-map-olive-half`) + right column z-10 stacking; docs synced.
- 2026-09-09 — Yandex map route/marker settings; SiteSettings merge + fill-blanks seeders; map wrapper clip; design_credit strip.
- 2026-09-09 — Idempotent seeders (`firstOrCreate` / skip existing settings); seeding directives in setup/content/tech.
- 2026-09-09 — Accent copy fields, icon picker + custom SVG, booking modal stub; docs synced.
- 2026-09-09 — Implemented Waves 1–4 (domain, Filament, home, MenuGrid, polish); docs updated from mockup plan.
- 2026-09-09 — Initial hub + branches from project bootstrap dialogue.
