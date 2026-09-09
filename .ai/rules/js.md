---
paths:
  - 'resources/js/**'
---

# Js

## Initialize sliders via data-swiper
Public Swiper instances are bootstrapped from `resources/js/app.js` on `[data-swiper]` (slidesPerView auto, Navigation module). Keep Swiper as a local npm dependency; never load it from CDN. Re-init is safe (skips elements that already have `.swiper`).

## Swiper shell offset via data-slides-offset-before
data-slides-offset-before/after accept a number or "shell". The shell value measures distance from the swiper’s left edge to [data-site-shell] content (padding box) and is refreshed on resize. Used by homepage stories for initial alignment + left gutter scroll.
