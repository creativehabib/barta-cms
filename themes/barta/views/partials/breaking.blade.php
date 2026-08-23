{{-- Scrolling breaking-news ticker. Hidden when there is no breaking news. --}}
@php($breakingItems = \App\Models\Post::published()->breaking()->latest('published_at')->take(8)->get())
@if ($breakingItems->isNotEmpty())
    <div class="border-b border-ink-100 bg-white">
        <div class="mx-auto flex max-w-7xl items-center gap-3 px-4 py-2">
            <span class="shrink-0 rounded bg-brand-600 px-2.5 py-1 text-xs font-bold uppercase tracking-wide text-white">
                {{ __('Breaking news') }}
            </span>
            <div class="barta-ticker flex-1 text-sm font-semibold text-ink-800">
                <div class="barta-ticker__track">
                    @foreach ($breakingItems as $item)
                        <a href="{{ $item->url() }}" class="mx-6 hover:text-brand-600">
                            {{ $item->getTranslation('title', app()->getLocale(), false) }}
                        </a>
                        <span class="text-brand-400">•</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif
