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
- Hero: full-bleed atmosphere + centered stories frame; brand absolute at bottom with fluid size and slight frame overlap; CMS `hero_subtitle` on desktop splits words left/right of the frame (vertically centered on the story), on mobile sits under the brand; amenity icons `bottom-0` + `translate-y-1/2` (half into next section); section `overflow-x-clip` only
- Hero story sizing: width `min(100%, --hero-story-max-w, (100svh - --hero-chrome) * 3/4)` so the 3/4 frame shrinks on short viewports. Short-height (`max-height` 900/780) densifies `--hero-title-size`, icon size/gap, content padding, header pill. Title size via CSS var `--hero-title-size`, not a fixed Tailwind `text-*` / inline clamp alone.
- Hero story edges: **no** `border-white/20` (composites over `bg-black/20` into a grey rim). Media is `absolute inset-[-3px]` inside `aspect-[3/4] overflow-hidden rounded-[2rem]` to crop ~1–3px baked-in poster/video fringe. When mask is fully open, `clip-path: none` — Chromium paints a 1px dark fringe on `inset(0 round …)`; `overflow` + radius already clip.
- Hero brand title (`data-hero-title`): split to glyphs only (spaces dropped for layout); equal **center-to-center** step via `--hero-title-step` (default `1.2em`); middle glyph (`data-hero-char-pivot`) on viewport center (`h1` at `left:50%` + slot `translateX(calc(-50% + n*step))`). Do not center the whole word box or rely on `tracking` / side absolute stacks — that makes uneven gaps.
- Hero overlay: CMS rgba gradient (`hero_overlay_*`) as inline `linear-gradient`, not hardcoded Tailwind stops
- Hero entrance motion-chain (home load + scroll reverse):
  - **Load:** one linear master `heroProgress` 0→1 (~2100ms); modules map overlapping ranges — **no** `onComplete` chains. Easing inside modules, not on the global clock.
  - Approximate load ranges: background `0–0.42` · header `0.06–0.30` · story `0.18–0.48` · title `0.40–0.65` · icons `0.58–0.78` · subtitle `0.72–1.0` (long opacity fade, last)
  - **Scroll reverse (after load, bidirectional):** not one shared progress — per-module locals.
    - **Bg:** reverse as soon as `scrollY > 0`; local = 0 when `[data-hero-story]` center hits the viewport top. Band order stays top→bottom lift (cover starts from bottom). On `scrollScrub`, `hero-bg` uses **linear** band opacity so covering reads immediately (`easeOutCubic` stays near 0 until late in each window).
    - **Story mask:** hold full until top crosses **~8vh** (`STORY_MASK_START_VH`); then collapse to 0 when center hits viewport top (shorter window, same end as before).
    - **Title + subtitle:** reverse only after element top crosses **20vh**; local = 0 when top ≤ 0.
    - **Icons:** load keeps per-bullet stagger; scroll hide **together** from page top until icons top hits **mid-viewport** (`ICONS_SCROLL_END_VH` 0.5).
    - **Header:** load-only (no scroll reverse).
  - **Background:** cream band opacities via `hero-bg.js` `setProgress` / `--hero-bg-progress` (cover stays in DOM for scrub; no one-shot keyframes / no `visibility:hidden` on done).
  - **Header** (shared): home pill `scaleX` unfold then logo → nav → CTA stagger; inner pages `variant=bar` content-only. `header-entrance.js`.
  - **Story:** clip-path morph point → circle → rounded square → open; at full open drop `clip-path` to `none` (keep `overflow-hidden` + radius). Media overscaled under mask (`inset -3px`). Video: no Blade `autoplay`; `play` only when mask fully open; `pause` on collapse **without** rewind (resume from same position).
  - **Title:** per-glyph Y-flip (`rotateY` ~90→0) + opacity; **left-to-right** stagger (not center-out). Layout still center-pivoted slots.
  - **Icons:** load: scale/pop stagger on circle then delayed glyph; scroll: all icons share one local (no chain). Sizes via `--hero-icon-*` CSS vars (densify on short height).
  - **Subtitle:** long opacity-only fade on `[data-hero-subtitle]` after icons (load last; scroll like title 20vh→0).
  - Language: minimal / editorial / no bounce / no blur-glow-glitch; `prefers-reduced-motion` → finals, no scrub.
  - Cover bands: cream `#ede2cf`; **14** tapered strips; opacity-only; authored `--i` / `--jitter` / `--dur` drive scrub stagger. Y jitter room capped (`$yRoomScale` 0.5) so seams keep ≥50% overlap; band `top`/`height` get ±0.5px to absorb subpixel olive hairlines.
