---
paths:
  - 'database/seeders/**'
---

# Seeders

## Idempotent demo seeders
Demo seeders must be create-if-missing (firstOrCreate / exists-then-skip). Never updateOrCreate for categories, menu items, stories, users, or SiteSettings — re-seed must not overwrite Filament/DB edits. Download stock media only when inserting a new row. Seed settings.key=site only when the row is absent. Intentional wipe: migrate:fresh --seed.

## Stable seeder lookup keys
Stable lookup keys: category slug; menu item category_id + title or demo image path; story video_path (not title); user email. Never match demos by editable titles alone or re-seed will duplicate after Filament renames.
