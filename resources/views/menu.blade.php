@extends('layouts.site')

@section('title', ($category?->title ? $category->title.' — ' : '').'Меню — ТЕТРИ')

@section('content')
    <section class="bg-cream px-4 pb-24 pt-10 sm:px-6 lg:px-8" data-entrance data-state="pending">
        <div class="mx-auto max-w-7xl">
            <x-site.mark
                :text="$settings['menu_section_eyebrow'] ?? null"
                ruled
                class="uppercase"
                data-entrance-fade
            />

            <div class="mt-4 flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                <h1 class="font-display text-4xl font-normal text-olive sm:text-5xl lg:text-6xl" data-entrance-title>
                    @typoBr($settings['menu_section_title'] ?? 'МЕНЮ')
                </h1>

                <x-site.mark
                    :text="$settings['menu_page_meta'] ?? null"
                    :note="$settings['menu_page_meta_note'] ?? null"
                    class="sm:items-end sm:text-right"
                    data-entrance-fade
                />
            </div>

            <div class="mt-10">
                <livewire:menu-grid :category-slug="$category?->slug" />
            </div>
        </div>
    </section>
@endsection
