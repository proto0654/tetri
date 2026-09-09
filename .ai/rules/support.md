---
paths:
  - 'app/Support/**'
---

# Support

## Heroicons for site icon fields
Public/site icons use Blade Heroicons (heroicon-o-*). Pick via HeroiconOptions::outlined() in Filament; render with x-site.icon. Legacy custom keys (cutlery, wine, etc.) are normalized in HeroiconOptions::normalize().

## Heroicon select previews
Filament Select allowHtml() previews must size SVGs with inline width/height (e.g. 20px), not Tailwind h-*/w-* — Filament/Choices containers stretch viewBox-only SVGs to huge sizes. Use HeroiconOptions::outlinedWithPreview() / previewSvgHtml().
