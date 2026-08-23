{{-- Small inline badges for a post (premium / breaking). Params: $post, $onDark --}}
@php
    $onDark = $onDark ?? false;
@endphp
@if ($post->is_premium)
    <span @class([
        'inline-flex items-center gap-0.5 rounded px-1.5 py-0.5 text-[11px] font-bold',
        'bg-amber-400 text-amber-950' => ! $onDark,
        'bg-amber-400/90 text-amber-950' => $onDark,
    ])>★ {{ __('premium') }}</span>
@endif
@if ($post->is_breaking)
    <span class="inline-flex items-center rounded bg-brand-600 px-1.5 py-0.5 text-[11px] font-bold text-white">{{ __('breaking') }}</span>
@endif
