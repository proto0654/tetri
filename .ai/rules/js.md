---
paths:
  - 'resources/js/**'
---

# Js

## Initialize sliders via data-swiper
Public Swiper instances are bootstrapped from `resources/js/app.js` on `[data-swiper]` (slidesPerView auto, Navigation module). Keep Swiper as a local npm dependency; never load it from CDN. Re-init is safe (skips elements that already have `.swiper`).

## Swiper shell offset via data-slides-offset-before
data-slides-offset-before/after accept a number or "shell". The shell value measures distance from the swiper’s left edge to [data-site-shell] content (padding box) and is refreshed on resize. Used by homepage stories for initial alignment + left gutter scroll.

## Public Swipers opt into loop via data-loop
Homepage menu-preview and stories enable continuous circular loop via data-loop on [data-swiper]. Init in app.js: loop only when data-loop is present (not data-loop="false"); with loop on, use loopAdditionalSlides: 2 and watchOverflow: false so arrows stay active at the seam. slidesPerView stays auto.

## Duplicate slides ×2 when loop lacks width
When data-loop is on and original slides’ total width is under ~2× the container (common for menu-preview with few categories), duplicate the slide nodes once (×2) in the wrapper before new Swiper(). Mark clones aria-hidden / tabindex=-1; set wrapper data-loop-duplicated to avoid re-cloning.
