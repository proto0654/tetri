# Tech stack

Back to [project context](CONTEXT.md)

## Purpose

Record settled technical choices for the Tetri Laravel application.

## Facts

| Layer | Choice | Version / note |
| --- | --- | --- |
| Runtime | PHP | 8.4 (Herd local; REG.RU prod `/opt/php/8.4` — required ≥8.4.1) |
| Local URL | Herd | `http://tetri.test` (not localhost/:8000) |
| Production URL | REG.RU ISPmanager | `https://tetri-cafe.ru` (`<DEPLOY_HOST>` / `<DEPLOY_USER>`) |
| Framework | Laravel | 13.31 |
| Admin | Filament | 5.8 (`AdminPanelProvider`) + Peek 4.1 Page Preview |
| Public UI | Blade + Livewire + Alpine | Livewire 4.4 (via Filament) |
| Tests | PHPUnit | 12.x |
| Frontend tooling | Vite + Tailwind CSS | Vite 8 / Tailwind 4 |
| Sliders | Swiper (local npm) | bundled via Vite; no CDN |
| Motion | GSAP + SplitType | section entrance; hero stays hand-rolled |
| DB (local + prod) | SQLite | prod absolute path under app `database/` |
| Cache / session / queue (prod) | file / file / sync | Redis not installed on shared host |
| AI boost | laravel/boost | 2.8 (dev) |
| Git remote | GitHub | `proto0654/tetri` (public; `docs/` tracked) |

## Decisions

