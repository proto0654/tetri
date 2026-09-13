# Content

Back to [project context](CONTEXT.md)

## Purpose

Structure of public pages and content sources for Tetri.

## Status

Active — IA and content model settled from UI mockup.

## Public IA (MVP)

| Route / anchor | Source |
| --- | --- |
| `/` Home sections: hero, kids, menu preview, concept, stories, contacts | `SiteSettings` + `Category` + `Story` |
| `/menu` All-menu blocks (default tab) + page accents | Livewire `MenuGrid` + `SiteSettings` |
| `/menu/{category}` Category grid (4-col, paginate 12) + SEO URL | Livewire `MenuGrid` + `MenuController` |
| `/menu/{category}/{item}` Dish detail + related from same category | `MenuItemController` + `MenuItem` |
| `/privacy` Privacy policy page | `PrivacyController` + SiteSettings |
| `/robots.txt` Crawl policy | `RobotsController` + `block_search_indexing` |
| Header / footer «НАВИГАЦИЯ» | `SiteSettings.nav_links` via `<x-site.nav-item>` (link \| booking) |

## Domain models

- `Category` — `title`, `slug`, `image`, `columns` (2|3 preview items in «Все меню»), `sort_order`, `is_active`
- `MenuItem` — belongs to category; `title`, `slug` (unique per category), `description`, `price`, `image`, `sort_order`, `is_active`
- `Story` — `title`, `video_path`, `preview_image`, `sort_order`, `is_active`
- `settings` key `site` — editable via Filament `ManageSiteSettings`

## Admin Peek preview

Filament Peek Page Preview (draft form → iframe). Mapping:

| Admin page | Preview Blade / public equivalent |
| --- | --- |
| Edit Category | `menu` → `/menu/{slug}` |
| Edit MenuItem | `menu.show` → dish page |
| Edit Story | `home` (story swapped into `stories`) |
| ManageSiteSettings | `home` (form merged over saved settings) |

`MenuGrid` inside the iframe still loads from DB. Details: [tech.md](tech.md).

## Demo seeding

`Database\Seeders\CafeContentSeeder` inserts missing demo categories, dishes, stories, admin user, and **blank** site settings keys. Re-running seed is safe: existing model rows and non-blank `settings.key=site` values are left untouched; only null/`''`/`[]` settings keys are filled. On disk, stock images are written only when missing or ≤50KB (solid fallback); videos are never overwritten if the path already exists. Stable keys: category `slug`, menu item (`category_id` + title/image path), story `video_path` (demo fingerprint), user `email`.

Demo **stories (MAX)**: 12 items — themes food (1–5), kids (6–9), social (10–12). Preview images from Unsplash; `video_path` is an empty placeholder `mp4` until real clips are uploaded in Filament.

Demo **main menu** (`osnovnoe-menyu`): 20 dishes so `/menu/osnovnoe-menyu` pagination (12/page) is exercisable locally.

## Menu page behavior

- Default `/menu`: tab «Все меню» — each active category as a block; preview count = `Category.columns` (2|3, default 3).
- Category tab / `/menu/{slug}`: single grid `lg:grid-cols-4`, Livewire `paginate(12)`; pills sync path via `history.pushState`.
- Legacy `?category=` redirects to `/menu/{slug}`.
- Home menu-preview cards → `route('menu.category')`; CTA «Смотреть все» → `/menu`.

