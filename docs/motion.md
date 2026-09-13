# Motion / entrance UX

Back to [project context](CONTEXT.md)

## Purpose

Public-site entrance and hero load motion: readable cascades, no empty pending blocks on dish pages, smoother hero FPS.

## Facts

- Orchestrator: [`resources/js/section-entrance.js`](../resources/js/section-entrance.js) (`[data-entrance]`).
- Hero master clock: [`resources/js/hero-entrance.js`](../resources/js/hero-entrance.js) (`HERO_RANGES`, ~2100ms).
- Dish related markup: [`resources/views/menu/show.blade.php`](../resources/views/menu/show.blade.php) — `[data-entrance-follow]`.
- Agent rules for JS motion live under `.ai/rules/js.md` (gitignored; shared via Boost `record-rule` locally).

## Decisions

| Topic | Decision | Reason |
| --- | --- | --- |
| ScrollTrigger start | `top 65%` | Earlier `78%` fired before the block was readable |
| Settle delay | `ENTER_PLAY_DELAY_MS = 160` after double-rAF | Eye settles before chain |
| Title line gap | `TITLE_LINE_STAGGER = 0.16` | Clearer line-by-line slide-up |
| Cross-section wait (home) | None | Waiting on previous home sections felt sluggish |
| Dish related | `[data-entrance-follow]` + delay when prev still in viewport (any edge) | Same-screen titles must not start together; edge peek must not stay `pending` empty |
| Follow delay | `FOLLOW_WHEN_BOTH_VISIBLE_MS = 1800` | Approximate dish entrance length; no promise queue |
| Hero concurrency | Max 2 modules; no story∩title | Clip-path + 3D glyphs together caused jank |
| Hero story box | Cache width/height; invalidate on resize/play | Avoid layout thrash each frame |
| Steady paints | Skip module apply when local stayed ≥ `FULL_LOCAL` (load path) | Less style churn after settle |

## Links

- Hub: [CONTEXT.md](CONTEXT.md)
- Changelog: [changelog.md](changelog.md)
