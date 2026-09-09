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

## Local notes

- `.env` exists with generated app key (not committed). `APP_URL=http://tetri.test`.
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
2. Download stock media only when inserting a new row or filling a blank media settings key (lazy), not when the value already exists.
3. `SiteSettings`: fill blank keys only (`SiteSettings::fillMissing` / seeder equivalent). Never overwrite non-blank keys on re-seed. `save()` merges with the existing row so partial updates are safe.
4. Users: `firstOrCreate` by email — do not reset passwords/names on re-seed.
5. Full reset of edited data is intentional only via `migrate:fresh --seed`.

## Not done yet

- Production deploy / hosting.
- Booking bot integration beyond modal stub.

## Links

- Tooling: [tooling.md](tooling.md)
- Tech: [tech.md](tech.md)
