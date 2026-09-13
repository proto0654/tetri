# Changelog

Back to [project context](CONTEXT.md)

## 2026-09-14

- CMS header/footer nav: `SiteSettings.nav_links` + `NavLinkFieldSchema` + `<x-site.nav-item>`; LIST_KEYS wholesale on read; MAX booking source labels for nav/footer.
- Open Graph: layout `@section('og_image_path')`; `menu_og_image` / `menu_seo_description`; dish uses product image then menu/site fallbacks. Documented in [content.md](content.md#seo-cms--open-graph).
- Documented motion UX decisions (section entrance timing, dish related follow delay, hero max-2 concurrency) in [motion.md](motion.md).
- Stopped ignoring `docs/` so the hub and branches commit normally; tracked existing branch files.
- Code: later entrance start + settle delay; wider title line stagger; `[data-entrance-follow]` on dish related; hero `HERO_RANGES` resequence + story box cache; menu-preview eager images / label gradient; `x-media` mergeable `loading`.