- Section entrance chain (`[data-entrance]`, GSAP + SplitType + ScrollTrigger):
  - **Scope:** home below-fold, menu page chrome (+ tabs), dish show (main + related roots), olive footer (own root nested in contacts; queries scoped to owning root).
  - **Out:** hero / header (separate systems); menu product cards keep CSS `menu-grid-*` (held until entrance `done` on direct `/menu` load; Livewire AJAX under `done` animates immediately).
  - **Roles:** `data-entrance-title` lines slideUp — `.site-text-shift` / `@typoBr` use BR split + structural hanging indent (not SplitType; inherited `text-indent` + overflow masks clipped glyphs). `data-entrance-info` line fade. `data-entrance-fade` / list / CTA fades. Static `data-entrance-media` clip morph (hero language; cap ~3). Swiper cards `data-entrance-media="fade"` — opacity via GSAP **proxy**.
  - **Media phase:** slides/morph start **after** title/fade/list (`tl.add('media', '>')` + `media+=n*MEDIA_STAGGER`). Do not overlap Swiper fades into the copy-role `-=` chain — short menu-preview title + dual fades drowned the card stagger; stories read OK because the title phase is longer.
  - **Stories Swiper:** `data-slides-offset-before="shell"`, linear (no loop/rewind — loop clones peeked before first slide).
  - **Stories viewer** (`#story-viewer` / `story-viewer.js`): click `[data-story-open]` card → GSAP FLIP into viewer (not in-card play). Stage **aspect 3/4** (same as carousel / hero frame — not 9:16). Mobile `<lg`: stage vertically centered in viewport; scrim denser (`ink` ~0.92); back top-left + collapse top-right; ↑↓ nav vertically centered on the right. Desktop: lightbox beside chrome rail (collapse + ↑↓); scrim ~0.72. Vertical Swiper (swipe / wheel / arrows); one-shot swipe/wheel hint per session. Reduced-motion: instant open/close.
  - **Menu-preview loop:** ×2 width fillers marked `data-entrance-clone`; stay opacity 0 until section `done` (do not snap visible mid-chain).
  - **Card transitions:** Swiper fade cards must not use Tailwind `transition` (all properties) — it fights JS opacity and muddies the stagger. Menu-preview: `transition-shadow` on the card, `transition-transform` on the image hover scale.
  - **Perf:** ST registered at init; timeline + SplitType built on first enter; no `will-change` / force3D on Swiper fades; playing-only `will-change` + morph `contain: paint` elsewhere; plain style clear on fade media complete.
  - **Slide fade jerk (menu-preview):** not a timeline-offset bug — first non-zero opacity uploads large category bitmaps (~2899px sources at ~256px display) and stalls the main thread ~200ms; GSAP then catch-up-jumps opacity. Fix: prewarm during copy (`img.decode` + opacity `0.001` for 2 rAF → `0`) before `media+=0`; `gsap.ticker.lagSmoothing(120, 33)` so residual stalls pause instead of jump; menu-preview `<x-media loading="eager">`.
- Soft large radii on media (~1.5–2rem)
- Menu pills: inactive cream-dark, active plum; first pill «Все меню»; category mode uses up to 4 columns
- Menu pagination: same pill language (plum active / cream-dark inactive) + olive round ←/→ like Swiper arrows; no gray Laravel default chrome
- Footer: olive-deep; `x-site.footer` (+ `.site-bg-viewport-olive`) + `footer-bar` (cookie/privacy strip); contacts embeds footer in olive column
- Olive viewport fills: `.site-bg-viewport-olive::before` is **mobile only** (`max-width: 1023px`) — on lg+ the map column’s `.site-contacts-map-olive-half` covers the left third and the footer’s own bg fills the 2/3 column. `.site-contacts-map-olive-half::before` is **desktop only** (`min-width: 1024px`) so the half-band does not peek beside the stacked map on mobile. `.site-bleed-left` is desktop-only (plain `@layer` utilities do not emit `lg:` variants).
- Section accents: small ◇ lines via `<x-site.mark>` for visual balance (eyebrow / aside / meta)
- Icons on site: `<x-site.icon>` (Heroicon name or custom SVG from storage)
- Carousels (menu preview, stories): local Swiper slides + prev/next; no Alpine overflow scroll. Stories cards stay `div[role=button]` (not `<button>` / focusable video) so horizontal drag does not freeze.
- Section titles share one scale: `text-4xl sm:text-5xl lg:text-6xl` (home sections, menu page, category heading). Hero brand uses fluid clamp, not that scale
- Section titles in Blade: `@typoBr` so CMS line breaks render; hero brand stays plain
- Kids/concept title hanging indent (lg+): `.site-text-shift` inside `.site-text-shift-scope` (`container-type: inline-size`); `padding-left` / `text-indent: calc(30cqw + 2.5rem)`. Use `cqw`, not `%`
- Info paragraphs (concept/kids descriptions): `.site-info` — uppercase, `text-plum` (same as CTA buttons), ~15% over `text-base`/`text-lg`, full column width, justified (`text-justify` + `hyphens: auto`)
- Menu-preview: col1 stretches (eyebrow/title/desc top, CTA `lg:mt-auto`); Swiper arrows under `.site-bleed-right` in col2–3; category titles white overlay on image with light `from-black/60` gradient; category cards `transition-shadow` only (not full `transition`) so entrance opacity chain stays clean
- Concept aside (`concept_aside`) sits at the bottom of col1 on lg (`lg:mt-auto`)
- Contacts map: left-bleed card; radius + overflow clip on wrapper (`isolate`), not on map root / iframe; right-only radius on lg while stacked with cream+olive
- Map column olive half-band: `.site-contacts-map-olive-half` (`::before` bottom 50%, `100vw`, z-index -1) so the rounded bottom-right sits on olive; contacts copy column uses `z-10`
- Map marker (JS API): large **rounded-rect** cream plate (`.site-yandex-map-pin-head`, ~`1rem` radius) with CMS logo or text «ТЕТРИ»; olive stem tip. Not a circle — rectangular form fits the logo better.
- Map tile tint: CSS filter on Yandex ground/areas/borders/buildings panes (not the pin). Tuned trade-off: warm desaturated olive cast + enough contrast for street lines; labels and roads share tiles so they cannot be styled independently. Current baseline in `app.css` `.site-yandex-map […]`.
- Home olive footer column: no `.site-bleed-right` (nav/social/copy stay inside 2/3 shell); cream contacts band may still bleed
- `design_credit`: single full-width strip under main/footer via `x-site.design-credit`
- Favicon: CMS upload only; no hardcoded `public/favicon.ico`

## Admin

Filament defaults until a custom admin theme is requested. Icon selects show compact HTML SVG previews (`allowHtml`).

## Links

- Product: [product.md](product.md)
- Content: [content.md](content.md)
- Tech: [tech.md](tech.md)
