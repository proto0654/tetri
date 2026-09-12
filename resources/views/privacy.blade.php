@extends('layouts.site')

@section('title', ($settings['privacy_title'] ?? 'Политика конфиденциальности').' — ТЕТРИ')

@section('content')
    @php
        $privacyBody = $settings['privacy_body'] ?? '';
        $privacyParagraphs = filled($privacyBody)
            ? preg_split("/\n\s*\n/", trim($privacyBody)) ?: []
            : [];
    @endphp

    <section class="bg-cream px-4 pb-16 pt-10 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <h1 class="font-display text-4xl font-normal text-olive sm:text-5xl lg:text-6xl">
                @typoBr($settings['privacy_title'] ?? 'Политика конфиденциальности')
            </h1>

            <div class="mt-10 max-w-3xl space-y-5 text-base leading-relaxed text-ink">
                @forelse ($privacyParagraphs as $paragraph)
                    @if (filled(trim($paragraph)))
                        <p>@typo(trim($paragraph))</p>
                    @endif
                @empty
                    <p>@typo('Текст политики пока не заполнен.')</p>
                @endforelse
            </div>
        </div>
    </section>
@endsection
