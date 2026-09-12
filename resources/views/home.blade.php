@extends('layouts.site')

@section('title', $settings['home_seo_title'] ?? $settings['seo_title'] ?? 'ТЕТРИ — семейное кафе')

@section('meta_description', filled($settings['home_seo_description'] ?? null) ? $settings['home_seo_description'] : ($settings['seo_description'] ?? ''))

@section('content')
    @include('home.partials.hero')
    @include('home.partials.kids')
    @include('home.partials.menu-preview')
    @include('home.partials.concept')
    @include('home.partials.stories')
@endsection
