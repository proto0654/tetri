# Project context

> Hub for Tetri documentation. Branches below are deep-linked meaning/function maps.

## Snapshot

Public site for restaurant **ТЕТРИ**: Laravel + Filament admin, Vite frontend, GSAP section/hero motion, Swiper carousels, Livewire booking/menu.

## Deep links

| Branch | Path | Status |
| --- | --- | --- |
| Motion / entrance UX | [motion.md](motion.md) | active |
| Changelog | [changelog.md](changelog.md) | active |

## Current decisions

- Section entrance starts at `top 65%` with a short settle delay (`ENTER_PLAY_DELAY_MS` 160).
- Dish related block uses `[data-entrance-follow]`: if it shares the viewport with the dish section (even a 1px edge), it plays with `FOLLOW_WHEN_BOTH_VISIBLE_MS` (1800) so titles do not slide up together; below the fold it keeps its own ScrollTrigger.
- Hero load caps concurrent modules at two; never overlap story `clip-path` with title 3D glyphs (`HERO_RANGES` in `hero-entrance.js`).
- Menu-preview category cards use `loading="eager"` via mergeable `x-media` `loading` attribute to avoid Swiper fade hitch.

## Open questions

- Exact `FOLLOW_WHEN_BOTH_VISIBLE_MS` may need tuning per dish layout height.

## Last actualized

- 2026-09-14 — Motion UX polish (entrance timing, post related delay, hero concurrency).
