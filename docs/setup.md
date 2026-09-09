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

## Not done yet

- Production deploy / hosting.
- Booking bot integration beyond modal stub.

## Links

- Tooling: [tooling.md](tooling.md)
- Tech: [tech.md](tech.md)
