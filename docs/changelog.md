# Documentation changelog

Back to [project context](CONTEXT.md)

## 2026-09-09

- Synced after Swiper carousels (menu preview + stories), CMS hero overlay colors (`CssColor`), and 12 themed MAX story demos (food / kids / social; empty video placeholders). See [content.md](content.md), [design.md](design.md), [tech.md](tech.md), [CONTEXT.md](CONTEXT.md).
- Documented idempotent seeding: `CafeContentSeeder` / `DatabaseSeeder` use create-if-missing so Filament edits survive `db:seed`; full wipe via `migrate:fresh --seed` only. See [setup.md](setup.md#seeding-idempotent), [content.md](content.md).
- Synced docs after public site polish: section accent fields (`*_eyebrow` / asides), Heroicon + custom SVG icons, Livewire booking modal stub, `PublicMedia`, Herd `tetri.test`.
- Updated [CONTENT](content.md), [tech.md](tech.md), [design.md](design.md), [setup.md](setup.md), [CONTEXT.md](CONTEXT.md).
- Implemented public site + admin content model from mockup (categories, menu items, stories, site settings, home, Livewire menu).
- Updated [content.md](content.md), [design.md](design.md), [tech.md](tech.md), [CONTEXT.md](CONTEXT.md) to active status.
- Created documentation hub [CONTEXT.md](CONTEXT.md) with deep links to meaning/function branches.
- Added branches: product, tech, tooling, setup, content, design.
- Added Cursor skill `actualize` (`.cursor/skills/actualize/SKILL.md`) to summarize dialogue into docs.
- Captured bootstrap decisions: Laravel 13 + Filament 5 + Boost for Cursor + `docs/` as project knowledge root.
