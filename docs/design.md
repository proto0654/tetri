# Design

Back to [project context](CONTEXT.md)

## Purpose

Visual direction and UI constraints for the public site and admin branding.

## Status

Active — tokens taken from the ТЕТРИ UI mockup.

## Public tokens (`resources/css/app.css`)

| Token | Role |
| --- | --- |
| `cream` (`#ede2cf`) / `cream-dark` | Page background / header pill |
| `olive` / `olive-deep` | Headings / footer |
| `plum` / `plum-dark` | Primary buttons, active pills |
| `ink` / `muted` | Body text |
| `font-display` Literata | Section titles / brand |
| `font-sans` Manrope | UI / body |

## Layout notes

- Header: cream `rounded-full` pill (logo + nav + CTA) inside `max-w-7xl` padding; home absolute over hero, other pages sticky; link/brand always ink/olive (never white on cream)
- Hero: full-bleed atmosphere + centered stories frame; brand absolute at bottom with fluid `clamp(vw)` and slight frame overlap; amenity icons `bottom-0` + `translate-y-1/2` (half into next section); section `overflow-x-clip` only
- Hero overlay: CMS rgba gradient (`hero_overlay_*`) as inline `linear-gradient`, not hardcoded Tailwind stops
- Soft large radii on media (~1.5–2rem)
- Menu pills: inactive cream-dark, active plum; first pill «Все меню»; category mode uses up to 4 columns
- Menu pagination: same pill language (plum active / cream-dark inactive) + olive round ←/→ like Swiper arrows; no gray Laravel default chrome
- Footer: olive-deep; `x-site.footer` + `footer-bar` (cookie/privacy strip); home embeds footer in contacts olive column
- Section accents: small ◇ lines via `<x-site.mark>` for visual balance (eyebrow / aside / meta)
- Icons on site: `<x-site.icon>` (Heroicon name or custom SVG from storage)
- Carousels (menu preview, stories): local Swiper slides + prev/next; no Alpine overflow scroll
- Section titles share one scale: `text-4xl sm:text-5xl lg:text-6xl` (home sections, menu page, category heading). Hero brand uses fluid clamp, not that scale
- Section titles in Blade: `@typoBr` so CMS line breaks render; hero brand stays plain
- Kids/concept title hanging indent (lg+): `.site-text-shift` inside `.site-text-shift-scope` (`container-type: inline-size`); `padding-left` / `text-indent: calc(30cqw + 2.5rem)`. Use `cqw`, not `%`
- Info paragraphs (concept/kids descriptions): `.site-info` — uppercase, `text-plum` (same as CTA buttons), ~15% over `text-base`/`text-lg`, full column width, justified (`text-justify` + `hyphens: auto`)
- Menu-preview: col1 stretches (eyebrow/title/desc top, CTA `lg:mt-auto`); Swiper arrows under `.site-bleed-right` in col2–3; category titles white overlay on image with light `from-black/60` gradient
- Concept aside (`concept_aside`) sits at the bottom of col1 on lg (`lg:mt-auto`)
- Contacts map: left-bleed card; radius + overflow clip on wrapper (`isolate`), not on iframe; right-only radius on lg while stacked with cream+olive
- Map column olive half-band: `.site-contacts-map-olive-half` (`::before` bottom 50%, `100vw`, z-index -1) so the rounded bottom-right sits on olive; contacts copy column uses `z-10`
- Home olive footer column: no `.site-bleed-right` (nav/social/copy stay inside 2/3 shell); cream contacts band may still bleed
- `design_credit`: single full-width strip under main/footer via `x-site.design-credit`
- Favicon: CMS upload only; no hardcoded `public/favicon.ico`

## Admin

Filament defaults until a custom admin theme is requested. Icon selects show compact HTML SVG previews (`allowHtml`).

## Links

- Product: [product.md](product.md)
- Content: [content.md](content.md)
