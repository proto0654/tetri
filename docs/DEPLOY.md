# Deploy — tetri.weblaba.ru

Back to [project context](CONTEXT.md)

Demo host on REG.RU (same account as weblaba.ru). Pattern mirrors molecule: GitHub Actions → rsync over SSH. Content is **not** seeded on deploy.

**Status (2026-09-12):** live — https://tetri.weblaba.ru (noindex, content synced from local via `demo:push`).

## Architecture

```
push main
  └─ deploy.yml  →  rsync code  →  ~/www/tetri.weblaba.ru/  (+ migrate)
                                         │
local demo content ── php artisan demo:push ──► import tables + media
```

| Action | When | Touches DB data? |
|--------|------|------------------|
| Code deploy (`deploy.yml`) | push `main` / Run workflow | No — only `migrate --force` |
| `php artisan demo:push` | Manually, when demo should match local | Yes — replaces content tables + `storage/app/public` |
| Real production later | Same code deploy | Do **not** run `demo:push` |

## GitHub

- Repo: https://github.com/proto0654/tetri (**public**; trade-controls blocked private create).
- Local-only (gitignored, not pushed): `docs/`, `.cursor/`, `.ai/`, `AGENTS.md`, `boost.json`.
- Secrets → Actions: `DEPLOY_HOST`, `DEPLOY_USER`, `DEPLOY_SSH_KEY`, `DEPLOY_PATH`, `DEPLOY_PHP` (optional; default `/opt/php/8.4/bin/php`).
- Typical values (also in local `.env.deploy`, never commit): host `server199.hosting.reg.ru`, user `u1396397`, path `www/tetri.weblaba.ru/`, key `~/.ssh/weblaba_deploy`.

## Server facts

1. App root: `~/www/tetri.weblaba.ru/`; root `.htaccess` forwards into `public/`.
2. PHP **8.4** for CLI and CGI: `/opt/php/8.4/bin/php`, `~/php-bin/tetri.weblaba.ru/php` → `php-cgi` 8.4. Composer platform check requires ≥8.4.1 (Symfony 8).
3. Demo `.env`: SQLite at `database/database.sqlite` (absolute `DB_DATABASE` path under hosting home). Sessions/cache file drivers.
4. Windows has no native `rsync`; Actions runs on Ubuntu. Local one-shot sync used `tar`/`scp` or WSL when needed.
5. `demo:push` uses long Process timeouts (SCP can exceed 60s for media zips).

## Local helpers

Copy [`.env.deploy.example`](../.env.deploy.example) → `.env.deploy` (gitignored):

```bash
php artisan demo:push
```

Options: `--dry-run`, `--host`, `--user`, `--path`, `--key`, `--php`. SCP uses `DEPLOY_PATH/storage/app/demo-sync/…`; remote `demo:import` gets **app-relative** `storage/app/demo-sync/demo-content.zip` after `cd` into the app (not the DEPLOY_PATH-prefixed path).

Tables in the zip: `users`, `categories`, `menu_items`, `stories`, `settings` (`DemoContentExporter` / `DemoContentImporter`).

## Search indexing

`SiteSettings.block_search_indexing` defaults to **true** (Filament «Подвал и CTA»). Effects:

- `<meta name="robots" content="noindex, nofollow">`
- `/robots.txt` → `Disallow: /` (`RobotsController`; do not restore static `public/robots.txt`)

Turn off only when the real production domain should be indexed.

## Verify

- https://tetri.weblaba.ru — site loads with demo media
- https://tetri.weblaba.ru/robots.txt — `Disallow: /`
- View source — `noindex, nofollow`
- https://tetri.weblaba.ru/admin — Filament login (from pushed users)
