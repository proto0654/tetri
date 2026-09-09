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

## Contacts map left-bleed and footer 1/3+2/3
`x-site.contacts`: map in col1 (1/3) with .site-bleed-left; round + overflow-hidden + isolate on the map wrapper only (not iframe); right-only rounding on lg; title+details in cream col2–3; olive `x-site.footer` (footer-main + footer-bar) stacked under contacts in the same col2–3. design_credit via layout strip only after contacts.

## Contacts+footer stacked 2/3 column
Contacts+footer share one 1/3+2/3 column on every public page via layout `x-site.contacts`: cream band = map (col1) + contact copy (col2–3); olive band = `x-site.footer` in col2–3. No standalone olive-only footer. design_credit is never inside contacts — only layout `x-site.design-credit`.

## Design credit strip outside map flow
design_credit is always rendered via x-site.design-credit as its own full-width strip after main/footer — outside the contacts/map grid. Empty CMS value falls back to «Разработка сайта winbaba.ru». html/body use overflow-x: clip to kill horizontal scroll from bleeds/100vw.

## Section title type scale
Public section titles share one scale: text-4xl sm:text-5xl lg:text-6xl (kids, concept, menu-preview, stories, contacts, menu page h1, menu-grid category). Hero brand uses fluid clamp(vw) sized to the hero container, not the section title scale. Do not bump only one section.

## Section titles use @typoBr
Render kids/concept/menu/stories/contacts section titles with @typoBr(...), not @typo(...), so CMS <br> or newlines become real line breaks. Hero brand title stays plain.

## Header cream pill vs internal bar
Home (transparent): absolute cream rounded-full pill over hero inside max-w-7xl with pt-4 + horizontal padding — no shadow. Internal pages: sticky full-bleed bg-cream bar (no shadow); horizontal padding is on the full-width wrapper, then mx-auto max-w-7xl for logo/nav/CTA — same as menu section (px outside, max-w-7xl inside). Do not put px inside max-w-7xl on internal headers (that makes the bar narrower than page content). Link/brand colors always dark (ink/olive).

## Footer via x-site.footer inside contacts
Olive footer (footer-main + footer-bar) is only `x-site.footer` inside `x-site.contacts` (2/3 olive stack). Do not render a standalone layout footer or inline footer-main / copyright markup elsewhere.

## Cookie notice only in footer-bar
Cookie/privacy notice text and the privacy link in the copyright strip come from x-site.footer-bar (settings cookie_notice + privacy_title → route('privacy')). Footer nav «Политика» is in footer-main. Do not duplicate cookie/privacy markup in contacts or page views.

## Contacts+footer block site-wide via layout
Site-wide closing block is x-site.contacts (map + contact copy + x-site.footer) rendered from layouts/site after </main> on every public page. Do not include home.partials.contacts or a standalone olive-only footer. design_credit stays after contacts. Edit contacts/footer/footer-main/footer-bar only.

## Cookie notice embeds {privacy} link
cookie_notice is one sentence with {privacy} placeholder; footer-bar replaces it with an <a href=route('privacy')> using privacy_title. Do not render the policy link as a separate sibling after the notice text.

## Cookie notice replaces copyright in footer-bar
footer-bar shows cookie_notice (with {privacy} → linked privacy_title) in place of copyright. Copyright is fallback only when cookie_notice is empty. Do not put a Политика link in footer-main nav.
