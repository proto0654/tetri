---
paths:
  - 'database/seeders/**'
---

# Seeders

## Idempotent demo seeders (fill blanks only)
Demo seeders create-if-missing (firstOrCreate / exists-then-skip for models). SiteSettings: fill only blank keys (null / '' / []); never overwrite non-blank Filament/DB values. Download stock media only when that key is being filled. Full wipe/overwrite: migrate:fresh --seed only — never on plain db:seed.

## Stable seeder lookup keys
Stable lookup keys: category slug; menu item category_id + title or demo image path; story video_path (not title); user email. Never match demos by editable titles alone or re-seed will duplicate after Filament renames.
