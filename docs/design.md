# Design

Back to [project context](CONTEXT.md)

## Purpose

Visual direction and UI constraints for the public site and admin branding.

## Status

Active — tokens taken from the ТЕТРИ UI mockup.

## Public tokens (`resources/css/app.css`)

| Token | Role |
| --- | --- |
| `cream` / `cream-dark` | Page background |
| `olive` / `olive-deep` | Headings / footer |
| `plum` / `plum-dark` | Primary buttons, active pills |
| `ink` / `muted` | Body text |
| `font-display` Literata | Section titles / brand |
| `font-sans` Manrope | UI / body |

## Layout notes

- Hero: full-bleed atmosphere image + centered brand + vertical media + amenity icons
- Hero overlay: CMS rgba gradient (`hero_overlay_*`) as inline `linear-gradient`, not hardcoded Tailwind stops
- Soft large radii on media (~1.5–2rem)
- Menu pills: inactive cream-dark, active plum
- Footer: olive-deep
- Section accents: small ◇ lines via `<x-site.mark>` for visual balance (eyebrow / aside / meta)
- Icons on site: `<x-site.icon>` (Heroicon name or custom SVG from storage)
- Carousels (menu preview, stories): local Swiper slides + prev/next; no Alpine overflow scroll

## Admin

Filament defaults until a custom admin theme is requested. Icon selects show compact HTML SVG previews (`allowHtml`).

## Links

- Product: [product.md](product.md)
- Content: [content.md](content.md)
