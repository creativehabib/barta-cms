@extends('theme::layouts.app')

@section('content')
@php
    $loc = app()->getLocale();
    $title = $page->getTranslation('title', $loc, false);
    $body = (string) $page->getTranslation('body', $loc, false);
    $cover = $page->coverUrl('large');
@endphp
<div class="mx-auto max-w-3xl px-4 py-10">
    <h1 class="text-4xl font-black leading-tight">{{ $title }}</h1>
    <p class="mt-2 text-sm text-ink-400">{{ localized_number(optional($page->updated_at)->translatedFormat('j F Y')) }}</p>

    @if ($cover)
        <img src="{{ $cover }}" alt="{{ $title }}" class="mt-6 w-full rounded-2xl object-cover">
    @endif

    <div class="barta-article prose prose-lg mt-6 max-w-none font-serif text-ink-800">
        {!! $body !!}
    </div>
</div>
@endsection
