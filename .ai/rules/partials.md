---
paths:
  - 'resources/views/home/partials/**'
---

# Partials

## Carousel media aspect ratios
Homepage menu-preview category cards and stories carousel slides share slide width (`!w-56 sm:!w-64`) and `aspect-[3/4]` (same as hero featured media). Avoid `aspect-[9/16]` on these carousels — too tall next to hero.

## Homepage desktop 3-col shell and right-bleed sliders
Desktop (lg+) sections use x-site.shell (max-w-7xl + px-4/6/8) with a 3-column grid. Headers either sit in col1 or span from col2; stories title row spans above the track. Menu-preview: copy+arrows in col1, Swiper in col2–3 with .site-bleed-right to the viewport edge. Stories: .site-bleed-x full-viewport track + data-slides-offset-before="shell" so the first slide starts on the shell content edge and can scroll into the left gutter. Slider sections use overflow-x-clip.

## Kids header spans above columns
Kids section: kids_eyebrow + kids_title sit together in one header block spanning above the 3-col grid; col1 = benefits + location, col2–3 = images + copy + CTA.

## Concept header spans above 1/3+2/3 body
Concept section matches kids: concept_eyebrow + concept_title in one header above the grid; body is lg:grid-cols-3 with aside in col1 (1/3) and description+CTA in col2–3 (2/3).

## Home map spans cream+olive column stack
Contacts+footer on home are one lg:grid-cols-3 block: map in col1 stretches full height of the right stack (absolute inset-y-0 + left-bleed). Map clip: rounded + overflow-hidden + isolate on the bleed wrapper only (not on the iframe). Right-only radius on lg. Map column uses .site-contacts-map-olive-half (::before olive band, bottom 50%, full viewport width, z-index -1); right column is relative z-10 so cream/olive copy stays above the band. Col2–3 split vertically: cream then olive, both site-bleed-right. design_credit only via layout x-site.design-credit. Layout skips x-site.footer on home.

## Contacts: map left-bleed only, no footer right-bleed
Contacts/footer: only the map uses .site-bleed-left past the shell. Cream + olive footer column stay inside max-w-7xl (no site-bleed-right).

## Olive viewport bg via pseudo; design credit row
Olive footer band uses .site-bg-viewport-olive (::before, 100vw) so the green fill spans the viewport while nav/social/copy stay in the 2/3 column. Map stays z-10 with left-bleed only. Narrow design_credit strip sits under the columns full-bleed; domain in the text becomes an external link.
