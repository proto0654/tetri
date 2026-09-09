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

## Favicon via SiteSettings.favicon
Site favicon lives in SiteSettings key favicon (public disk path, upload in ManageSiteSettings «Подвал и CTA»). Render only from layouts/site.blade.php via x-site.favicon + PublicMedia::url — rel=icon (type by extension, sizes=any) and apple-touch-icon for non-SVG. Do not hardcode public/favicon.ico or add a second apple-touch field.

## MAX credentials in SiteSettings
max_bot_token and max_chat_id are SiteSettings JSON keys (not Spatie). BookingModal reads them via MaxNotificationService. Both are required on the Filament settings form.
