---
paths:
  - 'app/Models/*.php'
---

# Models

## Category image and columns
Category must include image (homepage menu-preview cards) and columns (2 or 3, default 3) for «Все меню» preview item count / block grid width. Single-category /menu view uses a fixed 4-column grid with paginate(12). Mockup overrides any entity summary that omits image/columns.

## Category columns is all-menu preview count
Category.columns is 2 or 3 (default 3): how many dishes to show in each «Все меню» block on /menu (and that block’s grid width). Single-category view always uses a 4-column grid with paginate(12); do not reuse columns for that density.
