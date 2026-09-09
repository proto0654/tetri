# Setup

Back to [project context](CONTEXT.md)

## Purpose

What was already bootstrapped so agents do not redo or contradict it.

## Done

1. `composer create-project laravel/laravel .` in `e:\laravel\tetri`.
2. `composer require filament/filament -W` + `php artisan filament:install --panels --no-interaction`.
3. `composer require laravel/boost --dev -W` + Boost install for Cursor.
4. `git init`.
5. `npm install` (frontend tooling dependencies).
6. Created `docs/` documentation tree with hub + deep links.
7. Added Cursor skill `actualize`.

## Local notes

- `.env` exists with generated app key (not committed).
- SQLite database created and initial Laravel migrations applied.
- Filament assets published under `public/` (gitignored as Filament expects).

## Not done yet

- First application feature / Filament user.
- First feature commit beyond bootstrap (see [changelog.md](changelog.md)).
- Domain content and design decisions.

## Links

- Tooling: [tooling.md](tooling.md)
- Tech: [tech.md](tech.md)
