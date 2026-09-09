---
paths:
  - 'app/Settings/**'
---

# Settings

## Site settings without Spatie package
Editable site copy/media uses settings.key=site JSON via App\Settings\SiteSettings and Filament ManageSiteSettings. Do not add spatie/laravel-settings unless explicitly approved.

## Section accent copy fields
Section accent/balance lines (eyebrows, asides) live in SiteSettings as *_eyebrow / *_aside / *_meta / kids_location_note / kids_description_secondary. Render with x-site.mark (◇). Do not hardcode these in Blade.

## Hero overlay gradient fields
Hero gradient overlay is CMS-driven via SiteSettings hero_overlay_from / hero_overlay_via / hero_overlay_to (rgba). Do not hardcode Tailwind gradient classes in hero.blade.php; sanitize with CssColor::resolve before inline style.

## Yandex map coords and marker label
Contacts map uses SiteSettings map_latitude, map_longitude, map_marker_label. Route CTA builds via YandexMap::routeUrl; iframe prefers map_embed_url, else YandexMap::widgetUrl with pt (~label).

## Partial SiteSettings saves merge existing
SiteSettings::save merges defaults → existing DB row → incoming data. Partial updates must not wipe unrelated keys. Use fillMissing() / seeder fill-blanks for demo gaps; list keys in the payload replace wholesale.
