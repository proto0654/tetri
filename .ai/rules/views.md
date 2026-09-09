---
paths:
  - 'resources/views/**'
---

# Views

## Mockup is frontend source of truth
Public Blade UI follows the TETRI mockup. Prefer mockup layout/copy over outdated stack notes (Laravel 11 / Filament v3) in external briefs. Use installed Laravel 13 + Filament 5 + Livewire 4 APIs.

## Typography via @typo for CMS copy
Render CMS/prose fields with @typo(...) (or Typograph::apply in PHP). x-site.mark already typographs text/note. Keep {{ }} only for non-copy values (URLs, phone numbers, prices).

## All public sliders use local Swiper
Every carousel/slider on the public site must use locally installed Swiper (npm package `swiper`, bundled via Vite in `resources/js/app.js`). Do not use Alpine overflow scroll, CSS-only carousels, or CDN Swiper. Markup: `[data-swiper-root]` + `.swiper[data-swiper]` + `.swiper-wrapper` / `.swiper-slide`, with `[data-swiper-prev]` / `[data-swiper-next]` for arrows (no pagination dots unless design asks).

## Contacts map left-bleed and footer 1/3+2/3
Contacts: map in col1 (1/3) with .site-bleed-left to the viewport edge; round + overflow-hidden + isolate on the map wrapper only (not iframe); right-only rounding on lg; title+details in col2–3 (2/3). Footer: empty col1 on lg, main block (nav / social / about) in col2–3; bottom bar copyright + Наверх. design_credit via layout strip only.

## Home contacts/footer stacked 2/3 column
On home, contacts+footer share one 1/3+2/3 column: cream band = map (col1, left-bleed) + contact copy (col2–3); olive band = footer-main in the same col2–3. Footer component then renders only the narrow bar (copyright + Наверх) via withMain=false. Other pages keep footer withMain (empty col1 + footer-main + bar). design_credit is never inside contacts — only layout `x-site.design-credit`.

## Design credit strip outside map flow
design_credit is always rendered via x-site.design-credit as its own full-width strip after main/footer — outside the contacts/map grid. Empty CMS value falls back to «Разработка сайта winbaba.ru». html/body use overflow-x: clip to kill horizontal scroll from bleeds/100vw.
