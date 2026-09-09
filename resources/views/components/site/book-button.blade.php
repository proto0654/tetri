@props([
    'label' => null,
    'source' => 'site',
    'class' => 'inline-flex rounded-full bg-plum px-6 py-3 text-sm font-semibold tracking-wide text-white transition hover:bg-plum-dark',
])

@php
    $text = $label
        ?? ($attributes->get('label'))
        ?? (app(\App\Settings\SiteSettings::class)->get('booking_cta_label') ?? 'ЗАБРОНИРОВАТЬ');
@endphp

<button
    type="button"
    {{ $attributes->merge(['class' => $class]) }}
    onclick="Livewire.dispatch('booking-open', { source: @js($source) })"
>
    {{ $slot->isEmpty() ? $text : $slot }}
</button>
