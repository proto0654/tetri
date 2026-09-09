---
paths:
  - 'resources/views/**'
---

# Views

## Mockup is frontend source of truth
Public Blade UI follows the TETRI mockup. Prefer mockup layout/copy over outdated stack notes (Laravel 11 / Filament v3) in external briefs. Use installed Laravel 13 + Filament 5 + Livewire 4 APIs.

## Typography via @typo for CMS copy
Render CMS/prose fields with @typo(...) (or Typograph::apply in PHP). x-site.mark already typographs text/note. Keep {{ }} only for non-copy values (URLs, phone numbers, prices).
