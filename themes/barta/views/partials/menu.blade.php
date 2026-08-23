{{--
    Renders a horizontal menu with one level of dropdowns.
    Expects: $items (Collection of top-level MenuItem, each with ->children).
--}}
@foreach ($items as $item)
    @php
        $label = $item->getTranslation('label', app()->getLocale(), false)
            ?: $item->getTranslation('label', config('barta.default_locale', 'bn'), false);
    @endphp
    @if ($item->children->isNotEmpty())
        <li x-data="{ open: false }" class="relative" x-on:mouseleave="open = false">
            <button x-on:mouseenter="open = true" x-on:click="open = !open"
                    class="flex items-center gap-1 px-3 py-3 text-sm font-bold hover:bg-brand-700">
                {{ $label }}
                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <ul x-show="open" x-cloak x-transition
                class="absolute left-0 top-full z-50 min-w-48 rounded-b-lg border border-ink-100 bg-white py-1 text-ink-800 shadow-lg">
                @foreach ($item->children as $child)
                    <li>
                        <a href="{{ $child->resolveUrl() }}"
                           @if ($child->target === '_blank') target="_blank" rel="noopener" @endif
                           class="block px-4 py-2 text-sm font-semibold hover:bg-ink-50 hover:text-brand-600">
                            {{ $child->getTranslation('label', app()->getLocale(), false) ?: $child->getTranslation('label', config('barta.default_locale', 'bn'), false) }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </li>
    @else
        <li>
            <a href="{{ $item->resolveUrl() }}"
               @if ($item->target === '_blank') target="_blank" rel="noopener" @endif
               class="block px-3 py-3 text-sm font-bold hover:bg-brand-700">{{ $label }}</a>
        </li>
    @endif
@endforeach
