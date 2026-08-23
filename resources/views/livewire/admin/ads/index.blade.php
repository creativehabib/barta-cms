<div class="mx-auto max-w-5xl space-y-6">
    <h1 class="text-2xl font-black">{{ __('Advertisements') }}</h1>

    {{-- Slots --}}
    <div class="rounded-xl border border-ink-100 bg-white">
        <div class="flex items-center justify-between border-b border-ink-100 px-4 py-3">
            <h2 class="font-bold">{{ __('Ad slots') }}</h2>
            <button wire:click="createSlot" class="rounded-lg border border-ink-200 px-3 py-1.5 text-sm font-semibold hover:bg-ink-50">+ {{ __('New slot') }}</button>
        </div>
        <table class="w-full text-left text-sm">
            <thead class="bg-ink-50 text-xs uppercase tracking-wide text-ink-500">
                <tr>
                    <th class="px-4 py-3">{{ __('Key') }}</th>
                    <th class="px-4 py-3">{{ __('Name') }}</th>
                    <th class="px-4 py-3">{{ __('Size') }}</th>
                    <th class="px-4 py-3">{{ __('Ads') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-50">
                @forelse ($slots as $slot)
                    <tr class="hover:bg-ink-50/50">
                        <td class="px-4 py-3 font-mono text-xs text-ink-500">{{ $slot->key }}</td>
                        <td class="px-4 py-3 font-semibold">{{ $slot->name }}</td>
                        <td class="px-4 py-3 text-ink-500">{{ $slot->width ?: '—' }}&times;{{ $slot->height ?: '—' }}</td>
                        <td class="px-4 py-3 text-ink-500">{{ to_bn_number($slot->ads_count) }}</td>
                        <td class="px-4 py-3 text-right">
                            <button wire:click="editSlot({{ $slot->id }})" class="font-semibold text-brand-600 hover:underline">{{ __('Edit') }}</button>
                            <button wire:click="deleteSlot({{ $slot->id }})" wire:confirm="{{ __('Delete this slot and its ads?') }}" class="ml-2 text-ink-400 hover:text-brand-600">{{ __('Delete') }}</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-ink-400">{{ __('No ad slots yet.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Ads --}}
    <div class="rounded-xl border border-ink-100 bg-white">
        <div class="flex items-center justify-between border-b border-ink-100 px-4 py-3">
            <h2 class="font-bold">{{ __('Advertisements') }}</h2>
            <button wire:click="createAd" class="rounded-lg bg-brand-600 px-3 py-1.5 text-sm font-bold text-white hover:bg-brand-700">+ {{ __('New ad') }}</button>
        </div>
        <table class="w-full text-left text-sm">
            <thead class="bg-ink-50 text-xs uppercase tracking-wide text-ink-500">
                <tr>
                    <th class="px-4 py-3">{{ __('Name') }}</th>
                    <th class="px-4 py-3">{{ __('Slot') }}</th>
                    <th class="px-4 py-3">{{ __('Type') }}</th>
                    <th class="px-4 py-3">{{ __('Views / Clicks') }}</th>
                    <th class="px-4 py-3">{{ __('Status') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-50">
                @forelse ($ads as $ad)
                    <tr class="hover:bg-ink-50/50">
                        <td class="px-4 py-3 font-semibold">{{ $ad->name }}</td>
                        <td class="px-4 py-3 text-ink-500">{{ $ad->slot?->name ?? '—' }}</td>
                        <td class="px-4 py-3"><span class="rounded bg-ink-100 px-2 py-0.5 text-xs">{{ __(ucfirst($ad->type)) }}</span></td>
                        <td class="px-4 py-3 text-ink-500">{{ to_bn_number($ad->impressions) }} / {{ to_bn_number($ad->clicks) }}</td>
                        <td class="px-4 py-3">
                            <span @class([
                                'rounded-full px-2.5 py-0.5 text-xs font-semibold',
                                'bg-green-100 text-green-700' => $ad->is_active,
                                'bg-ink-100 text-ink-600' => ! $ad->is_active,
                            ])>{{ $ad->is_active ? __('Active') : __('Inactive') }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button wire:click="editAd({{ $ad->id }})" class="font-semibold text-brand-600 hover:underline">{{ __('Edit') }}</button>
                            <button wire:click="deleteAd({{ $ad->id }})" wire:confirm="{{ __('Delete this ad?') }}" class="ml-2 text-ink-400 hover:text-brand-600">{{ __('Delete') }}</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-ink-400">{{ __('No advertisements yet.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Slot modal --}}
    @if ($showSlotModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" wire:click.self="$set('showSlotModal', false)">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                <h2 class="mb-4 text-lg font-black">{{ $editingSlotId ? __('Edit slot') : __('New slot') }}</h2>
                <form wire:submit="saveSlot" class="space-y-3">
                    <div>
                        <label class="mb-1 block text-sm font-semibold">{{ __('Key') }}</label>
                        <input type="text" wire:model="slotKey" placeholder="header, sidebar, in-article…" class="w-full rounded-lg border-ink-200 font-mono text-sm focus:border-brand-500 focus:ring-brand-500">
                        @error('slotKey') <p class="mt-1 text-sm text-brand-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold">{{ __('Name') }}</label>
                        <input type="text" wire:model="slotName" class="w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500">
                        @error('slotName') <p class="mt-1 text-sm text-brand-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ __('Width (px)') }}</label>
                            <input type="number" wire:model="slotWidth" class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ __('Height (px)') }}</label>
                            <input type="number" wire:model="slotHeight" class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="$set('showSlotModal', false)" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-semibold hover:bg-ink-50">{{ __('Cancel') }}</button>
                        <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-bold text-white hover:bg-brand-700">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Ad modal --}}
    @if ($showAdModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" wire:click.self="$set('showAdModal', false)">
            <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
                <h2 class="mb-4 text-lg font-black">{{ $editingAdId ? __('Edit ad') : __('New ad') }}</h2>
                <form wire:submit="saveAd" class="space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ __('Slot') }}</label>
                            <select wire:model="ad_slot_id" class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                                @foreach ($slots as $slot)
                                    <option value="{{ $slot->id }}">{{ $slot->name }}</option>
                                @endforeach
                            </select>
                            @error('ad_slot_id') <p class="mt-1 text-sm text-brand-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ __('Type') }}</label>
                            <select wire:model.live="adType" class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                                <option value="image">{{ __('Image') }}</option>
                                <option value="html">{{ __('Custom HTML') }}</option>
                                <option value="adsense">{{ __('AdSense') }}</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-semibold">{{ __('Name') }}</label>
                        <input type="text" wire:model="adName" class="w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500">
                        @error('adName') <p class="mt-1 text-sm text-brand-600">{{ $message }}</p> @enderror
                    </div>

                    @if ($adType === 'image')
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ __('Image URL') }}</label>
                            <input type="text" wire:model="imagePath" placeholder="https://…/banner.jpg" class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ __('Link URL') }}</label>
                            <input type="url" wire:model="linkUrl" placeholder="https://" class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                            @error('linkUrl') <p class="mt-1 text-sm text-brand-600">{{ $message }}</p> @enderror
                        </div>
                    @else
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ __('Code / HTML') }}</label>
                            <textarea wire:model="content" rows="4" class="w-full rounded-lg border-ink-200 font-mono text-xs focus:border-brand-500 focus:ring-brand-500"></textarea>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ __('Starts') }}</label>
                            <input type="date" wire:model="startsAt" class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ __('Ends') }}</label>
                            <input type="date" wire:model="endsAt" class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                    </div>

                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" wire:model="adActive" class="rounded border-ink-300 text-brand-600"> {{ __('Active') }}
                    </label>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="$set('showAdModal', false)" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-semibold hover:bg-ink-50">{{ __('Cancel') }}</button>
                        <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-bold text-white hover:bg-brand-700">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
