{{--
    Livewire full-page layout that renders a component inside the active theme's
    chrome (header, footer, SEO head, Livewire assets). Front-end Livewire pages
    declare #[Layout('layouts.theme')]; the component output arrives as $slot and
    is dropped into the theme layout's "content" section.
--}}
@extends('theme::layouts.app')

@section('content')
    {{ $slot }}
@endsection
