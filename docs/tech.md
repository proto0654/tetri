# Tech stack

Back to [project context](CONTEXT.md)

## Purpose

Record settled technical choices for the Tetri Laravel application.

## Facts

| Layer | Choice | Version / note |
| --- | --- | --- |
| Runtime | PHP | 8.4 (Herd) |
| Framework | Laravel | 13.31 |
| Admin | Filament | 5.8 (`AdminPanelProvider`) |
| Tests | PHPUnit | 12.x |
| Frontend tooling | Vite + npm | installed |
| DB (local bootstrap) | SQLite | migrated on create-project |
| AI boost | laravel/boost | 2.8 (dev) |

## Decisions

- Use Filament panel scaffolding (`php artisan filament:install --panels`).
- Prefer Laravel Boost MCP tools over ad-hoc exploration when available.
- Keep project conventions via Boost guidelines in `AGENTS.md` and Cursor skills under `.cursor/skills/`.

## Open questions

- Switch local/prod DB from SQLite to MySQL/Postgres.
- Auth model for Filament users / roles.
- Public frontend approach (Blade, Livewire, Inertia, etc.).

## Links

- Setup history: [setup.md](setup.md)
- AI tooling: [tooling.md](tooling.md)
- Product: [product.md](product.md)
