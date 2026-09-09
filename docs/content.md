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
| `/menu` Interactive menu tabs + page accents | Livewire `MenuGrid` + `SiteSettings` |
| Nav «О нас» → `#concept`, «Детская» → `#kids`, «Банкет» / booking CTAs → `BookingModal` | Settings + Livewire |

## Domain models

- `Category` — `title`, `slug`, `image`, `columns` (2|3), `sort_order`, `is_active`
- `MenuItem` — belongs to category; `title`, `description`, `price`, `image`, `sort_order`, `is_active`
- `Story` — `title`, `video_path`, `preview_image`, `sort_order`, `is_active`
- `settings` key `site` — editable via Filament `ManageSiteSettings`

## Demo seeding

`Database\Seeders\CafeContentSeeder` inserts missing demo categories, dishes, stories, admin user, and site settings. Re-running seed is safe: existing rows and `settings.key=site` are left untouched. Stable keys: category `slug`, menu item (`category_id` + title/image path), story `video_path` (demo fingerprint), user `email`.

See [setup.md](setup.md#seeding-idempotent) for commands and seeder directives. Use `migrate:fresh --seed` only when intentionally discarding local edits.

## SiteSettings accent / balance copy

Decorative ◇ lines from the mockup are first-class settings (not hardcoded). Render with `<x-site.mark>`.

| Section | Keys |
| --- | --- |
| Kids | `kids_eyebrow`, `kids_location_note`, `kids_description`, `kids_description_secondary` |
| Menu (home + `/menu` eyebrow) | `menu_section_eyebrow`, `menu_section_description` |
| Menu page right meta | `menu_page_meta`, `menu_page_meta_note` |
| Concept | `concept_eyebrow`, `concept_aside` |
| Stories | `stories_section_aside`, `stories_section_aside_note` |

Also: hero icons / kids benefits / social links use `icon` + optional `custom_icon` (uploaded SVG).

## Booking

- CTA components: `<x-site.book-button>` → `Livewire.dispatch('booking-open')`
- Modal: `App\Livewire\BookingModal` in site layout
- Submit: stub (`alert` with form payload); production bot integration still open
- `booking_cta_url` kept for legacy; buttons prefer the modal

## Links

- Product: [product.md](product.md)
- Design: [design.md](design.md)
- Tech: [tech.md](tech.md)
