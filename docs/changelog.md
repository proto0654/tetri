# Changelog

Back to [project context](CONTEXT.md)

## 2026-09-17

- Production cutover to **https://tetri-cafe.ru** (REG.RU `<DEPLOY_HOST>` / `<DEPLOY_USER>`); Actions secrets and `deploy.yml` retargeted; SSH key `deploy_key`.
- Content pulled from former demo `former-staging.example` via `demo:export`/`demo:pull`, imported on prod; weblaba app + php-bin removed.
- Indexing enabled on prod (`block_search_indexing` false). SQLite + file session/cache; Redis not used.
- Clarified: ISPmanager panel password is not in `.env` — reset does not require env updates.
- Lean host tree: rsync excludes `docs/`, tests, npm/Vite sources, `phpunit`, factories/seeders, etc.; one-time cleanup on server. See [DEPLOY.md](DEPLOY.md#what-ships-vs-stays-in-git).
- Docs: [DEPLOY.md](DEPLOY.md), [tech.md](tech.md), [setup.md](setup.md), [content.md](content.md), hub [CONTEXT.md](CONTEXT.md).

## 2026-09-14

- CMS header/footer nav: `SiteSettings.nav_links` + `NavLinkFieldSchema` + `<x-site.nav-item>`; LIST_KEYS wholesale on read; MAX booking source labels for nav/footer.
- Open Graph: layout `@section('og_image_path')`; `menu_og_image` / `menu_seo_description`; dish uses product image then menu/site fallbacks. Documented in [content.md](content.md#seo-cms--open-graph).
- Documented motion UX decisions (section entrance timing, dish related follow delay, hero max-2 concurrency) in [motion.md](motion.md).
- Stopped ignoring `docs/` so the hub and branches commit normally; tracked existing branch files.
- Code: later entrance start + settle delay; wider title line stagger; `[data-entrance-follow]` on dish related; hero `HERO_RANGES` resequence + story box cache; menu-preview eager images / label gradient; `x-media` mergeable `loading`.
