<div class="mx-auto max-w-6xl space-y-6" x-data="widgetSorter">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <div class="mb-1 flex items-center gap-2">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-brand-50 text-lg text-brand-700">◫</span>
                <h1 class="text-2xl font-black">{{ __('Widget layout') }}</h1>
            </div>
            <p class="text-sm text-ink-500">{{ __('Drag widgets to reorder them or move them between theme areas. Changes are saved automatically.') }}</p>
        </div>
        <button wire:click="create" class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-brand-700 hover:shadow-md">
            <span class="text-lg leading-none">+</span> {{ __('Add widget') }}
        </button>
    </div>

    <div class="flex min-h-6 items-center gap-2 text-sm" aria-live="polite">
        <span x-show="saving" x-cloak class="inline-flex items-center gap-2 text-ink-500">
            <span class="h-3.5 w-3.5 animate-spin rounded-full border-2 border-ink-200 border-t-brand-600"></span>
            {{ __('Saving layout…') }}
        </span>
        @if ($orderMessage)
            <span x-show="!saving" class="inline-flex items-center gap-1.5 font-semibold text-green-700">✓ {{ $orderMessage }}</span>
        @endif
        @error('order') <span class="font-semibold text-brand-700">{{ $message }}</span> @enderror
    </div>

    <div class="grid items-start gap-5 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($areas as $areaKey => $areaLabel)
            <section class="overflow-hidden rounded-2xl border border-ink-100 bg-white shadow-sm">
                <header class="flex items-center justify-between border-b border-ink-100 bg-gradient-to-r from-ink-50 to-white px-4 py-3.5">
                    <div>
                        <h2 class="font-black text-ink-800">{{ __($areaLabel) }}</h2>
                        <span class="font-mono text-[11px] text-ink-400">{{ $areaKey }}</span>
                    </div>
                    <span class="rounded-full bg-white px-2.5 py-1 text-xs font-bold text-ink-500 shadow-sm ring-1 ring-ink-100">
                        {{ to_bn_number(collect($widgetsByArea[$areaKey] ?? [])->count()) }}
                    </span>
                </header>
                <div data-widget-area="{{ $areaKey }}"
                     class="min-h-32 space-y-2.5 p-3 transition-colors"
                     :class="draggingId && 'bg-brand-50/40'"
                     @dragover.prevent="move($event)"
                     @drop.prevent="drop()">
                    @forelse (($widgetsByArea[$areaKey] ?? []) as $widget)
                        <article wire:key="widget-{{ $widget->id }}"
                                 data-widget-id="{{ $widget->id }}"
                                 draggable="true"
                                 @dragstart="start($event, {{ $widget->id }})"
                                 @dragend="draggingId = null"
                                 :class="draggingId === {{ $widget->id }} && 'is-dragging scale-[.98] opacity-40'"
                                 class="group flex cursor-grab items-center gap-3 rounded-xl border border-ink-100 bg-white p-3 shadow-sm transition hover:border-brand-200 hover:shadow-md active:cursor-grabbing">
                            <div class="text-ink-300 transition group-hover:text-brand-500" title="{{ __('Drag to move') }}">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M7 4a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0 6a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm-1.5 7.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3ZM16 4a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm-1.5 7.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Zm1.5 4.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="truncate text-sm font-bold text-ink-800">
                                    {{ $widget->getTranslation('title', app()->getLocale(), false) ?: __($types[$widget->type] ?? $widget->type) }}
                                </div>
                                <div class="mt-0.5 flex items-center gap-1.5 text-xs text-ink-400">
                                    <span>{{ __($types[$widget->type] ?? $widget->type) }}</span>
                                    @if (! $widget->is_active)
                                        <span class="rounded-full bg-amber-50 px-1.5 py-0.5 font-semibold text-amber-700">{{ __('Inactive') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center opacity-60 transition group-hover:opacity-100">
                                <button wire:click="edit({{ $widget->id }})" class="rounded-lg p-2 text-xs font-bold text-brand-600 hover:bg-brand-50">{{ __('Edit') }}</button>
                                <button wire:click="delete({{ $widget->id }})" wire:confirm="{{ __('Delete this widget?') }}" class="rounded-lg p-2 text-lg leading-none text-ink-300 hover:bg-brand-50 hover:text-brand-600">&times;</button>
                            </div>
                        </article>
                    @empty
                        <div class="pointer-events-none grid min-h-24 place-items-center rounded-xl border-2 border-dashed border-ink-100 text-center text-xs text-ink-400">
                            <span><strong class="block text-ink-500">{{ __('Empty area') }}</strong>{{ __('Drop a widget here') }}</span>
                        </div>
                    @endforelse
                </div>
            </section>
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

                    <div class="rounded-lg bg-ink-50 p-3">
                        <label class="flex items-center gap-2 text-sm font-semibold">
                            <input type="checkbox" wire:model="is_active" class="rounded border-ink-300 text-brand-600"> {{ __('Active') }}
                        </label>
                        <p class="mt-1 text-xs text-ink-400">{{ __('Use the widget board to control its position after saving.') }}</p>
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
