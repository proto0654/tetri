# Setup

Back to [project context](CONTEXT.md)

## Purpose

What was already bootstrapped so agents do not redo or contradict it.

## Done

1. `composer create-project laravel/laravel .` in `e:\laravel\tetri`.
2. `composer require filament/filament -W` + `php artisan filament:install --panels --no-interaction`.
3. `composer require laravel/boost --dev -W` + Boost install for Cursor.
4. `git init` + bootstrap commit.
5. `npm install` (frontend tooling dependencies).
6. Created `docs/` documentation tree with hub + deep links.
7. Added Cursor skill `actualize`.
8. Domain migrations/models, Filament resources, `ManageSiteSettings`, public home + `/menu`, seeders, feature tests.
9. Herd site `tetri.test`; Filament admin user seeded for local use.
10. `composer require pboivin/filament-peek:^4.1` + `FilamentPeekPlugin` + `filament:assets`; Laravel 13 `serializable_classes` for Peek cache.

## Local notes

- `.env` exists with generated app key (not committed). `APP_URL=http://tetri.test`.
- `YANDEX_MAPS_API_KEY` — Yandex Maps JS API key for contacts map (see `.env.example`; `config/services.php` → `yandex_maps.key`). Restrict by HTTP Referrer in Yandex developer cabinet. Not stored in SiteSettings.
- SQLite database created; cafe content via `CafeContentSeeder`.
- Filament assets published under `public/` (gitignored as Filament expects).
- Storage link: `public/storage` → `storage/app/public`.
- Large hero video uploads may need raised PHP/`livewire.php` limits on Herd.

## Seeding (idempotent — fill blanks only)

`php artisan db:seed` / `CafeContentSeeder` must **not** overwrite non-blank content already edited in Filament or the DB. Missing/blank SiteSettings keys (null, `''`, `[]`) are filled; models use create-if-missing.

| Goal | Command |
| --- | --- |
| Fill missing demo data / blank settings keys only | `php artisan db:seed` or `php artisan db:seed --class=CafeContentSeeder` |
| Wipe DB and reseed demos (only intentional overwrite) | `php artisan migrate:fresh --seed` |

Directives for seeders:

1. Models: create-if-missing by stable keys — category `slug`, menu item (`category_id` + title **or** demo `image` path), story demo `video_path` (not editable title), user `email`. Do **not** use `updateOrCreate` for demo content.
2. Download stock media only when inserting a new row, filling a blank media key, or replacing a missing/tiny file on disk. Never overwrite an existing public file that already looks real (>50KB for images — above the ~13KB solid fallback; any existing file for videos).
3. `SiteSettings`: fill blank keys only (`SiteSettings::fillMissing` / seeder equivalent). Never overwrite non-blank keys on re-seed. `save()` merges with the existing row so partial updates are safe.
4. Users: `firstOrCreate` by email — do not reset passwords/names on re-seed.
5. Full reset of edited data is intentional only via `migrate:fresh --seed`.

## Not done yet

- Production TLS CA for MAX `platform-api2` (replace `withoutVerifying()` fallback).
- Fill MAX bot token + chat id in Filament after deploy (required to save site settings).

## Deploy

See [DEPLOY.md](DEPLOY.md).

- Production: **https://tetri-cafe.ru** (live as of 2026-09-17).
- GitHub: https://github.com/proto0654/tetri (public). `docs/` tracked in git but **not** rsynced to the host; Cursor / `.ai` stay gitignored.
- Code: push `main` → Actions rsync + migrate (SSH key; ISPmanager password not in env). Lean excludes: [DEPLOY.md](DEPLOY.md#what-ships-vs-stays-in-git).
- Content: `demo:pull` / `demo:push` cutover only (not remote seed; do not routine-push).
- Prod `/admin`: studio + client Filament users (credentials out of band). Local seed `admin@tetri.test` / `password` is Herd-only — see [DEPLOY.md](DEPLOY.md#filament-users-production).

## Links

- Tooling: [tooling.md](tooling.md)
- Tech: [tech.md](tech.md)