- Use Filament panel scaffolding (`php artisan filament:install --panels`).
- Admin `/admin` dashboard widgets (discovered under `app/Filament/Widgets`): full-width `AccountWidget` (Sign out; subclass of Filament’s), `AdminQuickLinks` (`StatsOverviewWidget` tiles → Categories / Dishes / Stories / Site settings), `AdminQuickSettings` (form: contacts, hero title/background, SEO text fields → `SiteSettings::save` partial merge). Panel `->widgets([])` so FilamentInfoWidget is off. Custom Blade in the panel must use Filament components / `fi-*` tokens — arbitrary Tailwind utilities are not in Filament’s CSS.
- Admin draft preview: `pboivin/filament-peek` `^4.1` (`FilamentPeekPlugin`) — **Page Preview** modal (not deprecated Builder side-by-side). Pattern aligned with sibling `e:/laravel/1st`. Header `PreviewAction` on Edit Category / MenuItem / Story and `ManageSiteSettings`. Targets: category → `menu` (`/menu/{slug}`); dish → `menu.show`; story + site settings → `home` (mutated collections / merged form settings). Laravel 13 `config/cache.php` `serializable_classes` must allow `CachedPreview`, `Category`/`MenuItem`/`Story`, Eloquent + Support `Collection` — else `__PHP_Incomplete_Class`. Livewire `MenuGrid` in iframe still reads DB (unsaved grid/`columns` not draftable). Tests: `FilamentPeekPreviewTest`.
- Prefer Laravel Boost MCP tools over ad-hoc exploration when available.
- Keep project conventions via Boost guidelines in `AGENTS.md`, `.ai/rules`, and Cursor skills under `.cursor/skills/`.
- Site copy/media settings live in `settings` JSON (`App\Settings\SiteSettings`) — no Spatie Settings package unless approved.
- Public frontend is Blade + Livewire (`MenuGrid`, `BookingModal`), not Inertia.
- All public carousels/sliders use local Swiper (`npm` + `resources/js/app.js`); no Alpine overflow scroll or CDN. Stories: shell offset, linear (no loop/rewind). Menu-preview keeps `data-loop`; when slide width is short, `ensureEnoughLoopSlides` ×2 clones with `data-entrance-clone` + `aria-hidden`.
- Stories viewer: `resources/js/story-viewer.js` (`initStoryViewer` after `initSwipers`; destroy on `livewire:navigated`). Markup in `home/partials/stories.blade.php` (`#story-viewer`). Vertical track uses `data-story-viewer-swiper` (never `data-swiper`) so horizontal init ignores it. Modules: Mousewheel + Keyboard. Open/close: GSAP FLIP from card rect; body scroll lock via `position:fixed` + restore with `scroll-behavior: auto` / `behavior: 'instant'` (CSS `html { scroll-behavior: smooth }` otherwise jumps to top then animates back). Active video: load `source[data-src]` on demand; play from `t=0` on `slideChangeTransitionEnd` after `loadeddata`/`canplay` (play token cancels stale plays); neighbors preload source; carousel previews stay muted. z-index 70 below booking modal 80.
- Hero entrance: `app.js` → `initHeaderEntrance` + `initHeroBg({ autoPlay: false })` + `startHeroEntrance`. Modules: `hero-entrance.js` (load master + scroll locals), `header-entrance.js` (pill|bar, load-only), `hero-bg.js` (`setProgress` / `finish` / `whenReady`; band opacities from `--i`/`--jitter`/`--dur`; `scrollScrub` → linear opacity), `motion-utils.js`. Load: linear master ~2100ms (subtitle last). Scroll: bg full travel to story-center/top; story mask hold ~8vh then same end; title/subtitle 20vh→0; icons together until mid-viewport. Video gated in `applyStory` (play when full; pause keeps `currentTime`). At full story local: `clipPath = 'none'` (not `inset(0 round 2rem)`). CSS: height-aware `[data-hero-story]` width + short-height densify vars; pending/playing/done + reduced-motion in `app.css`.
- Section entrance (home/menu/dish/footer): ScrollTrigger registered at init; timeline + SplitType built lazily on first enter. Morph math in `motion-morph.js` (cap ~3). Swiper `data-entrance-media="fade"`: **own phase** after copy — `tl.add('media', '>')` then `media+=n*MEDIA_STAGGER` with GSAP proxy opacity (avoid CSSPlugin `translate(0,0)` on track; avoid overlapping slides into title/fade `-=`). Loop fillers (`data-entrance-clone`) stay opacity 0 until `done`. No `will-change`/force3D on those fades. Fade cards: `transition-shadow` / `transition-transform` only. On timeline `onStart`: `prewarmFadeMedia` (`decode` + opacity `0.001` ×2 rAF → `0`) so GPU upload finishes during copy; module init `gsap.ticker.lagSmoothing(120, 33)`. Menu-preview category imgs: `loading="eager"` (`x-media` merge default remains `lazy`). Kill/re-init on `livewire:navigated` via `destroySectionEntrance`.
- Mockup is the source of truth over external entity summaries; stack versions in old briefs are ignored in favor of installed packages.
- Category needs `image` + `columns` (2|3 preview count for «Все меню» blocks on `/menu`). Single-category menu view is always 4-col + paginate(12).
- Menu routes: `/menu`, `/menu/{category}`, `/menu/{category}/{item}`; Livewire `MenuGrid` mounts from category slug; `?category=` redirects to path.
- Media URLs for the public site: `App\Support\PublicMedia` → relative `/storage/...`.
- Icon fields: `HeroiconOptions` + `IconFieldSchema` (Filament `Select::allowHtml()` previews must use fixed inline SVG size, not Tailwind `h-*`/`w-*`).
- Livewire temp uploads raised for hero video (`config/livewire.php`); Herd PHP upload limits may need matching.
- Seeders are idempotent (`firstOrCreate` / create-if-missing for models; SiteSettings fill blank keys only). On-disk media: never overwrite images >50KB or any existing video file. `updateOrCreate` must not be used for demo cafe content. Overwrite only via `migrate:fresh --seed`. Details: [setup.md](setup.md#seeding-idempotent--fill-blanks-only).
- Unsafe CSS color strings from CMS: `App\Support\CssColor::resolve` (rgba/hex allowlist + fallback).
- Russian typography: `akh/typograf` via `App\Support\Typograph`; Blade `@typo` (strip all HTML) and `@typoBr` (keep newlines/`<br>` for section titles).
- Booking → MAX: `App\Services\MaxNotificationService` posts to `https://platform-api2.max.ru/messages?chat_id=`; `Authorization` is the raw bot token (no Bearer). Credentials: SiteSettings `max_bot_token` / `max_chat_id`. Temporary `withoutVerifying()` for Минцифры TLS — prefer installing the CA in production.
- Deploy: Actions rsync (code + migrate only); `demo:export` / `demo:pull` / `demo:push` / `demo:import` for content snapshot. Details: [DEPLOY.md](DEPLOY.md).
- Robots: `block_search_indexing` (default true in code; **false** on prod) → layout meta + `RobotsController` at `/robots.txt`. ManageSiteSettings tab **SEO**.
- Public SEO / OG: SiteSettings keys + `documentTitle()`; layout `@yield('og_image_path')` → absolute `og:image` (home `og_image`; menu `menu_og_image`; dish `MenuItem.image` with fallbacks). See [content.md](content.md#seo-cms--open-graph).
- CMS nav: `SiteSettings.nav_links` (LIST_KEYS wholesale on read/save) + `NavLinkFieldSchema` + `<x-site.nav-item>`; booking sources `nav-{slug}` / `footer-{slug}` labeled in `MaxNotificationService`.
- Contacts map: prefer Yandex Maps **JS API 2.1** (`YANDEX_MAPS_API_KEY` → `config/services.php` `yandex_maps.key`). Blade `x-site.contacts` renders `div[data-yandex-map]` with coords/label/logo/apikey; `resources/js/yandex-map.js` lazy-loads CDN API, inits from `app.js` (DOMContentLoaded + `livewire:navigated`). Map options: `controls: []`, `suppressMapOpenBlock: true` (no ruler/traffic/«Открыть в Яндекс Картах»). Custom HTML placemark (rounded-rect cream plate + site logo via `data-logo` / text fallback). Tile tint: CSS filter on ground/areas/borders/buildings panes only (pin stays crisp). Fallback: `map_embed_url` iframe override, or `YandexMap::widgetUrl` when key missing. Route CTA still `YandexMap::routeUrl`. Do not put API key in SiteSettings/Filament. Restrict key by HTTP Referrer in Yandex dashboard.

## Open questions

- MySQL instead of SQLite for client prod (optional later).
- Production TLS trust store for MAX API (drop `withoutVerifying()` when CA is installed).

## Links

- Hub: [CONTEXT.md](CONTEXT.md)
- Setup: [setup.md](setup.md)
- Deploy: [DEPLOY.md](DEPLOY.md)
- Tooling: [tooling.md](tooling.md)
- Product: [product.md](product.md)
- Content: [content.md](content.md)
- Design: [design.md](design.md)
