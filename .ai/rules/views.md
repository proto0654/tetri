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
