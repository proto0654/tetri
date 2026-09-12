@props(['settings'])

@php
    $designCredit = filled($settings['design_credit'] ?? null)
        ? $settings['design_credit']
        : 'Разработка сайта weblaba.ru';

    preg_match('/https?:\/\/[^\s]+|(?:[a-z0-9-]+\.)+[a-z]{2,}/iu', $designCredit, $designCreditMatch);
    $designCreditHref = $designCreditMatch[0] ?? null;
    if ($designCreditHref && ! str_starts_with(strtolower($designCreditHref), 'http')) {
        $designCreditHref = 'https://'.$designCreditHref;
    }
@endphp

<div {{ $attributes->class(['bg-olive-deep px-4 py-2 text-[0.65rem] text-cream/40 sm:px-6 lg:px-8']) }}>
    @if ($designCreditHref)
        <a href="{{ $designCreditHref }}" class="transition hover:text-cream/70" target="_blank" rel="noopener noreferrer">
            @typo($designCredit)
        </a>
    @else
        <span>@typo($designCredit)</span>
    @endif
</div>
