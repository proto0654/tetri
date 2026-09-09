@extends('layouts.site')

@section('title', 'ТЕТРИ — семейное кафе в Симферополе')

@section('content')
    @include('home.partials.hero')
    @include('home.partials.kids')
    @include('home.partials.menu-preview')
    @include('home.partials.concept')
    @include('home.partials.stories')
    @include('home.partials.contacts')
@endsection
