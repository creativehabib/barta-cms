<div class="mx-auto max-w-5xl space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-black">{{ __('Widgets') }}</h1>
        <button wire:click="create" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-bold text-white hover:bg-brand-700">+ {{ __('New widget') }}</button>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        @foreach ($areas as $areaKey => $areaLabel)
            <div class="rounded-xl border border-ink-100 bg-white p-4">
                <h3 class="mb-3 flex items-center gap-2 text-sm font-bold uppercase tracking-wide text-ink-500">
                    {{ __($areaLabel) }}
                    <span class="rounded bg-ink-100 px-1.5 font-mono text-xs text-ink-400">{{ $areaKey }}</span>
                </h3>
                <div class="space-y-2">
                    @forelse (($widgetsByArea[$areaKey] ?? []) as $widget)
                        <div class="flex items-center justify-between rounded-lg border border-ink-100 px-3 py-2">
                            <div>
                                <div class="text-sm font-semibold">{{ $widget->title ?: __($types[$widget->type] ?? $widget->type) }}</div>
                                <div class="text-xs text-ink-400">{{ __($types[$widget->type] ?? $widget->type) }} · {{ __('pos') }} {{ to_bn_number($widget->position) }}</div>
                            </div>
                            <div class="flex items-center gap-2">
                                @if (! $widget->is_active) <span class="rounded bg-ink-100 px-1.5 text-xs text-ink-500">{{ __('off') }}</span> @endif
                                <button wire:click="edit({{ $widget->id }})" class="text-sm font-semibold text-brand-600 hover:underline">{{ __('Edit') }}</button>
                                <button wire:click="delete({{ $widget->id }})" wire:confirm="{{ __('Delete this widget?') }}" class="text-ink-400 hover:text-brand-600">&times;</button>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-ink-400">{{ __('Empty area.') }}</p>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" wire:click.self="$set('showModal', false)">
            <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
                <h2 class="mb-4 text-lg font-black">{{ $editingId ? __('Edit widget') : __('New widget') }}</h2>
                <form wire:submit="save" class="space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ __('Area') }}</label>
                            <select wire:model="area" class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                                @foreach ($areas as $k => $label)
                                    <option value="{{ $k }}">{{ __($label) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ __('Type') }}</label>
                            <select wire:model="type" class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                                @foreach ($types as $k => $label)
                                    <option value="{{ $k }}">{{ __($label) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @foreach (barta_locales() as $loc)
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ __('Title') }} ({{ locale_name($loc) }})</label>
                            <input type="text" wire:model="title.{{ $loc }}" class="w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500">
                        </div>
                    @endforeach

                    <div>
                        <label class="mb-1 block text-sm font-semibold">{{ __('Settings (JSON)') }}</label>
                        <textarea wire:model="settingsJson" rows="4" class="w-full rounded-lg border-ink-200 font-mono text-xs focus:border-brand-500 focus:ring-brand-500" placeholder='{"count": 5}'></textarea>
                        <p class="mt-1 text-xs text-ink-400">{{ __('Common keys: "count" for recent/popular, "category_id" for category list, "html" for custom HTML, "slot" for ad.') }}</p>
                        @error('settingsJson') <p class="mt-1 text-sm text-brand-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ __('Position') }}</label>
                            <input type="number" wire:model="position" class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <label class="flex items-end gap-2 pb-2 text-sm">
                            <input type="checkbox" wire:model="is_active" class="rounded border-ink-300 text-brand-600"> {{ __('Active') }}
                        </label>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="$set('showModal', false)" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-semibold hover:bg-ink-50">{{ __('Cancel') }}</button>
                        <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-bold text-white hover:bg-brand-700">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
