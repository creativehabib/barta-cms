{{--
    Renders one active ad for a slot key. Params: $slot (string key), $class (optional).
    Ad image_path may be a full URL (admin stores URLs) or a stored path.
--}}
@php
    use Illuminate\Support\Str;
    $slotKey = $slot ?? null;
    $wrapClass = $class ?? 'my-4 text-center';
    $adSlot = $slotKey ? \App\Models\AdSlot::where('key', $slotKey)->first() : null;
    $ad = $adSlot?->activeAds()->inRandomOrder()->first();
    if ($ad) {
        $ad->incrementQuietly('impressions');
        $imgSrc = Str::startsWith((string) $ad->image_path, ['http://', 'https://', '/'])
            ? $ad->image_path
            : $ad->imageUrl();
    }
@endphp
@if ($ad)
    <div class="{{ $wrapClass }}" data-ad-slot="{{ $slotKey }}">
        @if ($ad->type === 'image' && ! empty($imgSrc))
            <a href="{{ $ad->link_url ?: '#' }}" target="_blank" rel="noopener sponsored">
                <img src="{{ $imgSrc }}" alt="{{ $ad->name }}"
                     class="mx-auto inline-block max-w-full rounded"
                     @if ($adSlot->width) width="{{ $adSlot->width }}" @endif
                     @if ($adSlot->height) height="{{ $adSlot->height }}" @endif
                     loading="lazy">
            </a>
        @else
            {!! $ad->content !!}
        @endif
    </div>
@endif
