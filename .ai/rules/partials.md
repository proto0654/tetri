---
paths:
  - 'resources/views/home/partials/**'
---

# Partials

## Carousel media aspect ratios
Homepage menu-preview category cards and stories carousel slides share slide width (`!w-56 sm:!w-64`) and `aspect-[3/4]` (same as hero featured media). Avoid `aspect-[9/16]` on these carousels — too tall next to hero.

## Homepage desktop 3-col shell and right-bleed sliders
Desktop (lg+) sections use x-site.shell (max-w-7xl + px-4/6/8) with a 3-column grid. Headers either sit in col1 or span from col2; stories title row spans above the track. Menu-preview: copy+CTA in col1, Swiper in col2–3 with .site-bleed-right; arrows under the bleed wrapper. Stories: .site-bleed-x full-viewport track + data-slides-offset-before="shell" so the first slide starts on the shell content edge and can scroll into the left gutter. Slider sections use overflow-x-clip.

## Kids header spans above columns
Kids section: kids_eyebrow + kids_title sit together in one header block spanning above the 3-col grid; col1 = benefits + location, col2–3 = images + copy + CTA.

## Concept header spans above 1/3+2/3 body
Concept section matches kids: concept_eyebrow + concept_title in one header above the grid; body is lg:grid-cols-3 with aside in col1 (1/3, lg:mt-auto at column bottom) and description+CTA in col2–3 (2/3).

## Map spans cream+olive column stack
Contacts+footer are one lg:grid-cols-3 block in `x-site.contacts`: map in col1 stretches full height of the right stack (absolute inset-y-0 + left-bleed). Map clip: rounded + overflow-hidden + isolate on the bleed wrapper only (not on the iframe). Right-only radius on lg. Map column uses .site-contacts-map-olive-half (::before olive band, bottom 50%, full viewport width, z-index -1); right column is relative z-10 so cream/olive copy stays above the band. Col2–3 split vertically: cream (may site-bleed-right) then olive via `x-site.footer` (no right-bleed, stays in shell). design_credit only via layout x-site.design-credit after contacts.

## Contacts: map left-bleed only, no footer right-bleed
Contacts/footer: only the map uses .site-bleed-left past the shell. Olive footer column stays inside max-w-7xl (no site-bleed-right); cream contacts band may still bleed right.

## Olive viewport bg via pseudo; design credit row
Olive footer band uses .site-bg-viewport-olive (::before, 100vw) so the green fill spans the viewport while nav/social/copy stay in the 2/3 column. Map stays z-10 with left-bleed only. Narrow design_credit strip sits under the columns full-bleed; domain in the text becomes an external link.

## Kids/concept title text-shift on lg
Section h2 titles use .site-text-shift inside .site-text-shift-scope (container-type: inline-size). On lg+: padding-left/text-indent calc(30cqw + 2.5rem) so the first line is flush left and wraps align after gap-x-10. Do not use % — text-indent % resolves against the content box after padding and will not cancel.

## Menu-preview arrows under swiper column
Menu-preview col1 stretches full height: eyebrow/title/desc top, CTA lg:mt-auto. Swiper nav arrows sit under .site-bleed-right in col2–3 (static left flow), not in col1. Category titles overlay the image bottom in white with a light from-black/60 gradient.

## Olive footer column has no right-bleed
Contacts olive footer (`x-site.footer`) stays inside the 2/3 shell column — no .site-bleed-right and no shell-pad right padding. Cream contacts band may still bleed; viewport olive behind the map uses .site-contacts-map-olive-half.

## Hero brand bottom overlap and half-out icons
Hero: centered stories frame; brand h1 absolute at bottom of the stack with translate-y so letters slightly overlap the frame. Fluid size via clamp(vw) capped to the max-w container — not fixed text-5xl/lg:text-7xl. Amenity icons sit absolute bottom-0 with translate-y-1/2 (half into the next cream section). Section uses overflow-x-clip only (not overflow-hidden) so icons can protrude.

## Contacts embeds x-site.footer
`x-site.contacts` olive band renders `x-site.footer` (footer-main + footer-bar). Map spans cream+olive. Layout renders contacts site-wide after `</main>` — not a home partial.

## Stories carousel: no button/video hit targets
Do not wrap stories slides in <button> or let <video> receive pointer/focus. Swiper’s default focusableElements includes button and video; when focused, mouse-drag freezes until another click. Use div[role=button][tabindex=0] for play/pause, and pointer-events-none + tabindex=-1 + draggable=false on video/media. Menu-preview <a> slides are fine — anchors are not in that list.

## Info paragraphs use .site-info
CMS info descriptions (concept_description, kids_description, kids_description_secondary) use the .site-info utility — uppercase, text-plum (CTA button color), ~15% over text-base/text-lg, full parent column width, text-justify + hyphens:auto. Do not reintroduce ad-hoc size/color/tracking/justify/max-width stacks on those paragraphs; keep spacing (mt-*) on the element.
