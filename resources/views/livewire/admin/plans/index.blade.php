<div class="mx-auto max-w-5xl space-y-5">
    @php
        $locale = app()->getLocale();
    @endphp
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-black">{{ __('Subscription plans') }}</h1>
        <button wire:click="create" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-bold text-white hover:bg-brand-700">+ {{ __('New plan') }}</button>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        @forelse ($plans as $plan)
            <div class="rounded-xl border border-ink-100 bg-white p-5">
                <div class="flex items-start justify-between">
                    <h3 class="text-lg font-black">{{ $plan->getTranslation('name', $locale, false) }}</h3>
                    @if (! $plan->is_active) <span class="rounded bg-ink-100 px-1.5 text-xs text-ink-500">{{ __('off') }}</span> @endif
                </div>
                <div class="mt-1 text-2xl font-black text-brand-600">
                    {{ money($plan->price) }}
                    <span class="text-sm font-semibold text-ink-400">/ {{ to_bn_number($plan->interval_count) }} {{ __($plan->interval) }}</span>
                </div>
                @if ($description = $plan->getTranslation('description', $locale, false))
                    <p class="mt-2 text-sm text-ink-500">{{ $description }}</p>
                @endif
                @if ($plan->features)
                    <ul class="mt-3 space-y-1 text-sm text-ink-600">
                        @foreach ($plan->features as $feature)
                            @php
                                $localizedFeature = is_array($feature)
                                    ? ($feature[$locale] ?? collect($feature)->first(fn ($value) => is_scalar($value)))
                                    : $feature;
                                $featureLabel = is_scalar($localizedFeature) ? (string) $localizedFeature : '';
                            @endphp
                            @if ($featureLabel !== '')
                                <li class="flex gap-2"><span class="text-green-600">✓</span> {{ $featureLabel }}</li>
                            @endif
                        @endforeach
                    </ul>
                @endif
                <div class="mt-4 flex items-center justify-between border-t border-ink-100 pt-3 text-sm">
                    <span class="text-ink-400">{{ to_bn_number($plan->subscriptions_count) }} {{ __('subscribers') }}</span>
                    <div>
                        <button wire:click="edit({{ $plan->id }})" class="font-semibold text-brand-600 hover:underline">{{ __('Edit') }}</button>
                        <button wire:click="delete({{ $plan->id }})" wire:confirm="{{ __('Delete this plan?') }}" class="ml-2 text-ink-400 hover:text-brand-600">{{ __('Delete') }}</button>
                    </div>
                </div>
            </div>
        @empty
            <div class="md:col-span-3 rounded-xl border border-dashed border-ink-200 p-10 text-center text-ink-400">{{ __('No plans yet.') }}</div>
        @endforelse
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" wire:click.self="$set('showModal', false)">
            <div class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-2xl bg-white p-6 shadow-xl">
                <h2 class="mb-4 text-lg font-black">{{ $editingId ? __('Edit plan') : __('New plan') }}</h2>
                <form wire:submit="save" class="space-y-3">
                    @foreach (barta_locales() as $loc)
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ __('Name') }} ({{ locale_name($loc) }})</label>
                            <input type="text" wire:model="name.{{ $loc }}" class="w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500">
                            @error('name.'.$loc) <p class="mt-1 text-sm text-brand-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ __('Description') }} ({{ locale_name($loc) }})</label>
                            <textarea wire:model="description.{{ $loc }}" rows="2" class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500"></textarea>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ __('Features') }} ({{ locale_name($loc) }})</label>
                            <textarea wire:model="featuresText.{{ $loc }}" rows="3" class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500" placeholder="{{ __('One feature per line') }}"></textarea>
                        </div>
                    @endforeach

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ __('Price') }}</label>
                            <input type="number" step="0.01" wire:model="price" class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                            @error('price') <p class="mt-1 text-sm text-brand-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ __('Currency') }}</label>
                            <input type="text" wire:model="currency" class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ __('Interval') }}</label>
                            <select wire:model="interval" class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                                <option value="day">{{ __('Day') }}</option>
                                <option value="week">{{ __('Week') }}</option>
                                <option value="month">{{ __('Month') }}</option>
                                <option value="year">{{ __('Year') }}</option>
                                <option value="lifetime">{{ __('Lifetime') }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ __('Interval count') }}</label>
                            <input type="number" min="1" wire:model="interval_count" class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ __('Slug') }}</label>
                            <input type="text" wire:model="slug" placeholder="{{ __('auto') }}" class="w-full rounded-lg border-ink-200 font-mono text-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ __('Position') }}</label>
                            <input type="number" wire:model="position" class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                    </div>

                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" wire:model="is_active" class="rounded border-ink-300 text-brand-600"> {{ __('Active') }}
                    </label>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="$set('showModal', false)" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-semibold hover:bg-ink-50">{{ __('Cancel') }}</button>
                        <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-bold text-white hover:bg-brand-700">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
