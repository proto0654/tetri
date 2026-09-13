# Project context

> Hub for Tetri documentation. Branches below are deep-linked meaning/function maps.

## Snapshot

Public site for family cafe **Тетри**: Laravel + Filament admin, Vite frontend, GSAP section/hero motion, Swiper carousels, Livewire booking/menu.

## Deep links

| Branch | Path | Status |
| --- | --- | --- |
| Product | [product.md](product.md) | active |
| Tech | [tech.md](tech.md) | active |
| Tooling | [tooling.md](tooling.md) | active |
| Setup | [setup.md](setup.md) | active |
| Content | [content.md](content.md) | active |
| Design | [design.md](design.md) | active |
| Motion / entrance UX | [motion.md](motion.md) | active |
| Deploy | [DEPLOY.md](DEPLOY.md) | active |
| Changelog | [changelog.md](changelog.md) | active |

## Current decisions

- Section entrance starts at `top 65%` with settle delay `ENTER_PLAY_DELAY_MS` 160.
- Dish related uses `[data-entrance-follow]`: any shared viewport edge with the dish section → `FOLLOW_WHEN_BOTH_VISIBLE_MS` 1800 so titles do not slide up together; below the fold keeps its own ScrollTrigger.
- Hero load caps concurrent modules at two; never overlap story `clip-path` with title 3D glyphs.
- Menu-preview cards use mergeable `x-media` `loading="eager"` to avoid Swiper fade hitch.
- `docs/` is tracked in git (hub + branches).

## Open questions

- Exact `FOLLOW_WHEN_BOTH_VISIBLE_MS` may need tuning per dish layout height.

## Last actualized

- 2026-09-14 — Motion UX polish (entrance timing, post related delay, hero concurrency); track `docs/` in git.
