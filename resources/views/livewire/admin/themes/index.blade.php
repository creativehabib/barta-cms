<div class="mx-auto max-w-5xl space-y-5">
    <h1 class="text-2xl font-black">{{ __('Themes') }}</h1>
    <p class="text-sm text-ink-500">{{ __('Themes live in the /themes directory. Drop a new theme folder there and it appears here.') }}</p>

    <div class="grid gap-4 md:grid-cols-3">
        @forelse ($themes as $slug => $theme)
            <div @class([
                'overflow-hidden rounded-xl border bg-white',
                'border-brand-500 ring-2 ring-brand-100' => $slug === $active,
                'border-ink-100' => $slug !== $active,
            ])>
                <div class="flex aspect-video items-center justify-center bg-ink-100 text-ink-300">
                    @if (! empty($theme['screenshot']))
                        <img src="{{ $theme['screenshot'] }}" class="h-full w-full object-cover" alt="">
                    @else
                        <span class="text-4xl font-black">{{ strtoupper(substr($theme['name'] ?? $slug, 0, 1)) }}</span>
                    @endif
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold">{{ $theme['name'] ?? $slug }}</h3>
                        @if ($slug === $active)
                            <span class="rounded-full bg-brand-100 px-2 py-0.5 text-xs font-semibold text-brand-700">{{ __('Active') }}</span>
                        @endif
                    </div>
                    <p class="mt-1 text-xs text-ink-400">
                        v{{ $theme['version'] ?? '1.0.0' }}
                        @if (! empty($theme['author'])) · {{ $theme['author'] }} @endif
                    </p>
                    @if (! empty($theme['description']))
                        <p class="mt-2 text-sm text-ink-500">{{ $theme['description'] }}</p>
                    @endif
                    @if ($slug !== $active)
                        <button wire:click="activate('{{ $slug }}')" class="mt-3 w-full rounded-lg bg-brand-600 px-4 py-2 text-sm font-bold text-white hover:bg-brand-700">{{ __('Activate') }}</button>
                    @else
                        <button disabled class="mt-3 w-full rounded-lg border border-ink-200 px-4 py-2 text-sm font-semibold text-ink-400">{{ __('Currently active') }}</button>
                    @endif
                </div>
            </div>
        @empty
            <div class="md:col-span-3 rounded-xl border border-dashed border-ink-200 p-10 text-center text-ink-400">{{ __('No themes found in /themes.') }}</div>
        @endforelse
    </div>
</div>
