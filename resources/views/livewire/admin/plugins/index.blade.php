<div class="mx-auto max-w-5xl space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-black">{{ __('Plugins') }}</h1>
        <button wire:click="scan" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-semibold hover:bg-ink-50">↻ {{ __('Re-scan') }}</button>
    </div>
    <p class="text-sm text-ink-500">{{ __('Plugins live in the /plugins directory. Each has a plugin.json manifest and can register hooks and service providers.') }}</p>

    <div class="overflow-hidden rounded-xl border border-ink-100 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-ink-100 bg-ink-50 text-xs uppercase tracking-wide text-ink-500">
                <tr>
                    <th class="px-4 py-3">{{ __('Plugin') }}</th>
                    <th class="px-4 py-3">{{ __('Version') }}</th>
                    <th class="px-4 py-3">{{ __('Status') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-50">
                @forelse ($plugins as $slug => $plugin)
                    @php $isActive = in_array($slug, $activeSlugs, true); @endphp
                    <tr class="hover:bg-ink-50/50">
                        <td class="px-4 py-3">
                            <div class="font-semibold">{{ $plugin['name'] ?? $slug }}</div>
                            @if (! empty($plugin['description']))
                                <div class="mt-0.5 text-xs text-ink-400">{{ $plugin['description'] }}</div>
                            @endif
                            @if (! empty($plugin['author']))
                                <div class="text-xs text-ink-400">{{ __('by') }} {{ $plugin['author'] }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-ink-500">v{{ $plugin['version'] ?? '1.0.0' }}</td>
                        <td class="px-4 py-3">
                            <span @class([
                                'rounded-full px-2.5 py-0.5 text-xs font-semibold',
                                'bg-green-100 text-green-700' => $isActive,
                                'bg-ink-100 text-ink-600' => ! $isActive,
                            ])>{{ $isActive ? __('Active') : __('Inactive') }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if ($isActive)
                                <button wire:click="deactivate('{{ $slug }}')" class="font-semibold text-ink-500 hover:text-brand-600">{{ __('Deactivate') }}</button>
                            @else
                                <button wire:click="activate('{{ $slug }}')" class="font-semibold text-brand-600 hover:underline">{{ __('Activate') }}</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-10 text-center text-ink-400">{{ __('No plugins found in /plugins.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
