---
paths:
  - 'resources/js/**'
---

# Js

## Initialize sliders via data-swiper
Public Swiper instances are bootstrapped from `resources/js/app.js` on `[data-swiper]` (slidesPerView auto, Navigation module). Keep Swiper as a local npm dependency; never load it from CDN. Re-init is safe (skips elements that already have `.swiper`).
