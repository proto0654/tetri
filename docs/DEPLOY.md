# Deploy — tetri-cafe.ru

Back to [project context](CONTEXT.md)

Production on shared REG.RU hosting (ISPmanager). Pattern: GitHub Actions → rsync over SSH. Content is **not** seeded on deploy.

**Status (2026-09-17):** live — https://tetri-cafe.ru (indexing on; former staging demo removed after cutover).

## Architecture

```
push main
  └─ deploy.yml  →  rsync code  →  production app root  (+ migrate)
                                         │
one-shot cutover ── demo:pull (old host) / demo:push ──► import tables + media
```

| Action | When | Touches DB data? |
|--------|------|------------------|
| Code deploy (`deploy.yml`) | push `main` / Run workflow | No — only `migrate --force` |
| `php artisan demo:pull` | Pull zip from a remote (export + scp) | Local only if `--import` |
| `php artisan demo:push` | Manual content replace on a host | Yes — replaces content tables + `storage/app/public` |
| Day-to-day production | Same code deploy | Do **not** routine `demo:push` |

## GitHub

- Repo: https://github.com/proto0654/tetri (**public**; trade-controls blocked private create).
- Tracked: `docs/`. Still local-only (gitignored): `.cursor/`, `.ai/`, `AGENTS.md`, `boost.json`.
- Secrets → Actions: `DEPLOY_HOST`, `DEPLOY_USER`, `DEPLOY_SSH_KEY`, `DEPLOY_PATH`, `DEPLOY_PHP` (optional; default `/opt/php/8.4/bin/php`).
- Real host / SSH user / path / key path live only in local `.env.deploy` and GitHub Actions secrets — **never** commit account IDs, panel URLs, or passwords.
- ISPmanager password: panel login only. Never put it in `.env` / `.env.deploy` / git. Resetting the panel password does not affect SSH deploy or the Laravel app.

## Server facts

1. App root under the account `www/` tree for the production domain; root `.htaccess` forwards into `public/`.
2. PHP **8.4** for CLI and CGI (`/opt/php/8.4/bin/php`, account `php-bin/…/php` → `php-cgi` 8.4). Composer platform check requires ≥8.4.1 (Symfony 8).
3. Prod `.env`: SQLite at `database/database.sqlite` (absolute `DB_DATABASE` under hosting home). `SESSION_DRIVER`/`CACHE_STORE=file`, `QUEUE_CONNECTION=sync`. No Redis (extension absent on host). `APP_URL=https://tetri-cafe.ru`.
4. Windows has no native `rsync`; Actions runs on Ubuntu. Large content zips may need chunked SFTP from Windows (hosting resets long SCP).
5. Deploy step creates `storage/framework/*` and touches SQLite before `migrate` on fresh hosts.
6. Hosting panel: ISPmanager (credentials and panel URL stay out of the repo).

## What ships vs stays in git

`docs/` and other dev files live in the GitHub repo for the team; they are **not** needed on the host. Actions builds assets in CI (`npm run build` → `public/build`), then rsyncs a lean tree.

**On the host (runtime):** `app/`, `bootstrap/`, `config/`, `database/migrations/` (+ sqlite file, not overwritten by rsync), `lang/`, `public/`, `resources/`, `routes/`, `artisan`, `composer.json` / `composer.lock`, `vendor/`, `.htaccess`, server `.env`.

**Excluded from rsync** (see `.github/workflows/deploy.yml`): `.git/`, `.github/`, `.env` / `.env.*`, `.editorconfig`, `.gitattributes`, `.gitignore`, `.npmrc`, `docs/`, `tests/`, `node_modules/`, `README.md`, `phpunit.xml`, `package.json`, `package-lock.json`, `vite.config.js`, `database/factories/`, `database/seeders/`, runtime `storage/*` paths, `database/database.sqlite*`.

rsync `--delete` does **not** remove paths that are excluded. After adding excludes, junk already on the server was removed once by hand; future deploys simply never re-upload those files.

## Local helpers

Copy [`.env.deploy.example`](../.env.deploy.example) → `.env.deploy` (gitignored) and fill host/user/path/key:

```bash
php artisan demo:export
php artisan demo:pull --host=…   # remote export + download zip
php artisan demo:push            # upload local zip + remote demo:import
```

Options: `--dry-run`, `--host`, `--user`, `--path`, `--key`, `--php`; `demo:pull` also `--import`. SCP uses `DEPLOY_PATH/storage/app/demo-sync/…`; remote `demo:import` gets **app-relative** `storage/app/demo-sync/demo-content.zip` after `cd` into the app.

Tables in the zip: `users`, `categories`, `menu_items`, `stories`, `settings` (`DemoContentExporter` / `DemoContentImporter`).

## Search indexing

`SiteSettings.block_search_indexing` — Filament SEO tab. Effects when **true**:

- `<meta name="robots" content="noindex, nofollow">`
- `/robots.txt` → `Disallow: /` (`RobotsController`; do not restore static `public/robots.txt`)

Production has indexing **enabled** (`false`).

## Verify

- https://tetri-cafe.ru — site loads with media
- https://tetri-cafe.ru/robots.txt — empty `Disallow` (indexing allowed)
- View source — no `noindex` meta
- https://tetri-cafe.ru/admin — Filament login (from imported users)
