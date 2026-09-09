---
paths:
  - app/Livewire/BookingModal.php
---

# Livewire

## Booking form required fields
BookingModal required: name, phone, date, guests. Comment optional. Validate on submit with Russian messages(); form uses novalidate so Livewire owns errors (not HTML5). Date must be today or later; guests 1–50; phone digits/spaces/+-().