See [setup.md](setup.md#seeding-idempotent--fill-blanks-only) for commands and seeder directives. Use `migrate:fresh --seed` only when intentionally discarding local edits.

## SiteSettings accent / balance copy

Decorative ◇ lines from the mockup are first-class settings (not hardcoded). Render with `<x-site.mark>`.

| Section | Keys |
| --- | --- |
| Kids | `kids_eyebrow`, `kids_location_note`, `kids_description`, `kids_description_secondary` |
| Menu (home + `/menu` eyebrow) | `menu_section_eyebrow`, `menu_section_description` |
| Menu page right meta | `menu_page_meta`, `menu_page_meta_note` |
| Concept | `concept_eyebrow`, `concept_aside` |
| Stories | `stories_section_aside`, `stories_section_aside_note` |
| Hero overlay | `hero_overlay_from`, `hero_overlay_via`, `hero_overlay_to` (rgba; sanitized via `CssColor`) |
| Contacts map | `map_latitude`, `map_longitude`, `map_marker_label`; optional `map_embed_url` forces iframe. With `YANDEX_MAPS_API_KEY`, public map is JS API + logo from `logo`. Route CTA via `YandexMap::routeUrl`. |
| Favicon | `favicon` (public disk path; Filament «Подвал и CTA») |
| Logo / OG | `logo` («Подвал и CTA»); `og_image` + `menu_og_image` (tab **SEO**) |
| Nav | `nav_links` (LIST_KEYS): `label`, `type` (`link`\|`booking`), `url` / `booking_source`. Tab **Навигация** + QuickSettings. Render only via `<x-site.nav-item>`. |
| SEO | `seo_title`, `seo_description`, `seo_title_suffix`, `home_seo_title`, `home_seo_description`, `menu_seo_title`, `menu_seo_description`; `block_search_indexing` on tab **SEO**. Public titles via `SiteSettings::documentTitle()`. Dashboard `AdminQuickSettings` duplicates SEO text fields (not OG uploads). |
| MAX bot | `max_bot_token`, `max_chat_id` (Filament tab «Интеграция с мессенджером MAX»; both required to save full settings form) |

Also: hero icons / kids benefits / social links use `icon` + optional `custom_icon` (uploaded SVG).

Section title fields (kids / menu / concept / stories / contacts) are Textareas — Enter or `<br>` become line breaks via `@typoBr`. Hero brand title stays plain.

## Contacts / map

- Home contacts: map col1 left-bleed stretches cream+olive height; clip with `rounded` + `overflow-hidden` + `isolate` on the bleed **wrapper** (map root / iframe stay unrounded).
- Preferred render: JS API `div[data-yandex-map]` when env key + coords; pin uses `SiteSettings.logo` (`data-logo`) or marker label text.
- Fallback: `map_embed_url` iframe, or `YandexMap::widgetUrl` without API key.
- Map column: `.site-contacts-map-olive-half` — full-viewport olive `::before` on bottom 50% (z-index -1); right column `relative z-10` so copy stays above the band.
- «Проложить маршрут на карте» after working hours when coords are set.
- `design_credit`: layout `x-site.design-credit` only (not inside contacts partial).
- Details: [tech.md](tech.md), [design.md](design.md).

## Booking

- CTA components: `<x-site.book-button>` → `Livewire.dispatch('booking-open')`
- Modal: `App\Livewire\BookingModal` in site layout
- Submit: `MaxNotificationService::sendFormNotification()` → MAX group chat; on failure show `@error('form')` and keep modal open
- Credentials: SiteSettings `max_bot_token` + `max_chat_id` (see [tech.md](tech.md))
- `booking_cta_url` kept for legacy; buttons prefer the modal

## Search indexing

- `SiteSettings.block_search_indexing` (default **true**, Filament tab **SEO**).
- When true: layout meta `noindex, nofollow` + `/robots.txt` `Disallow: /`.
- Demo host stays blocked; turn off only for real production indexing. See [DEPLOY.md](DEPLOY.md).

## SEO (CMS) / Open Graph

| Key / page | Role |
| --- | --- |
| `seo_title` / `seo_description` | Site-wide fallback `<title>` / meta + og description |
| `seo_title_suffix` | Brand tail for «Part — {suffix}» (menu, dish, privacy) |
| `home_seo_title` / `home_seo_description` | Home overrides (empty home description → general) |
| `menu_seo_title` / `menu_seo_description` | Menu title segment + `/menu` description (empty → general) |
| `og_image` | Home share image; fallback for other pages |
| `menu_og_image` | `/menu` and category pages (empty → `og_image`) |
| Dish page | `og:image` = `MenuItem.image` → `menu_og_image` → `og_image`; description = item text → `seo_description` |

Layout `layouts/site.blade.php`: `@section('og_image_path')` → `PublicMedia::absoluteUrl` (relative paths are not enough for Telegram). Empty yield → `og_image`.

Telegram previews need a public HTTPS host, working `/storage` link, and uploaded images; crawler cache may lag (@webpagebot).

Do not hardcode public title/description strings in Blade. Edit on dashboard **Быстрые настройки** or ManageSiteSettings **SEO**.

## Favicon

- Upload in ManageSiteSettings «Подвал и CTA» → `SiteSettings.favicon`
- Render only via `<x-site.favicon>` in `layouts/site.blade.php` (`PublicMedia::url`); no hardcoded `public/favicon.ico`

## Admin dashboard (client UX)

| Widget | Role |
| --- | --- |
| `AccountWidget` | Welcome + Sign out (full width) |
| `AdminQuickLinks` | Tiles → Categories, Dishes, Stories, Site settings |
| `AdminQuickSettings` | Contacts, hero title/bg, nav_links, SEO text → partial `SiteSettings::save` |

## Links

- Product: [product.md](product.md)
- Design: [design.md](design.md)
- Tech: [tech.md](tech.md)
- Deploy: [DEPLOY.md](DEPLOY.md)
