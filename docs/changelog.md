# Documentation changelog

Back to [project context](CONTEXT.md)

## 2026-09-10

- Synced all open project diffs: MAX booking via `MaxNotificationService` + SiteSettings credentials; favicon CMS + `x-site.favicon`; `@typoBr` for section titles; cream pill header; hero brand/icons polish; cream token `#ede2cf`. See [CONTEXT.md](CONTEXT.md), [content.md](content.md), [tech.md](tech.md), [design.md](design.md), [setup.md](setup.md).

## 2026-09-09

- Homepage desktop UI polish: kids/concept title hanging indent via `.site-text-shift` + `cqw` (avoid `%` mismatch); shared section title scale `text-4xl/5xl/6xl`; menu-preview arrows under swiper + image label overlays; concept aside bottom-aligned; olive footer without right-bleed. See [design.md](design.md), [CONTEXT.md](CONTEXT.md).
- Contacts map: olive viewport half-band behind map column (`.site-contacts-map-olive-half`) and right-column `z-10` so BR radius reads on olive without covering cream/olive copy. See [design.md](design.md), [content.md](content.md), [CONTEXT.md](CONTEXT.md).
- Yandex map coords/marker + route CTA; SiteSettings partial-save merge + fill-blanks seeders; map clip on bleed wrapper; single `design_credit` strip. See [content.md](content.md), [design.md](design.md), [setup.md](setup.md#seeding-idempotent--fill-blanks-only), [CONTEXT.md](CONTEXT.md).
- SiteSettings `save()` merges with existing row (partial updates safe); seeder fills blank settings keys only; overwrite only via `migrate:fresh --seed`. design_credit rendered once via `x-site.design-credit`. See [setup.md](setup.md#seeding-idempotent--fill-blanks-only), [content.md](content.md), [tech.md](tech.md).
- Synced after Swiper carousels (menu preview + stories), CMS hero overlay colors (`CssColor`), and 12 themed MAX story demos (food / kids / social; empty video placeholders). See [content.md](content.md), [design.md](design.md), [tech.md](tech.md), [CONTEXT.md](CONTEXT.md).
- Documented idempotent seeding: `CafeContentSeeder` / `DatabaseSeeder` use create-if-missing so Filament edits survive `db:seed`; full wipe via `migrate:fresh --seed` only. See [setup.md](setup.md#seeding-idempotent--fill-blanks-only), [content.md](content.md).
- Synced docs after public site polish: section accent fields (`*_eyebrow` / asides), Heroicon + custom SVG icons, Livewire booking modal stub, `PublicMedia`, Herd `tetri.test`.
- Updated [CONTENT](content.md), [tech.md](tech.md), [design.md](design.md), [setup.md](setup.md), [CONTEXT.md](CONTEXT.md).
- Implemented public site + admin content model from mockup (categories, menu items, stories, site settings, home, Livewire menu).
- Updated [content.md](content.md), [design.md](design.md), [tech.md](tech.md), [CONTEXT.md](CONTEXT.md) to active status.
- Created documentation hub [CONTEXT.md](CONTEXT.md) with deep links to meaning/function branches.
- Added branches: product, tech, tooling, setup, content, design.
- Added Cursor skill `actualize` (`.cursor/skills/actualize/SKILL.md`) to summarize dialogue into docs.
- Captured bootstrap decisions: Laravel 13 + Filament 5 + Boost for Cursor + `docs/` as project knowledge root.
