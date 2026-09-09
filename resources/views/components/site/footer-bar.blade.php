@props([
    'settings',
    'ruled' => true,
])

@php
    $cookieNotice = $settings['cookie_notice'] ?? '';
    $privacyTitle = $settings['privacy_title'] ?? 'Политика конфиденциальности';
    $cookieNoticeParts = filled($cookieNotice) && str_contains($cookieNotice, '{privacy}')
        ? explode('{privacy}', $cookieNotice, 2)
        : null;
@endphp

<div {{ $attributes->class([
    'py-4 text-xs text-cream/60',
    'border-t border-cream/15' => $ruled,
]) }}>
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between sm:gap-6">
        <div class="min-w-0">
            @if ($cookieNoticeParts !== null)
                <p>
                    @typo($cookieNoticeParts[0])<a href="{{ route('privacy') }}" class="underline underline-offset-2 hover:text-cream">@typo($privacyTitle)</a>@typo($cookieNoticeParts[1])
                </p>
            @elseif (filled($cookieNotice))
                <p>@typo($cookieNotice)</p>
            @else
                <span>@typo($settings['copyright'] ?? '© ТЕТРИ')</span>
            @endif
        </div>
        <a href="#top" class="shrink-0 hover:text-cream">Наверх ↑</a>
    </div>
</div>
