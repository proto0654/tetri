# Project context — Tetri

> Hub for project documentation. Branches below are deep-linked maps of meaning and functionality for the family cafe site **Тетри**.

## Snapshot

Public website and admin panel for family cafe **Тетри**. Stack: Laravel + Filament. AI-assisted development via Laravel Boost MCP in Cursor.

## Deep links

| Branch | Path | Concern | Status |
| --- | --- | --- | --- |
| Product | [product.md](product.md) | Brand, audience, site goals | draft |
| Tech stack | [tech.md](tech.md) | Laravel, Filament, frontend tooling | active |
| Tooling | [tooling.md](tooling.md) | Cursor, Boost, MCP, skills | active |
| Setup | [setup.md](setup.md) | Bootstrap steps already done | active |
| Content | [content.md](content.md) | Pages and content plan | stub |
| Design | [design.md](design.md) | Visual direction | stub |
| Changelog | [changelog.md](changelog.md) | Documentation actualizations | active |

## Current decisions

- Project root: `e:\laravel\tetri` (Windows / Herd PHP 8.4).
- Docs live in `docs/`; hub is this file (`CONTEXT.md`).
- AI agent target is **Cursor** only (`boost.json` → `cursor`).
- Boost MCP config: `.cursor/mcp.json` → `php artisan boost:mcp`.
- Dialogue → docs workflow is the Cursor skill **`actualize`**.

## Open questions

- Public site IA (home, menu, events, contacts, booking?).
- Filament resources and content model (menu categories, dishes, events, leads).
- Design system / brand assets for Тетри.
- Production hosting and domain.

## Last actualized

- 2026-09-09 — Initial hub + branches from project bootstrap dialogue.
