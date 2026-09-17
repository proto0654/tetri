# Project context

> Hub for Tetri documentation. Branches below are deep-linked meaning/function maps.

## Snapshot

Public site for family cafe **Тетри**: Laravel + Filament admin, Vite frontend, GSAP section/hero motion, Swiper carousels, Livewire booking/menu.

## Deep links

| Branch | Path | Status |
| --- | --- | --- |
| Product | [product.md](product.md) | active |
| Tech | [tech.md](tech.md) | active |
| Tooling | [tooling.md](tooling.md) | active |
| Setup | [setup.md](setup.md) | active |
| Content | [content.md](content.md) | active |
| Design | [design.md](design.md) | active |
| Motion / entrance UX | [motion.md](motion.md) | active |
| Deploy | [DEPLOY.md](DEPLOY.md) | active |
| Changelog | [changelog.md](changelog.md) | active |

## Current decisions

- Production: **https://tetri-cafe.ru** (REG.RU ISPmanager). Deploy: GitHub Actions rsync + SSH key — details [DEPLOY.md](DEPLOY.md). Hosting account IDs / panel URL live only in local `.env.deploy` and Actions secrets.
- Public git history was rewritten (force-push) to scrub hosting account IDs, panel URLs, and a leaked maps API key string; keep secrets out of commits going forward.
- Deploy ships a **lean runtime tree** only: `docs/`, tests, Vite/npm sources, `phpunit`, factories/seeders stay in git and are rsync-excluded (see [DEPLOY.md](DEPLOY.md#what-ships-vs-stays-in-git)). `--delete` does not remove newly excluded paths — one-time host cleanup already done.
- ISPmanager panel password is **not** in app `.env`; changing it does not require env edits. Deploy uses Actions `DEPLOY_SSH_KEY` (and local deploy key via `.env.deploy`).
- Prod runtime: SQLite, `SESSION`/`CACHE` file drivers, `QUEUE_CONNECTION=sync`. **No Redis** (PHP redis ext not installed on host).
- Search indexing on for production (`block_search_indexing` false). Former staging demo removed after cutover.
- Prod Filament `/admin`: two operators (studio + cafe client). Seed users (`admin@tetri.test`, `test@example.com` / `password`) removed from prod. Credentials are out of band — **never** commit passwords.
- Content sync helpers: `demo:export` / `demo:pull` / `demo:push` / `demo:import` — cutover only, not routine prod pushes.
- Section entrance starts at `top 65%` with settle delay `ENTER_PLAY_DELAY_MS` 160.
- Dish related uses `[data-entrance-follow]`: any shared viewport edge with the dish section → `FOLLOW_WHEN_BOTH_VISIBLE_MS` 1800 so titles do not slide up together; below the fold keeps its own ScrollTrigger.
- Hero load caps concurrent modules at two; never overlap story `clip-path` with title 3D glyphs.
- Menu-preview cards use mergeable `x-media` `loading="eager"` to avoid Swiper fade hitch.
- `docs/` is tracked in git (hub + branches) for the team; not deployed to the host.
- Header/footer nav from CMS `nav_links` via `<x-site.nav-item>` (not hardcoded Blade labels).
- OG share images: home `og_image`, menu `menu_og_image`→`og_image`, dish photo→menu→site; absolute URLs only from layout.

## Open questions

- Exact `FOLLOW_WHEN_BOTH_VISIBLE_MS` may need tuning per dish layout height.
- Optional later: MySQL instead of SQLite; MAX TLS CA so `withoutVerifying()` can drop.

## Last actualized

- 2026-09-17 — Prod Filament admins (studio + client); history scrub force-push; lean deploy / cutover notes earlier same day.
- 2026-09-14 — CMS nav_links + per-page OG (menu/dish); docs hub refreshed.
